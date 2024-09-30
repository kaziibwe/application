<?php
defined('BASEPATH') or exit('No direct script access allowed');
if(!$CI->db->table_exists(db_prefix() . 'si_sms_templates')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_sms_templates` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`template_name` varchar(255) NOT NULL,
	`content` text NOT NULL,
	`dlt_template_id` varchar(50) NOT NULL DEFAULT '',
	`staff_id` int(11) NOT NULL DEFAULT '0',
	`is_public` INT NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `staff_id` (`staff_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if(!$CI->db->table_exists(db_prefix() . 'si_sms_schedule')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_sms_schedule` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`filter_by` varchar(25) NOT NULL,
	`content` text NOT NULL,
	`dlt_template_id_key` varchar(100) NOT NULL DEFAULT '',
	`dlt_template_id_value` varchar(100) NOT NULL DEFAULT '',
	`staff_id` int(11) NOT NULL DEFAULT '0',
	`schedule_date` datetime NOT NULL,
	`executed` int(11) NOT NULL DEFAULT '0',
	`dateadded` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `staff_id` (`staff_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if(!$CI->db->table_exists(db_prefix() . 'si_sms_schedule_rel')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_sms_schedule_rel` (
	`schedule_id` int(11) NOT NULL DEFAULT '0',
	`rel_id` int(11) NOT NULL DEFAULT '0',
	KEY `schedule_id` (`schedule_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
#add in settings
add_option(SI_SMS_MODULE_NAME.'_send_to_customer','primary');
add_option(SI_SMS_MODULE_NAME.'_send_to_alt_client',1);
add_option(SI_SMS_MODULE_NAME.'_activated',0);
add_option(SI_SMS_MODULE_NAME.'_activation_code','');
add_option(SI_SMS_MODULE_NAME.'_project_status_exclude',serialize([]));
add_option(SI_SMS_MODULE_NAME.'_task_status_exclude',serialize([]));
add_option(SI_SMS_MODULE_NAME.'_invoice_status_exclude',serialize([]));
add_option(SI_SMS_MODULE_NAME.'_lead_status_exclude',serialize([]));
add_option(SI_SMS_MODULE_NAME.'_ticket_status_exclude',serialize([]));
add_option('sms_trigger_'.SI_SMS_MODULE_NAME.'_custom_sms','');
add_option(SI_SMS_MODULE_NAME.'_trigger_schedule_sms_last_run',date('Y-m-d H:i:s',strtotime('-1 hour')));
add_option(SI_SMS_MODULE_NAME.'_clear_schedule_sms_log_after_days',30);