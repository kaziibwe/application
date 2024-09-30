<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'purpose',
    'orderer',
    'tblbooking.id',
    'tblbooking.resource_group',
    'resource',
    'start_time',
    'end_time',
    'tblbooking.status'
    ];
$sIndexColumn = 'id';
$sTable       = 'tblbooking';
$join = [ 'LEFT JOIN tblresource on tblresource.id = tblbooking.resource',
          'LEFT JOIN tblresource_group on tblresource_group.id = tblresource.resource_group'  
];
$where = [];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblbooking.status','group_name','resource_name']);

$output  = $result['output'];
$rResult = $result['rResult'];
$this->ci->load->model('resourcebooking_model');
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'purpose') {
            $_data = '<a href="' . admin_url('resourcebooking/booking/' . $aRow['tblbooking.id']) . '">' . $_data . '</a>';
        }elseif($aColumns[$i] == 'orderer'){
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['orderer']) . '">' . staff_profile_image($aRow['orderer'], [
                'staff-profile-image-small',
                ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['orderer']) . '">' . get_staff_full_name($aRow['orderer']) . '</a>';
        }elseif($aColumns[$i] == 'tblbooking.resource_group'){
            $_data = $aRow['group_name'];
        }elseif($aColumns[$i] == 'resource'){
            $_data = '<a href="' . admin_url('resourcebooking/resource/' . $aRow['resource']) . '">' . $aRow['resource_name'] . '</a>';
        }elseif($aColumns[$i] == 'start_time'){
            $_data = _dt($aRow['start_time']);
        }elseif($aColumns[$i] == 'end_time'){
            $_data = _dt($aRow['end_time']);
        }elseif($aColumns[$i] == 'tblbooking.id'){
            $list =  $this->ci->resourcebooking_model->get_list_follower_by_booking($aRow['tblbooking.id']);
            $str = '';
            foreach($list as $lst){
                $str .= '<a href="' . admin_url('staff/profile/' . $lst['follower']) . '">' . staff_profile_image($lst['follower'], [
                'staff-profile-image-small'], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => get_staff_full_name($lst['follower']),
                ]) . '</a>&nbsp';
            } 
            $_data = $str;
        }elseif($aColumns[$i] == 'tblbooking.status'){
			$timenow = date('Y-m-d h:i:s');
			
            if($aRow['start_time'] >= $timenow){
                $_data = '<span class="label label inline-block project-status-color-scheduled">' . _l('scheduled') . '</span>';
            }elseif($aRow['end_time'] <= $timenow){
                $_data = '<span class="label label inline-block project-status-color-completed">' . _l('ended') . '</span>';
            }
			else {
			    $_data = '<span class="label label inline-block project-status-color-currentlyactive">' . _l('currentlyactive') . '</span>';
			}
        }
        $row[] = $_data;
    }
    if($aRow['orderer'] == get_staff_user_id()){
        $options = icon_btn('resourcebooking/add_edit_booking/' . $aRow['tblbooking.id'], 'fa fa-pencil-square', 'btn-default');
        $options .= icon_btn('resourcebooking/delete_booking/' . $aRow['tblbooking.id'], 'fa fa-remove', 'btn-danger _delete');
    }else{
        $options = '';
    }
    $row[] = $options;

    $output['aaData'][] = $row;
}
