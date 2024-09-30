<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Affiliate Management
Description: A simple affiliate module.
Version: 1.0.6
Requires at least: 3.0.*
Author: ulutfa
Author URI: https://codecanyon.net/user/ulutfa
*/

defined('AFFILIATE_MANAGEMENT_MODULE_NAME') or define('AFFILIATE_MANAGEMENT_MODULE_NAME', 'affiliate_management');

$CI = &get_instance();

/**
 * Load the models
 */
$CI->load->model(AFFILIATE_MANAGEMENT_MODULE_NAME . '/' . AFFILIATE_MANAGEMENT_MODULE_NAME . '_model');

/**
 * Load the helpers
 */
$CI->load->helper(AFFILIATE_MANAGEMENT_MODULE_NAME . '/' . AFFILIATE_MANAGEMENT_MODULE_NAME);
$CI->load->helper(AFFILIATE_MANAGEMENT_MODULE_NAME . '/' . AFFILIATE_MANAGEMENT_MODULE_NAME . '_setup');
$CI->load->helper(AFFILIATE_MANAGEMENT_MODULE_NAME . '/' . AFFILIATE_MANAGEMENT_MODULE_NAME . '_php8_polyfill');


/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(AFFILIATE_MANAGEMENT_MODULE_NAME, [AFFILIATE_MANAGEMENT_MODULE_NAME]);

/**
 * Register activation module hook
 */
register_activation_hook(AFFILIATE_MANAGEMENT_MODULE_NAME, 'affiliate_management_module_activation_hook');
function affiliate_management_module_activation_hook()
{
    affiliate_management_install();
}

/**
 * Dactivation module hook
 */
