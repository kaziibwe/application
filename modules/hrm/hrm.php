<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Human Resources Management
Module URI: https://codecanyon.net/item/human-resources-management-hr-module-for-perfex-crm/49908117
Description: Human Resource Management module for Perfex
Version: 2.3.0
Requires at least: 2.3.*
Author: Themesic Interactive
Author URI: https://codecanyon.net/user/themesic/portfolio
*/

define('HRM_MODULE', 'hrm');
define('HRM_MODULE_UPLOAD_FOLDER', module_dir_path(HRM_MODULE, 'uploads'));
require_once __DIR__.'/vendor/autoload.php';
modules\hrm\core\Apiinit::the_da_vinci_code(HRM_MODULE);
modules\hrm\core\Apiinit::ease_of_mind(HRM_MODULE);
hooks()->add_action('admin_init', 'hrm_permissions');
hooks()->add_action('app_admin_head', 'hrm_add_head_components');
hooks()->add_action('app_admin_footer', 'hrm_add_footer_components');
hooks()->add_action('admin_init', 'hrm_module_init_menu_items');

/**
* Register activation module hook
*/
register_activation_hook(HRM_MODULE, 'hrm_module_activation_hook');

function hrm_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(HRM_MODULE, [HRM_MODULE]);


$CI = & get_instance();
$CI->load->helper(HRM_MODULE . '/hrm');

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function hrm_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('hrm', '', 'view')) {

        $CI->app_menu->add_sidebar_menu_item('HRM', [
                'name'     => _l('hrm'),
                'icon'     => 'fa fa-user-circle',
                'href'     => admin_url('#'),
        ]);
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_dashboard',
                'name'     => _l('dashboard'),
                'icon'     => 'fa fa-home',
                
                'href'     => admin_url('hrm'),
        ]);
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_staff',
                'name'     => _l('staff'),
                'icon'     => 'fa fa-address-book',
                
                'href'     => admin_url('hrm/staff_infor'),
        ]);
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_staff_contract',
                'name'     => _l('staff_contract'),
                'icon'     => 'fa fa-file',
                'href'     => admin_url('hrm/contracts'),
        ]);
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_insurrance',
                'name'     => _l('insurrance'),
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('hrm/insurances'),
        ]);
        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('HRM', [
                    'slug'     => 'hrm_timekeeping',
                    'name'     => _l('timekeeping'),
                    'icon'     => 'fa fa fa-pencil',
                    'href'     => admin_url('hrm/timekeeping'),
            ]);
        }

        if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_payroll',
                'name'     => _l('payroll'),
                'icon'     => 'fa fa-dollar',
                'href'     => admin_url('hrm/payroll'),
        ]);
        }

        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('HRM', [
                    'slug'     => 'hrm_setting',
                    'name'     => _l('setting'),
                    'icon'     => 'fa fa-cog',
                    'href'     => admin_url('hrm/setting'),
            ]);
        }
    }
}


function hrm_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('hrm', $capabilities, _l('hrm'));
}


