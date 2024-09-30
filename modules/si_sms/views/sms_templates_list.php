<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$is_admin = is_admin(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<h4 class="pull-left"><?php echo _l('si_sms')." - "._l('si_sms_templates'); ?></h4>
						<a href="#si_sms_template_modal" data-toggle="modal" class=" pull-right btn btn-info mleft4"><?php echo _l('si_sms_add'); ?></a>
						<div class="clearfix"></div>
						<hr />
						<table class="table dt-table scroll-responsive">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th><?php echo _l('si_sms_template_name'); ?></th>
									<th><?php echo _l('si_sms_text'); ?></th>
									<?php if($is_admin){?>
										<th><?php echo _l('permission_view').' ('._l('permission_global').')';?></th>
									<?php } ?>
									<th><?php echo _l('actions'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php
							if(!empty($filter_templates)){
								$i=1;
								$staff_id = get_staff_user_id();
								foreach($filter_templates as $row){?>
								<tr class="has-row-options">
									<td><?php echo ($i++);?></td>
									<td data-order="<?php echo ($row['template_name']); ?>">
										<span><?php echo  ($row['template_name']); ?></span>
									</td>
									<td><?php echo ($row['content']);?></td>
									<?php if($is_admin){?>
									<td><?php echo ($row['is_public']== 1 ? _l('settings_yes'):_l('settings_no'));?></td>
									<?php } ?>
									<td>
										<?php if($row['staff_id'] == $staff_id){?>
										<a href="#si_sms_template_modal" class="si_template_edit" data-toggle="modal" data-id="<?php echo ($row['id']);?>" title="<?php echo _l('edit');?>"><i class="fa fa-pencil-square-o fa-regular fa-pen-to-square"></i></a>  <a href="del_sms_template/<?php echo ($row['id']);?>" class="confirm text-danger _delete" title="<?php echo _l('delete');?>"><i class="fa fa-trash"></i></a>
										<?php } ?>
									</td>
								</tr>
								<?php } }?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="si_sms_template_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title hide"><?php echo _l('edit').' '._l('si_sms_templates'); ?></span>
					<span class="add-title hide"><?php echo _l('si_sms_add').' '._l('si_sms_templates'); ?></span>
				</h4>
			</div>
			<?php echo form_open('admin/si_sms/save_template',array('id'=>'si_sms_add_new_template')); ?>
			<div class="modal-body">
				<div class="row">
					<?php echo form_hidden('id',''); ?>
					<div class="col-md-12">
						<?php echo render_input('template_name','si_sms_template_name',''); ?>
					</div>
					<div class="col-md-12">
						<?php echo render_textarea('content','si_sms_text','');?>
					</div>
					<div class="col-md-12" id="div_dlt_template">
						<?php 
						$trigger_name = SI_SMS_MODULE_NAME.'_custom_sms';
						$trigger_opts = [];
						hooks()->do_action('after_sms_trigger_textarea_content', ['name' => $trigger_name, 'options' => $trigger_opts]);?>
					</div>
					<?php if(is_admin()){?>
					<div class="col-md-12">
						<div class="checkbox">
						 	<input type="checkbox" id="is_public" name="is_public" value="1">
						 	<label for="is_public"><?php echo _l('si_sms_is_public')?></label>
					  	</div>
					</div>
					<?php } ?>	
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
<?php init_tail(); ?>
</body>
</html>
<script>
	var si_sms_alphanumeric_validation = '<?php echo _l('si_sms_alphanumeric_validation'); ?>';
</script>	
<script src="<?php echo module_dir_url('si_sms','assets/js/si_sms_templates.js'); ?>"></script>

