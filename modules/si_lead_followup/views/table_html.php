<?php
defined('BASEPATH') or exit('No direct script access allowed');
$hide_class = 'not_visible not-export';
$table_data = [
	_l('the_number_sign'),
	_l('si_lfs_name'),
	_l('lead_status'),
	_l('lead_source'),
	_l('contract_send_to'),
	_l('si_lfs_text'),
	_l('si_lfs_schedule_days'),
	_l('si_lfs_schedule_hour'),
	_l('task_created_by'),
	_l('task_created_at'), 
	_l('si_lfs_schedule_last_executed'),
];
$table_data = hooks()->apply_filters('si_lead_followup_schedule_table_columns', $table_data);
render_datatable($table_data, isset($class) ?  $class : 'si-lead-followup-schedule', ['number-index-1'], [
	'data-last-order-identifier'=> 'si-lead-followup-schedule',
	'data-default-order'		=> get_table_last_order('si-lead-followup-schedule'),
]);