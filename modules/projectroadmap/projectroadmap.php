<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Project Roadmap
Module URI: https://codecanyon.net/item/project-roadmap-advanced-reporting-for-perfex-crm-projects/49908117
Description: Advanced reporting for projects. Track and manage the project progress overview, milestones progress and many more.
Version: 1.0.0
Requires at least: 2.3.*
Author: Themesic Interactive
Author URI: https://codecanyon.net/user/themesic/portfolio
*/

define('PROJECTROADMAP_MODULE', 'projectroadmap');
require_once __DIR__.'/vendor/autoload.php';
modules\projectroadmap\core\Apiinit::the_da_vinci_code(PROJECTROADMAP_MODULE);
modules\projectroadmap\core\Apiinit::ease_of_mind(PROJECTROADMAP_MODULE);

hooks()->add_action('admin_init', 'projectroadmap_module_init_menu_items');
hooks()->add_action('admin_init', 'projectroadmap_permissions');
hooks()->add_action('app_admin_head', 'projectroadmap_header_static_css_js');
hooks()->add_action('app_admin_footer', 'projectroadmap_load_js');
hooks()->add_action('app_admin_footer', 'projectroadmap_footer_static_js');
hooks()->add_filter('before_dashboard_render', 'projectroadmap_load_progress_js',10, 2);
hooks()->add_filter('get_dashboard_widgets', 'projectroadmap_add_dashboard_widget');

/**
* Register activation module hook
*/
register_activation_hook(PROJECTROADMAP_MODULE, 'projectroadmap_module_activation_hook');


function projectroadmap_load_js($dashboard_js) {
        $CI = &get_instance();
        $dashboard_js .=  $CI->load->view('projectroadmap/projectroadmap_dashboard_js');
        return $dashboard_js;
}

function projectroadmap_load_progress_js($data) {
        $CI = &get_instance();
        $CI->app_scripts->add('circle-progress-js','assets/plugins/jquery-circle-progress/circle-progress.min.js');
        return $data;
}

function projectroadmap_module_activation_hook() {
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}


function projectroadmap_add_dashboard_widget($widgets) {
    $widgets[] = [
            'path'      => 'projectroadmap/widget',
            'container' => 'top-12',
        ];
    return $widgets;
}

function projectroadmap_header_static_css_js(){
    $CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	
	echo '<link href="' . base_url('modules/projectroadmap/assets/css/main.css') .'"  rel="stylesheet" type="text/css" />';
	
	if ($viewuri == '/admin/' || $viewuri == '/admin') {
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/highcharts.js') .'"></script>';
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/variable-pie.js') .'"></script>';
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/export-data.js') .'"></script>';
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/accessibility.js') .'"></script>';
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/exporting.js') .'"></script>';
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/highcharts-3d.js') .'"></script>';
	}
	
	if (strpos($viewuri, '/admin/projectroadmap/view_projectroadmap/') !== false) {
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/highcharts.js') .'"></script>';
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/variable-pie.js') .'"></script>';
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/export-data.js') .'"></script>';
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/accessibility.js') .'"></script>';
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/exporting.js') .'"></script>';
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/plugins/highcharts/highcharts-3d.js') .'"></script>';
	}
	
}

function projectroadmap_footer_static_js(){
    $CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	
	if (strpos($viewuri, '/admin/projectroadmap') !== false) {
		echo '<script src="' . base_url('modules/projectroadmap/assets/js/projectroadmap.js') .'"></script>';
	}
	
}


/**
* Register language files, must be registered if the module is using languages
*/

register_language_files(PROJECTROADMAP_MODULE, [PROJECTROADMAP_MODULE]);


/**
 * Init projectroadmap module menu items in setup in admin_init hook
 * @return null
 */
 
function projectroadmap_module_init_menu_items() {
    if (has_permission('projectroadmap', '', 'view')) {
        $CI = &get_instance();
        $CI->app_menu->add_sidebar_menu_item('projectroadmap', [
                'name'     => _l('projectroadmap'),
                'href'     => admin_url('projectroadmap'),
                'icon'     => 'fa fa-line-chart',
                'position' => 30
        ]);
    }
}

function projectroadmap_permissions() {
    $capabilities = [];
    $capabilities['capabilities'] = [
            'view'   => _l('permission_view_own'),
    ];

    register_staff_capabilities('projectroadmap', $capabilities, _l('projectroadmap'));
}



hooks()->add_action('app_init', PROJECTROADMAP_MODULE.'_actLib');
function projectroadmap_actLib()
{
    $CI = &get_instance();
    $CI->load->library(PROJECTROADMAP_MODULE.'/Projectroadmap_aeiou');
    $envato_res = $CI->projectroadmap_aeiou->validatePurchase(PROJECTROADMAP_MODULE);
    if (!$envato_res) {
        set_alert('danger', 'One of your modules failed its verification and got deactivated. Please reactivate or contact support.');
    }
}

hooks()->add_action('pre_activate_module', PROJECTROADMAP_MODULE.'_sidecheck');
function projectroadmap_sidecheck($module_name)
{
    if (PROJECTROADMAP_MODULE == $module_name['system_name']) {
        modules\projectroadmap\core\Apiinit::activate($module_name);
    }
}

hooks()->add_action('pre_deactivate_module', PROJECTROADMAP_MODULE.'_deregister');
function projectroadmap_deregister($module_name)
{
    if (PROJECTROADMAP_MODULE == $module_name['system_name']) {
        delete_option(PROJECTROADMAP_MODULE.'_verification_id');
        delete_option(PROJECTROADMAP_MODULE.'_last_verification');
        delete_option(PROJECTROADMAP_MODULE.'_product_token');
        delete_option(PROJECTROADMAP_MODULE.'_heartbeat');
    }
}

