<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"><i class="fa fa-cubes" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr />
                    </div>
                  </div>
                   <div class="_buttons">
                        <a href="#" onclick="new_resource_group(); return false;" class="btn btn-info pull-left display-block">
                            <?php echo _l('new_resource_group'); ?>
                        </a>
                    </div>
                   <br><br><br>
                  <?php render_datatable(array(
                        _l('group_name'),
                        _l('rbcreator'),
                        _l('date_create'),
                        _l('options')
                        ),'table_resource_group'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="resource_group" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('resourcebooking/resource_group'),array('id'=>'resource_group-form')); ?>
        <div class="modal-content rbfullwidth">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_resource_group'); ?></span>
                    <span class="add-title"><?php echo _l('new_resource_group'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('group_name','group_name'); ?>
                        <?php echo form_hidden('creator',get_staff_user_id()); ?>

                    </div>
                </div>

				 <br>
                 <?php echo render_textarea('description','description',''); ?>
            </div>
                <div class="modal-footer">
                    <button type="
                    " class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button id="sm_btn" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<?php init_tail(); ?>
<script src="<?php echo module_dir_url('menu_setup','assets/jquery-nestable/jquery.nestable.js'); ?>"></script>
</body>
</html>
<script src="<?php echo module_dir_url('resourcebooking','assets/js/resource_group.js'); ?>"></script>
