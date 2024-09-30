<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="si_todo_category_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title hide"><?php echo _l('si_todo_edit_category_title'); ?></span>
					<span class="add-title hide"><?php echo _l('si_todo_add_category_title'); ?></span>
				</h4>
			</div>
			<?php echo form_open('admin/si_todo/category',array('id'=>'si_add_new_todo_category')); ?>
			<div class="modal-body">
				<div class="row">
					<?php echo form_hidden('id',''); ?>
					<div class="col-md-12">
						<?php echo render_input('category_name','si_todo_category_name',''); ?>
					</div>
					<div class="col-md-12">
						<?php echo render_color_picker('color',_l('si_todo_category_color'),'#333'); ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>