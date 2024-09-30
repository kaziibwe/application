<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'assets_code',
    'assets_name',
    'asset_group',
    'amount',
    'unit_price',
    'total_allocation',
    'depreciation',
    'date_buy',
    'id',
    'unit',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'assets';
$join         = [
    'LEFT JOIN '.db_prefix().'asset_unit on '.db_prefix().'asset_unit.unit_id = '.db_prefix().'assets.unit',
    'LEFT JOIN '.db_prefix().'assets_group on '.db_prefix().'assets_group.group_id = '.db_prefix().'assets.asset_group',
];
$where = [];

if ($this->ci->input->post('groups')) {
    $where_group = '';
    $groups      = $this->ci->input->post('groups');

    foreach ($groups as $g) {
        if ('' != $g) {
            if ('' == $where_group) {
                $where_group .= ' and ('.db_prefix().'assets_group.group_id = '.$g;
            } else {
                $where_group .= ' or '.db_prefix().'assets_group.group_id = '.$g;
            }
        }
    }
    if ('' != $where_group) {
        $where_group .= ')';
        array_push($where, $where_group);
    }
}
if ($this->ci->input->post('assets')) {
    $where_assets = '';
    $assets       = $this->ci->input->post('assets');

    foreach ($assets as $g) {
        if ('' != $g) {
            if ('' == $where_assets) {
                $where_assets .= ' and ('.db_prefix().'assets.id = '.$g;
            } else {
                $where_assets .= ' or '.db_prefix().'assets.id = '.$g;
            }
        }
    }
    if ('' != $where_assets) {
        $where_assets .= ')';
        array_push($where, $where_assets);
    }
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['description', 'warranty_period', 'asset_location', 'depreciation', 'series', 'date_buy', 'group_name']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row         = [];
    $m           = (strtotime(date('Y-m-d')) - strtotime($aRow['date_buy'])) / (60 * 60 * 24 * 31);
    $d_per_month = ($aRow['unit_price'] * $aRow['amount']) / $aRow['depreciation'];
    for ($i = 0; $i < count($aColumns); ++$i) {
        $_data = $aRow[$aColumns[$i]];
        if ('date_buy' == $aColumns[$i]) {
            $_data = _d($aRow['date_buy']);
        } elseif ('unit_price' == $aColumns[$i]) {
            $up    = $aRow['unit_price'];
            $_data = app_format_money($up, '');
        } elseif ('total_allocation' == $aColumns[$i]) {
            $op    = $aRow['unit_price'] * $aRow['amount'];
            $_data = app_format_money($op, '');
        } elseif ('id' == $aColumns[$i]) {
            $_data = app_format_money($m * $d_per_month, '');
        } elseif ('unit' == $aColumns[$i]) {
            $_data = app_format_money($aRow['unit_price'] * $aRow['amount'] - $m * $d_per_month, '');
        } elseif ('asset_group' == $aColumns[$i]) {
            $_data = $aRow['group_name'];
        } elseif ('depreciation' == $aColumns[$i]) {
            $_data = $aRow['depreciation'].' '._l('month');
        } elseif ('amount' == $aColumns[$i]) {
            $_data = $aRow['amount'];
        } elseif ('assets_name' == $aColumns[$i]) {
            $_data = ' <a href="'.admin_url('assets/manage_assets#'.$aRow['id']).'">'.$aRow['assets_name'].'</a>';
        } elseif ('assets_code' == $aColumns[$i]) {
            $_data = ' <a href="'.admin_url('assets/manage_assets#'.$aRow['id']).'">'.$aRow['assets_code'].'</a>';
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
