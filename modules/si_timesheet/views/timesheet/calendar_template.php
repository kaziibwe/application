<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade _event" id="newTimesheetModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo _l('si_ts_add_timesheet'); ?></h4>
			</div>
			<?php echo form_open('si_timesheet/save_timesheet',array('id'=>'si-add-tasksheet-form')); ?>
			<div class="modal-body">
				<div class="row">
					<!--start rel type-->
					<div class="col-md-4">
						<label for="rel_type_add" class="control-label"><?php echo _l('task_related_to'); ?></label>
						<select class="selectpicker" id="si_ts_rel_type_add" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
							<option value=""></option>
							<option value="project"><?php echo _l('project'); ?></option>
							<option value="invoice"><?php echo _l('invoice'); ?></option>
							<option value="customer"><?php echo _l('client'); ?></option>
							<option value="estimate"><?php echo _l('estimate'); ?></option>
							<option value="contract"><?php echo _l('contract'); ?></option>
							<option value="ticket"><?php echo _l('ticket'); ?></option>
							<option value="expense"><?php echo _l('expense'); ?></option>
							<option value="lead"><?php echo _l('lead'); ?></option>
							<option value="proposal"><?php echo _l('proposal'); ?></option>
						</select>
					</div>
					<!--end of list of rel type-->
					<!--start rel_id select from rel_type-->
					<div class="col-md-8 hide" id="si_ts_rel_id_wrapper_add">
						<label for="rel_id_add" class="control-label"><span class="si_ts_rel_id_label_add"></span></label>
						<div id="si_ts_rel_id_select_add">
							<select  id="si_ts_rel_id_add" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
							</select>
						</div>
					</div>
					<!--end rel_id select-->
					<div class="col-md-12 mtop15 form-group"  id="si_ts_task_id_wrapper_add">
					
		 					<label><?php echo _l('si_ts_task');?></label>
							<select id="si_ts_task_id" name="task_id" data-live-search="true" data-width="100%" class="ajax-search task-removed" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required></select>
					
					</div>
					<div class="col-md-6 form-group">		
						<?php echo render_datetime_input('start','task_log_time_start'); ?>
					</div>
					<div class="col-md-6 form-group">
						<?php echo render_datetime_input('end','task_log_time_end'); ?>
						<label class="text-danger"><?php echo _l('si_ts_time_spend');?> : <a class="text-danger" id="si_total_hours">00:00</a> <?php echo _l('si_ts_time_spend_hours');?></label>
					</div>
					<div class="col-md-12">	
						<?php echo render_textarea('note','note','',array('rows'=>5)); ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>
			<?php echo form_close(); ?>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
