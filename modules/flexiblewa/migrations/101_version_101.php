<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_101 extends App_module_migration
{
    public function up()
    {
        // Perform database upgrade here
        $CI = &get_instance();
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'flexibleworkflows` MODIFY `rule_value` TEXT;');
    }
}