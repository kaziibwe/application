<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_contract_type(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new_contract_type'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('contract_name'); ?></th>
    <th><?php echo _l('contract_type'); ?></th>
    <th><?php echo _l('duration'); ?></th>
    <th><?php echo _l('insurrance'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
    <?php foreach($contract as $c){ ?>
    <tr>
       <td><?php echo htmlspecialchars($c['id_contracttype']); ?></td>
       <td><?php echo htmlspecialchars($c['name_contracttype']); ?></td>
       <td><?php echo htmlspecialchars($c['contracttype']); ?></td>
       <td><?php echo htmlspecialchars($c['duration']).' '._l($c['unit']); ?></td>
       <td><?php if($c['insurance'] == 0){echo _l('no');}else{echo _l('yes');}?></td>
       <td>
         <a href="#" onclick="edit_contract_type(this,<?php echo htmlspecialchars($c['id_contracttype']); ?>); return false" data-name="<?php echo htmlspecialchars($c['name_contracttype']);  ?>" data-contracttype="<?php echo htmlspecialchars($c['contracttype']);  ?>" data-duration="<?php echo htmlspecialchars($c['duration']);  ?>" data-insurance="<?php echo htmlspecialchars($c['insurance']);?>" data-unit="<?php echo htmlspecialchars($c['unit']);?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>
          <a href="<?php echo admin_url('hrm/delete_contract_type/'.$c['id_contracttype']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
       </td>
    </tr>
    <?php } ?>
 </tbody>
</table>       
<div class="modal fade" id="contract_type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('hrm/contract_type')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_contract_type'); ?></span>
                    <span class="add-title"><?php echo _l('new_contract_type'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional_contract_type"></div>   
                     <div class="form">     
                        <?php 
                        echo render_input('name_contracttype','contract_name'); ?>
                        <?php 
                        echo render_input('contracttype','contract_type'); ?>
                        <div class="row">
                        	<div class="col-md-8">
                        	<?php 
                        	echo render_input('duration','duration','','number'); ?>
                        	</div>
                        	<div class="col-md-4">
	                        	<label for="unit" class="control-label"><?php echo _l('unit'); ?></label>
	                        	<select name="unit" class="selectpicker" id="unit" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
	                           	<option value=""></option>                  
	                           	<option value="month"><?php echo _l('month'); ?></option>
	                           	<option value="year"><?php echo _l('year'); ?></option>
	                        	</select>
                        	</div>
                        </div>
                        <label for="insurance" class="control-label"><?php echo _l('insurrance'); ?></label>
                        <select name="insurance" class="selectpicker" id="insurance" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
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
