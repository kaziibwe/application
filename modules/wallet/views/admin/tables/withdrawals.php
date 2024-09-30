<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'ref_id',
    'CONCAT(firstname, \' \', lastname) as name',
    'amount',
    'contact_id',
    'metadata',
    'created_at',
];

$CI = &get_instance();

$sTable       = $CI->wallet->ledger_table;
$sIndexColumn = 'id';

$contactTable = db_prefix() . 'contacts';
$join = [
    'LEFT JOIN ' . $contactTable . ' ON ' . $sTable . '.contact_id = ' . $contactTable . '.id',
];
$where = ["AND `tag`='" . Walletmanager::TAG_WITHDRAWAL . "'", "AND `mode`='" . Walletmanager::MODE_LOG . "'"];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [$sTable . '.id', $contactTable . '.userid', 'ref_id']);

$output  = $result['output'];
$rResult = $result['rResult'];
$currency = get_base_currency();

foreach ($rResult as $log) {
    $log = (object)$log;
    $row = [];

    $row[] = '#' . $log->ref_id;
    $_data = '<a href="' .  admin_url(WALLET_MODULE_NAME . '/wallet_admin/view_wallet/' . $log->contact_id) . '" target="_blank">' . $log->name . ' <i class="fa fa-external-link"></i></a>';
    $row[] = $_data;

    $row[] = $log->amount;
    $metadata = (object)json_decode($log->metadata);
    $row[] = $metadata->withdraw_method ?? '';
    $row[] = $metadata->withdraw_note ?? '';
    $row[] = $log->created_at;

    $action  = '<div class="tw-flex tw-gap-2">';
    $action .= '<a class="btn btn-sm btn-danger" href="javascript:;" onclick="walletWithdrawOptionClick($(this));" data-withdrawinfo="' . $metadata->withdraw_method . ' #' . html_escape($log->ref_id) . ' - ' . app_format_money($log->amount, $currency) . '" data-href="' . admin_url(WALLET_MODULE_NAME . '/wallet_admin/update_withdraw_request/' . $log->id . '/cancel') . '"><i class="fa fa-trash"></i> ' . _l('wallet_withrdawal_cancel') . '</a>';
    $action .= '<a class="btn btn-sm btn-success" href="javascript:;" onclick="walletWithdrawOptionClick($(this));" data-withdrawinfo="' . $metadata->withdraw_method . ' #' . html_escape($log->ref_id) . ' - ' . app_format_money($log->amount, $currency) . '" data-href="' . admin_url(WALLET_MODULE_NAME . '/wallet_admin/update_withdraw_request/' . $log->id . '/approve') . '"><i class="fa fa-check"></i>' . _l('wallet_withrdawal_approve') . '</a>';
    $action .= '</div>';
    $row[] = $action;
    $output['aaData'][] = $row;
}
