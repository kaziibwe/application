<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Lead Follow up Scheduler
Description: Module provides facility for staff to send followup SMS to Leads at scheduled time. This module uses default SMS gateways enabled in Perfex CRM.
Author: Sejal Infotech
Version: 1.0.1
Requires at least: 2.3.*
Author URI: http://www.sejalinfotech.com
*/

define('SI_LEAD_FOLLOWUP_MODULE_NAME', 'si_lead_followup');
define('SI_LEAD_FOLLOWUP_VALIDATION_URL','http://www.sejalinfotech.com/perfex_validation/index.php');
define('SI_LEAD_FOLLOWUP_KEY','c2lfbGVhZF9mb2xsb3d1cA==');

update_option('si_lead_followup_activated', 1);
update_option('si_lead_followup_activation_code', '********************');
$CI = &get_instance();
hooks()->add_action('admin_init', 'si_lead_followup_hook_admin_init');
hooks()->add_filter('module_'.SI_LEAD_FOLLOWUP_MODULE_NAME.'_action_links', 'module_si_lead_followup_action_links');
hooks()->add_action('settings_tab_footer','si_lead_followup_hook_settings_tab_footer');#for perfex low version V2.4 
hooks()->add_action('settings_group_end','si_lead_followup_hook_settings_tab_footer');#for perfex high version V2.8.4

/**
* Add additional settings for this module in the module list area
* @param  array $actions current actions
* @return array
*/
function module_si_lead_followup_action_links($actions)
{
	if(get_option(SI_LEAD_FOLLOWUP_MODULE_NAME.'_activated') && get_option(SI_LEAD_FOLLOWUP_MODULE_NAME.'_activation_code')!=''){
		$actions[] = '<a href="' . admin_url('settings?group=si_lead_followup_settings') . '">' . _l('settings') . '</a>';
	}
	else
		$actions[] = '<a href="' . admin_url('settings?group=si_lead_followup_settings') . '">' . _l('si_lead_followup_settings_validate') . '</a>';
	return $actions;
}

function si_lead_followup_hook_settings_tab_footer($tab)
{
	if($tab['slug']=='si_lead_followup_settings' && !get_option(SI_LEAD_FOLLOWUP_MODULE_NAME.'_activated')){
		echo '<script src="'.module_dir_url('si_lead_followup','assets/js/si_lead_followup_settings_footer.js').'"></script>';
	}
}
/**
* Load the module model
*/
$CI->load->model(SI_LEAD_FOLLOWUP_MODULE_NAME . '/si_lead_followup_model');
$CI->load->model('leads_model');
/**
* Load the module helper
*/
$CI->load->helper(SI_LEAD_FOLLOWUP_MODULE_NAME . '/si_lead_followup');
/**
* Register activation module hook
*/
register_activation_hook(SI_LEAD_FOLLOWUP_MODULE_NAME, 'si_lead_followup_activation_hook');
function si_lead_followup_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}
/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SI_LEAD_FOLLOWUP_MODULE_NAME, [SI_LEAD_FOLLOWUP_MODULE_NAME]);
/**
* Register cron run
*/
register_cron_task('si_lead_followup_hook_after_cron_run');
/**
*	Admin Init Hook for module
*/
function si_lead_followup_hook_admin_init()
{
	$capabilities = [];
	$capabilities['capabilities'] = [
		'view_own' => _l('permission_view_own'),
		'view'     => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create'   => _l('permission_create'),
		'edit'     => _l('permission_edit'),
		'delete'   => _l('permission_delete'),
	];
	register_staff_capabilities('si_lead_followup', $capabilities, _l('si_lead_followup'));
	$CI = &get_instance();
	/**  Add Tab In Settings Tab of Setup **/
	if (is_admin() || has_permission('settings', '', 'view')) {
		$CI->app_tabs->add_settings_tab('si_lead_followup_settings', [
			'name'     => _l('si_lead_followup_settings'),
			'view'     => 'si_lead_followup/si_lead_followup_settings',
			'position' => 60,
			'icon'     => 'fa fa-calendar',//supported from Perfex V 3.0
		]);
	}
	if(get_option(SI_LEAD_FOLLOWUP_MODULE_NAME.'_activated') && get_option(SI_LEAD_FOLLOWUP_MODULE_NAME.'_activation_code')!=''){
		if (is_admin() || has_permission('si_lead_followup', '', 'view') || has_permission('si_lead_followup', '', 'view_own')) {
			$CI->app_menu->add_sidebar_menu_item('si_lead_followup_menu', [
				'collapse' => true,
				'icon'     => 'fa fa-tty',
				'name'     => _l('si_lead_followup_menu'),
				'position' => 35,
			]);
			$CI->app_menu->add_sidebar_children_item('si_lead_followup_menu', [
				'slug'     => 'si-lead-followup-schedule-menu',
				'name'     => _l('si_lead_followup_schedule_send_submenu'),
				'href'     => admin_url('si_lead_followup'),
				'position' => 1,
			]);
		}
	}
}
/*hook to run a cron*/
function si_lead_followup_hook_after_cron_run($manually)
{
	$last_run = strtotime(get_option(SI_LEAD_FOLLOWUP_MODULE_NAME.'_trigger_schedule_sms_last_run'));
	$time_now                = time();
	if (($time_now < ($last_run)) || $manually === true) {
		return;
	}
	send_lead_followup_schedule_sms_cron_run();
	return;
}
