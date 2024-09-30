<?php

defined('BASEPATH') || exit('No direct script access allowed');

$aColumns = [
    'id',
    'template_name',
    'language',
    'category',
    'status',
    'body_data',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'whatsapp_templates';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], []);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];

    $row[] = $aRow['template_name'];

    $row[] = $aRow['language'];

    $row[] = $aRow['category'];
    if ('APPROVED' == $aRow['status']) {
        $status = '<span class="inline-block label label-success">' . $aRow['status'] . '</span>';
    }

    $row[] = $status;
    $row[] = $aRow['body_data'];

    $output['aaData'][] = $row;
}
