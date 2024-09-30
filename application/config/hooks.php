<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|   http://codeigniter.com/user_guide/general/hooks.html
|
*/

if (! function_exists('e')) {
    /**
     * Encode HTML special characters in a string.
     *
     * @param bool  $doubleEncode
     * @param mixed $value
     *
     * @return string
     */
    function e($value, $doubleEncode = true)
    {
        if ($value instanceof BackedEnum) {
            $value = $value->value;
        }

        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}

/**
 * @since  2.3.0
 * Moved here from hooks_helper.php that was included in config.php because some users config.php file permissions are incorrect.
 * NEW Global hooks function
 * This function must be used for all hooks
 *
 * @return Hooks|object Hooks instance
 */
function hooks()
{
    global $hooks;

    return $hooks;
}

$hook['pre_system'][] = [
    'class'    => 'EnhanceSecurity',
    'function' => 'protect',
    'filename' => 'EnhanceSecurity.php',
    'filepath' => 'hooks',
    'params'   => [],
];

$hook['pre_system'][] = [
    'class'    => 'App_Autoloader',
    'function' => 'register',
    'filename' => 'App_Autoloader.php',
    'filepath' => 'hooks',
    'params'   => [],
];

$hook['pre_system'][] = [
    'class'    => 'InitModules',
    'function' => 'handle',
    'filename' => 'InitModules.php',
    'filepath' => 'hooks',
    'params'   => [],
];

$hook['pre_controller_constructor'][] = [
    'class'    => '',
    'function' => '_app_init',
    'filename' => 'InitHook.php',
    'filepath' => 'hooks',
];

$hook['post_controller'] = function () {
    $ci = get_instance();

    if (! $ci->input->is_ajax_request()) {
        $currentUrl = current_full_url();

        $skip = [
            'pusher_auth', // Prchat issue
            'download/preview_image',
            'download/preview_video',
            'download/file'
        ];

        $remember = true;

        foreach($skip as $haystack) {
            if(strpos($currentUrl, $haystack) !== false) {
                $remember = false;
                break;
            }
        }

        if ($remember) {
            get_instance()->session->set_userdata('_prev_url', $currentUrl);
        }
    }
};

if (file_exists(APPPATH . 'config/my_hooks.php')) {
    include_once APPPATH . 'config/my_hooks.php';
}
