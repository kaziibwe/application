<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wallet extends ClientsController
{

    protected $redirect_url;

    /**
     * Constructs a new instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->redirect_url = admin_url(WALLET_MODULE_NAME);

        $this->load->model('payments_model');
        $this->load->model('payment_modes_model');
    }

    /**
     *  Method to show the logged in contact wallet view and transactions
     */
    public function index()
    {
        if (!is_client_logged_in()) {
            redirect_after_login_to_current_url();
            return redirect(site_url('authentication/login'));
        }

        $data = array();
        $data['title'] = _l('wallet');
        $contact_id = get_contact_user_id();
        $data['currency'] = get_base_currency();
        $data['currency_code'] = $data['currency']->name;
        $data['min_funding_amount'] = (int)get_option('wallet_min_funding_amount');
        $data['max_funding_amount'] = (int)get_option('wallet_max_funding_amount');
        $data['contact_id'] = $contact_id;
        $data['contact'] = $this->clients_model->get_contact($contact_id);
        $data['transactions'] = $this->wallet->ledger($contact_id);
        $this->data($data);
        $this->view('wallet');
        $this->layout();
    }

    /**
     * Method to handle adding fund to the wallet and withdraw of fund      
     *
     * @return void
     */
    public function transact($contact_id = '')
    {
        if (!wallet_is_staff_logged_in() && !is_client_logged_in()) {
            redirect_after_login_to_current_url();
            return redirect(site_url('authentication/login'));
        }

        if (wallet_is_staff_logged_in() && !has_permission(WALLET_MODULE_NAME, '', 'transact')) {
            return access_denied(WALLET_MODULE_NAME);
        }

        $amount = (float)$this->input->post("amount", true);
        $transaction_type = $this->input->post("transaction_type", true);
        $transaction_type = empty($transaction_type) ? 'fund' : $transaction_type;
        $metadata = $this->input->post('metadata', true);

        $min_funding_amount = get_option(WALLET_MODULE_NAME . '_min_funding_amount');
        $max_funding_amount = get_option(WALLET_MODULE_NAME . '_max_funding_amount');
        if ($amount == 0 || !$amount || $amount < $min_funding_amount || ($max_funding_amount > -1 && $amount > $max_funding_amount)) {
            set_alert('danger', _l('wallet_invalid_funding_amount', [$min_funding_amount, $max_funding_amount]));
            return wallet_redirect_back($this->redirect_url);
        }

        $contact_id = (int) (is_client_logged_in() ? get_contact_user_id() : $contact_id);
        $contact = $this->clients_model->get_contact($contact_id);
        if (empty($contact->id)) {
            return wallet_redirect_back();
        }

        $next_invoice_number = get_option('next_invoice_number');

        // Adding fund to the wallet
        if ($transaction_type === 'fund') {

            $allowed_funding = (int)get_option('wallet_allow_funding');
            if (!wallet_is_staff_logged_in() && !$allowed_funding) {
                set_alert('danger', _l('wallet_funding_not_allowed'));
                return wallet_redirect_back($this->redirect_url);
            }

            $invoice_id = wallet_generate_invoice($contact->userid, $amount, $next_invoice_number, true);
            if (!$invoice_id) {
                set_alert('danger', ((object)$this->db->error())->message);
                return wallet_redirect_back($this->redirect_url);
            }

            try {
                // Log
                if (!$this->wallet->log(
                    $amount,
                    'wallet_funding_with_invoice_desc',
                    $contact_id,
                    $invoice_id,
                    $next_invoice_number . '' . time(),
                    ['invoice_number' => format_invoice_number($invoice_id), 'description' => _l('wallet_funding_with_invoice_desc')]
                ))
                    throw new \Exception(_l('wallet_error_adding_funding_log'), 1);
            } catch (\Throwable $th) {
                $this->invoices_model->delete($invoice_id, true);
                set_alert('danger', $th->getMessage());
                return wallet_redirect_back($this->redirect_url);
            }

            // Redirect to the invoice for payment
            $invoice = $this->invoices_model->get($invoice_id);
            $invoice_url    = site_url('invoice/' . $invoice->id . '/' . $invoice->hash);
            if (!is_client_logged_in()) {
                $invoice_url = admin_url('invoices/list_invoices/' . $invoice->id . '#' . $invoice->id);
            }
            return redirect($invoice_url);
        }

        // Withdrawal
        if ($transaction_type === 'withdraw') {

            $allowed_withdraw = (int)get_option('wallet_allow_withdraw');
            if (!wallet_is_staff_logged_in() && !$allowed_withdraw) {
                set_alert('danger', _l('wallet_withdrawal_not_allowed'));
                return wallet_redirect_back($this->redirect_url);
            }

            if (empty($metadata['withdraw_note']) || empty($metadata['withdraw_method'])) {
                set_alert('danger', _l('wallet_withdrawal_note_required'));
                return wallet_redirect_back($this->redirect_url);
            }

            if (empty($metadata['description'])) {
                $metadata['description'] = _l('wallet_withdrawal_with_invoice_desc');
            }

            try {
                // Log
                $log_id = $this->wallet->log(
                    $amount,
                    'wallet_withdrawal_with_invoice_desc',
                    $contact_id,
                    '',
                    time(),
                    $metadata,
                    Walletmanager::TAG_WITHDRAWAL
                );
                if (!$log_id)
                    throw new \Exception(_l('wallet_error_withdrawing_log'), 1);

                // Send notifications
                if (!is_client_logged_in()) {
                    try {
                        // Send email
                        $data = ['log' => $this->wallet->ledger_find_by_id($log_id)];
                        wallet_notify('wallet_withdraw_request_for_admin', $contact->userid, $contact_id, $data, '');
                    } catch (\Throwable $th) {
                        log_message('error', $th->getMessage());
                        set_alert('danger', $th->getMessage());
                    }
                }

                set_alert('success', _l('added_successfully', _l('wallet_withdraw_request')));

                if (!is_client_logged_in())
                    return redirect(admin_url('wallet/wallet_admin'));
            } catch (\Throwable $th) {
                set_alert('danger', $th->getMessage());
                return wallet_redirect_back();
            }
        }

        return wallet_redirect_back();
    }

    /**
     * Revalidate a log transaction
     *
     * @param int $log_id
     * @return void
     */
    public function revalidate_funding($log_id)
    {
        if (!wallet_is_staff_logged_in() && !is_client_logged_in()) {
            redirect_after_login_to_current_url();
            return redirect(site_url('authentication/login'));
        }

        $log = $this->wallet->ledger_find_by_id($log_id);
        if (!empty($log->invoice_id)) {
            $invoice_id = $log->invoice_id;
            // Get payments
            $payments = $this->payments_model->get_invoice_payments($invoice_id);
            if (empty($payments)) {
                set_alert('danger', _l('wallet_no_payments_found'));
            }

            foreach ($payments as $payment) {
                wallet_process_payment($payment['paymentid']);
            }
        }
        return wallet_redirect_back($this->redirect_url);
    }

    /**
     * Method to show a transaction invoice from invoice id.
     * This is needed as the core invoice controller requires hash.
     * We are saving complexity in transaction list by using this method.
     *
     * @param int $invoice_id
     * @return void
     */
    public function invoice($invoice_id)
    {
        $invoice = $this->invoices_model->get($invoice_id);
        $url = base_url('invoice/' . $invoice_id . '/' . $invoice->hash);
        if (wallet_is_staff_logged_in())
            $url = admin_url('invoices/list_invoices/' . $invoice->id . '#' . $invoice->id);
        return redirect($url);
    }
}
