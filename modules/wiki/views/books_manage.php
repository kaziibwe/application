<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(WIKI_ASSETS_PATH.'/css/wiki_styles.css'); ?>">
<div id="wrapper">
    <div class="content">
        <div class="wiki-buttons-wrap">
            <div>
                <?php if(has_permission('wiki_books','','create')){ ?>
                    <a href="<?php echo admin_url('wiki/books/book'); ?>" class="btn btn-primary pull-left display-block"><?php echo _l('wiki_new_book'); ?></a>
                <?php } ?>
            </div>
            <div class="input-group">
                <a href="javascript:void(0);" class="input-group-addon btn-search-list">
                    <span class="fa fa-search"></span>
                </a>
                <input type="search" class="form-control input-search-list" value="<?php echo $filter_query; ?>" placeholder="<?php echo _l('wiki_search'); ?>...">
            </div>
        </div>
        <?php echo form_open($this->uri->uri_string(), array('id'=>'form_search', 'class' =>'hide', 'method'=>'get')); ?>
            <input type="hidden" name="filter_query" value="<?php echo $filter_query; ?>">
        <?php echo form_close(); ?>
        <div class="clearfix"></div>
        <hr class="wiki-hr-header" />
        <div class="row">
        <?php if(count($books) == 0){ ?>
            <div class="col-xs-12">
                <h3 class="text-center wiki-empty-results"><?php echo _l('wiki_empty_results'); ?></h3>
            </div>
        <?php } ?>
        <?php foreach($books as $book){ ?>
            <div class="col-md-4">
                <div class="panel_s wiki-item-panel wiki-book-panel">
                    <?php 
                        $hasAction = true;
                    ?>
                    <div class="panel-body">
                        <div class="wiki-book-title">
                            <div class="wiki-book-header">
                                
                            </div>

                            <div class="wiki-book-heading">
                                <h4>
                                <?php if($hasAction && has_permission('wiki_books','','edit')){ ?>
                                    <a href="<?php echo admin_url('wiki/books/book/'.$book['id']) ?>"><?php echo $book['name']; ?></a>
                                    <?php } else { ?>
                                        <?php echo $book['name']; ?>
                                        <?php } ?>
                                </h4>
                            </div>
                            
                        </div>
                        <div class="wiki-book-decription wiki-item-description">
                            <p><?php echo $book['short_description']; ?></p>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-xs-6">
                                <a href="<?php echo admin_url('wiki/articles') ?>?filter_book_id=<?php echo $book['id']; ?>" class="text-center wiki-footer-option">
                                    <span class="wiki-counter-value"><strong><?php echo $book['articles_total']; ?></strong></span>
                                    <div class="wiki-counter-name"><?php echo _l('wiki_articles'); ?></div>
                                </a>
                            </div>
                            <div class="col-xs-6">
                                <div class="text-center wiki-footer-option">
                                    <span class="wiki-counter-value"><strong><?php echo count(wiki_unserialize($book['assign_ids'], '')); ?></strong></span>
                                    <div class="wiki-counter-name"><?php echo _l('wiki_peoples_teams'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url(WIKI_ASSETS_PATH.'/js/books_manage.js'); ?>"></script>
</body>
</html>
