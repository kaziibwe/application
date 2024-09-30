<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_110 extends App_module_migration
{
    public function up()
    {
        $CI         =&get_instance();
        $table_name = db_prefix().'assets';

        $get_table = $CI->db->get($table_name);

        if ($get_table) {
            if (!$CI->db->field_exists('belongs_to', $table_name)) {
                $CI->db->query('ALTER TABLE `'.db_prefix()."assets`
                  ADD `belongs_to` TEXT NULL DEFAULT NULL AFTER `description`,
                  ADD `visible_to_client` TINYINT(1) NOT NULL DEFAULT '0' AFTER `belongs_to`,
                  ADD `asset_image` VARCHAR(200) NULL DEFAULT NULL AFTER `visible_to_client`;");
                if ($results) {
                    return true;
                }

                return false;
            }
        }
    }
}
