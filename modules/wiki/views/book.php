<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(WIKI_ASSETS_PATH.'/css/wiki_styles.css'); ?>">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open($this->uri->uri_string(), array('id'=>'form_main')); ?>
                        <?php if(isset($back_url)){ ?>
                            <input type="hidden" name="back_url" value="<?php echo $back_url; ?>">
                        <?php } ?>
                        <?php $attrs = (isset($book) ? array() : array('autofocus'=>true)); ?>
                        <?php $value = (isset($book) ? $book->name : ''); ?>
                        <?php echo render_input('name', 'book_name', $value, 'text', $attrs); ?>
                        <?php $value = (isset($book) ? $book->short_description : ''); ?>
                        <?php echo render_textarea('short_description', 'book_short_description', $value); ?>
                        <label for="specific_staff"><?php echo _l('permisson_for_views'); ?></label>
                        <div class="select-notification-settings">
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" name="assign_type" value="specific_staff" id="specific_staff" <?php if (isset($book) && $book->assign_type ==  'specific_staff' || !isset($book)) { echo 'checked'; } ?>>
                                <label for="specific_staff"><?php echo _l('specific_staff_members'); ?></label>
                            </div>
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" name="assign_type" id="roles" value="roles" <?php if (isset($book) && $book->assign_type == 'roles') {
                                echo 'checked';
                                } ?>>
                                <label for="roles"><?php echo _l('staff_with_roles'); ?></label>
                            </div>
                            <div class="clearfix mtop15"></div>
                            <div id="specific_staff_assign" class="types-assign <?php if (isset($book) && $book->assign_type != 'specific_staff') { echo 'hide'; } ?>">
                                <?php
                                    $selected = array();
                                    if (isset($book) && $book->assign_type == 'specific_staff') {
                                        $selected = wiki_unserialize($book->assign_ids, 'staff_');
                                    }
                                ?>
                                <?php echo render_select('assign_ids_staff[]', $members, array('staffid', array('firstname', 'lastname')), 'book_assign_specific_staff', $selected, array('multiple'=>true)); ?>
                            </div>
                            <div id="roles_assign" class="types-assign <?php if (isset($book) && $book->assign_type != 'roles' || !isset($book)) {
                                echo 'hide';} ?>">
                                <?php
                                    $selected = array();
                                    if (isset($book) && $book->assign_type == 'roles') {
                                        $selected = wiki_unserialize($book->assign_ids, 'role_');
                                    }
                                ?>
                                <?php echo render_select('assign_ids_roles[]', $roles, array('roleid', array('name')), 'book_assign_roles', $selected, array('multiple'=>true)); ?>
                            </div>
                        </div>

                        <div class="wiki-form-buttons">
                            <a href="<?php  echo isset($back_url) ? $back_url : admin_url('wiki/books'); ?>" class="btn btn-primary"><?php echo _l('back'); ?></a>
                            <?php if(isset($book) && has_permission('wiki_books','','delete')){ ?>
                                <a href="<?php echo admin_url('wiki/books/delete/' . $book->id); ?>" class="btn btn-danger btn-remove" data-lang="<?php echo _l('wiki_confirm_delete'); ?>"><?php echo _l('delete'); ?></a>
                            <?php } ?>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url(WIKI_ASSETS_PATH.'/js/book.js'); ?>"></script>
</body>
</html>
