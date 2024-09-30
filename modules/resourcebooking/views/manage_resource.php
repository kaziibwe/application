<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"><i class="fa fa-cube" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr />
                    </div>
                  </div>
                  <div class="row">    
                        <div class="_buttons col-md-3">
                        <a href="#" onclick="new_resource(); return false;" class="btn btn-info pull-left display-block">
                            <?php echo _l('new_resource'); ?>
                        </a>
						</div>
                        <div class="col-md-3">
                          
                          <select name="staff[]" id="staff" class="selectpicker" multiple="true" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('filter_by').' '._l('manager'); ?>">
                            
                            <?php foreach($staff as $s) { ?>
                              <option value="<?php echo htmlspecialchars($s['staffid']); ?>"><?php echo htmlspecialchars($s['firstname']); ?></option>
                              <?php } ?>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <select name="group[]" id="group" class="selectpicker"  multiple="true" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('filter_by').' '._l('resource_group'); ?>">
                            
                            <?php foreach($resource_group as $rg){ ?>
                            <option value="<?php echo htmlspecialchars($rg['id']); ?>"><?php echo htmlspecialchars($rg['group_name']); ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <select name="status_filter[]" id="status_filter" multiple="true" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('filter_by').' '._l('status'); ?>">
                            
                            <option value="active"><?php echo _l('active') ?></option>
                            <option value="deactive"><?php echo _l('deactive') ?></option>
                          </select>
                        </div>
                    </div>
                    <br><br>
                  <?php render_datatable(array(
                        _l('resource_name'),
                        _l('resource_group'),
                        _l('manager'),
                        _l('color'),
                        _l('status'),
                        _l('options')
                        ),'table_resource'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="resource" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('resourcebooking/resources'),array('id'=>'resource-form')); ?>
        <div class="modal-content rbfullwidth">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_resource'); ?></span>
                    <span class="add-title"><?php echo _l('new_resource'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('resource_name','resource_name'); ?>

                    </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label for="resource_group"><?php echo _l('resource_group'); ?></label>
                    <select name="resource_group" id="resource_group" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <option value=""></option>
                      <?php foreach($resource_group as $rg){ ?>
                      <option value="<?php echo htmlspecialchars($rg['id']); ?>"><?php echo htmlspecialchars($rg['group_name']); ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="status"><?php echo _l('status'); ?></label>
                    <select name="status" id="status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <option value=""></option>
                      <option value="active"><?php echo _l('active') ?></option>
                      <option value="deactive"><?php echo _l('deactive') ?></option>
                    </select>
                  </div>
                  
                </div>
                <br>
                <div class="row">
                  <div class="col-md-12">
                  <label for="manager"><?php echo _l('manager'); ?></label>
                  <select name="manager" id="manager" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                    <option value=""></option>
                    <?php foreach($staff as $s) { ?>
                      <option value="<?php echo htmlspecialchars($s['staffid']); ?>"><?php echo htmlspecialchars($s['firstname']); ?></option>
                      <?php } ?>
                  </select>
                  </div>
                  
                </div>
                <br>
                <div class="row">
                 <div class="col-md-12">
                  <?php
                   $event_colors = '';
                   $favourite_colors = get_system_favourite_colors();
                   $i = 0;
                   foreach($favourite_colors as $color){
                    $color_selected_class = 'cpicker-small';
                    $event_colors .= "<div class='calendar-cpicker cpicker ".$color_selected_class."' data-color='".$color."' style='background:".$color.";border:1px solid ".$color."'></div>";
                    $i++;
                  }
                  echo '<div class="cpicker-wrapper">';
                  echo '' . $event_colors;
                  echo '</div>';
                  echo form_hidden('color',$favourite_colors[0]);
                  ?>
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
<style>
th.sorting_disabled.sorting_asc:after {
    display: none !important;
}
</style>
</body>
</html>
<script src="<?php echo module_dir_url('resourcebooking','assets/js/manage_resource.js'); ?>"></script>
<script>
 var ResourceServerParams = {
        "staff": "[name='staff[]']",
        "group": "[name='group[]']",
        "status": "[name='status_filter[]']",
    };
    table_resource = $('table.table-table_resource');
    _table_api = initDataTable(table_resource, admin_url+'resourcebooking/resource_table', '', '', ResourceServerParams);
    $.each(ResourceServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_resource.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });
   $(".cpicker").on('click', function() {
      var color = $(this).data('color');
      // Clicked on the same selected color
      if ($(this).hasClass('cpicker-big')) { return false; }

      $(this).parents('.cpicker-wrapper').find('.cpicker-big').removeClass('cpicker-big').addClass('cpicker-small');
      $(this).removeClass('cpicker-small', 'fast').addClass('cpicker-big', 'fast');
      if ($(this).hasClass('calendar-cpicker')) {
          $('input[name="color"]').val(color);
      }
  });
</script>
