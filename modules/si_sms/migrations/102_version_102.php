<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_102 extends App_module_migration
{
	public function up()
	{ 
		$CI = &get_instance();
		if($CI->db->table_exists(db_prefix() . 'si_sms_templates')) {
			$CI->db->query("ALTER TABLE `" . db_prefix() . "si_sms_templates` ADD `dlt_template_id` varchar(50) NOT NULL DEFAULT '' AFTER `content`;");
		}   
	}
}