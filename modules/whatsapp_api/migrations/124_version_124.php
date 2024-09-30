<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_124 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if (table_exists('whatsapp_templates_mapping')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "whatsapp_templates_mapping` 
                CHANGE `header_params` `header_params` TEXT NOT NULL, 
                CHANGE `body_params` `body_params` TEXT NOT NULL,
                CHANGE `footer_params` `footer_params` TEXT NOT NULL"
            );
        }
    }
}