<?php

defined('BASEPATH') || exit('No direct script access allowed');

$aColumns = [
    'name',
    'bot_type',
    db_prefix().'wtc_campaigns.trigger as trigger_message',
    'rel_type',
    'is_bot_active',
];

$where = [];

$where[] = ' AND is_bot = 1';

$sIndexColumn = 'id';
$sTable       = db_prefix().'wtc_campaigns';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['name'];

    $row[] = _l('template_bot') . ' : ' . str_replace('Reply bot: ', '', wb_get_reply_type($aRow['bot_type'])['label']);

    $row[] = ($aRow['bot_type'] == 3 ||  $aRow['bot_type'] == 4) ? '-' : $aRow['trigger_message'];

    $color = ('leads' == $aRow['rel_type'] ? '#3a25e9' : ('contacts' == $aRow['rel_type'] ? '#ff4646' : '#7bf565'));
    $row[] = '<span class="label" style="color:' . $color . ';border:1px solid ' . adjust_hex_brightness($color, 0.4) . ';background: ' . adjust_hex_brightness($color, 0.04) . ';">' . _l($aRow['rel_type']) . '</span>';

    $checked = '';
    if (1 == $aRow['is_bot_active']) {
        $checked = 'checked';
    }

    $row[] = '<div class="onoffswitch">
                <input type="checkbox" data-switch-url="'.admin_url('whatsbot/bots/change_active_status/template').'" name="onoffswitch" class="onoffswitch-checkbox" id="c_'.$aRow['id'].'" data-id="'.$aRow['id'].'" '.$checked.'>
                <label class="onoffswitch-label" for="c_'.$aRow['id'].'"></label>
            </div>';

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';

    if (staff_can('edit', 'wtc_template_bot')) {
        $options .= '<a href="'.admin_url('whatsbot/bots/bot/template/'.$aRow['id']).'" data-toggle="tooltip" data-title="'._l('edit').'" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"><i class="fa-regular fa-pen-to-square fa-lg"></i></a>';
    }

    if (staff_can('delete', 'wtc_template_bot')) {
        $options .= '<a href="javascript:void(0)" data-id="'.$aRow['id'].'" data-type="template" id="delete_message_bot" data-toggle="tooltip" data-title="'._l('delete').'" class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete"><i class="fa-regular fa-trash-can fa-lg"></i></a>';
    }

    if (staff_can('clone_bot', 'wtc_template_bot')) {
        $options .= '<a href="' . admin_url('whatsbot/bots/clone_bot/template/' . $aRow['id']) . '" data-toggle="tooltip" data-title="' . _l('clone') . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"><i class="fa-regular fa-copy fa-lg "></i></a>';
    }

    if (!staff_can('edit', 'wtc_template_bot') && !staff_can('delete', 'wtc_template_bot') && !staff_can('clone_bot', 'wtc_template_bot')) {
        $options .= '-';
    }
    $options .= '</div>';

    $row[] = $options;

    $output['aaData'][] = $row;
}
