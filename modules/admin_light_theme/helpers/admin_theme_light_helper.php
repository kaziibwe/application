<?php
/*
Module Name: Perfex Light Theme
Description: light flat elegant theme for Perfex CRM
Version: 1.2.2
Author: Aleksandar Stojanov
Author URI: https://aleksandarstojanov.com
Requires at least: 2.3.2
*/
defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('app_admin_head', 'admin_theme_head_component');
hooks()->add_action('app_admin_footer', 'admin_light_theme_footer_js__component');
hooks()->add_action('admin_init', 'admin_light_theme_settings_tab');
hooks()->add_action('app_admin_authentication_head', 'admin_theme_staff_login');

// Check if customers theme is enabled
if (get_option('admin_light_theme_customers') == '1') {
    hooks()->add_action('app_external_form_head', 'app_client_light_head_custom_includes');
    hooks()->add_action('app_customers_head', 'app_client_light_head_includes');
    hooks()->add_action('app_customers_footer', 'admin_light_theme_footer_js__component');
}
/**
 * Theme customers login includes
 *
 * @return void
 */
function admin_theme_staff_login()
{
    echo '<link href="' . base_url('modules/admin_light_theme/assets/css/staff_login_styles.css') . '"  rel="stylesheet" type="text/css" >';
    echo '<script src="' . module_dir_url('admin_light_theme', 'assets/js/sign_in.js') . '"></script>';
}

/**
 * Theme clients footer includes
 *
 * @return void
 */
function app_client_light_head_includes()
{
    echo '<link href="' . base_url('modules/admin_light_theme/assets/css/clients/clients.css') . '"  rel="stylesheet" type="text/css" >';
    echo '<link href="' . base_url('modules/admin_light_theme/assets/css/animated.css') . '"  rel="stylesheet" type="text/css" >';
    echo '<script src="' . module_dir_url('admin_light_theme', 'assets/js/third-party/waves076.min.js') . '"></script>';
    echo '<script src="' . module_dir_url('admin_light_theme', 'assets/js/third-party/nanobar.js') . '"></script>';

}

function app_client_light_head_custom_includes()
{
    echo '<link href="' . base_url('modules/admin_light_theme/assets/css/clients/clients.css') . '"  rel="stylesheet" type="text/css" >';
    echo '<link href="' . base_url('modules/admin_light_theme/assets/css/animated.css') . '"  rel="stylesheet" type="text/css" >';
    echo '<script src="' . module_dir_url('admin_light_theme', 'assets/js/third-party/waves076.min.js') . '"></script>';
    echo '<script src="' . module_dir_url('admin_light_theme', 'assets/js/third-party/nanobar.js') . '"></script>';
    echo '<script src="' . module_dir_url('admin_light_theme', 'assets/js/common.js') . '"></script>';
    echo '<link href="' . base_url('modules/admin_light_theme/assets/css/clients/common.css') . '"  rel="stylesheet" type="text/css" >';
}

/**
 * [admin_light_theme_settings_tab net menu item in setup->settings]
 *
 * @return void
 */
function admin_light_theme_settings_tab()
{
    $CI = &get_instance();
    $CI->app_tabs->add_settings_tab('admin-theme-settings', [
        'name'     => '' . _l('admin_light_theme_settings_first') . '',
        'view'     => 'admin_light_theme/admin_theme_settings',
        'position' => 46,
    ]);
}

/**
 * Injects theme CSS
 *
 * @return null
 */
function admin_theme_head_component()
{
    echo '<link href="' . base_url('modules/admin_light_theme/assets/css/theme_styles.css') . '"  rel="stylesheet" type="text/css" >';
    echo '<link href="' . base_url('modules/admin_light_theme/assets/css/animated.css') . '"  rel="stylesheet" type="text/css" >';
    echo '<script src="' . module_dir_url('admin_light_theme', 'assets/js/third-party/nanobar.js') . '"></script>';
    echo '<script src="' . module_dir_url('admin_light_theme', 'assets/js/third-party/waves076.min.js') . '"></script>';
}

/**
 * Injects theme js components in footer
 *
 * @return null
 */
function admin_light_theme_footer_js__component()
{
    echo '<script src="' . module_dir_url('admin_light_theme', 'assets/js/clients.js') . '"></script>';
}


/**
 * Changes task filters color and background
 *
 * @return array
 */
hooks()->add_action('app_init', 'initialize_admin_light_theme_task_filters');

/**
 * Changes system favorite colors
 *
 * @return array
 */
hooks()->add_filter('system_favourite_colors', 'admin_light_system_colors_theme_hook');

function admin_light_system_colors_theme_hook($colors)
{
    foreach ($colors as $key => $color) {
        if ($color == '#ff2d42') {
            $colors[$key] = '#ff0019';
        }
        if ($color == '#28B8DA') {
            $colors[$key] = '#2663eb';
        }
        if ($color == '#03a9f4') {
            $colors[$key] = '#2a44c5';
        }
        if ($color == '#757575') {
            $colors[$key] = '#595959';
        }
        if ($color == '#8e24aa') {
            $colors[$key] = '#cc0099';
        }
        if ($color == '#d81b60') {
            $colors[$key] = '#8533ff';
        }
        if ($color == '#0288d1') {
            $colors[$key] = '#3333ff';
        }
        if ($color == '#7cb342') {
            $colors[$key] = '#2eb82e';
        }
        if ($color == '#fb8c00') {
            $colors[$key] = '#e67e00';
        }
        if ($color == '#84C529') {
            $colors[$key] = '#71a923';
        }
        if ($color == '#fb3b3b') {
            $colors[$key] = '#fa1e1e';
        }
    }

    return $colors;
}

function initialize_admin_light_theme_task_filters()
{
    hooks()->add_filter('before_get_task_statuses', 'admin_light_dashboard_label_task_colors');
}


function admin_light_dashboard_label_task_colors($statuses)
{
    $CI = &get_instance();

    if (!class_exists('tasks_model', false)) {
        $CI->load->model('tasks_model');
    }

    foreach ($statuses as $key => $status) {
        $id = $status['id'];
        switch ($id) {
            case $id == Tasks_model::STATUS_NOT_STARTED:
                $statuses[$key]['color'] = '#ffb822';
                break;
            case $id == Tasks_model::STATUS_AWAITING_FEEDBACK:
                $statuses[$key]['color'] = '#0abb87';
                break;
            case $id == Tasks_model::STATUS_IN_PROGRESS:
                $statuses[$key]['color'] = '#2663eb';
                break;
            case $id == Tasks_model::STATUS_COMPLETE:
                $statuses[$key]['color'] = '#84c529';
                break;
        }
    }
    return $statuses;
}

/**
 * Changes project filters color
 *
 * @return array
 */
hooks()->add_action('app_init', 'initialize_admin_light_theme_project_filters');

function initialize_admin_light_theme_project_filters()
{
    hooks()->add_filter('before_get_project_statuses', 'admin_light_dashboard_label_project_colors');
}

function admin_light_dashboard_label_project_colors($statuses)
{
    foreach ($statuses as $key => $status) {
        $id = $status['id'];

        switch ($id) {
            case $id == 3:
                $statuses[$key]['color'] = '#ffb822';
                break;
            case $id == 4:
                $statuses[$key]['color'] = '#0abb87';
                break;
            case $id == 1:
                $statuses[$key]['color'] = '#777777';
                break;
            case $id == 2:
                $statuses[$key]['color'] = '#2663eb';
                break;
            case $id == 5:
                $statuses[$key]['color'] = '#fa1e1e';
                break;
        }
    }
    return $statuses;
}
