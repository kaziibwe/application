<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_102 extends App_module_migration
{
	public function up()
	{   
		add_option(SI_TIMESHEET_MODULE_NAME.'_show_staff_icon_in_calendar',1); 
	}
}