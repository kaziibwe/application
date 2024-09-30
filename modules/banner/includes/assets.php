<?php
/*
 * Inject css file for banner module
 */
hooks()->add_action('app_admin_head', function () {
    if (get_instance()->app_modules->is_active('banner')) {
        echo '<link href="' . module_dir_url('banner', 'assets/css/banner.css') . '?v=' . get_instance()->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url('banner', 'assets/css/cropper.min.css') . '?v=' . get_instance()->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
    }
});

/*
 * Inject Javascript file for banner module
 */
hooks()->add_action('app_admin_footer', function () {
    if (get_instance()->app_modules->is_active('banner')) {
        echo '<script src="' . module_dir_url('banner', 'assets/js/banner.js') . '?v=' . get_instance()->app_scripts->core_version() . '"></script>';
        echo '<script src="' . module_dir_url('banner', 'assets/js/cropper.min.js') . '?v=' . get_instance()->app_scripts->core_version() . '"></script>';
        echo '<script src="' . module_dir_url('banner', 'assets/js/jquery-cropper.min.js') . '?v=' . get_instance()->app_scripts->core_version() . '"></script>';
    }
});

hooks()->add_action('app_customers_head', function() {
    if (get_instance()->app_modules->is_active('banner')) {
        echo '<link href="' . module_dir_url('banner', 'assets/css/banner.css') . '?v=' . get_instance()->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
    }
});
