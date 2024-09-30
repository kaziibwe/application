<?php defined('BASEPATH') or exit('No direct script access allowed');?>
			<?php echo form_open(admin_url('si_sms/save_edit_schedule/'.$schedule['id']),array('id'=>'si_sms_edit_schedule_form')); ?>	
				<div class="row" id="si_sms_edit_send_wrapper" data-wait-text="<?php echo '<i class=\'fa fa-spinner fa-pulse\'></i> '._l('wait_text'); ?>" data-original-text="<?php echo _l('submit'); ?>">
					<div class="col-md-12">
						<?php $filter_by = 'clients';
						$col_array = array('id','name');
						if($schedule['filter_by'] == 'lead') $filter_by = 'leads';
						if($schedule['filter_by'] == 'staff'){
							$filter_by = 'staff_members';
							$col_array = array('staffid',array('firstname','lastname'));
						}	
						echo render_select('si_rel_id[]',$all_rel_ids,$col_array,$filter_by,$selected_rel_ids,array('multiple'=>true,'data-actions-box'=>true,'required'=>true)); ?>
					</div>
					<div class="col-md-12">
						<a href="#" onclick="slideToggle('#si_sms_edit_custom_merge_fields'); return false;" class="pull-right"><small><?php echo _l('available_merge_fields')?></small></a>
						<?php echo render_textarea('sms_content','si_sms_text',nl2br($schedule['content']));?>
						<?php if($merge_fields != ''){?>
						<div id="si_sms_edit_custom_merge_fields" class="hide mbot10">
							<?php echo ($merge_fields);?>
						</div>
						<?php }?>
						<div id="div_dlt_template">
							<?php 
							$trigger_name = SI_SMS_MODULE_NAME.'_custom_sms';
							$trigger_opts = [];
							hooks()->do_action('after_sms_trigger_textarea_content', ['name' => $trigger_name, 'options' => $trigger_opts]);?>
						</div>
					</div>
					<div class="col-md-6">
						<?php echo render_datetime_input('schedule_date','si_sms_schedule_datetime',date('d-m-Y H:i:s',strtotime($schedule['schedule_date']))); ?>
					</div>
				</div>
			<?php echo form_close();?>	

