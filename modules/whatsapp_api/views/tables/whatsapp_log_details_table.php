<?php

defined('BASEPATH') || exit('No direct script access allowed');
$aColumns = [
    'message_category',
    'response_code',
    'recorded_at',
    '1',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'whatsapp_api_debug_log';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [db_prefix() . 'whatsapp_api_debug_log.id']);
$output       = $result['output'];
$rResult      = $result['rResult'];
foreach ($rResult as $aRow) {
    $row                = [];
    $row[]              = $aRow['message_category'];

    $color = 'label-default';
    if ($aRow['response_code'] >= 200 && $aRow['response_code'] <= 299) {
        $color = 'label-success';
    }
    if ($aRow['response_code'] >= 300 && $aRow['response_code'] <= 399) {
        $color = 'label-info';
    }
    if ($aRow['response_code'] >= 400 && $aRow['response_code'] <= 499) {
        $color = 'label-warning';
    }
    if ($aRow['response_code'] >= 500 && $aRow['response_code'] <= 599) {
        $color = 'label-danger';
    }
    $row[]              = '<span class="label ' . $color . '">' . $aRow['response_code'] . '</span>';

    $row[]              = _dt($aRow['recorded_at']);
    $row[]              = '<a href="' . admin_url('whatsapp_api/whatsapp_log_details/get_whatsapp_api_log_info/') . $aRow['id'] . '" class="btn btn-info btn-icon"><i class="fa fa-eye"></i></a>';
    $output['aaData'][] = $row;
}
