<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
    public function up()
    {
      $CI = &get_instance();

      $tblwiki_articles = db_prefix() . 'wiki_articles';

      if(!$CI->db->field_exists('is_publish', $tblwiki_articles)){
        $CI->db->query("
          ALTER TABLE " . $tblwiki_articles . "
            ADD is_publish TINYINT(1) NOT NULL DEFAULT '0';
        ");
      }

      if(!$CI->db->field_exists('slug', $tblwiki_articles)){
        $CI->db->query("
          ALTER TABLE " . $tblwiki_articles . "
            ADD slug VARCHAR(191) DEFAULT NULL;
        ");
        $this->seed_article_slug();
      }

      $tblwiki_staff_article = db_prefix() . 'wiki_staff_article';

      if (!$CI->db->table_exists($tblwiki_staff_article)) {
        $CI->db->query("
          CREATE TABLE " . $tblwiki_staff_article . " (
            id                    INT(11) NOT NULL,
            staff_id              INT(11) NOT NULL DEFAULT '0',
            article_id            INT(11) NOT NULL DEFAULT '0',
            updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
          ");
      
          $CI->db->query("
            ALTER TABLE " . $tblwiki_staff_article . "
            ADD PRIMARY KEY (id);
          ");
        
          $CI->db->query("
            ALTER TABLE " . $tblwiki_staff_article . "
              MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1
          ");
      }
      
    }

    public function down()
    {
      $CI = &get_instance();
      $tblwiki_article = db_prefix() . 'wiki_articles';

      if($CI->db->field_exists('is_publish', $tblwiki_article)){
        $CI->db->query("
          ALTER TABLE " . $tblwiki_article . "
            DROP COLUMN is_publish;
        ");
      }

      if($CI->db->field_exists('slug', $tblwiki_article)){
        $CI->db->query("
          ALTER TABLE " . $tblwiki_article . "
            DROP COLUMN slug;
        ");
      }

      $tblwiki_staff_article = db_prefix() . 'wiki_staff_article';

      if ($CI->db->table_exists($tblwiki_staff_article)) {
        $CI->db->query("
          DROP TABLE " . $tblwiki_staff_article . ";
        ");
      }

    }

    protected function seed_article_slug()
    {
      $CI = &get_instance();
      $tblwiki_articles = db_prefix() . 'wiki_articles';

      $CI->db->query("
        UPDATE " . $tblwiki_articles . " TBLArticles
        SET
          TBLArticles.slug = UUID()
        WHERE TBLArticles.slug IS NULL
      ");
    }
}