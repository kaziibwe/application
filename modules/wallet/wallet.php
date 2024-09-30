<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Wallet
Description: Wallet funding, gateway, recurring invoice, and monitor balances with the Wallet Module.
Version: 1.0.4
Requires at least: 3.0.*
Author: ulutfa
Author URI: https://codecanyon.net/user/ulutfa
*/

defined('WALLET_MODULE_NAME') or define('WALLET_MODULE_NAME', 'wallet');
defined('WALLET_MODULE_GATEWAY_NAME') or define('WALLET_MODULE_GATEWAY_NAME', 'wallet_gateway');

$CI = &get_instance();

/**
 * Load the models
 */
$CI->load->library(WALLET_MODULE_NAME . '/' . 'walletmanager', null, 'wallet');

/**
 * Load the helpers
 */
$CI->load->helper(WALLET_MODULE_NAME . '/' . WALLET_MODULE_NAME);

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(WALLET_MODULE_NAME, [WALLET_MODULE_NAME]);

/**
 * Register email templates merge fields
 */
register_merge_fields(WALLET_MODULE_NAME . '/merge_fields/' . WALLET_MODULE_NAME . '_merge_fields');

/**
 * Register activation module hook
 */
register_activation_hook(WALLET_MODULE_NAME, function () {
    wallet_activate_module();
});

/**
 * Register deactivation module hook
 */
register_deactivation_hook(WALLET_MODULE_NAME, function () {
    // Register as global Perfex SaaS module extension
    if (function_exists('perfex_saas_unregister_global_extension'))
        perfex_saas_unregister_global_extension(WALLET_MODULE_NAME);
});

/**
 * Handle permissions and admin menu
 */
hooks()->add_action('admin_init', function () {
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'transact'   => _l('wallet_permission_transact') . '(' . _l('permission_global') . ')',
    ];

    register_staff_capabilities(WALLET_MODULE_NAME, $capabilities, _l(WALLET_MODULE_NAME));

    $CI = &get_instance();
    if (has_permission(WALLET_MODULE_NAME, '', 'view')) {

        $badge = [];
        if ((int)get_option('wallet_allow_withdraw')) {
            $pending_withrawal = wallet_total_pending_withdrawal();
            if ($pending_withrawal > 0)
                $badge = [
                    'value' => wallet_total_pending_withdrawal(),
                    'color' => '',
                    'type'  => 'warning',
                ];
        }

        $CI->app_menu->add_sidebar_menu_item(WALLET_MODULE_NAME, [
            'name' => _l(WALLET_MODULE_NAME . '_staff_menu'),
            'icon' => 'fa fa-wallet',
            'position' => 5,
            'badge'    => $badge,
        ]);

        $CI->app_menu->add_sidebar_children_item(WALLET_MODULE_NAME, [
            'slug' => WALLET_MODULE_NAME . '_overview',
            'name' => _l('wallet_dashboard_menu'),
            'icon' => '',
            'href' => admin_url(WALLET_MODULE_NAME . '/wallet_admin'),
            'position' => 1,
        ]);

        $CI->app_menu->add_sidebar_children_item(WALLET_MODULE_NAME, [
            'slug' => WALLET_MODULE_NAME . '_settings',
            'name' => _l('settings'),
            'icon' => '',
            'href' => admin_url('settings?group=' . WALLET_MODULE_NAME),
            'position' => 2,
        ]);
    }

    // Add wallet settings
    $CI->app_tabs->add_settings_tab(WALLET_MODULE_NAME, [
        'name'     => _l(WALLET_MODULE_NAME),
        'view'     => 'wallet/admin/settings',
        'position' => 10,
        'icon'     => 'fa fa-wallet',
    ]);

    // Add wallet tab to client menu
    $CI->app_tabs->add_customer_profile_tab(WALLET_MODULE_NAME, [
        'name'     => _l(WALLET_MODULE_NAME),
        'icon'     => 'fa fa-wallet',
        'view'     => 'wallet/admin/client_primary_wallet',
        'visible'  => (has_permission(WALLET_MODULE_NAME, '', 'view') || has_permission(WALLET_MODULE_NAME, '', 'transact')),
        'position' => 10,
        'badge'    => [],
    ]);
});


/** Client menu */
hooks()->add_action('clients_init', function () {
    if (is_logged_in()) {
        add_theme_menu_item(WALLET_MODULE_NAME, [
            'name' =>  _l(WALLET_MODULE_NAME . '_client_menu'),
            'href' => base_url(WALLET_MODULE_NAME),
            'position' => 10,
        ]);
    }
});


// Bind wallet settings for neccessary control
hooks()->add_filter('before_settings_updated', function ($data) {
    $array_fields = ['wallet_allowed_payment_modes', 'wallet_taxname'];
    foreach ($array_fields as $key) {
        if (isset($data['settings'][$key]))
            $data['settings'][$key] = json_encode($data['settings'][$key]);
    }
    return $data;
});

// Register the module as a payment gateway
register_payment_gateway(WALLET_MODULE_GATEWAY_NAME, WALLET_MODULE_NAME);

// Register cron method to auto pay overdue invoice from wallet if enabled.
register_cron_task('wallet_process_invoices_autopayment');

// Hook to listen to payment removal and reverse debit if its wallet payment
hooks()->add_action('before_payment_deleted', 'wallet_log_reverse_payment');
hooks()->add_action('after_payment_deleted', 'wallet_reverse_payment');

// Hook to listen to new payment and return
hooks()->add_action('after_payment_added', 'wallet_process_payment');
hooks()->add_filter('before_payment_recorded', 'wallet_before_payment_recorded_filter');

/**
 * Hook to listen to invoice removal and reverse debit of payments. 
 * This is neccessary as Perfex does not emit hooks as it remove payments during invoice deletion.
 */
hooks()->add_action('before_invoice_deleted', function ($invoiceid) {
    $data = ['invoiceid' => $invoiceid];

    $payload = [];
    $CI = &get_instance();
    $CI->invoices_model->db->where('invoiceid', $invoiceid);
    $payments = $CI->invoices_model->db->get(db_prefix() . 'invoicepaymentrecords')->result();
    foreach ($payments as $payment) {

        $data['paymentid'] = $payment->id;
        if (wallet_log_reverse_payment($data))
            $payload[] = $data;
    }

    if (!empty($payload)) {

        $CI->session->set_userdata('wallet_before_invoice_deleted_' . $invoiceid, $payload);
    }
});
hooks()->add_action('after_invoice_deleted', function ($invoiceid) {
    $CI = &get_instance();
    $session_key = 'wallet_before_invoice_deleted_' . $invoiceid;
    $payloads = $CI->session->userdata($session_key);
    if (!empty($payloads)) {
        foreach ($payloads as $data) {
            if (isset($data['paymentid']) && isset($data['invoiceid']))
                wallet_reverse_payment($data);
        }
    }
    $CI->session->unset_userdata($session_key);
});
