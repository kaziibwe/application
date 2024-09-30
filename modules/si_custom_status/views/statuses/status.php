<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="si_custom_status" tabindex="-1" role="dialog">
	<div class="modal-dialog">
	<?php echo form_open(admin_url('si_custom_status/status'), array('id'=>'si-custom-status-form')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
				<span class="edit-title"><?php echo _l('edit_status'); ?></span>
				<span class="add-title"><?php echo _l('si_custom_status_new_status'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div id="additional"></div>
						<label for="name"><?php echo _l('si_custom_status_status_add_edit_name')?></label>
						<div class="pull-right" id="show_default_name_str">(<?php echo _l('si_custom_status_default_status_name')?>)</div>
						<?php echo render_input('name', ''); ?>
						<?php echo render_color_picker('color', _l('si_custom_status_status_color')); ?>
						<?php echo render_input('order', 'si_custom_status_status_add_edit_order', total_rows(db_prefix().'si_custom_status',['relto'=>$relto]) + 1, 'number'); ?>
						<?php echo form_hidden('relto',$relto);?>
						<?php echo form_hidden('is_default',false);?>
						<label><?php echo _l('si_custom_status_status_filter_default');?></label>
						<div class="onoffswitch mbot15">
							<input type="checkbox" id="filter_default" class="onoffswitch-checkbox" value="1" name="filter_default">
							<label class="onoffswitch-label" for="filter_default"></label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>
		</div>
		<!-- /.modal-content -->
	<?php echo form_close(); ?>
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
