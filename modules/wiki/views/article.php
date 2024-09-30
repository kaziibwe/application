<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(WIKI_ASSETS_PATH.'/css/wiki_styles.css'); ?>">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="d-flex">
                            <h4 class="no-margin"><?php echo $title; ?></h4>
                            
                        </div>

                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string(), array('id'=>'form_main')); ?>
                        <?php if(isset($article)){ ?>
                            <input type="hidden" name="article_id" value="<?php echo $article->id; ?>">
                        <?php } ?>
                        <?php if(isset($clone_id)){ ?>
                            <input type="hidden" name="clone_id" value="<?php echo $clone_id; ?>">
                        <?php } ?>
                        <?php if(isset($back_url)){ ?>
                            <input type="hidden" name="back_url" value="<?php echo $back_url; ?>">
                        <?php } ?>
                        <?php
                            if (isset($article)) {
                                $selected_type = $article->type;
                            }else{
                                $selected_type = 'document';
                            }
                        ?>
                        <div class="roww">
                            <div class="col-md-4">
                                <div>
                                    <?php
                                        $selected = array();
                                        if (isset($article)) {
                                            $selected = $article->book_id;
                                        }
                                    ?>
                                    <?php echo render_select('book_id', $books, array('id', array('name')), 'wiki_book', $selected, []); ?>
                                </div>
                                <?php $attrs = (isset($article) ? array() : array('autofocus'=>true)); ?>
                                
                                <?php $value = (isset($article) ? $article->title : ''); ?>
                                <?php echo render_input('title', 'wiki_title', $value, 'text', $attrs); ?>
                            </div>
                           
                            <div class="col-md-4">
                                <?php $value = (isset($article) ? $article->description : ''); ?>
                                <?php echo render_textarea('description', 'wiki_description', $value,['rows' => 6]); ?>
                                <?php $value = (isset($article) ? $article->content : ''); ?>
                            </div>

                            <div class="col-md-4">
                                <?php if(isset($article)){ ?>
                                    <div class="checkbox checkbox-primary">
                                        <small><?php echo _l('get_link_help'); ?></small><br>
                                        <input type="checkbox" name="is_publish" id="is_publish" <?php if(isset($article)){if($article->is_publish == 1){echo 'checked';} } else {echo 'checked';} ?>>
                                        <label for="is_publish"><?php echo _l('get_link'); ?></label>
                                    </div>
                                    <div class="wiki-input-slug-wrap">
                                        <?php $tmp_publish_link = site_url('wiki/' . $article->slug); ?>
                                        <p>
                                            <a href="<?php echo $tmp_publish_link; ?>" target="_blank"><?php echo $tmp_publish_link; ?></a>
                                            <span class="wiki-btn-copy" data-copy="<?php echo $tmp_publish_link; ?>" data-lang="<?php echo _l('wiki_copy_success'); ?>"><button type="button" class="btn btn-default btn-sm"><i class="fa fa-copy"></i></button></span>
                                        </p>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div>
                                            <?php echo render_select('type', [['id' => 'document', 'name' => _l('wiki_document'),],['id' => 'mindmap', 'name' => _l('wiki_mindmap'),],], array('id', array('name')), 'wiki_type', $selected_type, []); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4 wiki-article-type-wrap <?php echo $selected_type == 'mindmap' ? '' : 'hide' ?>" data-type="mindmap">
                                        <div>
                                            <label for="" class="control-label"><?php echo _l('wiki_help_build'); ?></label>
                                        </div>
                                        <button type="submit" name="submit" value="SAVE_AND_BUILD" class="btn btn-success"><?php echo _l('wiki_build_map'); ?></button>
                                        <?php 
                                            $value = null;
                                            if(isset($article) && isset($article->mindmap_thumb) && $article->mindmap_thumb != ""){
                                                $value = $article->mindmap_thumb;
                                            }
                                        ?>
                                        <img class="wiki-mindmap-thumb" src="<?php echo isset($value) ? wiki_get_mindmap_thumb($value) : wiki_get_mindmap_thumb() ?>" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="wiki-article-type-wrap <?php echo $selected_type == 'document' ? '' : 'hide' ?>" data-type="document">
                            <div class="row">
                                <div class="col-md-12">
                                    <p><?php echo _l('wiki_content'); ?>: <span class="text-warning">(<?php echo _l('article_help_menu'); ?>)</span></p>
                                    <p><small><?php echo _l('tinymce-help-article'); ?></small></p>
                                    <?php $value = (isset($article) ? $article->content : ''); ?>
                                    <?php echo render_textarea('content','',$value,array(),array(),'','tinymce-content'); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                            <a href="<?php  echo isset($back_url) ? $back_url : admin_url('wiki/articles'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
                            <?php if(isset($article)){ ?> 
                            <a href="<?php echo admin_url('wiki/articles/show/' . $article->id) ?>" class="btn btn-success"><?php echo _l('view'); ?></a>
                            <?php } ?>
                            <?php if(isset($article) && has_permission('wiki_articles','','delete')){ ?>
                                <a href="<?php echo admin_url('wiki/articles/delete/' . $article->id); ?>" class="btn btn-danger btn-remove" data-lang="<?php echo _l('wiki_confirm_delete'); ?>"><?php echo _l('delete'); ?></a>
                            <?php } ?>
                            <button type="submit" name="submit" value="ONLY_SAVE" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                         </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url(WIKI_ASSETS_PATH.'/js/article.js'); ?>"></script>
</body>
</html>
