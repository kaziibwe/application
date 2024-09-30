<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    get_sql_select_client_company(),
    'invoice_id',
    'amount',
    'mode',
    'tag',
    'description',
    'created_at',
];

$CI = &get_instance();

$sTable       = $CI->wallet->ledger_table;
$sIndexColumn = 'id';

$clientTable = db_prefix() . 'clients';
$contactTable = db_prefix() . 'contacts';
$join = [
    'LEFT JOIN ' . $contactTable . ' ON ' . $sTable . '.contact_id = ' . $contactTable . '.id',
    'LEFT JOIN ' . $clientTable . ' ON ' . $clientTable . '.userid = ' . $contactTable . '.userid'
];
$where = [];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [$sTable . '.id', $contactTable . '.userid', 'metadata']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $trans) {
    $trans = (object)$trans;
    $trans->metadata = empty($trans->metadata) ? [] : json_decode($trans->metadata, true);
    $invoice_number = format_invoice_number($trans->invoice_id);
    $row = [];
    $row[] = empty($invoice_number) ? ($trans->metadata['invoice_number'] ?? '') . ($trans->tag == Walletmanager::TAG_SYSTEM || $trans->mode == 'log' ? '-' : ' (' . trim(_l('deleted', '')) . ')') : '<a target="_blank" href="' . (!empty($trans->invoice_id) ? site_url(WALLET_MODULE_NAME . '/invoice/' . $trans->invoice_id) : '#') . '">' . $invoice_number . '</a>';
    $row[] = '<a href="' .  admin_url('clients/client/' . $trans->userid) . '" target="_blank">' . $trans->company . ' <i class="fa fa-external-link"></i></a>';
    $row[] = $trans->amount;
    $_data = '<span class="badge bg-' . ($trans->mode == 'credit' ? 'success' : ($trans->mode == 'debit' ? 'danger' : 'warning')) . '">' . $trans->mode . '</span>';
    if ($trans->mode === 'log' && $trans->tag === Walletmanager::TAG_FUNDING)
        $_data .= '<a class="tw-ml-2 text-danger" data-toggle="tooltip" title="' . _l('wallet_revalidate_funding') . '"
        href="' . site_url(WALLET_MODULE_NAME . '/revalidate_funding/' . $trans->id) . '">
        <i class="fa fa-refresh"></i>
    </a>';
    $row[] = $_data;

    $_data = _l('wallet_tag_' . $trans->tag);
    if ($trans->tag === Walletmanager::TAG_CANCELLED_WITHDRAWAL && !empty($trans->metadata)) {

        $admin_note = $trans->metadata['withdraw_admin_note'] ?? '';
        if (!empty($admin_note)) {
            $_data .= "<span class='tw-ml-2' data-title='$admin_note' data-toggle='tooltip'>
            <i class='fa fa-bell text-danger'></i></span>";
        }
    };
    $row[] = $_data;
    $row[] = wallet_get_transaction_description($trans);
    $row[] = $trans->created_at;
    $output['aaData'][] = $row;
}
