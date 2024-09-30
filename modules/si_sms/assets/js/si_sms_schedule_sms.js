(function($) {
"use strict";
//add form
appValidateForm($("#si_sms_schedule_form"), {
	sms_content: {'required':true,'maxlength':5000},
	schedule_date: 'required',
},manage_sms_send_form);
$('input[name="filter_by"]').on('change', function() {
	var filter_by = $(this).val();
	$('select').parents('.form-group').removeClass('has-error');
	$('.div_merge_field').hide();//hide all merge fields
	$('#div_merge_field_'+filter_by).show();//show only selected filter merge fields
	if(filter_by=='customer'){
		$('select[name="si_clients[]"]').selectpicker('val','').selectpicker('refresh');
		$('#si_clients_wrapper').removeClass('hide');
		$('#si_leads_wrapper').addClass('hide');
		$('#si_staffs_wrapper').addClass('hide');
		$('select[name="si_clients[]"]').attr('required',true);
		$('select[name="si_leads[]"]').removeAttr('required');
		$('select[name="si_staffs[]"]').removeAttr('required');
	}else if(filter_by=='lead'){
		$('select[name="si_leads[]"]').selectpicker('val','').selectpicker('refresh');
		$('#si_clients_wrapper').addClass('hide');
		$('#si_leads_wrapper').removeClass('hide');
		$('#si_staffs_wrapper').addClass('hide');
		$('select[name="si_leads[]"]').attr('required',true);
		$('select[name="si_clients[]"]').removeAttr('required');
		$('select[name="si_staffs[]"]').removeAttr('required');
	}else if(filter_by=='staff'){
		$('select[name="si_staffs[]"]').selectpicker('val','').selectpicker('refresh');
		$('#si_clients_wrapper').addClass('hide');
		$('#si_leads_wrapper').addClass('hide');
		$('#si_staffs_wrapper').removeClass('hide');
		$('select[name="si_staffs[]"]').attr('required',true);
		$('select[name="si_clients[]"]').removeAttr('required');
		$('select[name="si_leads[]"]').removeAttr('required');
	}
});
$('#sms_template').on('change', function() {									
	requestGetJSON('si_sms/get_template/' + $(this).val()).done(function(response) {
		$('#sms_content').val(response.content);
		$('#div_dlt_template').find('input.form-control').val(response.dlt_template_id);
	});
});
function manage_sms_send_form()
{
	var ubtn = $('#si_sms_send');
	var form = $('#si_sms_schedule_form');
	var data = form.serialize();
	var url = form.action;
	ubtn.html($('#si_sms_send_wrapper').data('wait-text'));
	ubtn.addClass('disabled');
	$.post(url,data).done(function(response){
		response=JSON.parse(response);
		if(response['success']){
			ubtn.removeClass('disabled');
			ubtn.html($('#si_sms_send_wrapper').data('original-text'));
			alert_float('success', response['message']);
			$('.table-si-sms-schedule').DataTable().ajax.reload();
		}else{
			ubtn.removeClass('disabled');
			ubtn.html($('#si_sms_send_wrapper').data('original-text'));
			alert_float('danger', response['message']);
		}	
	}).fail(function(response){
		alert_float('danger', response.responseText);
		ubtn.removeClass('disabled');
		ubtn.html($('#si_sms_send_wrapper').data('original-text'));
	});

}
$('#si_sms_clear').on('click', function() {
	$('select[name="si_clients[]"]').selectpicker('val','').selectpicker('refresh');
	$('#sms_template').selectpicker('val','').selectpicker('refresh');
	$('#div_dlt_template').find('input.form-control').val('');
	$('#si_clients_wrapper').removeClass('hide');
	$('#si_leads_wrapper').addClass('hide');
	$('#si_staffs_wrapper').addClass('hide');
	$('select[name="si_clients[]"]').attr('required',true);
	$('select[name="si_leads[]"]').removeAttr('required');
	$('select[name="si_staffs[]"]').removeAttr('required');
	$('select,textarea,input').parents('.form-group').removeClass('has-error');
	$('p.text-danger').hide();
});
$(document).ready(function() {
	$('.div_merge_field').hide();//hide all merge fields
	$('#div_merge_field_customer').show();//show only selected filter merge fields
	$.ajax({
		url: admin_url+"si_sms/get_clients_leads",
	}).done(function(data) {
		data = JSON.parse(data);
		var toAppend = '';
		$.each(data.clients, function(i, item) {
			toAppend += '<option value="'+item.id+'">'+item.name+'</option>';
		});
		$('select[name="si_clients[]"]').find('option:first').remove();
		$('select[name="si_clients[]"]').append(toAppend);
		$('select[name="si_clients[]"]').selectpicker('refresh');
		toAppend = '';
		$.each(data.leads, function(i, item) {
			toAppend += '<option value="'+item.id+'">'+item.name+'</option>';
		});
		$('select[name="si_leads[]"]').find('option:first').remove();
		$('select[name="si_leads[]"]').append(toAppend);
		$('select[name="si_leads[]"]').selectpicker('refresh');
		$('select[name="si_staffs[]"]').find('option:first').remove();
	});
});
$(document).on('click','.si_sms_schedule_delete',function(e){
	e.preventDefault();
	requestGet('si_sms/schedule_delete/' + $(this).data('id')).done(function(response) {
		response = JSON.parse(response);
		if (response.success === true || response.success == 'true') { 
			alert_float('success', response.message);
			$('.table-si-sms-schedule').DataTable().ajax.reload();
		}
		else{
			alert_float('warning', response.message); 
		}
	}).fail(function(data) {
		alert_float('danger', data.responseText);
	});
});

//save edit schedule
$(document).on('click','#si_sms_btn_for_edit',function(e){
	//edit form
	appValidateForm($("#si_sms_edit_schedule_form"), {
		si_rel_id : 'required',
		sms_content: {'required':true,'maxlength':5000},
		schedule_date: 'required',
	});
	
	var ubtn = $('#si_sms_btn_for_edit');
	var form = $('#si_sms_edit_schedule_form');
	
	if (form.length && !form.validate().checkForm())
		return false;
	var data = form.serialize();
	var url = form.attr('action');
	ubtn.html($('#si_sms_edit_send_wrapper').data('wait-text'));
	ubtn.addClass('disabled');
	$.post(url,data).done(function(response){
		response=JSON.parse(response);
		if(response['success']){
			ubtn.removeClass('disabled');
			ubtn.html($('#si_sms_edit_send_wrapper').data('original-text'));
			alert_float('success', response['message']);
			$('#si_sms_view_schedule_modal').modal('hide');
			$('.table-si-sms-schedule').DataTable().ajax.reload();
		}else{
			ubtn.removeClass('disabled');
			ubtn.html($('#si_sms_edit_send_wrapper').data('original-text'));
			alert_float('danger', response['message']);
		}	
	}).fail(function(response){
		alert_float('danger', response.responseText);
		ubtn.removeClass('disabled');
		ubtn.html($('#si_sms_edit_send_wrapper').data('original-text'));
	});
});	
})(jQuery);	

/* View schdule sms*/
function view_schedule_modal(id) {
	requestGetJSON('si_sms/get_schedule_sms_by_id/' + id).done(function(response) {
		var schedule_modal = $('#si_sms_view_schedule_modal');
		schedule_modal.find('#div_view_schedule_sms').html(response.html);
		schedule_modal.modal('show');
		$('#si_sms_btn_for_edit').hide();
	});
}
/* Edit schdule sms*/
function edit_schedule_modal(id) {
	requestGetJSON('si_sms/get_schedule_sms_by_id/' + id +'/1').done(function(response) {
		var schedule_modal = $('#si_sms_view_schedule_modal');
		schedule_modal.find('#div_view_schedule_sms').html(response.html);
		schedule_modal.modal('show');
		$('#si_sms_btn_for_edit').show();
		init_selectpicker();
		init_datepicker();
	});
}
