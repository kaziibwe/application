<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Advanced Task Timesheet Manager
Description: Module will allow to see timesheet of user tasks on calendar and update from there.
Author: Sejal Infotech
Version: 1.1.3
Requires at least: 2.3.*
Author URI: http://www.sejalinfotech.com
*/

define('SI_TIMESHEET_MODULE_NAME', 'si_timesheet');
define('SI_TS_FILTER_TYPE_SUMMARY', 1);
define('SI_TS_FILTER_TYPE_CALENDAR', 2);

$CI = &get_instance();

hooks()->add_action('admin_init', 'si_timesheet_admin_init_hook');
hooks()->add_filter('module_'.SI_TIMESHEET_MODULE_NAME.'_action_links', 'module_si_timesheet_action_links');
hooks()->add_filter('get_dashboard_widgets','si_ts_hook_get_dashboard_widgets');
hooks()->add_filter('before_settings_updated','si_timesheet_hook_before_settings_updated');

/**
 * Add additional settings for this module in the module list area
 * @param  array $actions current actions
 * @return array
 */
function module_si_timesheet_action_links($actions)
{
	$actions[] = '<a href="' . admin_url('settings?group=si_timesheet_settings') . '">' . _l('settings') . '</a>';
	return $actions;
}

/**
* Load the module helper
*/
$CI->load->helper(SI_TIMESHEET_MODULE_NAME . '/si_timesheet');

/**
* Load the module model
*/
$CI->load->model(SI_TIMESHEET_MODULE_NAME . '/si_timesheet_model');

/**
* Register activation module hook
*/
register_activation_hook(SI_TIMESHEET_MODULE_NAME, 'si_timesheet_activation_hook');

function si_timesheet_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SI_TIMESHEET_MODULE_NAME, [SI_TIMESHEET_MODULE_NAME]);

/**
*	Admin Init Hook for module
*/
function si_timesheet_admin_init_hook()
{
	$CI = &get_instance();
	/*Add customer permissions */
	$capabilities = [];
	$capabilities['capabilities'] = [
		'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
		'view_own'   => _l('permission_view_own'),
		'create'	 => _l('permission_create'),
		'edit'	 => _l('permission_edit'),
		'delete'	=> _l('permission_delete'),
	];
	register_staff_capabilities('si_timesheet', $capabilities, _l('si_timesheet'));
	
	/**  Add Tab In Settings Tab of Setup **/
	if (is_admin()) {
		$CI->app_tabs->add_settings_tab('si_timesheet_settings', [
			'name'     => _l('si_timesheet_settings'),
			'view'     => 'si_timesheet/si_timesheet_settings',
			'position' => 100,
		]);
	}
	/** Add Menu for Invoice Payments**/
	if (is_admin() || has_permission('si_timesheet', '', 'view') || has_permission('si_timesheet', '', 'view_own')) {
		$CI->app_menu->add_sidebar_menu_item('si_timesheet_menu', [
			'collapse' => true,
			'icon'     => 'fa fa-calendar',
			'name'     => _l('si_ts_main_menu'),
			'position' => 15,
		]);
		$CI->app_menu->add_sidebar_children_item('si_timesheet_menu', [
			'slug'     => 'si-timesheets',
			'name'     => _l('si_ts_timesheets_menu'),
			'href'     => admin_url('si_timesheet'),
			'position' => 1,
		]);
		$CI->app_menu->add_sidebar_children_item('si_timesheet_menu', [
			'slug'     => 'si-timesheets-summary',
			'name'     => _l('si_ts_timesheet_summary_menu'),
			'href'     => admin_url('si_timesheet/timesheet_summary'),
			'position' => 2,
		]);$CI->app_menu->add_sidebar_children_item('si_timesheet_menu', [
			'slug'     => 'si-timesheet-templates',
			'name'     => _l('si_ts_timesheet_templates_menu'),
			'href'     => admin_url('si_timesheet/timesheet_templates'),
			'position' => 3,
		]);
	}
}

/**Hook to add calendar widget to dashboard**/
function si_ts_hook_get_dashboard_widgets($widgets)
{
	if (is_admin() || has_permission('si_timesheet', '', 'view') || has_permission('si_timesheet', '', 'view_own')){
		$widgets[] = array(	'path' => 'si_timesheet/dashboard/widgets/timesheet_calendar',
						'container' => 'left-8',
					);
	}					
	return $widgets;				
}

/** hook for before settings saved**/
function si_timesheet_hook_before_settings_updated($data)
{
	if(isset($data['settings']) && array_key_exists('si_timesheet_completed_task_allow_add',$data['settings'])){
		$status_excludes = array('si_timesheet_task_status_exclude_add');
		foreach($status_excludes as $key){					
			if(array_key_exists($key,$data['settings']))
				$data['settings'][$key] = serialize($data['settings'][$key]);
			else
				$data['settings'][$key] = serialize([]);	
		}
	}
	return $data;	
}
