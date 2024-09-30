<?php
defined('BASEPATH') or exit('No direct script access allowed');
$CI = & get_instance();
$CI->load->helper(BILLS_MODULE_NAME . '/bills_database');

if ($CI->db->table_exists(db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `'. db_prefix() .'invoices` add column bill varchar(20) default "0"');
}
