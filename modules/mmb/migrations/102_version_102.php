<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
    public function up()
    {
        if (table_exists('contacts')) {
            if (!get_instance()->db->field_exists('client_message', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `client_message` tinyint(1) NOT NULL DEFAULT '1'");
            }
            if (!get_instance()->db->field_exists('invoice_message', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `invoice_message` tinyint(1) NOT NULL DEFAULT '1'");
            }
            if (!get_instance()->db->field_exists('tasks_message', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `tasks_message` tinyint(1) NOT NULL DEFAULT '1'");
            }
            if (!get_instance()->db->field_exists('projects_message', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `projects_message` tinyint(1) NOT NULL DEFAULT '1'");
            }
            if (!get_instance()->db->field_exists('proposals_message', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `proposals_message` tinyint(1) NOT NULL DEFAULT '1'");
            }
            if (!get_instance()->db->field_exists('payments_message', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `payments_message` tinyint(1) NOT NULL DEFAULT '1'");
            }
            if (!get_instance()->db->field_exists('ticket_message', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `ticket_message` tinyint(1) NOT NULL DEFAULT '1'");
            }

            if (!get_instance()->db->field_exists('client_forward_phone', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `client_forward_phone` varchar(100) NULL DEFAULT NULL");
            }
            if (!get_instance()->db->field_exists('invoice_forward_phone', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `invoice_forward_phone` varchar(100) NULL DEFAULT NULL");
            }
            if (!get_instance()->db->field_exists('tasks_forward_phone', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `tasks_forward_phone` varchar(100) NULL DEFAULT NULL");
            }
            if (!get_instance()->db->field_exists('projects_forward_phone', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `projects_forward_phone` varchar(100) NULL DEFAULT NULL");
            }
            if (!get_instance()->db->field_exists('proposals_forward_phone', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `proposals_forward_phone` varchar(100) NULL DEFAULT NULL");
            }
            if (!get_instance()->db->field_exists('payments_forward_phone', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `payments_forward_phone` varchar(100) NULL DEFAULT NULL");
            }
            if (!get_instance()->db->field_exists('ticket_forward_phone', db_prefix() . 'contacts')) {
                get_instance()->db->query("ALTER TABLE `" . db_prefix() . "contacts` ADD `ticket_forward_phone` varchar(100) NULL DEFAULT NULL");
            }
        }
    }
}