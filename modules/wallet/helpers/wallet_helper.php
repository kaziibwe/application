<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Function to activate wallet module
 *
 * @return void
 */
function wallet_activate_module()
{
    // Register as global Perfex SaaS module extension
    if (function_exists('perfex_saas_register_global_extension'))
        perfex_saas_register_global_extension('wallet');

    $CI = &get_instance();
    require_once(__DIR__ . '/../install.php');
}

/**
 * Processes a payment identified by the provided payment ID.
 * Will credit the wallet using the payment.
 *
 * @param int $paymentid The ID of the payment to be processed.
 * @return void
 */
function wallet_process_payment($paymentid)
{
    // Get payment details
    $CI = &get_instance();
    $CI->load->model('payments_model');
    $payment = $CI->payments_model->get($paymentid);

    // Check payment still exist
    if (!$payment) return false;

    // Ensure the payment is not made by wallet
    if ($payment->paymentmode === WALLET_MODULE_GATEWAY_NAME) return false;

    // Ensure payment not logged already
    $ref = $CI->wallet->ledger_find_by_ref($paymentid);
    if ($ref) return false;

    // Get log and validate
    $log = $CI->wallet->ledger_find_by_invoice_id($payment->invoiceid, ['mode' => Walletmanager::MODE_LOG, 'tag' => Walletmanager::TAG_FUNDING]);
    if (!$log) return;

    // Check if not already processed
    if ($log->mode !== Walletmanager::MODE_LOG) return false;

    try {

        $amount = $payment->amount;
        if ($amount > $log->amount)
            $amount = $log->amount;

        // Credit wallet with paid amount
        if (!$CI->wallet->credit(
            $amount,
            $log->description,
            $log->contact_id,
            $log->invoice_id,
            $paymentid,
            ['invoice_number' => format_invoice_number($log->invoice_id)]
        ))
            throw new Exception(_l('wallet_error_crediting_balance'), 1);

        if ($amount >= $log->amount) {
            // Remove the log
            $CI->wallet->unlog($log->id);
        } else {
            // Update log
            $left_amount = (float)$log->amount - (float)$amount;
            $CI->wallet->log_update($log->id, ['amount' => $left_amount]);
        }
        return true;
    } catch (\Throwable $th) {
        log_message('error', $th->getMessage());
    }

    return false;
}

/**
 * Initiates the automatic payment processing for overdue invoices using wallets.
 *
 * @return void
 */
