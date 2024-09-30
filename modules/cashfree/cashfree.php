<?php
defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Cashfree Payments Payment Gateway
Description: Cashfree Payments is easiest way to accept payments for businesses in India
Version: 1.0.0
Requires at least: 2.3.*
Author: Sejal Infotech
Author URI: http://www.sejalinfotech.com
*/
define('SI_CASHFREE_MODULE_NAME', 'cashfree');
define('SI_CASHFREE_VALIDATION_URL','http://www.sejalinfotech.com/perfex_validation/index.php');
define('SI_CASHFREE_KEY','Y2FzaGZyZWU=');
$CI = &get_instance();
hooks()->add_action('admin_init', 'cashfree_hook_admin_init');
hooks()->add_filter('module_'.SI_CASHFREE_MODULE_NAME.'_action_links', 'module_cashfree_action_links');
hooks()->add_action('settings_tab_footer','cashfree_hook_settings_tab_footer');
hooks()->add_action('settings_group_end','cashfree_hook_settings_tab_footer');
register_activation_hook(SI_CASHFREE_MODULE_NAME, 'si_cashfree_activation_hook');
function si_cashfree_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}
function module_cashfree_action_links($actions)
{
	if(get_option(SI_CASHFREE_MODULE_NAME.'_activated') && 
	get_option(SI_CASHFREE_MODULE_NAME.'_activation_code')!=''){
		$actions[] = '<a href="' . admin_url('settings?group=cashfree_settings') . '">' .
					_l('settings') . '</a>';
	}
	else
		$actions[] = '<a href="' . admin_url('settings?group=cashfree_settings') . '">' .
					_l('cashfree_settings_validate') . '</a>';
	return $actions;
}
register_language_files(SI_CASHFREE_MODULE_NAME, [SI_CASHFREE_MODULE_NAME]);
if(get_option(SI_CASHFREE_MODULE_NAME.'_activated') && get_option(SI_CASHFREE_MODULE_NAME.'_activation_code')!=''){
	register_payment_gateway('cashfree_gateway', 'cashfree');
}
function cashfree_hook_admin_init()
{
	$CI = &get_instance();
	if (is_admin() || has_permission('settings', '', 'view')) {
		$CI->app_tabs->add_settings_tab('cashfree_settings', [
			'name'     => _l('cashfree_settings'),
			'view'     => 'cashfree/cashfree_settings',
			'position' => 36,
			'icon'     => 'fa-regular fa-credit-card',
		]);
	}
}	
function cashfree_hook_settings_tab_footer($tab)
{
	if($tab['slug']=='cashfree_settings' && !get_option(SI_CASHFREE_MODULE_NAME.'_activated')){
		echo '<script src="'.module_dir_url('cashfree','assets/js/cashfree_settings_footer.js').'"></script>';
	}
}
