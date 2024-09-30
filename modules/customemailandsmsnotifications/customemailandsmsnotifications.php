<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Custom Email & SMS Notifications
Module URI: https://codecanyon.net/item/custom-sms-email-notifications-for-pefex/49908117
Description: Contact your Customers' contacts or Leads, using Emails/SMSes (templetized or custom ones)
Version: 2.3.3
Requires at least: 2.3.*
Author: Themesic Interactive
Author URI: https://codecanyon.net/user/themesic/portfolio
*/

define('CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE', 'customemailandsmsnotifications');
require_once __DIR__.'/vendor/autoload.php';
modules\customemailandsmsnotifications\core\Apiinit::the_da_vinci_code(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE);
modules\customemailandsmsnotifications\core\Apiinit::ease_of_mind(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE);

$CI = &get_instance();

hooks()->add_filter('sidebar_customemailandsmsnotifications_items', 'app_admin_sidebar_custom_options', 999);
hooks()->add_filter('sidebar_customemailandsmsnotifications_items', 'app_admin_sidebar_custom_positions', 998);
hooks()->add_filter('setup_customemailandsmsnotifications_items', 'app_admin_customemailandsmsnotifications_custom_options', 999);
hooks()->add_filter('setup_customemailandsmsnotifications_items', 'app_admin_customemailandsmsnotifications_custom_positions', 998);
hooks()->add_filter('module_customemailandsmsnotifications_action_links', 'module_customemailandsmsnotifications_action_links');
hooks()->add_action('app_admin_footer', 'sms_and_email_assets');
hooks()->add_action('admin_init', 'add_csrf_support');
hooks()->add_action('after_cron_run','run_cron_job_custom_email');
/**
 * Add CSRF Exclusion Support
 * @return stylesheet / script
 */
function add_csrf_support()
{
	$configfile = FCPATH . 'application/config/config.php';
	$searchforit = file_get_contents($configfile);
	$csrfstring = 'admin/customemailandsmsnotifications/email_sms/sendEmailSms';
	
	if(strpos($searchforit,$csrfstring) == false) {
		file_put_contents($configfile, str_replace('$config[\'csrf_exclude_uris\'] = [', '$config[\'csrf_exclude_uris\'] = [\'admin/customemailandsmsnotifications/email_sms/sendEmailSms\', ', $searchforit)); 
	}
}

/**
 * Staff login includes
 * @return stylesheet / script
 */
function sms_and_email_assets()
{
    echo '<link href="' . base_url('modules/customemailandsmsnotifications/assets/style.css') . '"  rel="stylesheet" type="text/css" >';
	echo '<script src="' . base_url('/modules/customemailandsmsnotifications/assets/check.js') . '"></script>';
}

/**
* Add additional settings for this module in the module list area
* @param  array $actions current actions
* @return array
*/
function module_customemailandsmsnotifications_action_links($actions)
{
    return $actions;
}
/**
* Load the module helper
*/
$CI->load->helper(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE . '/customemailandsmsnotifications');

/**
* Register activation module hook
*/
register_activation_hook(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE, 'customemailandsmsnotifications_activation_hook');

function customemailandsmsnotifications_activation_hook()
{
	$CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE, [CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE]);

//inject permissions Feature and Capabilities for module
hooks()->add_filter('staff_permissions', 'customemailandsmsnotifications_permissions_for_staff');
function customemailandsmsnotifications_permissions_for_staff($permissions)
{
    $viewGlobalName      = _l('permission_view').'('._l('permission_global').')';
    $allPermissionsArray = [
        'view'     => $viewGlobalName,
        'create'   => _l('permission_create'),
    ];
    $permissions['customemailandsmsnotifications'] = [
                'name'         => _l('sms_module_title'),
                'capabilities' => $allPermissionsArray,
            ];

    return $permissions;
}

hooks()->add_action('admin_init', 'custom_email_and_sms_menuitem');

function custom_email_and_sms_menuitem()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('custom-email-and-sms', [
            'slug'     => 'main-menu-options',
            'name'     => 'Custom Email/SMS',
            'href'     => admin_url('customemailandsmsnotifications/email_sms/email_or_sms'),
            'position' => 65,
            'icon'     => 'fa fa-envelope'
    ]);


    $CI->app_menu->add_sidebar_children_item('custom-email-and-sms', [
        'slug'     => 'main-menu-options',
        'name'     => 'Send a Notification',
        'href'     => admin_url('customemailandsmsnotifications/email_sms/email_or_sms'),
        'position' => 65,
    ]);

    if (has_permission('customemailandsmsnotifications', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('custom-email-and-sms', [
            'slug'     => 'add_edit_templates',
            'name'     => _l('templates'),
            'href'     => admin_url('customemailandsmsnotifications/template'),
            'position' => 5,
        ]);
    }

}



hooks()->add_action('app_init', CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE.'_actLib');
function customemailandsmsnotifications_actLib()
{
    $CI = &get_instance();
    $CI->load->library(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE.'/Customemailandsmsnotifications_aeiou');
    $envato_res = $CI->customemailandsmsnotifications_aeiou->validatePurchase(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE);
    if (!$envato_res) {
        set_alert('danger', 'One of your modules failed its verification and got deactivated. Please reactivate or contact support.');
    }
}

hooks()->add_action('pre_activate_module', CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE.'_sidecheck');
function customemailandsmsnotifications_sidecheck($module_name)
{
    if (CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE == $module_name['system_name']) {
        modules\customemailandsmsnotifications\core\Apiinit::activate($module_name);
    }
}

hooks()->add_action('pre_deactivate_module', CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE.'_deregister');
function customemailandsmsnotifications_deregister($module_name)
{
    if (CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE == $module_name['system_name']) {
        delete_option(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE.'_verification_id');
        delete_option(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE.'_last_verification');
        delete_option(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE.'_product_token');
        delete_option(CUSTOMEMAILANDSMSNOTIFICATIONS_MODULE.'_heartbeat');
    }
}


function run_cron_job_custom_email(){
   
    $CI = &get_instance();
    $CI->load->model('customemailandsmsnotifications/customemailandsmsnotifications_model');
     
    $CI->db->select('*');
     $CI->db->from('tblcustom_email_sms');
    $result =  $CI->db->get()->result();

    foreach ($result as $key => $value) {
        $scheduledDateTime = $value->custom_date . ' ' . $value->custom_time;
        $new_date = date('Y-m-d H:i', strtotime($scheduledDateTime));
        $currentDateTime = date('Y-m-d H:i');
        if ($currentDateTime >= $scheduledDateTime && $value->is_delivered == 0) {
            $arrayData = json_decode(json_encode($value), true);
            $unsetKeys = ['is_delivered','custom_date','custom_time'];
            $arrayData = array_diff_key($arrayData, array_flip($unsetKeys));
            $arrayData['select_customer'] = json_decode($arrayData['select_customer']);
            if ($arrayData['mail_or_sms'] == "mail") {
                if (!empty($arrayData['file_mail'])) {
                 $arrayData['file_mail'] = json_decode($arrayData['file_mail']);
                    if ($arrayData['file_mail']) {
                       $_FILES['file_mail']['tmp_name'] = $arrayData['file_mail']->tmp_name;
                       $_FILES['file_mail']['name'] = $arrayData['file_mail']->name;
                    }
                 }
                $CI->customemailandsmsnotifications_model->sendMail($arrayData);
            }else{
                $CI->customemailandsmsnotifications_model->sendSMS($arrayData);
            }
        }
    }
}
