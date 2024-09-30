<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_105 extends App_module_migration
{
	public function up()
	{   
		$CI = &get_instance();
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
		add_option(SI_SMS_MODULE_NAME.'_trigger_schedule_sms_last_run',date('Y-m-d H:i:s',strtotime('-1 hour')));
		add_option(SI_SMS_MODULE_NAME.'_clear_schedule_sms_log_after_days',30);
	}
}