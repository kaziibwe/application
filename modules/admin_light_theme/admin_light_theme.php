<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Perfex Light Theme
Description: Light Theme for Perfex CRM
Version: 1.3.2
Author: Aleksandar Stojanov
Author URI: https://idevalex.com
Requires at least: 2.3.2
*/

define('ADMIN_LIGHT_THEME_MODULE_NAME', 'admin_light_theme');
define('ADMIN_LIGHT_THEME_CSS', module_dir_path(ADMIN_LIGHT_THEME_MODULE_NAME, 'assets/css/theme_styles.css'));

$CI = &get_instance();

/**
 * Register the activation chat
 */
register_activation_hook(ADMIN_LIGHT_THEME_MODULE_NAME, 'admin_light_theme_activation_hook');

/**
 * The activation function
 */
function admin_light_theme_activation_hook()
{
    require(__DIR__ . '/install.php');
}

/**
 * Register chat language files
 */
register_language_files(ADMIN_LIGHT_THEME_MODULE_NAME, ['admin_light_theme']);

/**
 * Load the chat helper
 */
$CI->load->helper(ADMIN_LIGHT_THEME_MODULE_NAME . '/admin_theme_light');
