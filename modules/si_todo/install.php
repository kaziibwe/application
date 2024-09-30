<?php
defined('BASEPATH') or exit('No direct script access allowed');
if(!$CI->db->table_exists(db_prefix() . 'si_todos')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_todos` (
	`todoid` int(11) NOT NULL AUTO_INCREMENT,
	`category` int(11) NOT NULL,
	`description` text NOT NULL,
	`staffid` int(11) NOT NULL DEFAULT '0',
	`priority` int(11) NOT NULL DEFAULT '1',
	`dateadded` DATETIME NULL,
	`finished` tinyint(1) NOT NULL,
	`datefinished` DATETIME NULL,
	`item_order` int(11) NULL,
	PRIMARY KEY (`todoid`),
	KEY (`staffid`)
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if(!$CI->db->table_exists(db_prefix() . 'si_todos_category')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_todos_category` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`category_name` varchar(100) NOT NULL,
	`color` varchar(7) NOT NULL DEFAULT '#333',
	`staffid` int(11) NOT NULL DEFAULT '0',
	`dateadded` DATETIME NULL,
	`cat_order` int(11) NULL,
	PRIMARY KEY (`id`),
	KEY (`staffid`)
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if(!$CI->db->table_exists(db_prefix() . 'si_todos_settings')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_todos_settings` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`staffid` int(11) NOT NULL DEFAULT '0',
	`todos_load_limit` int(11) NOT NULL DEFAULT '20',
	`dashboard_finished_limit` int(11) NOT NULL DEFAULT '5',
	`dashboard_unfinished_limit` int(11) NOT NULL DEFAULT '5',
	`dateadded` DATETIME NULL,
	PRIMARY KEY (`id`),
	KEY (`staffid`)
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}



