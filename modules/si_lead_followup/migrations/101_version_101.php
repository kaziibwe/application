<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_101 extends App_module_migration
{
	public function up()
	{ 
		$CI = &get_instance();
		if($CI->db->table_exists(db_prefix() . 'si_lead_followup_schedule_rel')) {
			$CI->db->query("ALTER TABLE `" . db_prefix() . "si_lead_followup_schedule_rel` ADD `comment` text NULL  AFTER `rel_id`;");
		}   
	}
}