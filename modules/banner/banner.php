<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
    Module Name: BannerCraft
    Description: Robust tool to effortlessly organize your banners, enhancing the visual appeal and effectiveness of your CRM
    Version: 1.0.2
    Requires at least: 3.0.*
    Module URI: https://codecanyon.net/item/bannercraft-dynamic-banner-management-module-for-perfex-crm/51504146
    Author: <a href="https://codecanyon.net/user/corbitaltech" target="_blank">Corbital Technologies<a/>
*/

/*
 * Define module name
 * Module Name Must be in CAPITAL LETTERS
 */
define('BANNER_MODULE', 'banner');

define('BANNER_MODULE_ATTACHMENTS_FOLDER', FCPATH.'/uploads/banner');

// require_once __DIR__.'/vendor/autoload.php';

/*
 * Register activation module hook
 */
register_activation_hook(BANNER_MODULE, function () {
    require_once __DIR__.'/install.php';
});

/*
 * Register deactivation module hook
 */
register_deactivation_hook(BANNER_MODULE, function () {
    $my_files_list = [
        VIEWPATH.'themes/perfex/views/my_home.php',
    ];

    foreach ($my_files_list as $actual_path) {
        if (file_exists($actual_path)) {
            @unlink($actual_path);
        }
    }
});

/*
 * Register language files, must be registered if the module is using languages
 */
register_language_files(BANNER_MODULE, [BANNER_MODULE]);

/*
 * Load module helper file
 */
get_instance()->load->helper(BANNER_MODULE.'/banner');

require_once __DIR__.'/includes/assets.php';
require_once __DIR__.'/includes/staff_permissions.php';
require_once __DIR__.'/includes/sidebar_menu_links.php';

hooks()->add_filter('get_upload_path_by_type', function ($path, $type) {
    switch ($type) {
        case 'banner':
            $path = BANNER_MODULE_ATTACHMENTS_FOLDER;
            break;

        default:
            $path = $path;
            break;
    }

    return $path;
}, 0, 2);

require_once __DIR__ . '/install.php';
get_instance()->config->load(BANNER_MODULE . '/config');

function bannerContent($allowArea, $value = '')
{
    $details = getBannerDetails($allowArea);
    if (!empty($details)) {
        return renderBanner($details);
    }
}

hooks()->add_action('before_start_render_dashboard_content', function () {
    $content = bannerContent('admin_area');
    echo $content;
});

hooks()->add_action('display_banner_for_client_area', function () {
    $content = '<div class="row">';
    $content .= bannerContent('clients_area');
    $content .= '</div>';
    echo $content;
});
