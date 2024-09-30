$(function(){
"use strict";
	/*validate form*/
	jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || /^[\w. ]+$/i.test(value);
	}, si_sms_alphanumeric_validation);
	appValidateForm($("#si_sms_add_new_template"), {
		template_name: {'required':true,'alphanumeric':true,'maxlength':255},
		content:{'maxlength':5000},
	});
	/* Clear todo modal values when modal is hidden*/
	$("body").on("hidden.bs.modal", '#si_sms_template_modal', function() {
		var $template = $('#si_sms_template_modal');
		$template.find('input[name="id"]').val('');
		$template.find('input[name="template_name"]').val('');
		$template.find('textarea[name="content"]').val('');
		$template.find('#div_dlt_template').find('input.form-control').val('');
		$template.find('#is_public').prop('checked',false);
		$template.find('.add-title').addClass('hide');
		$template.find('.edit-title').addClass('hide');
	});
	/* Focus template name*/
	$("body").on("shown.bs.modal", '#si_sms_template_modal', function() {
		var $template = $('#si_sms_template_modal');
		$template.find('input[name="template_name"]').focus();
		if ($template.find('input[name="id"]').val() !== '') {
			$template.find('.add-title').addClass('hide');
			$template.find('.edit-title').removeClass('hide');
		} else {
			$template.find('.add-title').removeClass('hide');
			$template.find('.edit-title').addClass('hide');
		}
	});
	$(".si_template_edit").on('click', function() {								
		requestGetJSON('si_sms/get_template/' + $(this).data('id')).done(function(response) {
			var template = $('#si_sms_template_modal');
			template.find('input[name="id"]').val(response.id);
			template.find('input[name="template_name"]').val(response.template_name);
			template.find('textarea[name="content"]').val(response.content);
			template.find('#div_dlt_template').find('input.form-control').val(response.dlt_template_id);
			template.find('#is_public').prop('checked',(response.is_public == "1" ? true : false));
			template.modal('show');
			template.find('.add-title').addClass('hide');
			template.find('.edit-title').removeClass('hide');
		});
	});
});