<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<ul class="nav nav-tabs" role="tablist">
	<li role="presentation"  class="active">
		<a href="#set_timesheet_tab1" aria-controls="set_timesheet_tab1" role="tab" data-toggle="tab"><?php echo _l('si_ts_settings_tab1'); ?></a>
	</li>
</ul>
<div class="tab-content mtop30">
	<div role="tabpanel" class="tab-pane  active" id="set_timesheet_tab1">
		<div class="row">
			<div class="col-md-6">
			<?php render_yes_no_option(SI_TIMESHEET_MODULE_NAME.'_completed_task_allow_add','si_ts_settings_completed_task_allow_add'); ?>
			</div>
			<div class="col-md-6">
			<?php render_yes_no_option(SI_TIMESHEET_MODULE_NAME.'_completed_task_allow_edit','si_ts_settings_completed_task_allow_edit'); ?>
			</div>
		</div>
		<hr/>
		<div class="row">	
			<div class="col-md-6">
			<?php render_yes_no_option(SI_TIMESHEET_MODULE_NAME.'_show_task_custom_fields','si_ts_settings_show_task_custom_fields'); ?>
			</div>
			<div class="col-md-6">
			<?php render_yes_no_option(SI_TIMESHEET_MODULE_NAME.'_show_staff_icon_in_calendar','si_ts_settings_show_staff_icon_in_calendar'); ?>
			</div>
		</div>
		<hr/>
		<div class="row">	
			<div class="col-md-6 mtop10">
				<?php 
				echo render_select('settings['.SI_TIMESHEET_MODULE_NAME.'_task_status_exclude_add][]',get_instance()->tasks_model->get_statuses(),array('id','name'),'si_ts_settings_task_status_exclude_add',unserialize(get_option(SI_TIMESHEET_MODULE_NAME.'_task_status_exclude_add')),array('data-width'=>'100%','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);?>
			</div>
		</div>
		<hr/>
		
	</div>
</div>