<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="active">
		<a href="#set_client_profile_preview" aria-controls="set_client_profile_preview" role="tab" data-toggle="tab"><?php echo _l('si_customer_settings_tab1'); ?></a>
	</li>
	<li role="presentation">
		<a href="#set_client_matrix" aria-controls="set_client_matrix" role="tab" data-toggle="tab"><?php echo _l('si_customer_settings_tab2'); ?></a>
	</li>
</ul>
<div class="tab-content mtop30">
	<div role="tabpanel" class="tab-pane active" id="set_client_profile_preview">
		<h4><?php echo _l('si_customer_settings_print'); ?></h4>
		<hr/>
		<div class="row">
			<div class="col-md-6">
			<?php echo render_input('settings['.SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_heading_text]','si_customer_settings_print_heading_text',get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_heading_text'),'text',array('maxlength'=>200)); ?>
			</div>
			<div class="col-md-6">
			<?php echo render_input('settings['.SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_custom_fields_text]','si_customer_settings_print_custom_fields_text',get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_custom_fields_text'),'text',array('maxlength'=>200)); ?>
			</div>
			<div class="clearfix"></div>
			<hr/>
			<div class="col-md-6">
			<?php echo render_input('settings['.SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_attachment_text]','si_customer_settings_print_attachment_text',get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_attachment_text'),'text',array('maxlength'=>200)); ?>
			</div>
			<div class="col-md-6">
			<?php echo render_input('settings['.SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_specimen_sign_text]','si_customer_settings_print_specimen_sign_text',get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_specimen_sign_text'),'text',array('maxlength'=>200)); ?>
			</div>
			<div class="clearfix"></div>
			<hr/>
			<div class="col-md-6">
			<?php echo render_input('settings['.SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_services_text]','si_customer_settings_print_services_text',get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_services_text'),'text',array('maxlength'=>200)); ?>
			</div>
			<div class="col-md-6">
			<?php echo render_color_picker('settings['.SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_heading_color]',_l('si_customer_settings_print_heading_color'),get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_heading_color')); ?>
			</div>
			<div class="clearfix"></div>
			<hr/>
			<div class="col-md-6">
			<?php echo render_input('settings['.SI_EXPORT_CUSTOMER_MODULE_NAME.'_logo_size]','si_customer_settings_logo_size',get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_logo_size'),'number',array('min'=>0,'max'=>200)); ?>
			</div>
			<div class="col-md-6">
			<?php echo render_input('settings['.SI_EXPORT_CUSTOMER_MODULE_NAME.'_specimen_sign]','si_customer_settings_specimen_sign',get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_specimen_sign'),'number',array('min'=>0,'max'=>6)); ?>
			</div>
			
			<div class="clearfix"></div>
			<hr/>
			<div class="col-md-6">
			<?php echo render_yes_no_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_groups','si_customer_settings_show_groups'); ?>
			</div>
			<div class="col-md-6">
			<?php echo render_yes_no_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_primary_contact','si_customer_settings_show_primary_contact'); ?>
			</div>
			<div class="clearfix"></div>
			<hr/>
			<div class="col-md-6">
			<?php echo render_yes_no_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_shipping_address','si_customer_settings_show_shipping_address'); ?>
			</div>
			<div class="col-md-6">
			<?php echo render_yes_no_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_billing_address','si_customer_settings_show_billing_address'); ?>
			</div>
			<div class="clearfix"></div>
			<hr/>
			<div class="col-md-6">
			<?php echo render_yes_no_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_show_custom_fields','si_customer_settings_show_custom_fields'); ?>
			</div>
			<div class="col-md-6">
			<?php echo render_yes_no_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_files_separate_page','si_customer_settings_print_files_separate_page'); ?>
			</div>
			<div class="clearfix"></div>
			<hr/>
			<div class="col-md-6">
				<label><?php echo _l('si_customer_settings_print_orientation_text');?></label><br/>
				<div class="radio radio-inline radio-primary">
					<input type="radio" id="orientation_p" name="settings[<?php echo SI_EXPORT_CUSTOMER_MODULE_NAME;?>_print_orientation]" value="P" <?php if(get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_orientation') == 'P'){echo 'checked';} ?>>
					<label for="orientation_p"><?php echo _l('si_customer_settings_print_orientation_P_text'); ?></label>
				</div>
				<div class="radio radio-inline radio-primary">
					<input type="radio" id="orientation_l" name="settings[<?php echo SI_EXPORT_CUSTOMER_MODULE_NAME;?>_print_orientation]" value="L" <?php if(get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_orientation') == 'L'){echo 'checked';} ?>>
					<label for="orientation_l"><?php echo _l('si_customer_settings_print_orientation_L_text'); ?></label>
				</div>
			</div>
			<div class="col-md-6">
			<?php echo render_yes_no_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_services','si_customer_settings_print_services'); ?>
			</div>
		</div>
	</div>
	<div role="tabpanel" class="tab-pane" id="set_client_matrix">
		<h4><?php echo _l('si_customer_settings_matrix_print'); ?></h4>
		<hr/>
		<div class="row">
		<div class="col-md-6">
		<?php echo render_input('settings['.SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_heading_text]','si_customer_settings_matrix_print_heading_text',get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_heading_text'),'text',array('maxlength'=>200)); ?>
		</div>
		<div class="col-md-6">
		<?php echo render_color_picker('settings['.SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_heading_color]',_l('si_customer_settings_matrix_print_heading_color'),get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_heading_color')); ?>
		</div>
		<div class="clearfix"></div>
		<hr/>
		<div class="col-md-6">
			<label><?php echo _l('si_customer_settings_print_orientation_text');?></label><br/>
			<div class="radio radio-inline radio-primary">
				<input type="radio" id="matrix_orientation_p" name="settings[<?php echo SI_EXPORT_CUSTOMER_MODULE_NAME;?>_matrix_print_orientation]" value="P" <?php if(get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_orientation') == 'P'){echo 'checked';} ?>>
				<label for="matrix_orientation_p"><?php echo _l('si_customer_settings_print_orientation_P_text'); ?></label>
			</div>
			<div class="radio radio-inline radio-primary">
				<input type="radio" id="matrix_orientation_l" name="settings[<?php echo SI_EXPORT_CUSTOMER_MODULE_NAME;?>_matrix_print_orientation]" value="L" <?php if(get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_orientation') == 'L'){echo 'checked';} ?>>
				<label for="matrix_orientation_l"><?php echo _l('si_customer_settings_print_orientation_L_text'); ?></label>
			</div>
		</div>
		<div class="col-md-6">
		<?php echo render_yes_no_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_services','si_customer_settings_matrix_print_services'); ?>
		</div>
		<div class="clearfix"></div>
		<hr/>
	</div>
</div>