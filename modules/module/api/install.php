<?php

defined('BASEPATH') or exit('No direct script access allowed');

$key = base64_encode(time());

if (!$CI->db->table_exists(db_prefix() . 'api')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "api` (
  `id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL DEFAULT '". $key ."',
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `activate_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'api`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
 
 $CI->db->query('INSERT INTO `' . db_prefix() . 'api` (
   `key`, `status`,`activate_at`
)VALUES ("'.$key.'","0",now())');
}
