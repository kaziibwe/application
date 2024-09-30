<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'time_acction',
    'assets',
    'acction_code',
    'type',
    'amount',
    'acction_from',
    'acction_to',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'assets_acction_1';
$join         = [];
$where        = [];

if (isset($type)) {
    array_push($where, 'AND type = "'.$type.'"');
}

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
        if ('time_acction' == $aColumns[$i]) {
            $_data = _dt($aRow['time_acction']);
        } elseif ('type' == $aColumns[$i]) {
            $_data = _l($aRow['type']);
        } elseif ('acction_from' == $aColumns[$i]) {
            $_data = ' <a href="'.admin_url('staff/profile/'.$aRow['acction_from']).'">'.get_staff_full_name($aRow['acction_from']).'</a>';
        } elseif ('acction_to' == $aColumns[$i]) {
            $_data = ' <a href="'.admin_url('staff/profile/'.$aRow['acction_to']).'">'.get_staff_full_name($aRow['acction_to']).'</a>';
        } elseif ('assets' == $aColumns[$i]) {
            $_data = ' <a href="'.admin_url('assets/manage_assets#'.$aRow['assets']).'">'.get_asset_name_by_id($aRow['assets']).'</a>';
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
