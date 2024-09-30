<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This class describes class to manage wallet as admin
 */
class Wallet_admin extends AdminController
{

    protected $redirect_url;

    /**
     * Constructs a new instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->redirect_url = admin_url(WALLET_MODULE_NAME . '/wallet_admin');

        $this->load->model('payments_model');
        $this->load->model('payment_modes_model');
    }

    /**
     *  Method to show wallet view and transactions
     */
    public function index($table = '')
    {

        if (!has_permission(WALLET_MODULE_NAME, '', 'view'))
            return access_denied(WALLET_MODULE_NAME);

        // Return the table data for ajax request
        if ($this->input->is_ajax_request()) {
            if ($table == 'wallets')
                return $this->app->get_table_data(module_views_path(WALLET_MODULE_NAME, 'admin/tables/wallets'));

            if ($table == 'withdrawals')
                return $this->app->get_table_data(module_views_path(WALLET_MODULE_NAME, 'admin/tables/withdrawals'));

            $this->app->get_table_data(module_views_path(WALLET_MODULE_NAME, 'admin/tables/transactions'));
        }

        $data = array();
        $data['title'] = _l('wallet');
        $data['currency'] = get_base_currency();
        $data['currency_code'] = $data['currency']->name;
        $data['total_credited'] = $this->wallet->total_credit('');
        $data['total_debited'] = $this->wallet->total_debit('');
        $data['total_balance'] = $data['total_credited'] - $data['total_debited'];
        $data['pending_withdrawals'] = wallet_total_pending_withdrawal();
        $this->load->view('admin/overview', $data);
    }

    /**
     * Method to view a single contact wallet overview
     *
     * @param mixed $contact_id
     * @return void
     */
    public function view_wallet($contact_id)
    {
        if (!has_permission(WALLET_MODULE_NAME, '', 'view'))
            return access_denied(WALLET_MODULE_NAME);

        $data = array();
        $data['title'] = _l('wallet');
        $data['currency'] = get_base_currency();
        $data['currency_code'] = $data['currency']->name;
        $data['min_funding_amount'] = (int)get_option('wallet_min_funding_amount');
        $data['max_funding_amount'] = (int)get_option('wallet_max_funding_amount');
        $data['contact_id'] = $contact_id;
        $data['contact'] = $this->clients_model->get_contact($contact_id);
        $data['transactions'] = $this->wallet->ledger($contact_id);
        $this->load->view('admin/contact_wallet', $data);
    }

    /**
     * Update withdrawal request. Approve or cancel a withdraw request
     *
     * @param mixed $log_id
     * @param string $status
     * @return JSON string
     */
    public function update_withdraw_request($log_id, $status)
    {
        if (!has_permission(WALLET_MODULE_NAME, '', 'transact'))
            return access_denied(WALLET_MODULE_NAME);

        try {

            $log_id = (int)$log_id;

            // Get the log
            $log = $this->wallet->ledger_find_by_id($log_id);
            if ($log) {

                $success = false;
                $admin_note = $this->input->post('withdraw_admin_note', true) ?? '';
                $metadata = json_decode($log->metadata, true);
                $metadata['withdraw_admin_note'] = $admin_note;

                $contact_id = $log->contact_id;
                $contact = $this->clients_model->get_contact($contact_id);
                if (empty($contact->id))
                    throw new \Exception(_l("wallet_contact_not_found"), 1);


                if ($status === 'cancel') {
                    // Update metadata
                    $metadata['withdraw_status'] = _l('wallet_cancelled');
                    $log->metadata = json_encode($metadata); // Update for email notification reference
                    $success = $this->wallet->log_update($log_id, ['tag' => Walletmanager::TAG_CANCELLED_WITHDRAWAL, 'metadata' => json_encode($metadata)]);
                }

                if ($status === 'approve') {
                    // Generate Invoice
                    $next_invoice_number = get_option('next_invoice_number');
                    $invoice_id = wallet_generate_invoice($contact->userid, $log->amount, $next_invoice_number, false);
                    if (!$invoice_id) {
                        throw new \Exception(((object)$this->db->error())->message, 1);
                    }

                    if (!$this->wallet->can_transact_with_ref($log->id . '_' . $log->ref_id))
                        throw new \Exception(_l("wallet_reference_in_use"), 1);

                    // Update metadata
                    $metadata['withdraw_status'] = _l('wallet_approved');
                    $log->metadata = json_encode($metadata);
                    $this->wallet->log_update($log_id, ['tag' => 'processing', 'metadata' => json_encode($metadata)]);


                    $gateway = $this->payment_modes_model->get(WALLET_MODULE_GATEWAY_NAME);
                    if (!$gateway) {
                        throw new \Exception(_l("wallet_gateway_not_enabled"), 1);
                    }

                    $payment_data = ['amount' => $log->amount];
                    $payment_data['invoiceid'] = $invoice_id;
                    $payment_data['invoice']   = $this->invoices_model->get($invoice_id);
                    $payment_data['gateway_fee'] = $gateway->instance->getFee($payment_data['amount']);

                    $this->load->model('payment_attempts_model');
                    $data['payment_attempt'] = $this->payment_attempts_model->add([
                        'reference' => app_generate_hash(),
                        'amount' => $payment_data['amount'],
                        'fee' => $payment_data['gateway_fee'],
                        'invoice_id' => $payment_data['invoiceid'],
                        'payment_gateway' => $gateway->instance->getId()
                    ]);
                    $payment_data['amount']     += $payment_data['gateway_fee'];
                    $payment_data['amount_with_fee']     = $payment_data['amount'];

                    $payment_data['return_error_string'] = true;
                    $payment_data['no_redirection'] = true;
                    $payment_data['log'] = $log;
                    $payment_data['wallet_contact_id'] = $contact_id;
                    $payment_data['wallet_tag'] = Walletmanager::TAG_WITHDRAWAL;
                    $payment_data['wallet_description'] = $log->description;

                    // Force payment through the wallet gateway
                    $charge = $gateway->instance->process_payment($payment_data);

                    if ($charge !== true) {
                        $error_message = $charge === false ? _l('wallet_error_charging_balance') : $charge;
                        throw new \Exception($error_message, 1);
                    }

                    // Remove log
                    $success = $this->wallet->unlog($log->id);
                }

                // Send notification
                $data = ['log' => $log];
                wallet_notify('wallet_withdraw_request_updated', $contact->userid, $contact_id, $data, $contact->email);

                if (!$success)
                    throw new \Exception("Error Processing Request", 1);

                echo json_encode([
                    'status' => 'success',
                    'message' => _l('updated_successfully', _l('wallet_withdraw'))
                ]);
                exit;
            }
        } catch (\Throwable $th) {

            echo json_encode([
                'status' => 'danger',
                'message' => $th->getMessage()
            ]);
            exit;
        }
    }
}
