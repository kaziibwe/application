<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix().'assets_group')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'assets_group` (
  `group_id` INT(11) NOT NULL AUTO_INCREMENT,
  `group_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`group_id`));');
}
if (!$CI->db->table_exists(db_prefix().'asset_unit')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'asset_unit` (
  `unit_id` INT(11) NOT NULL AUTO_INCREMENT,
  `unit_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`unit_id`));');
}
if (!$CI->db->table_exists(db_prefix().'asset_location')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'asset_location` (
  `location_id` INT(11) NOT NULL AUTO_INCREMENT,
  `location` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`location_id`));');
}
if (!$CI->db->table_exists(db_prefix().'assets')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'assets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `assets_code` VARCHAR(20) NOT NULL,
  `assets_name` VARCHAR(255) NOT NULL,
  `amount` INT(11) NOT NULL,
  `unit` INT(11) NOT NULL,
  `series` VARCHAR(200) NULL,
  `asset_group` INT(11) NULL,
  `asset_location` INT(11) NULL,
  `department` INT(11) NULL,
  `date_buy` DATE NOT NULL,
  `warranty_period` INT(11) NOT NULL,
  `unit_price` DECIMAL(15,2) NOT NULL,
  `depreciation` INT(11) NOT NULL,
  `supplier_name` VARCHAR(255) NULL,
  `supplier_address` VARCHAR(255) NULL,
  `supplier_phone` INT(11) NULL,
  `description` TEXT NULL,
  `status` INT(11) NOT NULL DEFAULT "1",
  `total_allocation` INT(11) NULL DEFAULT "0",
  `total_lost` INT(11) NULL DEFAULT "0",
  `total_liquidation` INT(11) NULL DEFAULT "0",
  `total_damages` INT(11) NULL DEFAULT "0",
  `total_warranty` INT(11) NULL DEFAULT "0",
  PRIMARY KEY (`id`));');
}
if (!$CI->db->table_exists(db_prefix().'assets_acction_1')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'assets_acction_1` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `acction_code` VARCHAR(100) NOT NULL,
  `assets`  INT(11) NOT NULL,
  `acction_from`  INT(11) NOT NULL,
  `acction_to`  INT(11) NOT NULL,
  `amount`  INT(11) NOT NULL,
  `time_acction`  DATETIME NOT NULL,
  `asset_location`  VARCHAR(255) NULL,
  `acction_location`  VARCHAR(255) NOT NULL,
  `acction_reason`  TEXT NULL,  
  `type`  VARCHAR(50) NOT NULL,  
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix().'assets_acction_2')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'assets_acction_2` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `acction_code` VARCHAR(100) NOT NULL,
  `assets`  INT(11) NOT NULL,
  `acction_from`  INT(11) NOT NULL,
  `amount`  INT(11) NOT NULL,
  `cost`  DECIMAL(15,0) NULL,
  `time_acction`  DATETIME NOT NULL,
  `description`  TEXT NULL,  
  `type`  VARCHAR(50) NOT NULL,  
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix().'inventory_history')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'inventory_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `assets`  INT(11) NOT NULL,
  `date_time` DATETIME NOT NULL,
  `acction` VARCHAR(50) NOT NULL,
  `inventory_begin`  INT(11) NULL,
  `inventory_end`  INT(11) NOT NULL,
  `cost` DECIMAL(15,0) NULL,  
  PRIMARY KEY (`id`));');
}

if (!$CI->db->field_exists('belongs_to', db_prefix().'assets')) {
    $CI->db->query('ALTER TABLE `'.db_prefix()."assets`
      ADD `belongs_to` TEXT NULL DEFAULT NULL AFTER `description`,
      ADD `visible_to_client` TINYINT(1) NOT NULL DEFAULT '0' AFTER `belongs_to`,
      ADD `asset_image` VARCHAR(200) NULL DEFAULT NULL AFTER `visible_to_client`;");
}
