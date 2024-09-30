<?php
defined('BASEPATH') or exit('No direct script access allowed');

//check latest version of perfex
function si_timesheet_get_perfex_version()
{
	$CI = &get_instance();
	$version = (int)$CI->app->get_current_db_version();

	return $version;
}