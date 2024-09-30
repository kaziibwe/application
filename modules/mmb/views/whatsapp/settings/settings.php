<div class="row">
	<div class="col-md-6">
		<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('phone_number_id_description'); ?>" data-placement="left"></i>
		<?php echo render_input('settings[phone_number_id]', _l('phone_number_id'), get_option('phone_number_id')); ?>
	</div>
	<div class="col-md-6">
		<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('business_account_id_description'); ?>" data-placement="left"></i>
		<?php echo render_input('settings[whatsapp_business_account_id]', _l('whatsapp_business_account_id'), get_option('whatsapp_business_account_id')); ?>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('access_token_description'); ?>"></i>
		<?php echo render_input('settings[whatsapp_access_token]', _l('whatsapp_access_token'), get_option('whatsapp_access_token')); ?>
	</div>
</div>

<div class="alert alert-warning">
	<?php $html = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
	$html .= '<h4><b><i class="fa fa-warning"></i> ' . _l('template_edit_note') . '</b>!</h4>
            <hr class="hr-10">' . _l('whatsapp_settings_access_token_note_description');
	echo $html;
	?>
</div>