<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_106 extends App_module_migration
{
	public function up()
	{
		add_option(SI_TIMESHEET_MODULE_NAME.'_task_status_exclude_add',serialize([]));    
	}
}