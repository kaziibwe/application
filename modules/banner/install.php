<?php

defined('BASEPATH') || exit('No direct script access allowed');

get_instance()->config->load('banner' . '/config');

if (!get_instance()->db->table_exists(db_prefix().'banner')) {
    get_instance()->db->query('CREATE TABLE `'.db_prefix().'banner` (
	   	`id` int NOT NULL AUTO_INCREMENT,
		`title` varchar(100) NOT NULL,
		`detail` mediumtext NOT NULL,
		`status` tinyint(1) NOT NULL DEFAULT "0",
		`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`start_date` date NOT NULL,
		`end_date` date NOT NULL,
		`admin_area` tinyint(1) NOT NULL DEFAULT "0",
		`clients_area` tinyint(1) NOT NULL DEFAULT "0",
		`staff_ids` text,
		`client_ids` text,
		`has_action` tinyint(1) NOT NULL DEFAULT "0",
		`action_label` varchar(250) DEFAULT NULL,
		`label_color` varchar(250) DEFAULT NULL,
		`action_url` text,
		`action_target` tinyint(1) NOT NULL DEFAULT "0",
		PRIMARY KEY (`id`)
	) ENGINE = InnoDB DEFAULT CHARSET='.get_instance()->db->char_set.';');
}

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

$my_files_list = [
    VIEWPATH.'themes/perfex/views/my_home.php' => module_dir_path(BANNER_MODULE, '/resources/application/views/themes/perfex/views/my_home.php'),
];

// Copy each file in $my_files_list to its actual path if it doesn't already exist
foreach ($my_files_list as $actual_path => $resource_path) {
    if (!file_exists($actual_path)) {
        copy($resource_path, $actual_path);
    }
}
