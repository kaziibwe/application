<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_104 extends App_module_migration
{
    public function up()
    {
      $CI = &get_instance();
      if ($CI->db->table_exists(db_prefix() . 'wiki_articles')) {
        // Update type content to LONGTEXT
        $CI->db->query("
          ALTER TABLE " . db_prefix() . "wiki_articles" . "
          MODIFY content LONGTEXT DEFAULT NULL;
        ");
      }
    }

    public function down()
    {
      
    }
}