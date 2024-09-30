<?php

defined('BASEPATH') || exit('No direct script access allowed');

$aColumns = [
    'id',
    'title',
    'start_date',
    'end_date',
];

$sIndexColumn = 'id';

$sTable = db_prefix().'banner';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['status', 'admin_area', 'clients_area']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $row[] = $aRow['title'];

    $section = '';
    if (1 == $aRow['admin_area']) {
        $section .= '<span class="label label-success">'._l('admin').'</span> &nbsp; ';
    }
    if (1 == $aRow['clients_area']) {
        $section .= '<span class="label label-info">'._l('customer').'</span> &nbsp; ';
    }
    if (0 == $aRow['admin_area'] && 0 == $aRow['clients_area']) {
        $section .= '<span class="label label-default">'._l('no_section').'</span> &nbsp;';
    }
    $row[] = $section;

    $row[] = _d($aRow['start_date']);

    $row[] = _d($aRow['end_date']);

    $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="'._l('banner_status_tooltip').'">
    <input type="checkbox" data-switch-url="'.admin_url().'banner/changeBannerStatus" name="onoffswitch" class="onoffswitch-checkbox" id="'.$aRow['id'].'" data-id="'.$aRow['id'].'" '.(1 == $aRow['status'] ? 'checked' : '').'>
    <label class="onoffswitch-label" for="'.$aRow['id'].'"></label>
    </div>';
    $row[] = $toggleActive;

    $options = '';
    $options .= '<div class="tw-flex tw-items-center tw-space-x-3">';
    if (has_permission('banner', get_staff_user_id(), 'edit')) {
        $options .= '<a href="'.admin_url('banner/manage/').$aRow['id'].'" class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
            <i class="fa-regular fa-pen-to-square fa-lg"></i>
        </a>';
    }
    if (has_permission('banner', get_staff_user_id(), 'delete')) {
        $options .= '<a href="'.admin_url('banner/deleteBanner/').$aRow['id'].'" class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
            <i class="fa-regular fa-trash-can fa-lg"></i>
        </a>';
    }
    $options .= '</div>';
    $row[] = $options;

    $output['aaData'][] = $row;
}
