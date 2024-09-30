<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_101 extends App_module_migration
{
	public function up()
	{   
		add_option(SI_TIMESHEET_MODULE_NAME.'_show_task_custom_fields',1); 
	}
}