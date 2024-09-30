<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Define routes
$route['clients/affiliate_management/(:any)']    = 'affiliate_management/affiliate_management_client/$1';
$route['clients/affiliate_management/(:any)/(:any)']    = 'affiliate_management/affiliate_management_client/$1/$2';
$route['affiliate_management/join'] = 'affiliate_management/affiliate_management_client/join';
$route['affiliate_management/join/(:any)'] = 'affiliate_management/affiliate_management_client/join/$1';
$route['register/(:any)'] = 'affiliate_management/affiliate_management_client/register/$1';

/**
 * Early time hooks for email template.
 * Must be placed here in hooks to ensure its loaded with perfex email template loading.
 */
hooks()->add_filter('register_merge_fields', function ($fields) {
    $fields[] =  'affiliate_management/merge_fields/affiliate_management_merge_fields';
    return $fields;
});

// Add support for saas module by ulutfa
require_once(__DIR__ . '/my_saas.php');
