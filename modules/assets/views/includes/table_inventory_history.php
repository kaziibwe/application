<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'date_time',
    'acction',
    'inventory_begin',
    'inventory_end',
    'cost',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'inventory_history';
$join         = [];
$where        = [];

if (isset($asset_id)) {
    array_push($where, 'AND assets = '.$asset_id);
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); ++$i) {
        $_data = $aRow[$aColumns[$i]];
        if ('date_time' == $aColumns[$i]) {
            $_data = _dt($aRow['date_time']);
        } elseif ('acction' == $aColumns[$i]) {
            $_data = _l($aRow['acction']);
        } elseif ('cost' == $aColumns[$i]) {
            $_data = app_format_money($aRow['cost'], '');
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
