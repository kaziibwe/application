<?php defined('BASEPATH') or exit('No direct script access allowed');?>
			<?php echo form_open(admin_url('si_lead_followup/save_edit_schedule/'.$schedule['id']),array('id'=>'si_lead_followup_edit_schedule_form')); ?>	
				<div class="row" id="si_lead_followup_edit_send_wrapper" data-wait-text="<?php echo '<i class=\'fa fa-spinner fa-pulse\'></i> '._l('wait_text'); ?>" data-original-text="<?php echo _l('submit'); ?>">
					<div class="col-md-12 border-right">
						<?php echo render_input('name','si_lfs_name',$schedule['name'],'text',array('maxlength'=>100));?>
					</div>
					<div class="col-md-6 border-right">
						<?php echo render_select('status',$lead_statuses,array('id','name'),'lead_status',$schedule['status'],array('required'=>true)); ?>
					</div>
					<div class="col-md-6 border-right">
						<?php echo render_select('source',$lead_sources,array('id','name'),'lead_source',$schedule['source']); ?>
					</div>
					<div class="col-md-6">
						<?php echo render_input('schedule_days','si_lfs_schedule_days',$schedule['schedule_days'],'number',array('min'=>1,'max'=>30)); ?>
					</div>
					<div class="col-md-6">
						<?php echo render_input('schedule_hour','si_lfs_schedule_hour',$schedule['schedule_hour'],'number',array('data-toggle'=>'tooltip','data-title'=>_l('hour_of_day_perform_auto_operations').". "._l('hour_of_day_perform_auto_operations_format'),'min'=>0,'max'=>23)); ?>
					</div>
					<div class="col-md-12 mbot15">
						<label><?php echo _l('contract_send_to');?></label><br/>
						<div class="radio radio-inline radio-primary">
							<input type="radio" id="si_send_to_2" name="filter_by" value="lead" <?php if($schedule['filter_by']=='lead') echo 'checked';?> disabled>
							<label for="filter_by_2"><?php echo _l('leads'); ?></label>
						</div>
						<div class="radio radio-inline radio-primary">
							<input type="radio" id="si_send_to_3" name="filter_by" value="staff" <?php if($schedule['filter_by']=='staff') echo 'checked';?> disabled>
							<label for="filter_by_3" data-toggle="tooltip" data-title="<?php echo _l('si_lfs_staff_assigned');?>"><?php echo _l('staff_members'); ?></label>
						</div>
					</div>
					<div class="col-md-12">
						<a href="#" onclick="slideToggle('#si_lead_followup_edit_custom_merge_fields'); return false;" class="pull-right"><small><?php echo _l('available_merge_fields')?></small></a>
						<?php echo render_textarea('sms_content','si_lfs_text',nl2br($schedule['content']));?>
						<?php if($merge_fields != ''){?>
						<div id="si_lead_followup_edit_custom_merge_fields" class="hide mbot10">
							<?php echo ($merge_fields);?>
						</div>
						<?php }?>
						<div id="div_dlt_template">
							<?php 
							$trigger_name = SI_LEAD_FOLLOWUP_MODULE_NAME.'_custom_sms';
							$trigger_opts = [];
							hooks()->do_action('after_sms_trigger_textarea_content', ['name' => $trigger_name, 'options' => $trigger_opts]);?>
						</div>
					</div>
					
				</div>
			<?php echo form_close();?>