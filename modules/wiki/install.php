<?php

if (!$CI->db->table_exists(db_prefix() . 'wiki_books')) {
    $CI->db->query("
    CREATE TABLE " . db_prefix() . "wiki_books (
    id                    INT(11) NOT NULL,
    name                  VARCHAR(191) DEFAULT NULL,
    short_description     TEXT DEFAULT NULL,
    assign_type           VARCHAR(20) DEFAULT 'specific_staff',
    assign_ids            MEDIUMTEXT,
    author_id             INT(11) NOT NULL DEFAULT '0',
    updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
  ");
    
    $CI->db->query("
    ALTER TABLE " . db_prefix() . "wiki_books
    ADD PRIMARY KEY (id);
  ");
    
    $CI->db->query("
    ALTER TABLE " . db_prefix() . "wiki_books
    MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1
  ");
}

if (!$CI->db->table_exists(db_prefix() . 'wiki_articles')) {
    $CI->db->query("
    CREATE TABLE " . db_prefix() . "wiki_articles (
    id                    INT(11) NOT NULL,
    title                 VARCHAR(191) DEFAULT NULL,
    description           TEXT DEFAULT NULL,
    content               LONGTEXT DEFAULT NULL,
    is_bookmark           TINYINT(3) DEFAULT 0,
    view_counter          INT(11) NOT NULL DEFAULT 0,
    author_id             INT(11) DEFAULT NULL,
    book_id               INT(11) DEFAULT NULL,
    updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
  ");
    
    $CI->db->query("
    ALTER TABLE " . db_prefix() . "wiki_articles
    ADD PRIMARY KEY (id);
  ");
    
    $CI->db->query("
    ALTER TABLE " . db_prefix() . "wiki_articles
    MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1
  ");
}

// version 1.0.2
$tblwiki_articles = db_prefix() . 'wiki_articles';

if (!$CI->db->field_exists('is_publish', $tblwiki_articles)) {
    $CI->db->query("
        ALTER TABLE " . $tblwiki_articles . "
          ADD is_publish TINYINT(1) NOT NULL DEFAULT '0';
      ");
}

if (!$CI->db->field_exists('slug', $tblwiki_articles)) {
    $CI->db->query("
        ALTER TABLE " . $tblwiki_articles . "
          ADD slug VARCHAR(191) DEFAULT NULL;
      ");
    
    $CI->db->query("
        UPDATE " . $tblwiki_articles . " TBLArticles
        SET
          TBLArticles.slug = UUID()
        WHERE TBLArticles.slug IS NULL
      ");
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

// version 1.0.3
if(!$CI->db->field_exists('type', db_prefix() . "wiki_articles")){
  $CI->db->query("
    ALTER TABLE " . db_prefix() . "wiki_articles" . "
      ADD type VARCHAR(191) DEFAULT NULL;
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