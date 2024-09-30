<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
	public function __construct()
	{
		parent::__construct();
	}

	public function up()
	{
		if (get_instance()->db->table_exists(db_prefix() . 'banner')) {
			if (!get_instance()->db->field_exists('has_action', db_prefix() . 'banner')) {
				get_instance()->db->query('ALTER TABLE `' . db_prefix() . 'banner` ADD `has_action` tinyint(1) NOT NULL DEFAULT "0"');
			}
			if (!get_instance()->db->field_exists('action_label', db_prefix() . 'banner')) {
				get_instance()->db->query('ALTER TABLE `' . db_prefix() . 'banner` ADD `action_label` varchar(250) DEFAULT NULL');
			}
			if (!get_instance()->db->field_exists('action_url', db_prefix() . 'banner')) {
				get_instance()->db->query('ALTER TABLE `' . db_prefix() . 'banner` ADD `action_url` text');
			}
			if (!get_instance()->db->field_exists('label_color', db_prefix() . 'banner')) {
				get_instance()->db->query('ALTER TABLE `' . db_prefix() . 'banner` ADD `label_color` varchar(250) DEFAULT NULL');
			}
			if (!get_instance()->db->field_exists('action_target', db_prefix() . 'banner')) {
				get_instance()->db->query('ALTER TABLE `' . db_prefix() . 'banner` ADD `action_target` tinyint(1) NOT NULL DEFAULT "0"');
			}
		}
	}
}
