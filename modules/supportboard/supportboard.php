<?php

/*
 * ==========================================================
 * PERFEX CRM APP MODULE FOR SUPPORT BOARD
 * ==========================================================
 *
 * Perfex CRM App Module. © 2021 board.support. All rights reserved.
 *
 */

/*
 *
Module Name: Support Board
Description: Add-on for Support Board - https://board.support/
Version: 1.0.5
Requires at least: 2.3.*

 */

defined('BASEPATH') or exit('No direct script access allowed');
hooks()->add_action('app_customers_footer', 'sb_perfex_customer_scripts');
hooks()->add_action('app_admin_footer', 'sb_perfex_admin_scripts');
hooks()->add_action('admin_init', 'sb_perfex_menu');
hooks()->add_action('admin_init', 'sb_perfex_menu_support_board');
hooks()->add_action('clients_init', 'sb_perfex_menu_client');

function sb_perfex_menu_client(){
    $button = get_option('sb_button_tickets');
    if ($button != '' && is_client_logged_in()) {
        add_theme_menu_item('sb-client', [
            'name'     => _l($button),
            'href'     => site_url('supportboard/tickets'),
            'position' => 99,
        ]);
    }
}

function sb_perfex_menu() {
    $CI = &get_instance();
    $CI->app_menu->add_setup_menu_item('sb-admin', [
        'name'     => 'Support Board',
        'href'     => admin_url('supportboard'),
        'position' => 99
    ]);
}

function sb_perfex_menu_support_board() {
    $button = get_option('sb_button');
    if ($button != '') {
        $CI = &get_instance();
        $CI->app_menu->add_sidebar_menu_item('sb', [
            'name'     => _l($button),
            'href'     => get_option('sb_admin_type') == 'inside' ? admin_url('supportboard') . '/area' : get_option('sb_url') . '/admin.php',
            'position' => 80,
            'icon'     => 'fa fa-comment',
        ]);
    }
}

function sb_perfex_customer_scripts() {
    $user = sb_perfex_get_session_user();
    if ($user) {
        if (get_option('sb_disable_chat') != 'true') echo '<script src="' . get_option('sb_url') . '/js/main.js" id="sbinit"></script>';
        echo '<script>var SB_PERFEX_ACTIVE_USER = "' . $user . '";var SB_PERFEX_CONTACT_ID = "' . $_SESSION['contact_user_id'] . '";</script>';
    }
}

function sb_perfex_admin_scripts() {
    echo '<script src="' . APP_BASE_URL . '/modules/supportboard/assets/main.js" id="sb-admin-js"></script>';
}

function sb_perfex_get_session_user($type = 'client') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION[$type . '_user_id']) && !empty($_SESSION[$type . '_logged_in'])) {
        return $_SESSION[$type . '_user_id'];
    }
    return false;
}

?>