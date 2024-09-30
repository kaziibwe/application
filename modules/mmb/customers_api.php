<?php

defined('BASEPATH') || exit('No direct script access allowed');
/*
 * Define module name
 * Module Name Must be in CAPITAL LETTERS
*/
define('CUSTOMERS_API_MODULE', 'mmb');

// Register language files, must be registered if the module is using languages
register_language_files(MMB_MODULE, ["customers_api"]);

// Load module helper file
get_instance()->load->helper(CUSTOMERS_API_MODULE.'/customers_api');

hooks()->add_action('admin_init', 'add_view_to_settings_tabs');
function add_view_to_settings_tabs()
{
    get_instance()->app_tabs->add_settings_tab('customer_rest_api', [
        'name'     => _l('customer_rest_api'),
        'view'     => CUSTOMERS_API_MODULE . '/customers_api/rest_api_settings',
        'icon'     => 'fab fa-app-store',
        'position' => 5,
    ]);
}

hooks()->add_action('admin_init', 'customers_api_module_init_menu_items');
function customers_api_module_init_menu_items()
{
    get_instance()->app_menu->add_sidebar_menu_item('customers_api', [
        'slug'     => 'customers_api',
        'name'     => _l('customers_api'),
        'icon'     => 'fa-brands fa-app-store',
        'position' => 30,
    ]);

    get_instance()->app_menu->add_sidebar_children_item('customers_api', [
        'slug'     => 'customers_api',
        'name'     => _l('api_settings'),
        'href'     => admin_url('settings?group=customer_rest_api'),
        'position' => 31,
    ]);

}

hooks()->add_action('client_status_changed', function($clientData) {
    if ($clientData['status'] == 0) {
        get_instance()->db->update(db_prefix() . 'contacts', ['customer_api_key' => NULL], ['userid' => $clientData['id']]);
    }
});

hooks()->add_action('contact_updated', function($contactID) {
    get_instance()->db->update(db_prefix() . 'contacts', ['customer_api_key' => NULL], ['id' => $contactID]);
});

hooks()->add_action('after_user_reset_password', function($data) {
    get_instance()->db->update(db_prefix() . 'contacts', ['customer_api_key' => NULL], ['id' => $data['userid']]);
});