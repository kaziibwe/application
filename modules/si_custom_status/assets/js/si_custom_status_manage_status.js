(function($) {
"use strict";
	jQuery.validator.addMethod("alphanumeric", function(value, element) {
    	return this.optional(element) || /^[\w. ]+$/i.test(value);
	}, "Letters, numbers, and underscores only please");
	appValidateForm($("body").find('#si-custom-status-form'), {
		//name: {'required':true,/*'alphanumeric':true,*/'maxlength':50},
		name: {
            required: {
                depends: function(element) {
                    var $is_default = $('input[name="is_default"]').val();
                    if ($is_default=='true') {
                        return false;//name not compulsary in case of Default
                    }
					return true;
                }
            },
			'maxlength':50,
        },
		color:{'maxlength':7},
		order:{'min':0},
	}, manage_custom_statuses);
	$('#si_custom_status').on("hidden.bs.modal", function (event) {
		$('#additional').html('');
		$('#si_custom_status input[name="is_default"]').val(false);
		$('#si_custom_status #show_default_name_str').hide();
		$('#si_custom_status input[name="name"]').val('');
		$('#si_custom_status input[name="name"]').parents('.form-group').removeClass('has-error');
		$('#si_custom_status input[name="name"]').parents('.form-group').find('p.text-danger').remove();
		$('#si_custom_status input[name="color"]').val('');
		$('#si_custom_status input[name="order"]').val('');
		$('#si_custom_status #filter_default').attr('checked',false);
		$('.add-title').removeClass('hide');
		$('.edit-title').removeClass('hide');
		$('#si_custom_status input[name="order"]').val($('table tbody tr').length + 1);
	});
})(jQuery);

function si_new_status() {
	$('#si_custom_status').modal('show');
	$('.edit-title').addClass('hide');
}

function si_edit_status(invoker, id, is_default) {
	$('#additional').append(hidden_input('id', id));
	if(is_default)
		$('#si_custom_status #show_default_name_str').show();
	else
		$('#si_custom_status #show_default_name_str').hide();
	$('#si_custom_status input[name="is_default"]').val(is_default);	
	$('#si_custom_status input[name="name"]').val($(invoker).data('name'));
	$('#si_custom_status .colorpicker-input').colorpicker('setValue', $(invoker).data('color'));
	$('#si_custom_status input[name="order"]').val($(invoker).data('order'));
	$('#si_custom_status #filter_default').attr('checked',($(invoker).data('filter_default')?true:false));
	$('#si_custom_status').modal('show');
	$('.add-title').addClass('hide');
}

function manage_custom_statuses(form) {
	var data = $(form).serialize();
	var url = form.action;
	$.post(url, data).done(function (response) {
		window.location.reload();
	});
	return false;
}

function toggle_edit_default_status(invoker,relto)
{
	var edit_default_status = ($(invoker).prop('checked') ? 1 : 0);
	var data = {'edit_default_status':edit_default_status,'relto':relto};
	$.post(admin_url+'si_custom_status/save_edit_default_status', data).done(function (response) {
		response = JSON.parse(response);
		if (response.success === true || response.success == 'true') { 
			window.location.reload();
		}
		else{
			alert_float('warning', response.message); 
		}
		
	});
	return false;
}