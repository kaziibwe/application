<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_103 extends App_module_migration
{
    public function up()
    {
      $CI = &get_instance();

      if(!$CI->db->field_exists('type', db_prefix() . "wiki_articles")){
        $CI->db->query("
          ALTER TABLE " . db_prefix() . "wiki_articles" . "
            ADD type VARCHAR(191) DEFAULT 'document';
        ");
      }

      if(!$CI->db->field_exists('mindmap_content', db_prefix() . "wiki_articles")){
        $CI->db->query("
          ALTER TABLE " . db_prefix() . "wiki_articles" . "
            ADD mindmap_content TEXT DEFAULT NULL;
        ");
      }

      if(!$CI->db->field_exists('mindmap_thumb', db_prefix() . "wiki_articles")){
        $CI->db->query("
          ALTER TABLE " . db_prefix() . "wiki_articles" . "
            ADD mindmap_thumb VARCHAR(191) DEFAULT NULL;
        ");
      }
    }

    public function down()
    {
      $CI = &get_instance();

      if($CI->db->field_exists('type', db_prefix() . "wiki_articles")){
        $CI->db->query("
          ALTER TABLE " . db_prefix() . "wiki_articles" . "
            DROP COLUMN type;
        ");
      }

      if($CI->db->field_exists('mindmap_content', db_prefix() . "wiki_articles")){
        $CI->db->query("
          ALTER TABLE " . db_prefix() . "wiki_articles" . "
            DROP COLUMN mindmap_content;
        ");
      }

      if($CI->db->field_exists('mindmap_thumb', db_prefix() . "wiki_articles")){
        $CI->db->query("
          ALTER TABLE " . db_prefix() . "wiki_articles" . "
            DROP COLUMN mindmap_thumb;
        ");
      }
    }
}