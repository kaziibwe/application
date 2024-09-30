<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'description',
    'active',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'payment_modes';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
    'expenses_only',
    'invoices_only',
    'show_on_pdf',
    'selected_by_default',
    ]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'active') {
            $checked = $aRow['active'] == 1 ? 'checked' : '';
            
            $_data = '<div class="onoffswitch">
                <input type="checkbox" data-switch-url="' . admin_url() . 'paymentmodes/change_payment_mode_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . e($aRow['id']) . '" data-id="' . e($aRow['id']) . '" ' . $checked . '>
                <label class="onoffswitch-label" for="c_' . e($aRow['id']) . '"></label>
            </div>';
            // For exporting
            $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
        } elseif ($aColumns[$i] == 'name' || $aColumns[$i] == 'id') {
            $_data = '<a href="#" data-toggle="modal" data-default-selected="' . e($aRow['selected_by_default']) . '" data-show-on-pdf="' . e($aRow['show_on_pdf']) . '" data-target="#payment_mode_modal" data-expenses-only="' . e($aRow['expenses_only']) . '" data-invoices-only="' . e($aRow['invoices_only']) . '" data-id="' . e($aRow['id']) . '">' . e($_data) . '</a>';
        } elseif ($aColumns[$i] == 'description') {
            $_data = process_text_content_for_display($_data);
        }

        $row[] = $_data;
    }

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';
    $options .= '<a href="#" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700" ' . _attributes_to_string([
        'data-toggle'           => 'modal',
        'data-target'           => '#payment_mode_modal',
        'data-id'               => e($aRow['id']),
        'data-expenses-only'    => e($aRow['expenses_only']),
        'data-invoices-only'    => e($aRow['invoices_only']),
        'data-show-on-pdf'      => e($aRow['show_on_pdf']),
        'data-default-selected' => e($aRow['selected_by_default']),
        ]) . '>
        <i class="fa-regular fa-pen-to-square fa-lg"></i>
    </a>';

    $options .= '<a href="' . admin_url('paymentmodes/delete/' . $aRow['id']) . '"
    class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
        <i class="fa-regular fa-trash-can fa-lg"></i>
    </a>';
    $options .= '</div>';

    $row[] = $options;

    $output['aaData'][] = $row;
}