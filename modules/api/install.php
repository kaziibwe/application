<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI =& get_instance();

if (!$CI->db->table_exists(db_prefix() . 'user_api')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'user_api` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user` VARCHAR(50) NOT NULL,
        `name` VARCHAR(50) NOT NULL,
        `token` VARCHAR(255) NOT NULL,
        `expiration_date` DATETIME,
        `permission_enable` TINYINT(4) DEFAULT 0,
        PRIMARY KEY (`id`));
    ');
} else {
    if (!$CI->db->field_exists('permission_enable', db_prefix() . 'user_api')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'user_api`
            ADD `permission_enable` TINYINT(4) DEFAULT 0;
        ');
    }
}

if (!$CI->db->table_exists(db_prefix() . 'user_api_permissions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'user_api_permissions` (
        `api_id` INT(11) NOT NULL,
        `feature` VARCHAR(50) NOT NULL,
        `capability` VARCHAR(50) NOT NULL);
    ');
}

if ($CI->db->field_exists('password', db_prefix() . 'user_api')) {
    $CI->db->query('ALTER TABLE ' . db_prefix() . 'user_api DROP `password`');
}