<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'resource_name',
    'resource_group',
    'manager',
    'color',
    'status',
    ];
$sIndexColumn = 'id';
$sTable       = 'tblresource';
$join = ['LEFT JOIN tblresource_group on tblresource_group.id = tblresource.resource_group'];
$where = [];
if($this->ci->input->post('group')){
    $group = $this->ci->input->post('group');
    $where_group = '';
    foreach ($group as $p) {
        if($p != '')
        {
            if($where_group == ''){
                $where_group .= ' AND (tblresource_group.id = '.$p;
            }else{
                $where_group .= ' or tblresource_group.id ='.$p;
            }
        }
    }
    if($where_group != '')
    {
        $where_group .= ')';
        array_push($where, $where_group);
    }
}
if($this->ci->input->post('staff')){
    $staff = $this->ci->input->post('staff');
    $where_staff = '';
    foreach ($staff as $s) {
        if($s != '')
        {
            if($where_staff == ''){
                $where_staff .= ' AND (tblresource.manager = '.$s;
            }else{
                $where_staff .= ' or tblresource.manager ='.$s;
            }
        }
    }
    if($where_staff != '')
    {
        $where_staff .= ')';
        array_push($where, $where_staff);
    }
}
if($this->ci->input->post('status')){
    $status = $this->ci->input->post('status');
    $where_status = '';
    foreach ($status as $y) {
        if($y != '')
        {
            if($where_status == ''){
                $where_status .= ' AND (tblresource.status = "'.$y.'"';
            }else{
                $where_status .= ' or tblresource.status = "'.$y.'"';
            }
        }
    }
    if($where_status != '')
    {
        $where_status .= ')';

        array_push($where, $where_status);
    }
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblresource.id as id','approved','tblresource.description as description','group_name','resource_name','status']);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'resource_name') {
            $_data = '<a href="' . admin_url('resourcebooking/resource/' . $aRow['id']) . '">' . $_data . '</a>';
        }elseif($aColumns[$i] == 'manager'){
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['manager']) . '">' . staff_profile_image($aRow['manager'], [
                'staff-profile-image-small',
                ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['manager']) . '">' . get_staff_full_name($aRow['manager']) . '</a>';
        }elseif ($aColumns[$i] == 'color'){
            $_data = '<span class="label label-tag tag-id-1" style="background-color: '.$aRow['color'].';">'.'&nbsp;&nbsp;&nbsp;'.'</span>';
            
        }elseif ($aColumns[$i] == 'status') {
            if($aRow['status'] == 'active'){
                $_data = '<span class="label label inline-block project-status-color-currentlyactive">' . _l($aRow['status']) . '</span>';
            }elseif($aRow['status'] == 'deactive'){
                 $_data = '<span class="label label inline-block project-status-color-completed">' . _l($aRow['status']) . '</span>';
            }
            
        }elseif ($aColumns[$i] == 'resource_group') {
            $_data = $aRow['group_name'];
        }
        $row[] = $_data;
    }
    $options = icon_btn('resourcebooking/resources/' . $aRow['id'], 'fa fa-pencil-square', 'btn-default', ['onclick' => 'edit_resource(this,' . $aRow['id'] . '); return false', 'data-resource_name' => $aRow['resource_name'],'data-manager' => $aRow['manager'],'data-description' => $aRow['description'],'data-status' => $aRow['status'],'data-resource_group' => $aRow['resource_group'],'data-color' => $aRow['color'],'data-approved' => $aRow['approved']]);
    $options .= icon_btn('resourcebooking/delete_resource/' . $aRow['id'], 'fa fa-remove', 'btn-danger _delete');
    $row[] = $options;

    $output['aaData'][] = $row;
}
