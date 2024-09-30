<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wallet_gateway extends App_gateway
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
        $this->setId(WALLET_MODULE_GATEWAY_NAME);

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Wallet');

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
            echo '<p class="alert alert-info">' . _l(WALLET_MODULE_NAME . '_gateway_admin_note') . '</p>';
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
        $CI = &get_instance();

        $success = false;
        $invoice = $data['invoice'];
        $invoice_number = format_invoice_number($invoice->id);
        $amount = (float)$data['amount'];
        $currency = $invoice->currency_name;

        $invoiceUrl    = site_url('invoice/' . $invoice->id . '/' . $invoice->hash);

        try {

            $left_payment = get_invoice_total_left_to_pay($invoice->id, $invoice->total);
            if ($left_payment < $amount)
                throw new \Exception(_l('wallet_gateway_invalid_amount'), 1);

            if ($CI->wallet->ledger_find_by_invoice_id($invoice->id, ['mode' => Walletmanager::MODE_LOG, 'tag' => Walletmanager::TAG_FUNDING]))
                throw new \Exception(_l('wallet_gateway_invalid_payment_gateway'), 1);

            if ($currency !== get_base_currency()->name)
                throw new Exception(_l('wallet_unsupported_currency'), 1);

            check_invoice_restrictions($invoice->id, $invoice->hash);

            $contact_id = $data['wallet_contact_id'] ?? '';

            // Adding payment from payment gateway
            if (wallet_is_staff_logged_in() && empty($contact_id)) {
                $data['no_redirection'] = true;
                $contact_id = get_primary_contact_user_id($invoice->clientid);
            }

            if (empty($contact_id)) {
                if (!is_client_logged_in()) {
                    redirect_after_login_to_current_url();
                    return redirect(site_url('authentication/login'));
                }
                $contact_id = get_contact_user_id();
            }


            $available_balance = $CI->wallet->balance($contact_id);
            if ($available_balance < $amount)
                throw new Exception(_l('wallet_insufficient_balance'), 1);

            $CI->load->helper('string');

            $log =  (object)(!empty($data['log']) ? $data['log'] : []);

            $uid = !empty($log->ref_id) ? $log->id . '_' . $log->ref_id : $invoice_number . '_' . random_string('numeric', 4);
            // Create payment metadata
            $pay_data = array(
                'puid' => $uid,
            );

            if (!empty($log->id)) {
                $pay_data['log_id'] = $log->id;
            }

            $pay_data['invoice_number'] = $invoice_number;

            if (!empty($log->metadata)) {
                $metadata = !is_array($log->metadata) ? json_decode($log->metadata, true) : (array)$log->metadata;
                $pay_data = array_merge($metadata, $pay_data);
            }

            // Charge wallet directly
            $description = 'wallet_invoice_payment_note';
            if (!empty($data['wallet_description']))
                $description = $data['wallet_description'];

            $tag = Walletmanager::TAG_INVOICE_PAYMENT;
            if (!empty($data['wallet_tag']))
                $tag = $data['wallet_tag'];

            $charge_id = $CI->wallet->debit($amount, $description, $contact_id, $invoice->id, $uid, $pay_data, $tag);
            if (!$charge_id)
                throw new Exception(_l('wallet_error_charging_balance'), 1);

            $success = $CI->wallet_gateway->addPayment(
                [
                    'amount'                    => $amount,
                    'invoiceid'                 => $invoice->id,
                    'transactionid'             => $charge_id, // Important to set this to avoid race from function  wallet_before_payment_recorded_filter
                ]
            );

            if ($success) {
                set_alert('success', _l('online_payment_recorded_success'));
            } else {
                set_alert('danger', _l('online_payment_recorded_success_fail_database'));
            }
        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());
            set_alert('danger', $th->getMessage());
            if (!empty($data['return_error_string']))
                return $th->getMessage();
        }

        if (!empty($data['no_redirection'])) return $success;

        redirect($invoiceUrl);
    }
}
