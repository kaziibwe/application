<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'CONCAT(firstname, \' \', lastname) as name',
    'balance',
    'updated_at',
];

$CI = &get_instance();

$sTable       = $CI->wallet->wallet_table;
$sIndexColumn = 'id';

$contactTable = db_prefix() . 'contacts';
$join = [
    'LEFT JOIN ' . $contactTable . ' ON ' . $sTable . '.contact_id = ' . $contactTable . '.id',
];
$where = [];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [$sTable . '.id', $contactTable . '.userid', 'contact_id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $wallet) {
    $wallet = (object)$wallet;
    $row = [];

    $_data = '<a href="' .  admin_url('clients/client/' . $wallet->userid) . '" target="_blank">' . $wallet->name . ' <i class="fa fa-external-link"></i></a>';
    $_data .= '<div class="row-options text-center">';
    $_data .= '<a href="' . admin_url(WALLET_MODULE_NAME . '/wallet_admin/view_wallet/' . $wallet->contact_id) . '" class="text-danger">' . _l('view') . ' ' . _l('wallet') . '</a>';
    $_data .= '</div>';

    $row[] = $_data;
    $row[] = $CI->wallet->encryption->decrypt($wallet->balance);
    $row[] = $wallet->updated_at;
    $output['aaData'][] = $row;
    $row['DT_RowClass'] = 'has-row-options';
}