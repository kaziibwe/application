<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: PerfexDashboard
Description: Unlimited dashboard builder for PerfexCRM
Version: 1.0.1
Requires at least: 2.3.*
*/

define('PERFEX_DASHBOARD_MODULE_NAME', 'perfex_dashboard');
define('PERFEX_DASHBOARD_ASSETS_PATH', 'modules/perfex_dashboard/assets');

$CI = &get_instance();

hooks()->add_action('admin_init', 'perfex_dashboard_module_menu_admin_items');
hooks()->add_action('admin_init', 'perfex_dashboard_permissions');

function perfex_dashboard_module_menu_admin_items()
{
  $CI = &get_instance();

  if (has_permission('perfex_dashboard', '', 'my_dashboard_view') || has_permission('perfex_dashboard', '', 'all_dashboard_view') || has_permission('perfex_dashboard', '', 'widget_view') || has_permission('perfex_dashboard', '', 'dashboard_settings')) {
    $CI->app_menu->add_sidebar_menu_item('perfex-dashboard-module-menu-master', [
        'name'     => _l('perfex_dashboard'),
        'href'     => 'javascript:void(0);',
        'position' => 2,
        'icon'     => 'fa fa-home menu-icon',
    ]);
  }

  if (has_permission('perfex_dashboard', '', 'my_dashboard_view')) {
    $CI->app_menu->add_sidebar_children_item('perfex-dashboard-module-menu-master', [
      'name'     => _l('my_dashboard'),
      'href'     => admin_url('perfex_dashboard/dashboards/my_dashboard'),
      'position' => 1,
      'slug'     => 'dashboards',
    ]);
  }
  if (has_permission('perfex_dashboard', '', 'all_dashboard_view')) {
    $CI->app_menu->add_sidebar_children_item('perfex-dashboard-module-menu-master', [
      'name'     => _l('all_dashboards'),
      'href'     => admin_url('perfex_dashboard/dashboards'),
      'position' => 2,
      'slug'     => 'dashboards',
    ]);
  }
  if (has_permission('perfex_dashboard', '', 'widget_view')) {
    $CI->app_menu->add_sidebar_children_item('perfex-dashboard-module-menu-master', [
      'name'     => _l('all_widgets'),
      'href'     => admin_url('perfex_dashboard/widgets'),
      'position' => 3,
      'slug'     => 'widgets',
    ]);
  }
  if (has_permission('perfex_dashboard', '', 'widget_category_view')) {
    $CI->app_menu->add_sidebar_children_item('perfex-dashboard-module-menu-master', [
      'name'     => _l('widget_categories'),
      'href'     => admin_url('perfex_dashboard/categories'),
      'position' => 4,
      'slug'     => 'categories',
    ]);
  }
  if (has_permission('perfex_dashboard', '', 'dashboard_settings')) {
    $CI->app_menu->add_sidebar_children_item('perfex-dashboard-module-menu-master', [
      'name'     => _l('settings'),
      'href'     => admin_url('perfex_dashboard/settings'),
      'position' => 4,
      'slug'     => 'settings',
    ]);
  }
}

function perfex_dashboard_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'my_dashboard_view'   => _l('my_dashboard_view'),
            'all_dashboard_view'   => _l('all_dashboard_view'),
            'dashboard_create' => _l('dashboard_create'),
            'dashboard_edit'   => _l('dashboard_edit'),
            'dashboard_delete' => _l('dashboard_delete'),
            'dashboard_clone' => _l('dashboard_clone'),
            'widget_view'   => _l('widget_view'),
            'widget_create' => _l('widget_create'),
            'widget_edit'   => _l('widget_edit'),
            'widget_delete' => _l('widget_delete'),
            'widget_category_view'   => _l('widget_category_view'),
            'widget_category_create' => _l('widget_category_create'),
            'widget_category_edit'   => _l('widget_category_edit'),
            'widget_category_delete' => _l('widget_category_delete'),
            'dashboard_settings' => _l('dashboard_settings'),
    ];

    register_staff_capabilities('perfex_dashboard', $capabilities, _l('perfex_dashboard'));
}

$CI->load->helper(PERFEX_DASHBOARD_MODULE_NAME . '/perfex_dashboard');

/**
 * Register activation module hook
 */
register_activation_hook(PERFEX_DASHBOARD_MODULE_NAME, 'perfex_dashboard_module_activation_hook');

function perfex_dashboard_module_activation_hook()
{
  $CI = &get_instance();
  require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(PERFEX_DASHBOARD_MODULE_NAME, [PERFEX_DASHBOARD_MODULE_NAME]);
