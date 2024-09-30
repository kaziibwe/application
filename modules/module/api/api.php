<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Api
Description: Default module for defining Api
Version: 2.3.0
Requires at least: 2.3.*
*/

define('API_MODULE_NAME', 'api');

hooks()->add_action('admin_init', 'api_module_init_menu_items');
hooks()->add_action('admin_init', 'api_permissions');

hooks()->add_filter('migration_tables_to_replace_old_links', 'api_migration_tables_to_replace_old_links');



	function api_migration_tables_to_replace_old_links($tables)
	{
		$tables[] = [
					'table' => db_prefix() . 'api',
					'field' => 'description',
				];

		return $tables;
	}

	function api_permissions()
	{
		$capabilities = [];

		$capabilities['capabilities'] = [
				'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
		];

		register_staff_capabilities('api', $capabilities, _l('api'));
	}



/**
* Register activation module hook
*/
	register_activation_hook(API_MODULE_NAME, 'api_module_activation_hook');

	function api_module_activation_hook()
	{
		$CI = &get_instance();
		require_once(__DIR__ . '/install.php');
	}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(API_MODULE_NAME, [API_MODULE_NAME]);

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function api_module_init_menu_items()
{
    $CI = &get_instance();

    $CI->app->add_quick_actions_link([
            'name'       => _l('Api'),
            'url'        => 'api/api',
            'permission' => 'api',
            'position'   => 56,
            ]);

    if (has_permission('api', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'api-tracking',
                'name'     => _l('Api'),
                'href'     => admin_url('api'),
                'position' => 24,
        ]);
    }
}



