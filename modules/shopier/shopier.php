<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Shopier Payment Gateway
Description: Shopier payment gateway module
Version: 1.0.0
Author: Noxfor
Author URI: https://noxfor.com
Requires at least: 2.3.*
*/

define('SHOPIER_MODULE_NAME', 'shopier');
define('SHOPIER_API_PAGE_URL', 'https://www.shopier.com/m/apiaccess.php');
define('SHOPIER_TCMB_PAGE_URL', 'https://www.tcmb.gov.tr/kurlar/today.xml');

require_once __DIR__ . '/vendor/autoload.php';

register_language_files(SHOPIER_MODULE_NAME, [SHOPIER_MODULE_NAME]);

register_payment_gateway('shopier_gateway', SHOPIER_MODULE_NAME);