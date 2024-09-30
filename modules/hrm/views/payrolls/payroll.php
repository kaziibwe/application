<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->model('hrm/hrm_model'); ?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_contract_type(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('add_payslip'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('month'); ?></th>
    <th><?php echo _l('payroll_type') ?></th>
    <th><?php echo _l('status'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
    <?php foreach($payrolls as $pay){ ?>
        <tr>
            <td><?php echo htmlspecialchars($pay['id']); ?></td>
            <td>
                <a href="<?php echo admin_url('hrm/payroll_table/'.$pay['id']); ?>">
                <?php echo date("m/Y", strtotime($pay['payroll_month'])); ?>
                </a>
            </td>
            <td><?php echo $this->hrm_model->get_payroll_type($pay['payroll_type'])->payroll_type_name; ?></td>
            <td>
                <?php if($pay['status'] == 0){ ?>
                <span class="label label inline-block project-status-<?php echo htmlspecialchars($pay['status']);?> hrm-payrollorange"><?php echo _l('not_yet_latched') ?></span>

                <?php }elseif($pay['status'] == 1){ ?>
                    <span class="label label inline-block project-status-<?php echo htmlspecialchars($pay['status']);?> hrm-payrollgreen"><?php echo _l('latched') ?></span>
              <?php  } ?>
            </td>
            <td>
                <a href="<?php echo admin_url('hrm/payroll_table/'.$pay['id']); ?>" class="btn btn-default btn-icon" ><i class="fa fa-eye"></i></a>
                <a href="<?php echo admin_url('hrm/delete_payroll_table/'.$pay['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
            </td>
        </tr>
    <?php } ?>
 </tbody>
</table>       
<div class="modal fade" id="contract_type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('hrm/payroll_table'),array('class'=>'payroll-table-form','autocomplete'=>'off')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="add-title"><?php echo _l('add_payslip'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional_contract_type"></div>   
                     <div class="form">     
                        <div class="row">
                        	<div class="col-md-6">
                        	   <label for="payroll_month"><?php echo _l('month') ?></label>
                                  <select name="payroll_month" class="selectpicker" id="payroll_month" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                    <option value=""></option> 
                                    <?php
                                    foreach($month as $m){                             
                                      ?>
                                      <option value="<?php echo htmlspecialchars($m['id']); ?>" <?php if(isset($from_month) && $newFormat == $m['id'] ){echo 'selected';} ?>><?php echo htmlspecialchars($m['name']); ?></option>

                                     <?php } ?>
                                  </select>
                        	</div>
                        	<div class="col-md-6">
                                <?php  $payroll_type = isset($payroll_type) ? $payroll_type : '' ; ?>

	                        	<label for="payroll_type" class="control-label"><?php echo _l('payroll_type'); ?></label>
	                        	<select name="payroll_type" class="selectpicker" id="payroll_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
	                           	<option value=""></option> 
                                <?php foreach ($payroll_type as $payrolltype){ ?>
	                           	   <option value="<?php echo htmlspecialchars($payrolltype['id']); ?>"><?php echo htmlspecialchars($payrolltype['payroll_type_name']); ?></option>
                               <?php } ?>
	                        	</select>
                        	</div>
                        </div>
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