register_deactivation_hook(AFFILIATE_MANAGEMENT_MODULE_NAME, 'affiliate_management_module_deactivation_hook');
function affiliate_management_module_deactivation_hook()
{
    affiliate_management_uninstall();
}

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
hooks()->add_action('admin_init', AFFILIATE_MANAGEMENT_MODULE_NAME . '_module_init_menu_items');
function affiliate_management_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item(AFFILIATE_MANAGEMENT_MODULE_NAME, [
            'name' =>  _l('affiliate_management_menu_title'),
            'icon' => 'fa fa-users',
            'href' => admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/admin'),
            'position' => 10
        ]);


        $CI->app_menu->add_sidebar_children_item(AFFILIATE_MANAGEMENT_MODULE_NAME, [
            'slug'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_dashboard',
            'name'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_dashboard',
            'href'     => admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/index'),
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item(AFFILIATE_MANAGEMENT_MODULE_NAME, [
            'slug'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_affiliates',
            'name'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_affiliates',
            'href'     => admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/affiliates'),
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item(AFFILIATE_MANAGEMENT_MODULE_NAME, [
            'slug'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_affiliate_groups',
            'name'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_affiliate_groups',
            'href'     => admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/groups'),
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item(AFFILIATE_MANAGEMENT_MODULE_NAME, [
            'slug'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_referrals',
            'name'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_referrals_side_menu',
            'href'     => admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/referrals'),
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item(AFFILIATE_MANAGEMENT_MODULE_NAME, [
            'slug'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_commissions',
            'name'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_commissions_side_menu',
            'href'     => admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/commissions'),
            'position' => 6,
        ]);

        $CI->app_menu->add_sidebar_children_item(AFFILIATE_MANAGEMENT_MODULE_NAME, [
            'slug'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_payouts',
            'name'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_payouts',
            'href'     => admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/payouts'),
            'position' => 7,
        ]);

        // Settings
        $CI->app_menu->add_sidebar_children_item(AFFILIATE_MANAGEMENT_MODULE_NAME, [
            'slug'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_settings',
            'name'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '_settings',
            'href'     => admin_url('settings?group=' . AFFILIATE_MANAGEMENT_MODULE_NAME),
            'position' => 8,
        ]);

        $CI->app_tabs->add_settings_tab(AFFILIATE_MANAGEMENT_MODULE_NAME, [
            'name'     => _l('settings_group_' . AFFILIATE_MANAGEMENT_MODULE_NAME),
            'view'     => AFFILIATE_MANAGEMENT_MODULE_NAME . '/admin/settings',
            'position' => 10,
            'icon'     => 'fa fa-user-plus',
        ]);
    }
}

hooks()->add_filter('before_settings_updated', 'affiliate_management_before_settings_updated_hook');
function affiliate_management_before_settings_updated_hook($data)
{
    // Ensure the commission rule and type align
    if (isset($data['settings']['affiliate_management_commission_rule'])) {
        $commission_rule = $data['settings']['affiliate_management_commission_rule'];
        $commission_type = $data['settings']['affiliate_management_commission_type'];
        if (
            $commission_rule === AffiliateManagementHelper::COMMISION_RULE_NO_PAYMENT &&
            $commission_type !== AffiliateManagementHelper::COMMISION_TYPE_FIXED
        ) {
            set_alert('danger', _l('affiliate_management_mismatched_commission_type_rule'));
            redirect(admin_url('settings?group=' . AFFILIATE_MANAGEMENT_MODULE_NAME));
        }
    }

    return $data;
}


/** Client menu */
hooks()->add_action('clients_init', 'affiliate_management_clients_area_menu_items');
function affiliate_management_clients_area_menu_items()
{

    add_theme_menu_item('affiliate', [
        'name' =>  _l(AFFILIATE_MANAGEMENT_MODULE_NAME . '_client_menu'),
        'href' => is_client_logged_in() ? base_url('clients/' . AFFILIATE_MANAGEMENT_MODULE_NAME . '/profile') : base_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/join'),
        'position' => 10,
    ]);
}


/**
 * Handle permissions
 */
hooks()->add_action('admin_init', AFFILIATE_MANAGEMENT_MODULE_NAME . '_permissions');
function affiliate_management_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities(AFFILIATE_MANAGEMENT_MODULE_NAME, $capabilities, _l(AFFILIATE_MANAGEMENT_MODULE_NAME));
}

// Detect referral slug in http and set as cookie
if (!empty($_GET[AffiliateManagementHelper::URL_IDENTIFIER])) {

    $affiliate_slug = $_GET[AffiliateManagementHelper::URL_IDENTIFIER];
    AffiliateManagementHelper::set_referral_cookie($affiliate_slug);
}

/** Referral and commission hooks */
hooks()->add_action('after_payment_added', 'AffiliateManagementHelper::after_payment_added_hook');
hooks()->add_action('after_payment_deleted', 'AffiliateManagementHelper::after_payment_deleted_hook');
hooks()->add_action('after_client_register', 'AffiliateManagementHelper::after_client_register_hook');
hooks()->add_action('contact_email_verified', 'AffiliateManagementHelper::after_client_contact_verification_hook');
hooks()->add_action('contact_email_verified_but_requires_admin_confirmation', 'AffiliateManagementHelper::after_client_contact_verification_hook');

/** PAYMENT GATEWAY */

//register the module as a payment gateway
register_payment_gateway(AFFILIATE_MANAGEMENT_MODULE_NAME . '_gateway', AFFILIATE_MANAGEMENT_MODULE_NAME);

/** Detect update and reinstall */
hooks()->add_action('database_updated', function ($updateToVersion) {
    affiliate_management_install();
});

// Track lead submission
hooks()->add_action('lead_created', function ($data) {
    if (is_array($data) && !empty($data['web_to_lead_form'])) {
        $lead_id = $data['lead_id'] ?? 0;
        if ($lead_id) {
            $CI = &get_instance();
            $lead = $CI->leads_model->get($lead_id);
            $field = 'email';
            if (empty($lead->email) && !empty($lead->phonenumber))
                $field = 'phonenumber';

            $lead_tracking_value = $lead->{$field};
            if (!empty($lead_tracking_value)) {
                // Track lead for convertion to customer is has a referral
                $affiliate_referral_slug = AffiliateManagementHelper::get_referral_cookie();
                if (!empty($affiliate_referral_slug)) {
                    $tracking_data = [
                        'rel_type' => 'lead',
                        'rel_id' => $lead_id,
                        'affiliate_slug' => $affiliate_referral_slug,
                    ];
                    $tracking_data[$field] = $lead_tracking_value;
                    $CI->affiliate_management_model->add_referral_tracking($tracking_data);
                }
            }
        }
    }
});

// Assign affiliate - referal when the lead is converted to customer.
hooks()->add_action('lead_converted_to_customer', function ($data) {
    $lead_id = $data['lead_id'] ?? '';
    $client_id = $data['customer_id'] ?? '';
    if (!empty($lead_id) && !empty($client_id)) {
        $CI = &get_instance();
        $affiliate_referral_slug  = $CI->affiliate_management_model->find_affilate_slug_from_trackings(['rel_type' => 'lead', 'rel_id' => $lead_id]);
        if (!empty($affiliate_referral_slug))
            AffiliateManagementHelper::referral_signup($client_id, $affiliate_referral_slug);
    }
});
