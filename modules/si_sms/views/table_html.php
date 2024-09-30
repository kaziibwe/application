<?php
defined('BASEPATH') or exit('No direct script access allowed');
$hide_class = 'not_visible not-export';
$table_data = [
	_l('the_number_sign'),
    _l('si_sms_schedule_date'),
    _l('task_related_to'),
    _l('si_sms_text'),
    _l('task_created_by'),
    _l('task_created_at'), 
	_l('si_sms_schedule_executed'),
];
$table_data = hooks()->apply_filters('si_sms_schedule_table_columns', $table_data);
render_datatable($table_data, isset($class) ?  $class : 'si-sms-schedule', ['number-index-1'], [
	'data-last-order-identifier'=> 'si-sms-schedule',
	'data-default-order'		=> get_table_last_order('si-sms-schedule'),
]);