<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'flexibleleadfinder')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "flexibleleadfinder` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `keyword` VARCHAR(255) NOT NULL,
        `location` VARCHAR(255) NOT NULL,
        `date_added` DATETIME NOT NULL,
        `date_updated` DATETIME NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'flexibleleadfindercontacts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "flexibleleadfindercontacts` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `leadfinder_id` INT(11) NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(255) NOT NULL,
        `website` VARCHAR(255) NOT NULL,
        `phonenumber` VARCHAR(255) NOT NULL,
        `address` VARCHAR(255) NOT NULL,
        `city` VARCHAR(255) NOT NULL,
        `state` VARCHAR(255) NOT NULL,
        `country` VARCHAR(255) NOT NULL,
        `postal_code` VARCHAR(255) NOT NULL,
        `synced` tinyint(1) NOT NULL DEFAULT 0,
        `date_added` DATETIME NOT NULL,
        `date_updated` DATETIME NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

//create a source that will be used to identify leads that are created from flexible leadfinder
if (!option_exists(FLEXIBLELEADFINDER_LEAD_SOURCE_SETTING)) {
    $data = [
      'name' => FLEXIBLELEADFINDER_LEAD_SOURCE_DEFAULT_NAME,
    ];
    $CI->load->model('leads_model');
    $id = $CI->leads_model->add_source($data);
    add_option(FLEXIBLELEADFINDER_LEAD_SOURCE_SETTING, $id);
  }
  //create a status that will be used to identify leads that are created from flexible leadfinder
  if (!option_exists(FLEXIBLELEADFINDER_LEAD_STATUS_SETTING)) {
    $data = [
      'name' => FLEXIBLELEADFINDER_LEAD_STATUS_DEFAULT_NAME,
    ];
    $CI->load->model('leads_model');
    $id = $CI->leads_model->add_status($data);
    add_option(FLEXIBLELEADFINDER_LEAD_STATUS_SETTING, $id);
  }