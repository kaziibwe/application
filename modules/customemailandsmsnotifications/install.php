<?php

defined('BASEPATH') or exit('No direct script access allowed');
add_option('customemailandsmsnotifications', 1);

add_option('aside_customemailandsmsnotifications_active', '[]');
add_option('setup_customemailandsmsnotifications_active', '[]');

// Moving necessary dependencies to the correct place for clean installs of v2.7.0+
$checkfolder = FCPATH . 'application/third_party/php-imap';
$srcloc = APP_MODULES_PATH . 'mailbox/third_party/php-imap'; 
$destloc = FCPATH . 'application/third_party/';

if(!is_dir($checkfolder)){
  mkdir($checkfolder);
  shell_exec("cp -r $srcloc $destloc");
}

$CI->db->query('SET foreign_key_checks = 0');

//create customer_sites_info table
if (!$CI->db->table_exists(db_prefix().'custom_templates')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'custom_templates` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `staff_id` INT NOT NULL,
    `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `template_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
     PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET='.$CI->db->char_set.';');
}

//create customer_sites_info table
if (!$CI->db->table_exists(db_prefix().'custom_email_sms')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'custom_email_sms` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `customer_or_leads` varchar(200) NOT NULL,
    `select_customer` varchar(255) NOT NULL,
    `template` varchar(255) NOT NULL,
    `subject` varchar(255) NOT NULL,
    `message` varchar(255) NOT NULL,
    `mail_or_sms` varchar(255) NOT NULL,
    `custom_date` date NOT NULL,
    `custom_time` varchar(100) NOT NULL,
    `file_mail` varchar(200) NOT NULL,
    `is_delivered` int DEFAULT NULL,
     PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET='.$CI->db->char_set.';');
}