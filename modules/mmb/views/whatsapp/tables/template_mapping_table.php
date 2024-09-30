<?php

defined('BASEPATH') || exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'whatsapp_templates_mapping.id as id',
    db_prefix() . 'whatsapp_templates.template_name as template_name',
    db_prefix() . 'whatsapp_templates_mapping.category as category',
    'send_to',
    'active',
    'debug_mode',
];

$additionalSelect = [];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'whatsapp_templates_mapping';

$join = [
    'LEFT JOIN ' . db_prefix() . 'whatsapp_templates ON ' . db_prefix() . 'whatsapp_templates.id = ' . db_prefix() . 'whatsapp_templates_mapping.template_id',
];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[]            = $aRow['template_name'];
    $row[]            = $aRow['category'];

    $color = 'label-default';
    if ('contact' == $aRow['send_to']) {
        $color = 'label-danger';
    }
    if ('staff' == $aRow['send_to']) {
        $color = 'label-info';
    }

    $row[] = "<span class='label " . $color . "'>" . _l($aRow['send_to']) . '</span>';

    $is_active        = '';
    $is_debug_mode_on = '';
    if (1 == $aRow['active']) {
        $is_active = 'checked';
    }
    if (1 == $aRow['debug_mode']) {
        $is_debug_mode_on = 'checked';
    }
    $row[] = '<div class="onoffswitch">
    <input type="checkbox" data-switch-url="' . admin_url() . WHATSAPP_API_MODULE . '/template_mapping/change_status_hook" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $is_active . '>
    <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
    </div>';

    $row[] = '<div class="onoffswitch">
    <input type="checkbox" data-switch-url="' . admin_url() . WHATSAPP_API_MODULE . '/template_mapping/change_debug_status_hook" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '_debug_mode" data-id="' . $aRow['id'] . '" ' . $is_debug_mode_on . '>
    <label class="onoffswitch-label" for="c_' . $aRow['id'] . '_debug_mode"></label>
    </div>';

    $options = icon_btn(WHATSAPP_API_MODULE . '/template_mapping/add/' . $aRow['id'], 'pencil-square-o fa-solid fa-pencil edit', 'btn-default edit');
    $options .= icon_btn(WHATSAPP_API_MODULE . '/template_mapping/delete/' . $aRow['id'], 'remove fa-solid fa-trash', 'btn-danger _delete');

    $row[]   = $options;

    $output['aaData'][] = $row;
}
