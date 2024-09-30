<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_122 extends App_module_migration
{
    public function up()
    {
        if (!get_option('admin_light_theme_customers')) {
            add_option('admin_light_theme_customers', 1);
        }
    }
}