function wallet_process_invoices_autopayment()
{
    if (!(int)get_option('wallet_enable_overdue_invoice_auto_payment')) return;

    load_client_language();

    $CI = &get_instance();
    $CI->load->model('invoices_model');
    $overdue = Invoices_model::STATUS_OVERDUE;
    $partially_paid = Invoices_model::STATUS_PARTIALLY;

    $wallet_table = $CI->wallet->wallet_table;
    $contact_table = db_prefix() . 'contacts';
    $invoice_table = db_prefix() . 'invoices';

    $CI->db->select($invoice_table . '.id,hash,status,date,number,duedate,recurring,cycles,clientid,is_primary,contact_id,total,subtotal');
    $CI->db->from($invoice_table);
    $CI->db->join($contact_table, $contact_table . '.userid = ' . $invoice_table . '.clientid', 'left');
    $CI->db->join($wallet_table, $wallet_table . '.contact_id = ' . $contact_table . '.id', 'left');
    $CI->db->where('is_primary', 1);
    $CI->db->where('contact_id > 0'); // Want to get those will wallet already
    $CI->db->where("(status=$overdue OR status=$partially_paid)"); // Overdue invoices or partialy paid
    $CI->db->distinct('id');
    $invoices = $CI->db->get()->result();

    foreach ($invoices as $invoice) {

        if ($invoice->status == $partially_paid) {
            // Invoice is with status partialy paid and its not due
            if (!$invoice->duedate) continue;
            if (date('Y-m-d') <= date('Y-m-d', strtotime($invoice->duedate))) {
                continue;
            }
        }

        if ($invoice->recurring && ($invoice->cycles == 'total_cycles' || $invoice->cycles = 0))
            continue;

        $total_left_to_pay = get_invoice_total_left_to_pay($invoice->id, $invoice->total);
        $data = [
            'amount' => $total_left_to_pay,
            'invoice' => $invoice,
            'invoiceid' => $invoice->id,
            'hash' => $invoice->hash,
            'wallet_contact_id' => $invoice->contact_id,
            'paymentmode' => WALLET_MODULE_GATEWAY_NAME, // Important set wallet as payment mode
            'no_redirection' => true,
        ];
        try {
            $CI->payments_model->process_payment($data, $invoice->id);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
    }
}

function wallet_log_reverse_payment($data)
{
    $paymentid = $data['paymentid'];
    $invoiceid = $data['invoiceid'];

    // Get payment details
    $CI = &get_instance();
    $CI->load->model('payments_model');
    $CI->payments_model->db->where('invoiceid', $invoiceid);
    $payment = $CI->payments_model->get($paymentid);

    // Check payment still exist
    if (!$payment) return false;

    // Ensure the payment is made by wallet
    // @todo Disable this line and support reversal for wallet funding
    if ($payment->paymentmode !== WALLET_MODULE_GATEWAY_NAME) return false;

    // Ensure payment transaction exist and have been fullfiled
    $ref = $CI->wallet->ledger_find_by_id((int)$payment->transactionid);
    if (!$ref || ($ref && $ref->mode === Walletmanager::MODE_LOG)) return false;

    // Ensure payment reversal not logged already
    $new_ref_id = 'REVERSAL-LOG-' . $paymentid;
    $reversal_ref = $CI->wallet->ledger_find_by_ref($new_ref_id);
    if ($reversal_ref) return false;

    try {
        // Log reversal
        if (!$CI->wallet->log(
            $payment->amount,
            'wallet_payment_reversal',
            $ref->contact_id,
            $ref->invoice_id,
            $new_ref_id,
            ['invoice_number' => format_invoice_number($ref->invoice_id), 'ref_description' => $ref->description],
            Walletmanager::TAG_REVERSAL
        ))
            throw new Exception(_l('online_payment_recorded_success_fail_database'), 1);

        return true;
    } catch (\Throwable $th) {
        log_message('error', $th->getMessage());
    }
    return false;
}

function wallet_reverse_payment($data)
{
    $paymentid = $data['paymentid'];
    $invoiceid = $data['invoiceid'];

    // Get payment details
    $CI = &get_instance();
    $CI->load->model('payments_model');

    $log_id = 'REVERSAL-LOG-' . $paymentid;
    $log = $CI->wallet->ledger_find_by_ref($log_id, ['invoice_id' => $invoiceid]);

    // Check payment still exist
    if (!$log) return false;

    // Ensure payment reversal not logged already
    $new_ref_id = 'REVERSAL-' . $paymentid;
    $reversal_ref = $CI->wallet->ledger_find_by_ref($new_ref_id);
    if ($reversal_ref) return false;

    try {
        // Credit wallet with previously paid amount
        $unlog = $CI->wallet->unlog($log->id);
        if (!$unlog)
            throw new Exception(_l('wallet_error_crediting_balance'), 1);

        $metadata = [];
        if (!empty($log->metadata))
            $metadata = (array)json_decode($log->metadata, true);

        if (!$CI->wallet->credit(
            $log->amount,
            $log->description,
            $log->contact_id,
            $log->invoice_id,
            $new_ref_id,
            $metadata,
            Walletmanager::TAG_REVERSAL
        ))
            throw new Exception(_l('wallet_error_crediting_balance'), 1);

        return true;
    } catch (\Throwable $th) {
        log_message('error', $th->getMessage());
    }
    return false;
}

/**
 * Process wallet payment from admin when 'Do not redirect to payment gateway' is checked
 *
 * @param array $data
 * @return mixed
 */
function wallet_before_payment_recorded_filter($data)
{
    $CI = &get_instance();
    $post_data = $CI->input->post();

    if (
        empty($data['transactionid']) &&
        $data['paymentmode'] === WALLET_MODULE_GATEWAY_NAME &&
        isset($post_data['do_not_redirect']) &&
        wallet_is_staff_logged_in()
    ) {
        // Making payment for invoice from admin and check 'Do not redirect to payment gateway'
        // We need to enforce this for wallet payments to ensure user wallet is charged before adding a payment with wallet
        $payment_data = ['amount' => $data['amount']];
        $payment_data['invoiceid'] = $data['invoiceid'];
        $payment_data['invoice']   = $CI->invoices_model->get($data['invoiceid']);

        $CI->load->model('payment_modes_model');
        $gateway = $CI->payment_modes_model->get($data['paymentmode']);
        $payment_data['gateway_fee'] = $gateway->instance->getFee($data['amount']);

        $CI->load->model('payment_attempts_model');
        $data['payment_attempt'] = $CI->payment_attempts_model->add([
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

        // Force payment through the wallet gateway
        $charge = $gateway->instance->process_payment($payment_data);

        if ($charge !== true) {
            $error_message = $charge === false ? _l('wallet_error_charging_balance') : $charge;
            set_alert('danger', $error_message);
        }

        wallet_redirect_back(admin_url('invoices/list_invoices/' . $data['invoiceid']));
        return;
    }
    return $data;
}

/**
 * Create invoice data
 *
 * @param float $amount
 * @param string $next_invoice_number
 * @param array $payment_modes
 * @param array $taxes
 * @param object $client
 * @param string $description
 * @return array
 */
function wallet_create_invoice_data(float $amount, string $next_invoice_number, $payment_modes, $taxes, $client, $description)
{
    $invoice_number = str_pad($next_invoice_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);

    $data = [
        "clientid" => $client->userid,
        "number" => $invoice_number,
        "date" => date('Y-m-d'),
        "tags" => '',
        "duedate" => '',
        "allowed_payment_modes" => $payment_modes,
        "currency" => get_base_currency()->id,
        "sale_agent" => "",
        "recurring" => "",
        "show_quantity_as" => "1",
        "newitems" => [
            [
                "order" => "1",
                "description" => $description,
                "long_description" => "",
                "qty" => "1",
                "unit" => "",
                "rate" => $amount,
                "taxname" => $taxes
            ]
        ],
        "subtotal" => $amount,
        "discount_percent" => "0",
        "discount_total" => "0.00",
        "adjustment" => "0",
        "total" => $amount,
        "billing_street" => $client->billing_street,
        "billing_city" => $client->billing_city,
        "billing_state" => $client->billing_state,
        "billing_zip" => $client->billing_zip,
        "billing_country" => $client->billing_country,
        "shipping_street" => $client->shipping_street,
        "shipping_city" => $client->shipping_city,
        "shipping_state" => $client->shipping_state,
        "shipping_zip" => $client->shipping_zip,
        "shipping_country" => $client->shipping_country,
    ];

    if (!empty($taxes)) {
        // Calculate taxes if available
        $total_tax = 0;
        foreach ($taxes as $key => $tax) {
            $tax = explode('|', $tax);
            $tax_amount = (float) end($tax);
            $total_tax += (($tax_amount / 100) * $data["subtotal"]);
        }
        $data["total"] = (float) $data["subtotal"] + $total_tax;
    }

    return $data;
}

/**
 * Generates an invoice for wallet funding or withdrawal based on provided parameters.
 *
 * @param int $clientid              The client ID
 * @param float  $amount             The amount to be invoiced.
 * @param string $next_invoice_number The next invoice number to use.
 * @param bool   $isFunding          Indicates whether the invoice is for funding (default: true).
 *
 * @return int|bool The ID of the generated invoice if successful, otherwise false.
 */
function wallet_generate_invoice(int $clientid, float $amount, string $next_invoice_number, bool $isFunding = true)
{
    $CI = &get_instance();
    $CI->load->model('payments_model');
    $CI->load->model('payment_modes_model');

    $payment_modes = get_option('wallet_allowed_payment_modes');
    $payment_modes = empty($payment_modes) ? [] : json_decode($payment_modes);
    $payment_modes = empty($payment_modes) ? ['1'] : $payment_modes;

    $taxes = get_option('wallet_taxname');
    $taxes = empty($taxes) ? [] : (array) json_decode($taxes);

    if (!$isFunding) {
        $payment_modes = [WALLET_MODULE_GATEWAY_NAME];
        $taxes = [];
    }

    // Additional logic to retrieve taxes and client data...
    $client = (object) $CI->clients_model->get($clientid);
    $is_staff = wallet_is_staff_logged_in();

    if ($is_staff)
        load_client_language($clientid);
    $desc = $isFunding ? _l('wallet_funding_with_invoice_desc') : _l('wallet_withdrawal_with_invoice_desc');
    if ($is_staff)
        load_admin_language();

    $data = wallet_create_invoice_data($amount, $next_invoice_number, $payment_modes, $taxes, $client, $desc);
    $add = $CI->invoices_model->add($data);

    return $add;
}

/**
 * Sends notification email based on the provided template and data.
 * Notifies via email using a specified template, email address, client ID, contact ID, and additional data.
 * @param string $template The email template identifier
 * @param int $client_id The client's ID
 * @param int $contact_id The contact's ID
 * @param array $data Additional data for the email
 * @param string $email The recipient's email address. When empty, it send to the admin
 * @return mixed
 */
function wallet_notify($template, $client_id, $contact_id, $data, $email)
{
    if (empty($email)) {
        $CI = &get_instance();
        $CI->load->model('staff_model');
        $admin = $CI->staff_model->db
            ->where('admin', '1')
            ->where('active', '1')
            ->order_by('staffid', 'ASC')
            ->limit(1)
            ->get(db_prefix() . 'staff')
            ->row();
        $email = $admin->email;
    }
    return send_mail_template($template, WALLET_MODULE_NAME, $email, $client_id, $contact_id, $data);
}

function wallet_total_pending_withdrawal()
{
    $CI = &get_instance();
    return $CI->wallet->db->where([
        'tag' => Walletmanager::TAG_WITHDRAWAL,
        'mode' => Walletmanager::MODE_LOG
    ])
        ->count_all_results($CI->wallet->ledger_table);
}

/**
 * Redirect back to preivous page
 *
 * @param string $fallback_url
 * @return void
 */
function wallet_redirect_back($fallback_url = '')
{
    if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
        redirect($_SERVER['HTTP_REFERER']);
    } else {
        redirect($fallback_url);
    }
}

/**
 * Determine if the current session is a real staff session
 *
 * @return void
 */
function wallet_is_staff_logged_in()
{
    $CI = &get_instance();
    return is_staff_logged_in() && $CI->session->userdata('logged_in_as_client') != true;
}


function wallet_get_transaction_description($trans)
{
    if ($trans->description == 'wallet_payment_reversal') {
        return _l($trans->description) . ': ' . _l($trans->metadata['ref_description'] ?? '', false);
    }
    if ($trans->description == 'wallet_invoice_payment_note')
        return _l($trans->description, $trans->metadata['invoice_number'] ?? '', false);

    return _l($trans->description, '', false);
}
