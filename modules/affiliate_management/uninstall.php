<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

/** OPTIONS */
$CI->db->where("`name` LIKE 'affiliate_management%'");
$CI->db->delete(db_prefix() . 'options');

/** TABLES */

$table = db_prefix() . 'affiliate_m_payouts';
if ($CI->db->table_exists($table)) {
    $CI->db->query("DROP TABLE $table");
}

$table = db_prefix() . 'affiliate_m_commissions';
if ($CI->db->table_exists($table)) {
    $CI->db->query("DROP TABLE $table");
}

$table = db_prefix() . 'affiliate_m_referrals';
if ($CI->db->table_exists($table)) {
    $CI->db->query("DROP TABLE $table");
}

$table = db_prefix() . 'affiliate_m_affiliates';
if ($CI->db->table_exists($table)) {
    $CI->db->query("DROP TABLE $table");
}

/*** EMAIL TEMPLATES */
$CI->db->where("`slug` LIKE 'affiliate_management%'");
$CI->db->delete(db_prefix() . 'emailtemplates');
