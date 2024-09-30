<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
    Module Name: Perfex Marketing Modules Bundle
    Module URI: https://codecanyon.net/item/marketing-business-modules-bundle-for-perfex-crm/50406188
    Description: Keep your Customers & Staff updated in real-time about New Invoices, Project's Tasks and more!
    Version: 1.0.2
    Requires at least: 3.0.*
*/

use app\services\zip\Unzip;

/*
 * Define module name
 * Module Name Must be in CAPITAL LETTERS
 */
define('MMB_MODULE', 'mmb');

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/install.php';

define('MMB_UPLOAD_FOLDER', module_dir_path(MMB_MODULE, 'uploads'));
define('WHATSAPP_API_UPLOAD_FOLDER', module_dir_path(MMB_MODULE, 'uploads/whatsapp'));

// Get codeigniter instance
$CI = &get_instance();

hooks()->add_filter('pre_upload_module', function ($file) {
    $uploadedTmpZipPath = $file['tmp_name'];
    $unzip = new Unzip();
    $moduleTemporaryDir = get_temp_dir() . time() . '/';
    $unzip->extract($uploadedTmpZipPath, $moduleTemporaryDir);

    // Check the folder contains at least 1 valid module.
    $modules_found = false;

    $files = get_dir_contents($moduleTemporaryDir);

    if ($files) {
        foreach ($files as $file) {
            if (endsWith($file, '.php')) {
                $info = get_instance()->app_modules->get_headers($file);
                if (isset($info['module_name']) && !empty($info['module_name'])) {
                    break;
                }
            }
        }
    }

    if (isset($info['uri'])) {
        if (in_array(basename($info['uri']), [38690826, 38350010, 45916466, 49922588])) {
            set_alert('warning', _l('cannot_upload_already_available_in_bundle'));
            redirect(admin_url('modules'));
        }
    }
});

/*
 * Register activation module hook
 */
register_activation_hook(MMB_MODULE, 'mmb_module_activation_hook');
function mmb_module_activation_hook()
{
    $CI = &get_instance();
    require_once __DIR__ . '/install.php';

    $deactivatedModules = [];
    foreach (['whatsapp_api', 'webhooks', 'customers_api'] as $value) {
        if (get_instance()->app_modules->is_active($value)) {
            $deactivatedModules[] = $value;
            get_instance()->app_modules->deactivate($value);
        }
    }

    if (!empty($deactivatedModules)) {
        set_alert('warning', implode(', ', $deactivatedModules) . ' ' . _l('modules_has_been_deactivated'));
    }

    // Create invoices and proposal folders within module folder. perfex has .htaccess that is blocking access
    _maybe_create_upload_path(MMB_UPLOAD_FOLDER);
    _maybe_create_upload_path(WHATSAPP_API_UPLOAD_FOLDER);
    _maybe_create_upload_path(WHATSAPP_API_UPLOAD_FOLDER . '/invoices');
    _maybe_create_upload_path(WHATSAPP_API_UPLOAD_FOLDER . '/proposals');
    _maybe_create_upload_path(WHATSAPP_API_UPLOAD_FOLDER . '/broadcast_images');
}

get_instance()->config->load(MMB_MODULE . '/config');

hooks()->add_action('app_admin_head', 'mmb_add_head_components');
function mmb_add_head_components()
{
    $CI = &get_instance();
    if ($CI->app_modules->is_active(MMB_MODULE)) {
        echo '<link href="' . module_dir_url(MMB_MODULE, 'assets/css/tribute.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(MMB_MODULE, 'assets/css/prism.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
    }
}

hooks()->add_action('app_admin_footer', 'mmb_load_js');
function mmb_load_js()
{
    $CI = &get_instance();
    if ($CI->app_modules->is_active(MMB_MODULE)) {
        $CI->load->library('App_merge_fields');
        $CI->load->library('App_merge_fields');
        $merge_fields = $CI->app_merge_fields->all();
        echo '<script>var merge_fields = ' . json_encode($merge_fields) . '</script>';
        echo '<script src="' . module_dir_url(MMB_MODULE, 'assets/js/underscore-min.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
        echo '<script src="' . module_dir_url(MMB_MODULE, 'assets/js/tribute.min.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
        echo '<script src="' . module_dir_url(MMB_MODULE, 'assets/js/prism.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
    }
}

register_language_files(MMB_MODULE, [MMB_MODULE]);

include_once 'webhooks.php';
include_once 'whatsapp.php';
include_once 'customers_api.php';
include_once 'custom_pdf.php';