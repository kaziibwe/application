<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_110 extends App_module_migration
{
    public function up()
    {
        if (table_exists('wtc_bot')) {
            if (get_instance()->db->field_exists('trigger', db_prefix() . 'wtc_bot')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "wtc_bot` CHANGE `trigger` `trigger` TEXT ;");
            }
        }
        if (table_exists('wtc_campaigns')) {
            if (get_instance()->db->field_exists('trigger', db_prefix() . 'wtc_campaigns')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "wtc_campaigns` CHANGE `trigger` `trigger` TEXT ;");
            }
        }
        if (table_exists('wtc_interactions')) {
            if (!get_instance()->db->field_exists('agent', db_prefix() . 'wtc_interactions')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "wtc_interactions` ADD `agent` TEXT NULL ;");
            }
        }
    }

    public function down()
    {
        // Write your code here
    }
}
