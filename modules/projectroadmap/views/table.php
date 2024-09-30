<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tblprojects.id as id',
    'name', 
    'start_date', 
    'deadline',
    '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members',
    'status',
    ];

$sIndexColumn = 'id';
$sTable       = 'tblprojects';
$join         = ['Left join tbllist_widget on tbllist_widget.rel_id = tblprojects.id and tbllist_widget.rel_type = "projectroadmap"'];
$where  = [];
$filter = [];

$statusIds = [];
    if ($this->ci->input->post('status')) {
        $status  = $this->ci->input->post('status');
        $_where = '';
        foreach ($status as $value) {
            if($_where == ''){
                $_where .= 'AND (';
            }else{
                $_where .= ' or ';
            }
            $_where .= 'status = '.$value;
        }
         if($_where != ''){
                $_where .= ')';
            }
        array_push($where, $_where);
    }
    if ($this->ci->input->post('from_date')) {
        $from_date = to_sql_date($this->ci->input->post('from_date'));
    }
    if ($this->ci->input->post('to_date')) {
        $to_date = to_sql_date($this->ci->input->post('to_date'));
    }

    if(isset($from_date) && isset($to_date)){
        array_push($where, 'AND ((start_date >= "' . $from_date . '" and start_date <= "' . $to_date . '") or if(deadline > 0, (start_date <= "' . $from_date . '" and deadline >= "' . $to_date . '"), (start_date <= "' . $from_date . '")))');
    }elseif(isset($from_date) && !isset($to_date)){
        array_push($where, 'AND if(deadline > 0, (start_date <= "' . $from_date . '" and deadline >= "' . $from_date . '"), (start_date <= "' . $from_date . '"))');
    }elseif(!isset($from_date) && isset($to_date)){
        array_push($where, 'AND if(deadline > 0, (start_date <= "' . $to_date . '" and deadline >= "' . $to_date . '"), (start_date <= "' . $to_date . '"))');
    }
   


    
    if (!is_admin() && !has_permission('projects', '', 'view')) {

    }

foreach ($this->ci->projects_model->get_project_statuses() as $status) {
    if ($this->ci->input->post('project_status_' . $status['id'])) {
        array_push($statusIds, $status['id']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'OR tblprojects.status IN (' . implode(', ', $statusIds) . ')');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members_ids','tbllist_widget.id as list_widget_id','tbllist_widget.add_from'
]);


$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $link = admin_url('projects/view/' . $aRow['id']);

    $row[] = '<a href="' . $link . '">' . $aRow['id'] . '</a>';
    
        $name = '<a href="' . $link . '">' . $aRow['name'] . '</a>';

    $row[] = $name;

    $row[] = _d($aRow['start_date']);

    $row[] = _d($aRow['deadline']);

    $membersOutput = '';
    $exportMembers = '';
    $list_member_id = [];
    $members       = explode(',', $aRow['members']);
    
    foreach ($members as $key => $member) {
        if ($member != '') {
            $members_ids = explode(',', $aRow['members_ids']);
            $member_id   = $members_ids[$key];
            if(in_array($member_id, $list_member_id))
            {
                continue;
            }
            $list_member_id[] = $member_id;
            $membersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .
            staff_profile_image($member_id, [
                'staff-profile-image-small mright5',
                ], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => $member,
                ]) . '</a>';
            $exportMembers .= $member . ', ';
        }
    }
    $membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
    $row[] = $membersOutput;

    $task = $this->ci->projects_model->get_tasks($aRow['id'],array(), false, true);
    $task_finish = $this->ci->projects_model->get_tasks($aRow['id'],['status' => 5], false, true);    

    $progress = ob_get_contents();
    ob_end_clean();


    $status = get_project_status_by_id($aRow['status']);
    $row[]  = '<span class="label label inline-block project-status-' . $aRow['status'] . '" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '">' . $status['name'] . '</span>';
  
    $data_title  = [];
    $data_title['data-toggle'] = 'tooltip';
    $data_title['title'] = _l('view_projectroadmap');
    $options = icon_btn('projectroadmap/view_projectroadmap/' . $aRow['id'], 'fa fa-eye', 'btn-default', $data_title);
    if(is_numeric($aRow['list_widget_id']) && $aRow['add_from'] == get_staff_user_id()){
        $options .= '<a href="Javascript:void(0);" class="btn btn-danger btn-icon" data-toggle="tooltip" title="" onclick="remove_dashboard('.$aRow['list_widget_id'].')" data-original-title="'._l('remove_dashboard').'"><i class="fa fa-compress"></i></a>';
    }else{
        $data_title['onclick'] = 'add_dashboard('.$aRow['id'].')';
        $data_title['title'] = _l('add_dashboard');
        $options .= '<a href="Javascript:void(0);" class="btn btn-success btn-icon" data-toggle="tooltip" title="" onclick="add_dashboard('.$aRow['id'].')" data-original-title="'._l('add_dashboard').'"><i class="fa fa-external-link"></i></a>';
    }
    
    $row[] =  $options;

    $output['aaData'][] = $row;
}
