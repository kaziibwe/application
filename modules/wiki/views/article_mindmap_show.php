<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="en" />
    <title><?php echo $title; ?></title>
    <?php $favicon = get_option('favicon'); ?>
    <link rel="icon" href="<?php echo base_url('uploads/company/'.$favicon); ?>" type="image/png">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,900" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(WIKI_ASSETS_PATH . '/builder/ui/theme/default/css/default.all.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(WIKI_ASSETS_PATH . '/builder/ui/theme/default/css/customize.css'); ?>">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(WIKI_ASSETS_PATH . '/css/mindmap.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(WIKI_ASSETS_PATH . '/css/mindmap-show.css'); ?>">
</head>
<body>
    <div id="content-wrapper">
        <div id="panel"></div>
        <div id="kityminder" onselectstart="return false">
        </div>
    </div>

     <div class="navbar-bottom-builder">
        <a id="wiki_btn_back" href="<?php echo admin_url('wiki/articles'); ?>">
            <i class="fa fa-arrow-left"></i>
            <span class=""><?php echo _l('articles'); ?></span>
        </a>
        <a href="<?php echo admin_url('wiki/books'); ?>">
            <i class="fa fa-arrow-left"></i>
            <span class=""><?php echo _l('books'); ?></span>
        </a>
    </div>
    <script>
        const IMAGE_BUILDER_URL = "<?php echo base_url(WIKI_ASSETS_PATH . '/builder/ui/theme/default/images/'); ?>";
        const MINDMAP_CONTENT = `<?php echo $article['mindmap_content']; ?>`;
    </script>
    <script src="<?php echo base_url(WIKI_ASSETS_PATH . '/builder/builder.js'); ?>"></script>
    <script src="<?php echo base_url(WIKI_ASSETS_PATH . '/builder/lang/en-us/en-us.js'); ?>"></script>
    <script src="<?php echo base_url(WIKI_ASSETS_PATH . '/js/article_mindmap_show.js'); ?>"></script>
</body>
</html>