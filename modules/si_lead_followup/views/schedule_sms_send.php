<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<h4 class="pull-left"><?php echo _l('si_lfs_schedule_send_title'); ?></h4>
						<?php if (has_permission('si_lead_followup', '', 'create')) {?>
						<?php echo form_open($this->uri->uri_string(),array('id'=>'si_lead_followup_schedule_form')); ?>
						<div class="pull-right">
							<div class="btn-group">
								<button type="button" class="btn btn-info" onclick="slideToggle('.si-lead-followup-add-schedule'); return false;">
									<?php echo _l('si_lfs_schedule_add')?>
								</button>
							</div>
						</div>		
						<div class="clearfix"></div>
						<div class="si-lead-followup-add-schedule hide">
							<hr />
							<div class="row" id="si_lead_followup_send_wrapper" data-wait-text="<?php echo '<i class=\'fa fa-spinner fa-pulse\'></i> '._l('wait_text'); ?>" data-original-text="<?php echo _l('save'); ?>">
								<div class="col-md-12">
									<p class="text-info"><?php echo _l('si_lfs_schedule_send_title_info');?></p>
								</div>
								<div class="col-md-4 border-right">
									<?php echo render_input('name','si_lfs_name','','text',array('maxlength'=>100));?>
								</div>
								<div class="col-md-4 border-right">
									<?php echo render_select('status',$lead_statuses,array('id','name'),'lead_status','',array('required'=>true)); ?>
								</div>
								<div class="col-md-4 border-right">
									<?php echo render_select('source',$lead_sources,array('id','name'),'lead_source',''); ?>
								</div>
								<div class="col-md-4 border-right">
									<?php echo render_input('schedule_days','si_lfs_schedule_days',1,'number',array('data-toggle'=>'tooltip','data-title'=>_l('si_lfs_schedule_days_info'),'min'=>1,'max'=>30)); ?>
								</div>
								<div class="col-md-4 border-right">
									<?php echo render_input('schedule_hour','si_lfs_schedule_hour',12,'number',array('data-toggle'=>'tooltip','data-title'=>_l('hour_of_day_perform_auto_operations').". "._l('hour_of_day_perform_auto_operations_format'),'min'=>0,'max'=>23)); ?>
								</div>
								<div class="col-md-4">
									<label><?php echo _l('contract_send_to');?></label><br/>
									<div class="radio radio-inline radio-primary">
										<input type="radio" id="si_send_to_2" name="filter_by" value="lead" checked>
										<label for="filter_by_2"><?php echo _l('leads'); ?></label>
									</div>
									<div class="radio radio-inline radio-primary">
										<input type="radio" id="si_send_to_3" name="filter_by" value="staff">
										<label for="filter_by_3" data-toggle="tooltip" data-title="<?php echo _l('si_lfs_staff_assigned');?>"><?php echo _l('staff_members'); ?></label>
									</div>
								</div>
								<div class="col-md-8">
									<a href="#" onclick="slideToggle('#si_lead_followup_custom_merge_fields'); return false;" class="pull-right"><small><?php echo _l('available_merge_fields')?></small></a>
									<?php echo render_textarea('sms_content','si_lfs_text','');?>
									<?php if($merge_fields != ''){?>
									<div id="si_lead_followup_custom_merge_fields" class="hide mbot10">
										<?php if(is_array($merge_fields)){
												foreach($merge_fields as $key=>$mf){
													echo "<div id='div_merge_field_".$key."' class='div_merge_field'>".$mf."</div>";
												}
											}
											else		
												echo ($merge_fields);
										?>
									</div>
									<?php }?>
									<div id="div_dlt_template">
										<?php 
										$trigger_name = SI_LEAD_FOLLOWUP_MODULE_NAME.'_custom_sms';
										$trigger_opts = [];
										hooks()->do_action('after_sms_trigger_textarea_content', ['name' => $trigger_name, 'options' => $trigger_opts]);?>
									</div>
								</div>
								<div class="col-md-12 text-center">
									<hr class="hr-10" />
									<button id="si_lead_followup_send" type="submit" class="btn btn-info mleft4"><?php echo _l('save'); ?></button>
									<button id="si_lead_followup_clear" type="reset" class="btn btn-default mleft4"><?php echo _l('clear'); ?></a>
								</div>
							</div>
						</div>
						<?php echo form_close(); ?>
						<?php }?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php $this->load->view('table_html'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="si_lead_followup_view_schedule_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span><?php echo _l('si_lead_followup_menu') ."-". _l('si_lead_followup_schedule_send_submenu'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div id="div_view_schedule_sms"></div>
			</div>
			<div class="modal-footer">
				<button id="si_lead_followup_btn_for_edit" type="button" class="btn btn-info"><?php echo _l('submit'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script src="<?php echo module_dir_url('si_lead_followup','assets/js/si_lead_followup_schedule_sms.js'); ?>"></script>