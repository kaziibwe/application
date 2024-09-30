<?php

/*
 * Inject sidebar menu and links for banner module
 */
hooks()->add_action('admin_init', function () {
    if (has_permission('banner', get_staff_user_id(), 'view')) {
        get_instance()->app_menu->add_sidebar_menu_item('banner', [
            'slug'     => 'banner',
            'name'     => _l('banner_management'),
            'icon'     => 'fa-regular fa-images',
            'href'     => admin_url('banner'),
            'position' => 20,
        ]);
    }
});
