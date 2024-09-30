<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Manage Custom Statuses
Description: Module will allow to add more statuses to Projects and Tasks.
Author: Sejal Infotech
Version: 1.0.4
Requires at least: 1.0.*
Author URI: http://www.sejalinfotech.com
*/

define('SI_CUSTOM_STATUS_MODULE_NAME', 'si_custom_status');

$CI = &get_instance();

hooks()->add_action('admin_init','si_custom_status_hook_admin_init');
hooks()->add_filter('module_'.SI_CUSTOM_STATUS_MODULE_NAME.'_action_links', 'module_si_custom_status_action_links');
hooks()->add_filter('before_get_project_statuses','si_custom_status_hook_before_get_project_statuses');
hooks()->add_filter('before_get_task_statuses','si_custom_status_hook_before_get_task_statuses');

/**
 * Add additional settings for this module in the module list area
 * @param  array $actions current actions
 * @return array
 */
function module_si_custom_status_action_links($actions)
{
	$actions[] = '<a href="' . admin_url('si_custom_status/statuses/projects') . '">' . _l('settings') . '</a>';
	return $actions;
}

/**
* Load the module model
*/
$CI->load->model(SI_CUSTOM_STATUS_MODULE_NAME . '/si_custom_status_model');
/**
* Load the module helper
*/
$CI->load->helper(SI_CUSTOM_STATUS_MODULE_NAME . '/si_custom_status');

/**
* Register activation module hook
*/
register_activation_hook(SI_CUSTOM_STATUS_MODULE_NAME, 'si_custom_status_activation_hook');

function si_custom_status_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SI_CUSTOM_STATUS_MODULE_NAME, [SI_CUSTOM_STATUS_MODULE_NAME]);

/**
*	Admin Init Hook for module
*/
function si_custom_status_hook_admin_init()
{
	$CI = &get_instance();
	/*Add customer permissions */
	$capabilities = [];
	$capabilities['capabilities'] = [
		'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create'	 => _l('permission_create'),
		'edit'	 => _l('permission_edit'),
		'delete'	=> _l('permission_delete'),
	];
	register_staff_capabilities('si_custom_status', $capabilities, _l('si_custom_status'));
	
	/** Add Menu for Custom Statuses in Setup**/
	if (is_admin() || has_permission('si_custom_status', '', 'view')) {
		$CI->app_menu->add_setup_menu_item('si_custom_status_setup_menu', [
			'collapse' => true,
			'name'     => _l('si_custom_status_setup_menu'),
			'position' => 35,
		]);
		$CI->app_menu->add_setup_children_item('si_custom_status_setup_menu', [
			'slug'     => 'si-custom-status-project',
			'name'     => _l('si_custom_status_project_statuses_menu'),
			'href'     => admin_url('si_custom_status/statuses/projects'),
			'position' => 10,
		]);
		$CI->app_menu->add_setup_children_item('si_custom_status_setup_menu', [
			'slug'     => 'si-custom-status-task',
			'name'     => _l('si_custom_status_task_statuses_menu'),
			'href'     => admin_url('si_custom_status/statuses/tasks'),
			'position' => 10,
		]);
	}
}

/** Hook for call extra project statuses**/
function si_custom_status_hook_before_get_project_statuses($statuses)
{
	$CI = &get_instance();
	$edit_default_status = (int)get_option(SI_CUSTOM_STATUS_MODULE_NAME.'_edit_default_status_projects');
	if($edit_default_status){
		$statuses = [];
		$custom_default_statuses = si_cs_get_custom_default_statuses("projects");
		if(!empty($custom_default_statuses)){
			foreach($custom_default_statuses as $key=>$status){
				if($status['name']=='')
					$custom_default_statuses[$key]['name'] = _l('project_status_'.$status['status_id']);	
			}
			$statuses = array_merge($statuses,$custom_default_statuses);
		}
	}
	$custom_statuses = si_cs_get_custom_statuses("projects");
	if(!empty($custom_statuses))
		$statuses = array_merge($statuses,$custom_statuses);    
	return $statuses;
}

/** Hook for call extra task statuses**/
function si_custom_status_hook_before_get_task_statuses($statuses)
{
	$CI = &get_instance();
	$edit_default_status = (int)get_option(SI_CUSTOM_STATUS_MODULE_NAME.'_edit_default_status_tasks');
	if($edit_default_status){
		$statuses = [];
		$custom_default_statuses = si_cs_get_custom_default_statuses("tasks");
		if(!empty($custom_default_statuses)){
			foreach($custom_default_statuses as $key=>$status){
				if($status['name']=='')
					$custom_default_statuses[$key]['name'] = _l('task_status_'.$status['status_id']);
			}
			$statuses = array_merge($statuses,$custom_default_statuses);
		}
	}	
	$custom_statuses = si_cs_get_custom_statuses("tasks");
	if(!empty($custom_statuses))
		$statuses = array_merge($statuses,$custom_statuses);     
	return $statuses;
}



