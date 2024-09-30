<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_125 extends App_module_migration
{
    public function up()
    {
        if (table_exists('staff')) {
            $CI = &get_instance();
            if (!$CI->db->field_exists('whatsapp_auth_enabled', db_prefix() . 'staff')) {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD `whatsapp_auth_enabled` TINYINT(1) NOT NULL DEFAULT "0"');
            }
            if (!$CI->db->field_exists('whatsapp_auth_code', db_prefix() . 'staff')) {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD `whatsapp_auth_code` VARCHAR(100) NULL DEFAULT NULL');
            }
            if (!$CI->db->field_exists('whatsapp_auth_code_requested', db_prefix() . 'staff')) {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD `whatsapp_auth_code_requested` DATETIME NULL DEFAULT NULL');
            }
        }
    }
}