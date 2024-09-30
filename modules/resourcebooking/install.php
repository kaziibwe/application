<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!is_dir(RESOURCEBOOKING_MODULE_UPLOAD_FOLDER)) {
  mkdir(RESOURCEBOOKING_MODULE_UPLOAD_FOLDER, 0755);
  $fp = fopen(RESOURCEBOOKING_MODULE_UPLOAD_FOLDER . '/index.html', 'w');
  fclose($fp);
}

if (!$CI->db->table_exists(db_prefix() . 'resource_group')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'resource_group` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `group_name` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `creator` INT(11) NOT NULL,
  `date_create` DATE NOT NULL,
  PRIMARY KEY (`id`));');
}
if (!$CI->db->table_exists(db_prefix() . 'resource')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'resource` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `resource_name` VARCHAR(100) NOT NULL,
  `resource_group` INT(11) NOT NULL,
  `approved` INT(11) NOT NULL,
  `manager` INT(11) NULL,
  `color` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `status` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`));');
}
if (!$CI->db->table_exists(db_prefix() . 'booking')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'booking` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `purpose` VARCHAR(255) NOT NULL,
  `orderer` INT(11) NOT NULL,
  `resource_group` INT(11) NOT NULL,
  `resource` INT(11) NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  `status` INT(11) NOT NULL DEFAULT "1",
  `description` TEXT NULL,
  PRIMARY KEY (`id`));');
}
if (!$CI->db->table_exists(db_prefix() . 'booking_follower')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'booking_follower` (
  `follower_id` INT(11) NOT NULL AUTO_INCREMENT,
  `booking` INT(11) NOT NULL,
  `follower` INT(11) NOT NULL,
  PRIMARY KEY (`follower_id`));');
}

if (!$CI->db->field_exists('type', 'task_comments')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'task_comments` 
  ADD COLUMN `type` VARCHAR(50) NULL DEFAULT "task" AFTER `dateadded`;');            
}