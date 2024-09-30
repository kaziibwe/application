<?php
defined('BASEPATH') or exit('No direct script access allowed');
if(!$CI->db->table_exists(db_prefix() . 'si_custom_status')) {	
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_custom_status` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL DEFAULT '',
	`order` int(11) NOT NULL DEFAULT '0',
	`color` varchar(7) NOT NULL DEFAULT '#757575',
	`filter_default` int(11) NOT NULL DEFAULT '0',
	`relto` varchar(20) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
	$CI->db->query('ALTER TABLE `' . db_prefix() . 'si_custom_status` AUTO_INCREMENT=200');
}

if(!$CI->db->table_exists(db_prefix() . 'si_custom_status_default')) {	
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_custom_status_default` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`status_id` int(11) NOT NULL DEFAULT '0',
	`name` varchar(50) NOT NULL DEFAULT '',
	`order` int(11) NOT NULL DEFAULT '0',
	`color` varchar(7) NOT NULL DEFAULT '#757575',
	`filter_default` int(11) NOT NULL DEFAULT '0',
	`active` int(11) NOT NULL DEFAULT '1',
	`relto` varchar(20) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");
	
	$CI->db->query('INSERT INTO '.db_prefix() . 'si_custom_status_default VALUES 
	(1,1,\'\',1,"#64748b",1,1,"tasks"),
	(2,2,\'\',4,"#84cc16",1,1,"tasks"),
	(3,3,\'\',3,"#0284c7",1,1,"tasks"),
	(4,4,\'\',2,"#3b82f6",1,1,"tasks"),
	(5,5,\'\',100,"#22c55e",0,1,"tasks"),
	(6,1,\'\',1,"#475569",1,1,"projects"),
	(7,2,\'\',2,"#2563eb",1,1,"projects"),
	(8,3,\'\',3,"#f97316",1,1,"projects"),
	(9,4,\'\',100,"#16a34a",0,1,"projects"),
	(10,5,\'\',4,"#94a3b8",0,1,"projects")
	');
}
add_option(SI_CUSTOM_STATUS_MODULE_NAME.'_edit_default_status_tasks',0);
add_option(SI_CUSTOM_STATUS_MODULE_NAME.'_edit_default_status_projects',0);
