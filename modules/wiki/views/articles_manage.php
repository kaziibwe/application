<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(WIKI_ASSETS_PATH.'/css/wiki_styles.css'); ?>">
<div id="wrapper">
    <div class="content">
        <div class="wiki-buttons-wrap">
            <div>
                <?php if(has_permission('wiki_articles','','create')){ ?>
                    <a href="<?php echo admin_url('wiki/articles/article'); ?>" class="btn btn-primary pull-left display-block"><?php echo _l('wiki_new_article'); ?></a>
                <?php } ?>
            </div>
            <div>
                <?php 
                    $tmpActiveItem = '';
                    if(isset($filter_is_owner) && $filter_is_owner == 1){
                        $tmpActiveItem = 'OWNER';
                    }else if(isset($filter_is_bookmark) && $filter_is_bookmark == 1){
                        $tmpActiveItem = 'BOOKMARK';
                    }else{
                        $tmpActiveItem = 'ALL';
                    }
                ?>
                <ul class="wiki-nav">
                    <li class="wiki-nav-item <?php echo $tmpActiveItem == 'ALL' ? 'active' : '' ?>"><a href="<?php echo admin_url('wiki/articles'); ?>" class="wiki-nav-link"><?php echo _l('wiki_all_articles') ?></a></li>
                    <li class="wiki-nav-item <?php echo $tmpActiveItem == 'OWNER' ? 'active' : '' ?>"><a href="<?php echo admin_url('wiki/articles'); ?>?filter_is_owner=1" class="wiki-nav-link"><?php echo _l('posted_by_me') ?></a></li>
                    <li class="wiki-nav-item <?php echo $tmpActiveItem == 'BOOKMARK' ? 'active' : '' ?>"><a href="<?php echo admin_url('wiki/articles'); ?>?filter_is_bookmark=1" class="wiki-nav-link"><?php echo _l('wiki_bookmark') ?></a></li>
                </ul>
            </div>
            <div class="wiki-search-wrap">
                <?php
                    $selected = array();
                    if (isset($filter_book_id)) {
                        $selected = $filter_book_id;
                    }
                ?>
                <?php echo render_select('filter_book_id', $books, array('id', array('name')), '', $selected, [], [], 'wiki-dropdown-wrapper'); ?>
                <div class="input-group">
                    <a href="javascript:void(0);" class="input-group-addon btn-search-list">
                        <span class="fa fa-search"></span>
                    </a>
                    <input type="search" class="form-control input-search-list" value="<?php echo $filter_query; ?>" placeholder="<?php echo _l('wiki_search'); ?>...">
                </div>
            </div>
        </div>
        <?php echo form_open($this->uri->uri_string(), array('id'=>'form_search', 'class' =>'hide', 'method'=>'get')); ?>
            <input type="hidden" name="filter_query" value="<?php echo $filter_query; ?>">
            <?php if(isset($filter_book_id)){ ?>
                <input type="hidden" name="filter_book_id" value="<?php echo $filter_book_id; ?>">
            <?php } ?>
            <?php if(isset($filter_is_owner)){ ?>
                <input type="hidden" name="filter_is_owner" value="<?php echo $filter_is_owner; ?>">
            <?php } ?>
        <?php echo form_close(); ?>
        <div class="clearfix"></div>
        <hr class="wiki-hr-header" />
        <div class="row">
        <?php if(count($articles) == 0){ ?>
            <div class="col-xs-12">
                <h3 class="text-center wiki-empty-results"><?php echo _l('wiki_empty_results'); ?></h3>
            </div>
        <?php } ?>
        <?php foreach($articles as $article){ ?>
            <div class="col-md-4">
                <div class="panel_s wiki-item-panel">
                    <?php 
                        $hasAction = true;
                    ?>
                    <div class="panel-body">
                        <div class="wiki-article-panel wiki-item-panel">
                            <div class="wiki-article-header">
                                 <div class="wiki-item-btns">
                                    <a href="<?php echo admin_url('wiki/articles/show/' . $article['id']) ?>" class=" btn btn-success btn-xs wiki-item-btn"><i class="fa fa-eye"></i></a>
                                    <?php if($hasAction && has_permission('wiki_articles','','edit')){ ?>
                                        <?php
                                            $article_back_url = current_url();
                                            if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != ''){
                                                $article_back_url .= '?' . $_SERVER['QUERY_STRING'];
                                            }
                                            $article_back_url = urlencode($article_back_url);
                                        ?>
                                        <a href="<?php echo admin_url('wiki/articles/article/' . $article['id']) . '?back_url=' . $article_back_url; ?>" class="btn btn-default btn-xs wiki-item-btn"><i class="fa fa-edit"></i></a>
                                    <?php } ?>
                                  
                                    <?php if($hasAction && has_permission('wiki_articles','','create')){ ?>
                                        <?php
                                            $article_back_url = current_url();
                                            if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != ''){
                                                $article_back_url .= '?' . $_SERVER['QUERY_STRING'];
                                            }
                                            $article_back_url = urlencode($article_back_url);
                                        ?>
                                        <a href="<?php echo admin_url('wiki/articles/article'); ?>?clone_id=<?php echo $article['id']; ?>&back_url=<?php echo $article_back_url; ?>" class="btn btn-default btn-xs wiki-item-btn" title="<?php echo _l('wiki_clone_article'); ?>"><i class="fa fa-copy"></i></a>
                                    <?php } ?>
                                      <a href="javascript:void(0);" class="btn btn-default btn-xs wiki-item-btn <?php echo isset($article['bookmark_id']) ? 'wiki-bookmark-on' : 'wiki-bookmark-off'  ?> wiki-btn-bookmark" title="Bookmark" data-id="<?php echo $article['id']; ?>"><i class="fa fa-bookmark"></i></a>
                                </div>
                                <div class="article-headbar text-right"><strong class="wiki-counter-value"><?php echo $article['view_counter']; ?></strong> Views</div>
                            </div>
                            
                           
                            <div class="wiki-item-title">
                                <div class="wiki-item-heading">
                                    <h4><a href="<?php echo admin_url('wiki/articles/show/' . $article['id']) ?>" target="_blank"><?php echo $article['title']; ?></a></h4>
                                </div>
                            </div>
                            <div class="wiki-item-description wiki-article-description">
                                <p><?php echo $article['description']; ?></p>
                            </div>
                            <div class="wiki-article-footer">
                                <div>
                                    <img class="wiki-author-thumbnail" src="<?php echo staff_profile_image_url($article['author_id']); ?>" />
                                </div>
                                <div class="wiki-author-detail">
                                    <div><span class="wiki-author-name"><strong><?php echo $article['author_fullname']; ?></strong></span> <?php echo _l('wiki_at'); ?> <?php echo date("Y-m-d", strtotime($article['created_at'])); ?></div>
                                    <div><?php echo _l('wiki_saved_in'); ?> <strong><?php echo $article['book_name']; ?></strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    const APP_CSRF_TOKEN = "<?php echo $this->security->get_csrf_hash(); ?>";
    const bookmark_switch_url = "<?php echo admin_url('wiki/articles/bookmark_switch'); ?>";
</script>
<script src="<?php echo base_url(WIKI_ASSETS_PATH.'/js/articles_manage.js'); ?>">
</script>
</body>
</html>

