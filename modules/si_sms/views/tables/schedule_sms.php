<?php
defined('BASEPATH') or exit('No direct script access allowed');
$hasPermissionView   = has_permission('si_sms_schedule_send', '', 'view');
$hasPermissionEdit   = has_permission('si_sms_schedule_send', '', 'edit');
$hasPermissionDelete = has_permission('si_sms_schedule_send', '', 'delete');
$hasPermissionCreate = has_permission('si_sms_schedule_send', '', 'create');

$aColumns = [
	db_prefix() . 'si_sms_schedule.id as id',
	'schedule_date',
	'filter_by',
	'content',
	'staff_id',
	'dateadded',
	'executed',
	];
$sIndexColumn = 'id';
$sTable       = db_prefix().'si_sms_schedule';
$join = [
	'JOIN '.db_prefix().'staff ON '.db_prefix().'staff.staffid = '.db_prefix().'si_sms_schedule.staff_id',
];
$where  = [];
$filter = [];

if(!$hasPermissionView) {
	array_push($where, ' AND staff_id=' . get_staff_user_id());
}

//if no rights to see staffs
if(!has_permission('staff','','view')){
	array_push($where, ' AND filter_by <> "staff"');
}

if (count($filter) > 0) {
	array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

$aColumns = hooks()->apply_filters('si_sms_schedule_table_sql_columns', $aColumns);

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'CONCAT(firstname," ",lastname) as staff_name',
]);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
	$row = [];
	$row[] = '<a href="#" onclick="view_schedule_modal(' . $aRow['id'] . ');return false;">' . $aRow['id'] . '</a>';
	
	$schedule = '<a href="#" onclick="view_schedule_modal(' . $aRow['id'] . ');return false;">' . _d($aRow['schedule_date']) . '</a>';
	$schedule .= '<div class="row-options">';
	$schedule .= '<a href="#" onclick="view_schedule_modal(' . $aRow['id'] . ');return false;">' . _l('view') . '</a>';
	if ($hasPermissionEdit && !$aRow['executed']) {
		$schedule .= ' | <a href="#" onclick="edit_schedule_modal(' . $aRow['id'] . ');return false;">' . _l('edit') . '</a>';
	}
	if ($hasPermissionDelete) {
		$schedule .= ' | <a href="#" class="text-danger _delete si_sms_schedule_delete" data-id="'.$aRow['id'].'">' .
		 _l('delete') . '</a>';
	}
	$schedule .= '</div>';
	$row[] = $schedule;
	
	$filter_by = _l('clients');
	if($aRow['filter_by'] == 'lead') $filter_by = _l('leads');
	if($aRow['filter_by'] == 'staff') $filter_by = _l('staff_members');
	$row[] = $filter_by;
	
	$row[] = substr($aRow['content'],0,100).(strlen($aRow['content'])>100 ? "...":"");
	
	$assignedOutput = '';
    if ($aRow['staff_id'] != 0) {
        $full_name = $aRow['staff_name'];

        $assignedOutput = '<a data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $aRow['staff_id']) . '">' . staff_profile_image($aRow['staff_id'], [
            'staff-profile-image-small',
            ]) . '</a>';

        // For exporting
        $assignedOutput .= '<span class="hide">' . $full_name . '</span>';
    }

    $row[] = $assignedOutput;
	
	$row[] = _d($aRow['dateadded']);
	
	$row[] = ($aRow['executed'] ? 
			 '<i class="fa fa-check text-success"></i><span class="hide">' . _l('is_active_export') . '</span>' : 
			 '<i class="fa fa-close text-danger"></i><span class="hide">' . _l('is_not_active_export') . '</span>');

	$row['DT_RowClass'] = 'has-row-options';
	$row = hooks()->apply_filters('si_sms_schedule_table_row_data', $row, $aRow);
	$output['aaData'][] = $row;
}