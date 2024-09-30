<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'flexibleworkflows')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'flexibleworkflows` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rule_id` varchar(100) NOT NULL,
  `order` int(10) NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `rule_name` varchar(100) NOT NULL,
  `rule_value` TEXT NOT NULL,
  `date_created` datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'flexibleworkflows`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'flexibleworkflows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

//create media storage
flexiblewa_create_storage_directory();

flexiblewa_create_automator_bot();