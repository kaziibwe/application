<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'asset_image',
    'asset_image',
    'assets_code',
    'assets_name',
    'asset_group',
    'date_buy',
    'total_allocation',
    'amount',
    'unit_price',
    'unit',
    'department',
    'belongs_to',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'assets';
$join         = [
    'LEFT JOIN '.db_prefix().'asset_unit on '.db_prefix().'asset_unit.unit_id = '.db_prefix().'assets.unit',
    'LEFT JOIN '.db_prefix().'assets_group on '.db_prefix().'assets_group.group_id = '.db_prefix().'assets.asset_group',
    'LEFT JOIN '.db_prefix().'departments on '.db_prefix().'departments.departmentid = '.db_prefix().'assets.department',
    'LEFT JOIN '.db_prefix().'clients on find_in_set('.db_prefix().'clients.userid, '.db_prefix().'assets.belongs_to)'
];
$where = [];

if (isset($status)) {
    if (1 == $status) {
        array_push($where, 'AND total_allocation = 0');
    } elseif (2 == $status) {
        array_push($where, 'AND total_allocation > 0');
    } elseif (3 == $status) {
        array_push($where, 'AND total_liquidation > 0');
    } elseif (4 == $status) {
        array_push($where, 'AND total_warranty > 0');
    } elseif (5 == $status) {
        array_push($where, 'AND total_lost > 0');
    } elseif (6 == $status) {
        array_push($where, 'AND total_damages > 0');
    }
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'description', 'warranty_period', 'asset_location', 'depreciation', 'series', 'supplier_name', 'supplier_address', 'supplier_phone', 'unit_name', 'group_name', db_prefix().'departments.name as dpm_name', 'visible_to_client','company'], ' GROUP BY '.db_prefix().'assets.id');

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); ++$i) {
        $_data = $aRow[$aColumns[$i]];
        if ('date_buy' == $aColumns[$i]) {
            $_data = _d($aRow['date_buy']);
        } elseif ('unit_price' == $aColumns[$i]) {
            $op    = $aRow['unit_price'] * $aRow['amount'];
            $_data = app_format_money($op, '');
        } elseif ('unit' == $aColumns[$i]) {
            $_data = $aRow['unit_name'];
        } elseif ('asset_group' == $aColumns[$i]) {
            $_data = $aRow['group_name'];
        } elseif ('department' == $aColumns[$i]) {
            $_data = $aRow['dpm_name'];
        } elseif ('amount' == $aColumns[$i]) {
            $_data = $aRow['amount'] - $aRow['total_allocation'];
        } elseif ('assets_name' == $aColumns[$i]) {
            $name = '<a href="'.admin_url('assets/manage_assets/'.$aRow['id']).'" onclick="init_asset('.$aRow['id'].'); return false;">'.$aRow['assets_name'].'</a>';

            $name .= '<div class="row-options">';

            $name .= '<a href="'.admin_url('assets/manage_assets/'.$aRow['id']).'" onclick="init_asset('.$aRow['id'].'); return false;">'._l('view').'</a>';
            if (has_permission('assets', '', 'edit') || is_admin()) {
                $options    = '';
                if (!empty($aRow['belongs_to'])) {
                    $belongs_to = explode(',', $aRow['belongs_to']);
                    foreach ($belongs_to as $value) {
                        $options .= "<option value='".$value."' selected>".get_company_name($value).'</option>';
                    }
                }
                if (!empty($aRow['belongs_to'])) {
                    $aRow['visible_to_client'] = "1";
                }

                $name .= ' | <a href="#" onclick="edit_asset(this,'.$aRow['id'].'); return false;" data-assets_name="'.$aRow['assets_name'].'" data-assets_code="'.$aRow['assets_code'].'" data-date_buy="'.$aRow['date_buy'].'" data-amount="'.$aRow['amount'].'" data-unit_price="'.$aRow['unit_price'].'" data-description="'.$aRow['description'].'" data-supplier_phone="'.$aRow['supplier_phone'].'" data-supplier_name="'.$aRow['supplier_name'].'" data-supplier_address="'.$aRow['supplier_address'].'" data-warranty_period="'.$aRow['warranty_period'].'" data-depreciation="'.$aRow['depreciation'].'" data-series="'.$aRow['series'].'" data-unit="'.$aRow['unit'].'" data-department="'.$aRow['department'].'" data-asset_image_url="'.module_dir_url('assets', 'uploads').'/'.$aRow['asset_image'].'" data-belongs_to_option="'.$options.'" data-visible_to_client="'.$aRow['visible_to_client'].'" data-asset_group="'.$aRow['asset_group'].'" data-asset_location="'.$aRow['asset_location'].'" >'._l('edit').'</a>';
            }

            if (has_permission('assets', '', 'delete') || is_admin()) {
                $name .= ' | <a href="'.admin_url('assets/delete_assets/'.$aRow['id']).'" class="text-danger _delete">'._l('delete').'</a>';
            }

            $name .= '</div>';

            $_data = $name;
        } elseif ('assets_code' == $aColumns[$i]) {
            $_data = '<a href="'.admin_url('assets/manage_assets/'.$aRow['id']).'" onclick="init_asset('.$aRow['id'].'); return false;">'.$aRow['assets_code'].'</a>';
        } elseif ('asset_image' == $aColumns[$i]) {
            if (0 == $i) {
                $_data = "<img alt='".module_dir_url('assets', 'uploads').'/'.$aRow['asset_image']."' src='".module_dir_url('assets', 'uploads')."/{$aRow['asset_image']}' class='img-thumbnail img-responsive zoom' onerror=\"this.src='".module_dir_url('assets', 'uploads')."/image-not-available.png'\">";
            }
            if (1 == $i) {
                $_data = module_dir_url('assets', 'uploads').'/'.$aRow['asset_image'];
            }
        } elseif ('belongs_to' == $aColumns[$i]) {
            $_data      = 'No';
            if (!empty($aRow['belongs_to'])) {
                $_data      = 'Yes';
                $belongs_to = explode(',', $aRow['belongs_to']);
                foreach ($belongs_to as $value) {
                    $_data .= ' - <a href="'.admin_url('clients/client/'.$value).'">'.get_company_name($value).'</a>, <br>';
                }
                $_data = trim($_data,", <br>");
            }
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}