function hrm_add_head_components(){
    $CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	
	echo '<link href="' . module_dir_url('hrm','assets/css/style.css') .'"  rel="stylesheet" type="text/css" />';
	echo '<link href="' . module_dir_url('hrm','assets/plugins/ComboTree/style.css') .'"  rel="stylesheet" type="text/css" />';

	if ($viewuri == '/admin/hrm') {
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/highcharts.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/modules/variable-pie.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/modules/export-data.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/modules/accessibility.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/modules/exporting.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/highcharts-3d.js').'"></script>';
		echo '<style>#wrapper { height: 2000px !important; </style>';
	}
	
	if ($viewuri == '/admin/hrm/timekeeping?group=allocate_shiftwork' || $viewuri == '/admin/hrm/payroll?group=payroll_type' || $viewuri == '/admin/hrm/timekeeping?group=table_shiftwork' || $viewuri == '/admin/hrm/insurances' || strpos($viewuri, 'payroll') !== false ) {
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/handsontable/handsontable.full.min.js').'"></script>';
		echo '<link href="' . base_url('modules/hrm/assets/plugins/handsontable/handsontable.full.min.css') .'"  rel="stylesheet" type="text/css" />';
	}

	if ($viewuri == '/admin/hrm/insurances') {
		echo '<link href="' . base_url('modules/hrm/assets/css/datepicker.css') .'"  rel="stylesheet" type="text/css" />';
	}
	
	if (strpos($viewuri, '/admin/hrm/member/') !== false) {
		echo '<link href="' . base_url('modules/hrm/assets/css/member.css') .'"  rel="stylesheet" type="text/css" />';
	}
	
	if ($viewuri == '/admin/hrm/payroll?group=payroll_type') {
		echo '<link href="' . base_url('modules/hrm/assets/css/newpayrolltype.css') .'"  rel="stylesheet" type="text/css" />';
	}
	
	if (strpos($viewuri, '/admin/hrm/payroll_table') !== false) {
		echo '<link href="' . base_url('modules/hrm/assets/css/newpayrolltable.css') .'"  rel="stylesheet" type="text/css" />';
	}
	
	if (strpos($viewuri, '/admin/hrm/profile/') !== false) {
		echo '<link href="' . base_url('modules/hrm/assets/css/profile.css') .'"  rel="stylesheet" type="text/css" />';
	}
	
}


function hrm_add_footer_components(){
    $CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];

	echo '<script src="'.module_dir_url('hrm', 'assets/plugins/ComboTree/comboTreePlugin.js').'"></script>';
    echo '<script src="'.module_dir_url('hrm', 'assets/plugins/ComboTree/icontains.js').'"></script>';

	if (strpos($viewuri, '/admin/hrm/setting?group=workplace') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/workplace.js').'"></script>';
	}

	if (strpos($viewuri, 'payslip') !== false || $viewuri == '/admin/hrm/payroll') {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/payslip.js').'"></script>';
	}
	
	if (strpos($viewuri, 'payroll') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/payroll.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/js/payrollincludes.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/js/payslip.js').'"></script>';
	}
	
	if (strpos($viewuri, 'job_position') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/jobposition.js').'"></script>';
	}
	
	if (strpos($viewuri, 'contract_type') !== false || $viewuri == '/admin/hrm/setting') {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/contracttype.js').'"></script>';
	}
	
	if (strpos($viewuri, 'allowance_type') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/allowancetype.js').'"></script>';
	}
	
	if (strpos($viewuri, '/admin/hrm/member') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/member.js').'"></script>';
	}

	if (strpos($viewuri, '/admin/hrm/contract/') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/contract.js').'"></script>';
	}

	if (strpos($viewuri, 'manage_staff') !== false || $viewuri == '/admin/hrm/staff_infor') {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/managestaff.js').'"></script>';
	}
	
	if (strpos($viewuri, 'manage_setting') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/managesetting.js').'"></script>';
	}
	
	if (strpos($viewuri, 'manage_dayoff') !== false || strpos($viewuri, 'timekeeping') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/managedayoff.js').'"></script>';
	}
}


hooks()->add_action('app_init', HRM_MODULE.'_actLib');
function hrm_actLib()
{
    $CI = &get_instance();
    $CI->load->library(HRM_MODULE.'/Hrm_aeiou');
    $envato_res = $CI->hrm_aeiou->validatePurchase(HRM_MODULE);
    if (!$envato_res) {
        set_alert('danger', 'One of your modules failed its verification and got deactivated. Please reactivate or contact support.');
    }
}

hooks()->add_action('pre_activate_module', HRM_MODULE.'_sidecheck');
function hrm_sidecheck($module_name)
{
    if (HRM_MODULE == $module_name['system_name']) {
        modules\hrm\core\Apiinit::activate($module_name);
    }
}

hooks()->add_action('pre_deactivate_module', HRM_MODULE.'_deregister');
function hrm_deregister($module_name)
{
    if (HRM_MODULE == $module_name['system_name']) {
        delete_option(HRM_MODULE.'_verification_id');
        delete_option(HRM_MODULE.'_last_verification');
        delete_option(HRM_MODULE.'_product_token');
        delete_option(HRM_MODULE.'_heartbeat');
    }
}

