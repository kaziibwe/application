<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_104 extends App_module_migration
{
	public function up()
	{   
		$CI = &get_instance();
		if(!$CI->db->field_exists('is_public',db_prefix() . 'si_sms_templates')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'si_sms_templates` ADD `is_public` INT NOT NULL DEFAULT "0" AFTER `staff_id`');
		}
	}
}