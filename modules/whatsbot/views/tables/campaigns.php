<?php

defined('BASEPATH') || exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'template_id',
    'rel_type',
    '1',
    '1',
    '1',
    '1',
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'wtc_campaigns';

$where = [];

$where[] = ' AND is_bot != 1';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    if (staff_can('show', 'wtc_campaign')) {
        $row[] = '<a href="'.admin_url('whatsbot/campaigns/view/'.$aRow['id']).'">'.$aRow['name'].'</a>';
    }

    if (!staff_can('show', 'wtc_campaign')) {
        $row[] = $aRow['name'];
    }

    $row[] = wb_get_whatsapp_template($aRow['template_id'])['template_name'];

    $color = ('leads' == $aRow['rel_type'] ? '#3a25e9' : ('contacts' == $aRow['rel_type'] ? '#ff4646' : '#7bf565'));
    $row[] = '<span class="label" style="color:'.$color.';border:1px solid '.adjust_hex_brightness($color, 0.4).';background: '.adjust_hex_brightness($color, 0.04).';">'._l($aRow['rel_type']).'</span>';

    $row[] = count(wb_get_campaign_data($aRow['id']));

    $row[] = total_rows(db_prefix().'wtc_campaign_data', ['status' => 2, 'campaign_id' => $aRow['id']]);

    $row[] = total_rows(db_prefix().'wtc_campaign_data', ['message_status' => 'read', 'campaign_id' => $aRow['id']]);

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';

    if (staff_can('edit', 'wtc_campaign')) {
        $options .= '<a href="'.admin_url('whatsbot/campaigns/campaign/'.$aRow['id']).'" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700" data-toggle="tooltip" data-title='._l('edit ').'><i class="fa-regular fa-pen-to-square fa-lg"></i></a>';
    }

    if (staff_can('delete', 'wtc_campaign')) {
        $options .= '<a href="'.admin_url('whatsbot/campaigns/delete/'.$aRow['id']).'" data-id='.$aRow['id'].' class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete" data-toggle="tooltip" data-title='._l('delete').'><i class="fa-regular fa-trash-can fa-lg"></i></a></div>';
    }

    if (!staff_can('edit', 'wtc_campaign') && !staff_can('delete', 'wtc_campaign')) {
        $options .= '-';
    }

    $row[] = $options;

    $output['aaData'][] = $row;
}
