<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('delivery_note_pdf_heading') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . $delivery_note_number . '</b>';

if (get_option('show_status_on_pdf_ei') == 1) {
    $info_right_column .= '<br /><span style="color:rgb(' . delivery_note_status_color_pdf($status) . ');text-transform:uppercase;">' . format_delivery_note_status($status, '', false) . '</span>';
}

// Add logo
$info_left_column .= pdf_logo_url();
// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(10);

$organization_info = '<div style="color:#424242;">';
$organization_info .= format_organization_info();
$organization_info .= '</div>';

// Delivery note to
$delivery_note_info = '<b>' . _l('delivery_note_to') . '</b>';
$delivery_note_info .= '<div style="color:#424242;">';
$delivery_note_info .= format_customer_info($delivery_note, 'delivery_note', 'billing');
$delivery_note_info .= '</div>';

// ship to to
if ($delivery_note->include_shipping == 1 && $delivery_note->show_shipping_on_delivery_note == 1) {
    $delivery_note_info .= '<br /><b>' . _l('ship_to') . '</b>';
    $delivery_note_info .= '<div style="color:#424242;">';
    $delivery_note_info .= format_customer_info($delivery_note, 'delivery_note', 'shipping');
    $delivery_note_info .= '</div>';
}

$delivery_note_info .= '<br />' . _l('delivery_note_data_date') . ': ' . _d($delivery_note->date) . '<br />';

if (!empty($delivery_note->reference_no)) {
    $delivery_note_info .= _l('reference_no') . ': ' . $delivery_note->reference_no . '<br />';
}

if ($delivery_note->sale_agent && get_option('show_sale_agent_on_delivery_notes') == 1) {
    $delivery_note_info .= _l('sale_agent_string') . ': ' . get_staff_full_name($delivery_note->sale_agent) . '<br />';
}

if ($delivery_note->project_id && get_option('show_project_on_delivery_note') == 1) {
    $delivery_note_info .= _l('project') . ': ' . get_project_name_by_id($delivery_note->project_id) . '<br />';
}

foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($delivery_note->id, $field['id'], 'delivery_note');
    if ($value == '') {
        continue;
    }
    $delivery_note_info .= $field['name'] . ': ' . $value . '<br />';
}

$left_info  = $swap == '1' ? $delivery_note_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $delivery_note_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
$items = delivery_notes_get_items_table_data($delivery_note, 'delivery_note', 'pdf');

$tblhtml = $items->table();

$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->Ln(8);
$tbltotal = '';
$tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
$total = $items->total_quantity();
$tbltotal .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('delivery_note_total') . '</strong></td>
    <td align="right" width="15%">' . $total . '</td>
</tr>';

$tbltotal .= '</table>';

$pdf->writeHTML($tbltotal, true, false, false, false, '');

if (get_option('total_to_words_enabled') == 1 && !delivery_note_item_field_hidden('amount')) {
    // Set the font bold
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->writeHTMLCell('', '', '', '', _l('num_word') . ': ' . $CI->numberword->convert($delivery_note->total, $delivery_note->currency_name), 0, 1, false, true, 'C', true);
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
}

if (!empty($delivery_note->clientnote)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('delivery_note_note'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $delivery_note->clientnote, 0, 1, false, true, 'L', true);
}

if (!empty($delivery_note->terms)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('terms_and_conditions') . ":", 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $delivery_note->terms, 0, 1, false, true, 'L', true);
}