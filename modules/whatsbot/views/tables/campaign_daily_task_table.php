<?php

defined('BASEPATH') || exit('No direct script access allowed');

$aColumns = [];
if (isset($rel_type) && 'leads' == $rel_type) {
    $aColumns[] = db_prefix().'leads.phonenumber as phonenumber';
    $aColumns[] = db_prefix().'leads.name as name';
} elseif (isset($rel_type) && 'contacts' == $rel_type) {
    $aColumns[] = db_prefix().'contacts.phonenumber as phonenumber';
    $aColumns[] = db_prefix().'contacts.firstname as name';
}
$aColumns[] = '1';
$aColumns[] = db_prefix().'wtc_campaign_data.status as status';

$join = [
    'LEFT JOIN '.db_prefix().'leads ON '.db_prefix().'leads.id = '.db_prefix().'wtc_campaign_data.rel_id',
    'LEFT JOIN '.db_prefix().'contacts ON '.db_prefix().'contacts.id = '.db_prefix().'wtc_campaign_data.rel_id',
    'LEFT JOIN '.db_prefix().'wtc_campaigns ON '.db_prefix().'wtc_campaigns.id = '.db_prefix().'wtc_campaign_data.campaign_id',
    'LEFT JOIN '.db_prefix().'wtc_templates ON '.db_prefix().'wtc_campaigns.template_id = '.db_prefix().'wtc_templates.id',
];

$where = [];

$where[] = 'AND campaign_id = '.$id;

$sIndexColumn = 'id';
$sTable       = db_prefix().'wtc_campaign_data';

$additionalSelect = [
    db_prefix().'wtc_campaign_data.id',
    db_prefix().'wtc_campaign_data.rel_id',
    db_prefix().'contacts.userid',
    db_prefix().'wtc_campaigns.header_params',
    db_prefix().'wtc_campaigns.body_params',
    db_prefix().'wtc_campaigns.footer_params',
    db_prefix().'wtc_templates.header_params_count',
    db_prefix().'wtc_templates.body_params_count',
    db_prefix().'wtc_templates.footer_params_count',
    db_prefix().'wtc_campaign_data.header_message',
    db_prefix().'wtc_campaign_data.body_message',
    db_prefix().'wtc_campaign_data.footer_message',
    db_prefix().'wtc_campaign_data.response_message',
    db_prefix().'contacts.lastname',
];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['phonenumber'];

    $row[] = $aRow['name'].' '.((isset($rel_type) && 'contacts' == $rel_type) ? $aRow['lastname'] : '');

    $message = wbParseText($rel_type, 'header', $aRow);
    $message .= wbParseText($rel_type, 'body', $aRow);
    $message .= wbParseText($rel_type, 'footer', $aRow);

    $row[] = $message;

    $status = wb_campaign_status($aRow['status']);
    $row[]  = '<div><span class="label '.$status['label_class'].'" data-toggle="tooltip" data-title="'.$aRow['response_message'].'">'.$status['label'].'</span></div>';

    $output['aaData'][] = $row;
}
