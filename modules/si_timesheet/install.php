<?php
defined('BASEPATH') or exit('No direct script access allowed');
if(!$CI->db->table_exists(db_prefix() . 'si_timesheet_filter')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_timesheet_filter` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`filter_name` varchar(200) NOT NULL,
	`filter_parameters` text NOT NULL,
	`filter_type` int(11) NOT NULL DEFAULT '1',
	`staff_id` int(11) NOT NULL DEFAULT '0',
	`created_dt` DATETIME NOT NULL,
	PRIMARY KEY (`id`),
	KEY (`staff_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
//add in settings
add_option(SI_TIMESHEET_MODULE_NAME.'_completed_task_allow_add',1);
add_option(SI_TIMESHEET_MODULE_NAME.'_completed_task_allow_edit',1);
//V1.0.1
add_option(SI_TIMESHEET_MODULE_NAME.'_show_task_custom_fields',1);
//V1.0.2
add_option(SI_TIMESHEET_MODULE_NAME.'_show_staff_icon_in_calendar',1);
//V1.0.6
add_option(SI_TIMESHEET_MODULE_NAME.'_task_status_exclude_add',serialize([]));