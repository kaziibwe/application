<?php

defined('BASEPATH') || exit('No direct script access allowed');
$aColumns = [
    'name',
    'request_url',
    'active',
    'debug_mode',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'webhooks_master';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output       = $result['output'];
$rResult      = $result['rResult'];
foreach ($rResult as $aRow) {
    $row              = [];
    $row[]            = $aRow['name'];
    $row[]            = $aRow['request_url'];
    $is_active        = '';
    $is_debug_mode_on = '';
    if (1 == $aRow['active']) {
        $is_active = 'checked';
    }
    if (1 == $aRow['debug_mode']) {
        $is_debug_mode_on = 'checked';
    }
    $row[] = '<div class="onoffswitch">
    <input type="checkbox" data-switch-url="' . admin_url() . WEBHOOKS_MODULE . '/webhooks/change_status_hook" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $is_active . '>
    <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
    </div>';
    $row[] = '<div class="onoffswitch">
    <input type="checkbox" data-switch-url="' . admin_url() . WEBHOOKS_MODULE . '/webhooks/change_debug_status_hook" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '_debug_mode" data-id="' . $aRow['id'] . '" ' . $is_debug_mode_on . '>
    <label class="onoffswitch-label" for="c_' . $aRow['id'] . '_debug_mode"></label>
    </div>';
    $options = icon_btn(WEBHOOKS_MODULE . '/webhooks/webhook/' . $aRow['id'], 'pencil-square-o fa-solid fa-pencil', 'btn-default', ['data-toggle' => 'tooltip', 'data-title' => _l('edit')]);
    $options .= icon_btn(WEBHOOKS_MODULE . '/webhooks/delete_webhook/' . $aRow['id'], 'remove fa-solid fa-trash', 'btn-danger _delete', ['data-toggle' => 'tooltip', 'data-title' => strtolower(_l('delete'))]);
    $options .= icon_btn(WEBHOOKS_MODULE . '/webhooks/copy_webhook/' . $aRow['id'], 'fa-solid fa-copy', 'btn-info', ['data-toggle' => 'tooltip', 'data-title' => _l('copy')]);

    $row[] = $options;

    $output['aaData'][] = $row;
}
