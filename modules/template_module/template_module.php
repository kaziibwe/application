<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Template module 
Description: Template module For Perfex CRM
Version: 1.0.0
Requires at least: 2.3.*
*/


define('Template_module', 'template_module');

hooks()->add_action('admin_init', 'template_module_init_menu_items');
hooks()->add_action('customers_navigation_end', 'customers_navigation_template_module');

/**
 * Register activation module hook
 */
register_activation_hook(Template_module, 'template_module_activation_hook');

function template_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(Template_module, [Template_module]);

/**
 * Init backup module menu items in setup in admin_init hook
 * @return null
 */
function template_module_init_menu_items()
{
    /**
     * If the logged in user is administrator, add custom menu in Setup
     */
    if (is_admin()) {
        $CI = &get_instance();

        $CI->app_menu->add_sidebar_menu_item('template_menu', [
            'name'     => _l('template_menu'),
            'href'     => admin_url('template_module'),
            'icon'     => 'fa fa-file',
            'position' => 5,
        ]);
    }
}

function customers_navigation_template_module()
{
    echo '<li><a href="' . admin_url('template_module/template_module_client') . '">' . _l('template_menu') . '</a></li>';
}
