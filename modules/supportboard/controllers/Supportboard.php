<?php

/*
 * ==========================================================
 * PERFEX CRM APP MODULE FOR SUPPORT BOARD
 * ==========================================================
 *
 * Perfex CRM App Module. © 2021 board.support. All rights reserved.
 *
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Supportboard extends AdminController {

    public function index() {
        $this->load->view('form');
    }

    public function area() {
        $SB_PERFEX_PATH = get_option('sb_perfex_path');
        if (empty($SB_PERFEX_PATH)) {
            die('Please insert the Support Board Path in Perfex > Setup > Support Board.');
        }
        if (!file_exists($SB_PERFEX_PATH . '/include/functions.php')) {
            $path = dirname(__DIR__);
            $text = 'Your Support Board Path is not correct. Please insert the correct path it in Perfex > Setup > Support Board > Support Board Path. Get it from Support Board > Settings > Miscellaneous > Support Board Path.';
            if (strpos($path, 'perfex')) {
                $path = substr($path, 0, strpos($path, 'perfex'));
                $text .= ' Example: ' . $path . 'supportboard';
            }
            die($text);
        }
        require_once($SB_PERFEX_PATH . '/include/functions.php');
        require_once($SB_PERFEX_PATH . '/include/components.php');
        $this->app_scripts->add('sb-main', SB_URL . '/js/main.js?v=' . SB_VERSION);
        $this->app_scripts->add('sb-admin', SB_URL . '/js/admin.js?v=' . SB_VERSION);
        $this->app_css->add('sb-css', SB_URL . '/css/admin.css?v=' . SB_VERSION);
        $this->app_css->add('sb-perfex', SB_URL . '/apps/perfex/admin.css?v=' . SB_VERSION);
        if (sb_set_external_active_admin(sb_perfex_get_user(sb_perfex_get_session_user('staff'))) === 'logout') {
             echo '<script src="' . SB_URL . '/js/min/jquery.min.js"></script><script>document.cookie = "sb-login=false;expires=Thu, 01 Jan 1970 00:00:01 GMT" + ";path=/;";</script><style>body > #header { position: static !important }</style>';
        }
        sb_js_global();
        sb_js_admin();
        $this->load->view('sb');
    }

    public function save() {
        $url = str_replace('/admin.php', '', $_POST['sb_url']);
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            if (substr($url, -1) == '/') {
                $url = substr($url, 0, -1);
            }
            update_option('sb_url', $url);
        } else if (!empty($url)) {
            die('error');
        }
        update_option('sb_button', $_POST['sb_button']);
        update_option('sb_button_tickets', $_POST['sb_button_tickets']);
        update_option('sb_admin_type', $_POST['sb_admin_type']);
        update_option('sb_perfex_path', $_POST['sb_perfex_path']);
        update_option('sb_disable_chat', $_POST['sb_disable_chat']);
        die('success');
    }
}

?>