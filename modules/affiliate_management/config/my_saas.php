<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Global hooks for Perfex saas module support. Do not use contstance as the affiliate module is not fully loaded
 * Must be placed here in hooks to ensure its loaded for tenant in bridge.
 */
// Load language
hooks()->add_action('modules_loaded', function () {
    if (function_exists('perfex_saas_is_tenant')) {
        register_language_files('affiliate_management', ['affiliate_management']);
    }
});
// Add affiliate to SaaS client menu in tenant bridge
hooks()->add_filter('perfex_saas_tenant_account_menu', function ($child_menu_items) {
    $child_menu_items[] = [
        'slug'     => 'affiliates',
        'name'     => _l('affiliate_management_client_menu'),
        'href'     => admin_url('billing/my_account?redirect=clients/affiliate_management/profile'),
        'position' => 10,
        'badge'    => [],
    ];
    return $child_menu_items;
});
