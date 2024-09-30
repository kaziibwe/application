<?php

use app\services\utilities\Str;

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_310 extends CI_Migration
{
    public function up()
    {
        $this->db->query('alter table `' . db_prefix() . 'templates` modify `content` longtext null;');

        $incompatible_tables = [];
        
        $tables_path = VIEWPATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'tables';

        foreach(list_files($tables_path) as $path) {
            if(Str::startsWith($path,'my_')) {
                $incompatible_tables[basename($path)] = filemtime($tables_path.DIRECTORY_SEPARATOR.$path);
            }
        }

        update_option('v310_incompatible_tables', json_encode($incompatible_tables));

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `' . db_prefix() . 'filters` (
                `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                `builder` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
                `staff_id` int UNSIGNED NOT NULL,
                `identifier` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                `is_shared` tinyint UNSIGNED NOT NULL DEFAULT \'0\',
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
        );
        
        $this->db->query('CREATE TABLE IF NOT EXISTS `'.db_prefix().'filter_defaults` (
            `filter_id` int UNSIGNED NOT NULL,
            `staff_id` int NOT NULL,
            `identifier` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
            `view` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
            FOREIGN KEY (`filter_id`) REFERENCES `'.db_prefix().'filters`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`staff_id`) REFERENCES `'.db_prefix().'staff`(`staffid`) ON DELETE CASCADE
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
        );
    }
}
