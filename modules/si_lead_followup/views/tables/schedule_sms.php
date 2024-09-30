<?php
defined('BASEPATH') or exit('No direct script access allowed');
$hasPermissionView   = has_permission('si_lead_followup', '', 'view');
$hasPermissionEdit   = has_permission('si_lead_followup', '', 'edit');
$hasPermissionDelete = has_permission('si_lead_followup', '', 'delete');
$hasPermissionCreate = has_permission('si_lead_followup', '', 'create');

$aColumns = [
	db_prefix() . 'si_lead_followup_schedule.id as id',
	db_prefix() . 'si_lead_followup_schedule.name as schedule_name',
	'status',
	'source',
	'filter_by',
	'content',
	'schedule_days',
	'schedule_hour',
	'staff_id',
	'dateadded',
	'last_executed',
	];
$sIndexColumn = 'id';
$sTable       = db_prefix().'si_lead_followup_schedule';
$join = [
	'JOIN '.db_prefix().'staff ON '.db_prefix().'staff.staffid = '.db_prefix().'si_lead_followup_schedule.staff_id',
	'LEFT JOIN '.db_prefix().'leads_status ON '.db_prefix().'leads_status.id = '.db_prefix().'si_lead_followup_schedule.status',
	'LEFT JOIN '.db_prefix().'leads_sources ON '.db_prefix().'leads_sources.id = '.db_prefix().'si_lead_followup_schedule.source',
];
$where  = [];
$filter = [];
if(!$hasPermissionView) {
	array_push($where, ' AND staff_id=' . get_staff_user_id());
}

if (count($filter) > 0) {
	array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}
$aColumns = hooks()->apply_filters('si_lead_followup_schedule_table_sql_columns', $aColumns);
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'CONCAT(firstname," ",lastname) as staff_name',
	db_prefix().'leads_status.name as lead_status_name',
	db_prefix().'leads_sources.name as lead_source_name',
]);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

	$row = [];
	
	$row[] = '<a href="#" onclick="view_schedule_modal(' . $aRow['id'] . ');return false;">' . $aRow['id'] . '</a>';
	
	$schedule = '<a href="#" onclick="view_schedule_modal(' . $aRow['id'] . ');return false;">' . $aRow['schedule_name'] . '</a>';
	$schedule .= '<div class="row-options">';
	$schedule .= '<a href="#" onclick="view_schedule_modal(' . $aRow['id'] . ');return false;">' . _l('view') . '</a>';
	if ($hasPermissionEdit) {
		$schedule .= ' | <a href="#" onclick="edit_schedule_modal(' . $aRow['id'] . ');return false;">' . _l('edit') . '</a>';
	}
	if ($hasPermissionDelete) {
		$schedule .= ' | <a href="#" class="text-danger _delete si_lead_followup_schedule_delete" data-id="'.$aRow['id'].'">' .
		 _l('delete') . '</a>';
	}
	$schedule .= '</div>';
	
	$row[] = $schedule;
	
	$row[] = $aRow['lead_status_name'];
	
	$row[] = $aRow['lead_source_name'];
	
	$filter_by = _l('leads');
	if($aRow['filter_by']=='staff') $filter_by = _l('staff');
	
	$row[] = $filter_by;
	
	$row[] = substr($aRow['content'],0,100).(strlen($aRow['content'])>100 ? "...":"");
	
	$row[] = $aRow['schedule_days'].' '._l('days');
	
	$row[] = date('h A',strtotime('01-01-1970 '.$aRow['schedule_hour'].":00:00"));
	
	$assignedOutput = '';
	if ($aRow['staff_id'] != 0) {
		$full_name = $aRow['staff_name'];
		$assignedOutput = '<a data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $aRow['staff_id']) . '">' . staff_profile_image($aRow['staff_id'], [
			'staff-profile-image-small',
			]) . '</a>';
		$assignedOutput .= '<span class="hide">' . $full_name . '</span>';
	}
	
	$row[] = $assignedOutput;
	
	$row[] = _d($aRow['dateadded']);
	
	$row[] = _d($aRow['last_executed']);
	
	$row['DT_RowClass'] = 'has-row-options';
	
	$row = hooks()->apply_filters('si_lead_followup_schedule_table_row_data', $row, $aRow);
	
	$output['aaData'][] = $row;
}