<?php

defined('BASEPATH') || exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'wtc_activity_log.id as id',
    'category',
    '1',
    '1',
    'response_code',
    db_prefix() . 'wtc_activity_log.rel_type as rel_type',
    'recorded_at',
    '1',
];

$join = [
    'LEFT JOIN ' . db_prefix() . 'wtc_campaigns ON ' . db_prefix() . 'wtc_campaigns.id = ' . db_prefix() . 'wtc_activity_log.category_id',
    'LEFT JOIN ' . db_prefix() . 'wtc_bot ON ' . db_prefix() . 'wtc_bot.id = ' . db_prefix() . 'wtc_activity_log.category_id',
];

$where = [];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'wtc_activity_log';

$additionalSelect = [
    db_prefix() . 'wtc_campaigns.template_id',
    db_prefix() . 'wtc_bot.name as bot_name',
    db_prefix() . 'wtc_campaigns.name as campaign_name',
];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $row[] = _l($aRow['category']);

    if ('Message Bot' == $aRow['category']) {
        $name = $aRow['bot_name'];
    } else {
        $template      =  wb_get_whatsapp_template($aRow['template_id']);
        $template_name = $template['template_name'] ?? _l('not_found_or_deleted');
        $name          = $aRow['campaign_name'];
    }
    $row[] = $name ?? _l('not_found_or_deleted');

    $row[] = $template_name ?? '-';

    $template_name = '-';

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
    $row[] = '<span class="label ' . $color . '">' . $aRow['response_code'] . '</span>';

    $row[] = _l($aRow['rel_type']);

    $row[] = $aRow['recorded_at'];

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';

    $options .= '<a href="' . admin_url('whatsbot/view_log_details/') . $aRow['id'] . '" class="btn btn-primary btn-icon"><i class="fa fa-eye"></i></a>';

    if (staff_can('clear_log', 'wtc_log_activity')) {
        $options .= '<a href="' . admin_url('whatsbot/delete_log/' . $aRow['id']) . '" data-id="' . $aRow['id'] . '" class="btn btn-danger btn-icon btn-lg _delete"><i class="fa-regular fa-trash-can"></i></a>';
    }

    $options .= '</div>';
    $row[]   = $options;

    $output['aaData'][] = $row;
}
