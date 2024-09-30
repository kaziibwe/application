<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<h4 class="pull-left"><?php echo _l('si_sms_schedule_send_title'); ?></h4>
						<?php if (has_permission('si_sms_schedule_send', '', 'create')) {?>
						<?php echo form_open($this->uri->uri_string(),array('id'=>'si_sms_schedule_form')); ?>
						<div class="pull-right">
							<div class="btn-group">
								<button type="button" class="btn btn-info" onclick="slideToggle('.si-sms-add-schedule'); return false;">
									<?php echo _l('si_sms_schedule_add')?>
								</button>
							</div>
						</div>		
						<div class="clearfix"></div>
						<div class="si-sms-add-schedule hide">
							<hr />
							<div class="row" id="si_sms_send_wrapper" data-wait-text="<?php echo '<i class=\'fa fa-spinner fa-pulse\'></i> '._l('wait_text'); ?>" data-original-text="<?php echo _l('save'); ?>">
								<div class="col-md-4 border-right">
									<label><?php echo _l('si_sms_send_to');?></label><br/>
									<div class="radio radio-inline radio-primary">
										<input type="radio" id="si_send_to_1" name="filter_by" value="customer" checked >
										<label for="filter_by_1"><?php echo _l('clients'); ?></label>
									</div>
									<div class="radio radio-inline radio-primary">
										<input type="radio" id="si_send_to_2" name="filter_by" value="lead">
										<label for="filter_by_2"><?php echo _l('leads'); ?></label>
									</div>
									<?php if(has_permission('staff','','view')){ ?>
									<div class="radio radio-inline radio-primary">
										<input type="radio" id="si_send_to_3" name="filter_by" value="staff">
										<label for="filter_by_3"><?php echo _l('staff_members'); ?></label>
									</div>
									<?php }?>
								</div>
								<div class="col-md-4 border-right">
									<div id="si_clients_wrapper">
										<?php echo render_select('si_clients[]',[],[],'clients','',array('multiple'=>true,'data-actions-box'=>true,'required'=>true)); ?>
									</div>
									<div id="si_leads_wrapper" class="hide">
										<?php echo render_select('si_leads[]',[],[],'leads','',array('multiple'=>true,'data-actions-box'=>true)); ?>
									</div>
									<?php if(has_permission('staff','','view')){ ?>
									<div id="si_staffs_wrapper" class="hide">
										<?php echo render_select('si_staffs[]',$staff_list,array('staffid',array('firstname','lastname')),'staff_members','',array('multiple'=>true,'data-actions-box'=>true)); ?>
									</div>
									<?php } ?>
								</div>
								<div class="col-md-4">
									<label class="control-label"><?php echo _l('si_sms_templates'); ?></label>								
									<?php echo render_select('sms_template',$templates,array('id','template_name')); ?>
								</div>
								<div class="col-md-8 col-md-offset-4">
									<a href="#" onclick="slideToggle('#si_sms_custom_merge_fields'); return false;" class="pull-right"><small><?php echo _l('available_merge_fields')?></small></a>
									<?php echo render_textarea('sms_content','si_sms_text','');?>
									<?php if($merge_fields != ''){?>
									<div id="si_sms_custom_merge_fields" class="hide mbot10">
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
										$trigger_name = SI_SMS_MODULE_NAME.'_custom_sms';
										$trigger_opts = [];
										hooks()->do_action('after_sms_trigger_textarea_content', ['name' => $trigger_name, 'options' => $trigger_opts]);?>
									</div>
								</div>
								<div class="col-md-4 col-md-offset-4">
									<?php echo render_datetime_input('schedule_date','si_sms_schedule_datetime',_dt(date('Y-m-d H:i:s'))); ?>
								</div>
								<div class="col-md-8 col-md-offset-4">
									<hr class="hr-10" />
									<button id="si_sms_send" type="submit" class="btn btn-info mleft4"><?php echo _l('save'); ?></button>
									<button id="si_sms_clear" type="reset" class="btn btn-default mleft4"><?php echo _l('clear'); ?></a>
								</div>
							</div><!--end row-->
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
<!--Modal for View schedule-->
<div class="modal fade" id="si_sms_view_schedule_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span><?php echo _l('si_sms_schedule_send_menu'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div id="div_view_schedule_sms"></div>
			</div>
			<div class="modal-footer">
				<button id="si_sms_btn_for_edit" type="button" class="btn btn-info"><?php echo _l('submit'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
			</div>
		</div>
	</div>
</div>
<!--End modal for View schedule-->
<?php init_tail(); ?>
</body>
</html>
<script src="<?php echo module_dir_url('si_sms','assets/js/si_sms_schedule_sms.js'); ?>"></script>
<script>
(function($) {
"use strict";
	var SiScheduleSMSServerParams = {};
	initDataTable('.table-si-sms-schedule', admin_url+'si_sms/schedule_table', undefined, undefined,
	SiScheduleSMSServerParams, [[ 6, "asc" ], [ 1, "asc" ]]);
})(jQuery);
</script>