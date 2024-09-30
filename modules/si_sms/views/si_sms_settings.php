<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<ul class="nav nav-tabs" role="tablist">
	<li role="presentation"  class="active">
		<a href="#si_sms_settings_tab1" aria-controls="si_sms_settings_tab1" role="tab" data-toggle="tab"><?php echo _l('si_sms_settings_tab1'); ?></a>
	</li>
</ul>
<div class="tab-content mtop30">
	<div role="tabpanel" class="tab-pane  active" id="si_sms_settings_tab1">
		<?php if(!get_option(SI_SMS_MODULE_NAME.'_activated') || get_option(SI_SMS_MODULE_NAME.'_activation_code')==''){?>
		<div class="row" id="si_sms_validate_wrapper" data-wait-text="<?php echo '<i class=\'fa fa-spinner fa-pulse\'></i> '._l('wait_text'); ?>" data-original-text="<?php echo _l('si_sms_settings_validate'); ?>">
			<div class="col-md-9">
				<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('si_sms_settings_purchase_code_help'); ?>"></i>
				<?php echo render_input('settings['.SI_SMS_MODULE_NAME.'_activation_code]','si_sms_settings_activation_code',get_option(SI_SMS_MODULE_NAME.'_activation_code'),'text',array('data-toggle'=>'tooltip','data-title'=>_l('si_sms_settings_purchase_code_help'),'maxlength'=>60)); 
					echo form_hidden('settings['.SI_SMS_MODULE_NAME.'_activated]',get_option(SI_SMS_MODULE_NAME.'_activated'));
				?>
				<span><a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"><?php echo _l('setup_help'); ?></a></span>
			</div>
			<div class="col-md-3 mtop25">
				<button id="si_sms_validate" class="btn btn-success"><?php echo _l('si_sms_settings_validate');?></button>
			</div>
			<div class="col-md-12" id="si_sms_validate_messages" class="mtop25 text-left"></div>
		</div>
		<?php } else {?>
		<div class="row">
			<div class="col-md-12">
				<h5 class="mbot15 pull-right">
					<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('edit').' SMS '._l('triggers'); ?>"></i>
					<a href="<?php echo admin_url('settings?group=sms');?>"><?php echo _l('edit').' SMS '._l('triggers'); ?></a>
				</h5>
			</div>
		</div>
		<div class="row">	
			<div class="col-md-12">
				<label><?php echo _l('si_sms_settings_send_to_customer');?></label><br/>
				<div class="radio radio-inline radio-primary">
					<input type="radio" id="send_to_p" name="settings[<?php echo SI_SMS_MODULE_NAME;?>_send_to_customer]" value="primary" <?php if(get_option(SI_SMS_MODULE_NAME.'_send_to_customer') == 'primary'){echo 'checked';} ?>>
					<label for="send_to_p"><?php echo _l('si_sms_settings_primary'); ?></label>
				</div>
				<div class="radio radio-inline radio-primary">
					<input type="radio" id="send_to_a" name="settings[<?php echo SI_SMS_MODULE_NAME;?>_send_to_customer]" value="all" <?php if(get_option(SI_SMS_MODULE_NAME.'_send_to_customer') == 'all'){echo 'checked';} ?>>
					<label for="send_to_a"><?php echo _l('si_sms_settings_all'); ?></label>
				</div>
				<div class="radio radio-inline radio-primary">
					<input type="radio" id="send_to_c" name="settings[<?php echo SI_SMS_MODULE_NAME;?>_send_to_customer]" value="client" <?php if(get_option(SI_SMS_MODULE_NAME.'_send_to_customer') == 'client'){echo 'checked';} ?>>
					<label for="send_to_c"><?php echo _l('si_sms_settings_client'); ?></label>
				</div>
			</div>
		</div>
		<hr/>
		<div class="row">	
			<div class="col-md-12">
				<?php render_yes_no_option(SI_SMS_MODULE_NAME.'_send_to_alt_client','si_sms_settings_send_to_alt_client'); ?>
			</div>
		</div>
		<hr/>
		<div  class="row">
			<div class="col-md-12">
				<h5><?php echo _l('si_sms_settings_exclude_status_change'); ?></h5>
			</div>
			<div class="col-md-6 mtop10">
				<?php 
				echo render_select('settings['.SI_SMS_MODULE_NAME.'_project_status_exclude][]',get_instance()->projects_model->get_project_statuses(),array('id','name'),'si_sms_settings_project_status_exclude',unserialize(get_option(SI_SMS_MODULE_NAME.'_project_status_exclude')),array('data-width'=>'100%','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);?>
			</div>
			<div class="col-md-6 mtop10 hide">
				<?php 
				echo render_select('settings['.SI_SMS_MODULE_NAME.'_task_status_exclude][]',get_instance()->tasks_model->get_statuses(),array('id','name'),'si_sms_settings_task_status_exclude',unserialize(get_option(SI_SMS_MODULE_NAME.'_task_status_exclude')),array('data-width'=>'100%','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);?>
			</div>
			<div class="col-md-6 mtop10">
				<?php 
				$invoice_statuses = [];
				foreach(get_instance()->invoices_model->get_statuses() as $id)
					$invoice_statuses[] = array('id'=>$id,'name'=>format_invoice_status($id,false));
				echo render_select('settings['.SI_SMS_MODULE_NAME.'_invoice_status_exclude][]',$invoice_statuses,array('id','name'),'si_sms_settings_invoice_status_exclude',unserialize(get_option(SI_SMS_MODULE_NAME.'_invoice_status_exclude')),array('data-width'=>'100%','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);?>
			</div>
			<div class="col-md-6 mtop10">
				<?php 
				echo render_select('settings['.SI_SMS_MODULE_NAME.'_lead_status_exclude][]',get_instance()->leads_model->get_status(),array('id','name'),'si_sms_settings_lead_status_exclude',unserialize(get_option(SI_SMS_MODULE_NAME.'_lead_status_exclude')),array('data-width'=>'100%','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);?>
			</div>
			<div class="col-md-6 mtop10">
				<?php 
				echo render_select('settings['.SI_SMS_MODULE_NAME.'_ticket_status_exclude][]',get_instance()->tickets_model->get_ticket_status(),array('ticketstatusid','name'),'si_sms_settings_ticket_status_exclude',unserialize(get_option(SI_SMS_MODULE_NAME.'_ticket_status_exclude')),array('data-width'=>'100%','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);?>
			</div>
		</div>
		<hr />
		<div  class="row">
			<div class="col-md-12">
				<h5><?php echo _l('si_sms_schedule_send_menu'); ?></h5>
			</div>
			<div class="col-md-6">
				<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('si_sms_settings_clear_schedule_sms_log_after_days_info'); ?>"></i>
				<label><?php echo _l('si_sms_settings_clear_schedule_sms_log_after_days');?></label>
				<div class="input-group">
					<input type="number" class="form-control" name="settings[<?php echo SI_SMS_MODULE_NAME?>_clear_schedule_sms_log_after_days]" value="<?php echo get_option(SI_SMS_MODULE_NAME.'_clear_schedule_sms_log_after_days'); ?>" min="0" max="1000">
					<div class="input-group-addon">
						<span><?php echo _l('days'); ?></span>
					</div>
				</div>
			</div>
		</div>	
		<?php } ?>
		<hr/>
	</div>
</div>