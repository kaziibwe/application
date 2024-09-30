<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (is_admin()) { ?>
                        <div class="_buttons">
                            <a href="<?php echo admin_url('hrm/member'); ?>" class="btn mright5 btn-info pull-left display-block hidden-xs"><?php echo _l('new_staff'); ?></a>

                            </a>
                        </div>
                        <br><br>
                        <hr class="hr-panel-heading">
                        <div class="row">
                            <div class="col-md-3 pull-left">
                                <select name="staff_role[]" class="selectpicker" multiple="true" id="staff_role" data-width="100%" data-none-selected-text="<?php echo _l('filter_by_role'); ?>"> 
                                     <?php 
                                    foreach ($staff_role as $value) { ?>
                                      <option value="<?php echo htmlspecialchars($value['roleid']); ?>"><?php echo htmlspecialchars($value['name']); ?></option>  
                                   <?php } ?>              
                                </select>
                            </div>
                            <div class="col-md-3 pull-left">
                                <select name="status_work[]" class="selectpicker" multiple="true" id="status_work" data-width="100%" data-none-selected-text="<?php echo _l('fillter_by_status'); ?>"> 
                                                   
                                    <option value=""><?php echo _l('working'); ?></option>
                                    <option value="<?php echo 'maternity_leave'; ?>"><?php echo _l('maternity_leave'); ?></option>
                                    <option value="<?php echo 'inactivity'; ?>"><?php echo _l('inactivity'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <?php
                        $table_data = array(
                            _l('staff_id'),
                            _l('hr_code'),
                            _l('staff_dt_name'),
                            _l('staff_dt_email'),
                            _l('birthday'),
                            _l('sex'),
                            _l('role'),
                            _l('staff_dt_last_Login'),
                            _l('staff_dt_active'),
                            _l('status_work'),                            
                            );
                        $custom_fields = get_custom_fields('staff',array('show_on_table'=>1));
                        foreach($custom_fields as $field){
                            array_push($table_data,$field['name']);
                        }
                        render_datatable($table_data,'table_staff');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete_staff" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('hrm/delete_staff',array('delete_staff_form'))); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('delete_staff'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="delete_id">
                    <?php echo form_hidden('id'); ?>
                </div>
                <p><?php echo _l('delete_staff_info'); ?></p>
                <?php
                echo render_select('transfer_data_to',$staff_members,array('staffid',array('firstname','lastname')),'staff_member',get_staff_user_id(),array(),array(),'','',false);
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-danger _delete"><?php echo _l('confirm'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>
$(function(){
//combotree
    var tree_dep = $('#hrm_derpartment_tree').comboTree({
              source : <?php echo ''.$dep_tree; ?>
            });
    
//combotree end
     var StaffServerParams = {
        "status_work": "[name='status_work[]']",
        "hrm_deparment": "input[name='hrm_deparment']",
        "staff_role": "[name='staff_role[]']",
    };
    table_staff = $('table.table-table_staff');
    initDataTable(table_staff,admin_url + 'hrm/table', '','', StaffServerParams);
    
    $.each(StaffServerParams, function() {
            $('#status_work').on('change', function() {
                table_staff.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
        });
    //combotree department
     $('#hrm_derpartment_tree').on('change', function() {
                $('#hrm_deparment').val(tree_dep.getSelectedItemsId());
                table_staff.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
    //staff role
    $.each(StaffServerParams, function() {
            $('#staff_role').on('change', function() {
                table_staff.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
        });
})
</script>
</body>
</html>
