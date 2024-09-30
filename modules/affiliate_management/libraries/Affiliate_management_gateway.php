<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Affiliate_management_gateway extends App_gateway
{

    public function __construct()
    {
        /**
         * Call App_gateway __construct function
         */
        parent::__construct();


        $this->ci = &get_instance();
        $this->ci->load->database();

        /**
         * Gateway unique id - REQUIRED
         */
        $this->setId(AFFILIATE_MANAGEMENT_MODULE_NAME . '_gateway');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Affiliate Earnings');

        /**
         * Add gateway settings. This is used for generating UI setting by perfex core
         */
        $this->setSettings([
            [
                'name'              => 'currencies',
                'label'             => 'settings_paymentmethod_currencies',
                'default_value'     => get_base_currency()->name
            ]
        ]);

        // Notice hook
        hooks()->add_action('before_render_payment_gateway_settings', [$this, 'admin_notice']);
    }

    /**
     * Add Payment Gateway Notice
     *
     * @param  array $gateway
     *
     * @return void
     */
    function admin_notice($gateway)
    {
        if ($gateway['id'] == $this->id) {
            echo '<p class="alert alert-info">' . _l(AFFILIATE_MANAGEMENT_MODULE_NAME . '_gateway_admin_note') . '</p>';
        }
    }

    /**
     * Each time a customer click PAY NOW button on the invoice HTML area, the script will process the payment via this function.
     * You can show forms here, redirect to gateway website, redirect to Codeigniter controller etc..
     * @param  array $data - Contains the total amount to pay and the invoice information
     * @return mixed
     */
    public function process_payment($data)
    {
        $invoice = $data['invoice'];
        $invoice_number = format_invoice_number($invoice->id);
        $amount = (float)$data['amount'];
        $currency = $invoice->currency_name;

        //ensure the requst is ajax
        $invoiceUrl    = site_url('invoice/' . $data['invoice']->id . '/' . $data['invoice']->hash);

        try {

            if ($currency !== get_base_currency()->name)
                throw new Exception(_l('affiliate_management_unsupported_currency'), 1);

            check_invoice_restrictions($data['invoiceid'], $data['hash']);

            if (!is_client_logged_in()) {
                redirect_after_login_to_current_url();
                return redirect(site_url('authentication/login'));
            }


            $CI = &get_instance();

            // Get affiliate and check balance.
            $contact_id = get_contact_user_id();

            $CI->affiliate_management_model->db->trans_begin();

            $affiliate = $CI->affiliate_management_model->get_affiliate_by_contact_id($contact_id);

            if (!$affiliate)
                throw new \Exception(_l('affiliate_management_gateway_not_enrolled'), 1);

            $available_balance = $CI->affiliate_management_model->get_affiliate_available_balance((int)$affiliate->affiliate_id);
            if ($available_balance < $amount)
                throw new Exception(_l('affiliate_management_insufficient_balance'), 1);

            $data = [
                'amount' => $amount,
                'note_for_affiliate' => _l('affiliate_management_gateway_invoice_payment_note_for_affiliate', $invoice_number),
                'note_for_admin' => _l('affiliate_management_gateway_invoice_payment_note_for_admin', $invoice_number),
                'payout_method' => $invoice_number,
                'affiliate_id' => $affiliate->affiliate_id,
                'status' => AffiliateManagementHelper::STATUS_APPROVED
            ];

            // Add payout and deduct from affiliate balance
            if (($payout_id = $CI->affiliate_management_model->add_payout($data))) {

                // Deduct the affiliate balance
                $new_balance = (float)$affiliate->balance - $amount;
                if (!$CI->affiliate_management_model->update_affiliate($affiliate->affiliate_id, ['balance' => $new_balance]))
                    throw new \Exception(_l('affiliate_management_error_update_balance'), 1);

                $success = $CI->affiliate_management_gateway->addPayment(
                    [
                        'amount'                    => $amount,
                        'invoiceid'                 => $invoice->id,
                        'transactionid'             => $payout_id,
                    ]
                );

                if ($success) {
                    set_alert('success', _l('online_payment_recorded_success'));
                } else {
                    set_alert('danger', _l('online_payment_recorded_success_fail_database'));
                }
            }

            $CI->affiliate_management_model->db->trans_commit();
        } catch (\Throwable $th) {

            set_alert('danger', $th->getMessage());
            $CI->affiliate_management_model->db->trans_rollback();
        }

        redirect($invoiceUrl);
    }
}