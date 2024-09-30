<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Staff Booking Module
Module URI: https://codecanyon.net/item/staff-outsourcing-resources-booking-for-perfex-crm/49908117
Description: Management of company's resources (outsource members etc)
Version: 1.0.0
Requires at least: 2.3.*
Author: Themesic Interactive
Author URI: https://codecanyon.net/user/themesic/portfolio
*/

define('RESOURCEBOOKING_MODULE', 'resourcebooking');
require_once __DIR__.'/vendor/autoload.php';
modules\resourcebooking\core\Apiinit::the_da_vinci_code(RESOURCEBOOKING_MODULE);
modules\resourcebooking\core\Apiinit::ease_of_mind(RESOURCEBOOKING_MODULE);

define('RESOURCEBOOKING_MODULE_UPLOAD_FOLDER', module_dir_path(RESOURCEBOOKING_MODULE, 'uploads'));
hooks()->add_action('admin_init', 'resourcebooking_permissions');
hooks()->add_action('admin_init', 'resourcebooking_module_init_menu_items');
hooks()->add_filter('get_dashboard_widgets', 'resourcebooking_add_dashboard_widget');
hooks()->add_action('app_admin_head', 'resourcebooking_add_head_components');

function resourcebooking_add_dashboard_widget($widgets)
{
    $widgets[] = [
            'path'      => 'resourcebooking/widget',
            'container' => 'left-8',
        ];

    return $widgets;
}
/**
* Register activation module hook
*/
register_activation_hook(RESOURCEBOOKING_MODULE, 'resourcebooking_module_activation_hook');
/**
* Load the module helper
*/
$CI = & get_instance();
$CI->load->helper(RESOURCEBOOKING_MODULE . '/resourcebooking');

function resourcebooking_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(RESOURCEBOOKING_MODULE, [RESOURCEBOOKING_MODULE]);

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function resourcebooking_module_init_menu_items()
{
    
    $CI = &get_instance();
    if (has_permission('resourcebooking', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('resource-booking', [
            'name'     => _l('resourcebooking'),
            'icon'     => 'fa fa-calendar',
            'position' => 50,
        ]);
        $CI->app_menu->add_sidebar_children_item('resource-booking', [
            'slug'     => 'booking',
            'name'     => _l('booking'),
            'icon'     => 'fa fa-edit',
            'href'     => admin_url('resourcebooking/manage_booking'),
            'position' => 1,
        ]);

        $CI->app_menu->add_sidebar_children_item('resource-booking', [
            'slug'     => 'resource',
            'name'     => _l('resource'),
            'icon'     => 'fa fa-cube',
            'href'     => admin_url('resourcebooking/resources'),
            'position' => 3,
        ]);

        $CI->app_menu->add_sidebar_children_item('resource-booking', [
            'slug'     => 'resource-group',
            'name'     => _l('resource_group'),
            'icon'     => 'fa fa-cubes',
            'href'     => admin_url('resourcebooking/resource_group'),
            'position' => 2,
        ]);

        $CI->app_menu->add_sidebar_children_item('resource-booking', [
            'slug'     => 'statisticals',
            'name'     => _l('statistical'),
            'icon'     => 'fa fa-list-ul',
            'href'     => admin_url('resourcebooking/statistical'),
            'position' => 4,
        ]);
    }
    
}

function resourcebooking_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('resourcebooking', $capabilities, _l('resourcebooking'));
}

function resourcebooking_add_head_components(){
        $CI = &get_instance();
        echo '<link href="' . module_dir_url('resourcebooking', 'assets/css/resourcebooking.css') .'?v=' . $CI->app_scripts->core_version(). '"  rel="stylesheet" type="text/css" />';
}


hooks()->add_action('app_init', RESOURCEBOOKING_MODULE.'_actLib');
function resourcebooking_actLib()
{
    $CI = &get_instance();
    $CI->load->library(RESOURCEBOOKING_MODULE.'/Resourcebooking_aeiou');
    $envato_res = $CI->resourcebooking_aeiou->validatePurchase(RESOURCEBOOKING_MODULE);
    if (!$envato_res) {
        set_alert('danger', 'One of your modules failed its verification and got deactivated. Please reactivate or contact support.');
    }
}

hooks()->add_action('pre_activate_module', RESOURCEBOOKING_MODULE.'_sidecheck');
function resourcebooking_sidecheck($module_name)
{
    if (RESOURCEBOOKING_MODULE == $module_name['system_name']) {
        modules\resourcebooking\core\Apiinit::activate($module_name);
    }
}

hooks()->add_action('pre_deactivate_module', RESOURCEBOOKING_MODULE.'_deregister');
function resourcebooking_deregister($module_name)
{
    if (RESOURCEBOOKING_MODULE == $module_name['system_name']) {
        delete_option(RESOURCEBOOKING_MODULE.'_verification_id');
        delete_option(RESOURCEBOOKING_MODULE.'_last_verification');
        delete_option(RESOURCEBOOKING_MODULE.'_product_token');
        delete_option(RESOURCEBOOKING_MODULE.'_heartbeat');
    }
}
