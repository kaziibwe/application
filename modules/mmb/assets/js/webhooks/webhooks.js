let wh_tribute;
$(function() {
	"use strict";
	$(document.body).on('change', '#webhook_for', function(event) {
		var selectedValue = $(this).val()
		var fields = _.filter(merge_fields, function(num){
			return typeof num[selectedValue] != "undefined" || typeof num["other"] != "undefined";
		});

		var other_index = _.findIndex(fields, function (data) {
			return _.allKeys(data)[0] == "other";
		});
		var selected_index = _.findIndex(fields, function (data) {
			return _.allKeys(data)[0] == selectedValue;
		});

		var options = [];

		if (fields[selected_index]) {
			if(selectedValue == "tasks"){
				options.push({ key: "Task Link (Staff)", value: "{staff_task_link}" });
				options.push({ key: "Task Link (Client)", value: "{client_task_link}" });
			}
			if(selectedValue == "projects"){
				options.push({ key: "Project Link (Staff)", value: "{staff_project_link}" });
				options.push({ key: "Project Link (Client)", value: "{client_project_link}" });
			}
			if(selectedValue == "ticket"){
				options.push({ key: "Ticket URL (Staff)", value: "{staff_project_link}" });
				options.push({ key: "Ticket URL (Client)", value: "{client_project_link}" });
			}
			fields[selected_index][selectedValue].forEach(field => {
				if (field.name != "" && field.key != "{task_link}" && field.key != "{project_link}" && field.key != "{ticket_url}") {
					options.push({ key: field.name, value: field.key });
				}
			})
		}
		if (fields[other_index]) {
			fields[other_index]["other"].forEach(field => {
				if (field.name != "") {
					options.push({ key: field.name, value: field.key });
				}
			})
		}
		wh_tribute = new Tribute({
			values: options,
			selectClass: "highlights"
		});
		wh_tribute.detach(document.querySelectorAll(".mentionable"));
		wh_tribute.attach(document.querySelectorAll(".mentionable"));

		$('input[name="webhook_action[]"]').removeAttr('disabled');
		$('.status_change_checkbox, .accept_checkbox, .decline_checkbox, .sent_checkbox').addClass('hide');
		if (selectedValue == 'leads') {
			$('#webhook_action_edit').attr('disabled', true);
			$('#webhook_action_edit').removeAttr('checked');
			$('.status_change_checkbox').removeClass('hide');
			$('#webhook_action_status_change').attr('disabled', false);
		} else if (selectedValue == 'ticket') {
			$('.status_change_checkbox').removeClass('hide');
			$('#webhook_action_status_change').attr('disabled', false);
		} else if (selectedValue == 'tasks') {
			$('#webhook_action_delete').attr('disabled', true);
			$('#webhook_action_delete').removeAttr('checked');
		} else if (selectedValue == 'proposals') {
			$('.accept_checkbox, .decline_checkbox, .sent_checkbox').removeClass('hide');
			$('#webhook_action_accept, #webhook_action_decline, #webhook_action_sent').attr('disabled', false);
		} else if (selectedValue == 'event') {
			$('#webhook_action_add, #webhook_action_delete').attr('disabled', true);
			$('#webhook_action_add, #webhook_action_delete').removeAttr('checked');
		} else {
			$('#webhook_action_status_change').attr('disabled', true);
			$('#webhook_action_accept, #webhook_action_decline, #webhook_action_sent').attr('disabled', true);
		}
	});
	$("#webhook_for").trigger('change');

	appValidateForm($("#webhook-form"), {
        webhook_name: 'required',
        webhook_for: 'required',
        request_url: 'required',
        "webhook_action[]":'required',
    });

});

function refreshTribute(){
	"use strict";
	if($("#webhook_for").val() != ""){
		wh_tribute.attach(document.querySelectorAll(".mentionable"));
	}
}