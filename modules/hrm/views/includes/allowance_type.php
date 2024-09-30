<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_allowance_type(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new_allowance_type'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('allowance_type_name'); ?></th>
    <th><?php echo _l('amount'); ?></th>
    <th><?php echo _l('taxable'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
    <?php foreach($allowance_type as $c){ ?>
    <tr>
       <td><?php echo htmlspecialchars($c['type_id']); ?></td>
       <td><?php echo htmlspecialchars($c['type_name']); ?></td>
       <td><?php echo app_format_money($c['allowance_val'],$base_currency->symbol); ?></td>
       <td><?php if($c['taxable'] == 0){echo _l('no');}else{echo _l('yes');}?></td>
       <td>
         <a href="#" onclick="edit_allowance_type(this,<?php echo htmlspecialchars($c['type_id']); ?>); return false" data-taxable="<?php echo htmlspecialchars($c['taxable']); ?>" data-name="<?php echo htmlspecialchars($c['type_name']); ?>" data-amount="<?php echo htmlspecialchars($c['allowance_val']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>
          <a href="<?php echo admin_url('hrm/delete_allowance_type/'.$c['type_id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
       </td>
    </tr>
    <?php } ?>
 </tbody>
</table>       
<div class="modal fade" id="allowance_type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('hrm/allowance_type')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_allowance_type'); ?></span>
                    <span class="add-title"><?php echo _l('new_allowance_type'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional_allowance_type"></div>   
                     <div class="form">     
                        <?php 
                        echo render_input('type_name','allowance_type_name'); ?>
                        <?php 
                        echo render_input('allowance_val','amount','','number'); ?>

                        <label for="taxable" class="control-label"><?php echo _l('taxable'); ?></label>
                        <select name="taxable" class="selectpicker" id="taxable" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                           <option value=""></option>                  
                           <option value="0"><?php echo _l('no'); ?></option>
                           <option value="1"><?php echo _l('yes'); ?></option>
                        </select>

                    </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
</body>
</html>
