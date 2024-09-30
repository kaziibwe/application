<?php

defined('BASEPATH') or exit('No direct script access allowed');

/** This file contain extension code for the Perfex SaaS module */

defined('WALLET_MODULE_NAME') or define('WALLET_MODULE_NAME', 'wallet');

if (perfex_saas_is_tenant()) {
    register_language_files(WALLET_MODULE_NAME, [WALLET_MODULE_NAME]);

    // Add special support for bridge client menu in Perfex SaaS module by Ulutfa
    hooks()->add_filter('perfex_saas_tenant_account_menu', function ($menu_items) {

        $menu_items[] = [
            'slug' => WALLET_MODULE_NAME,
            'name' =>  _l(WALLET_MODULE_NAME . '_client_menu'),
            'href' => admin_url('billing/my_account?redirect=' . WALLET_MODULE_NAME),
            'position' => 3,
        ];
        return $menu_items;
    });
}