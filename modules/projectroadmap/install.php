<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'list_widget')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'list_widget` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `add_from` INT(11) NOT NULL,
    `rel_id` INT(11) NULL,
    `rel_type` VARCHAR(45) NULL,
    `layout` VARCHAR(45) NULL,
    PRIMARY KEY (`id`));');
}