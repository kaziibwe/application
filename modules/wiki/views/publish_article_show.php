<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <?php $favicon = get_option('favicon'); ?>
    <link rel="icon" href="<?php echo base_url('uploads/company/'.$favicon); ?>" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="<?php echo base_url(WIKI_ASSETS_PATH.'/articles_show/styles/jquery.tocify.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/plugins/tinymce/plugins/codesample/css/prism.css'); ?>">
    <link href="<?php echo base_url(WIKI_ASSETS_PATH.'/css/publish_article_show.css'); ?>" rel="stylesheet">
</head>

<style>

</style>
</head>

<body>

    <div id="mySidebar" class="sidebar">
        <div class="header">
           <div id="logo">
              <?php get_company_logo(get_admin_uri().'/') ?>
           </div>
           
        </div>
        <div id="toc">
        </div>
    </div>

    <div id="main">
        <div id="header" style="">
            <div class="header-left">
              <a href="javascript:void(0)" id="closebtn" class="closebtn">
                  <svg xmlns="http://www.w3.org/2000/svg" width="30" height="20" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                  </svg>
              </a>
              <span class="openbtn" id="openbtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
              </svg>
              </span>
              <span class=""> <?php echo $article['title']; ?></span>
            </div>
          
            
        </div>
        <div class="content-main">
            <?php 
                if($article['type'] == 'document'){
                    echo $article['content'];
                }else if($article['type'] == 'mindmap'){
                    echo '<img class="wiki-mindmap-thumb-content" src="' . wiki_get_mindmap_thumb($article['mindmap_thumb']) . '" />';
                };
            ?>
        </div>

    </div>

    <script>

    </script>

   
    <script src=" <?php echo base_url(WIKI_ASSETS_PATH.'/articles_show/javascripts/jquery/jquery-1.8.3.min.js'); ?>"></script>
    <script src=" <?php echo base_url(WIKI_ASSETS_PATH.'/articles_show/javascripts/jqueryui/jquery-ui-1.9.1.custom.min.js'); ?>"></script>
    <script src=" <?php echo base_url(WIKI_ASSETS_PATH.'/articles_show/javascripts/jquery.tocify.min.js'); ?>"></script>
    <script src="<?php echo base_url(WIKI_ASSETS_PATH.'/js/article_show.js'); ?>"></script>

</body>

</html>