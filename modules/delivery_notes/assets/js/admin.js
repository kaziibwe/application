$(function () {
	let table_delivery_notes = $("table.table-delivery_notes");

	if (table_delivery_notes.length > 0) {
		var Sales_table_ServerParams = {};
		var Sales_table_Filter = $("._hidden_inputs._filters input");

		$.each(Sales_table_Filter, function () {
			Sales_table_ServerParams[$(this).attr("name")] =
				'[name="' + $(this).attr("name") + '"]';
		});

		if (table_delivery_notes.length) {
			// Delivery notes table
			initDataTable(
				table_delivery_notes,
				admin_url + "delivery_notes/table",
				"undefined",
				"undefined",
				Sales_table_ServerParams,
				[
					[7, "desc"],
					[0, "desc"],
				]
			);
		}
	}

	if ($("body").hasClass("delivery_notes-pipeline")) {
		var delivery_noteid = $('input[name="delivery_noteid"]').val();
		delivery_note_pipeline_open(delivery_noteid);
	}

	// add note
	$("body").on("submit", ".delivery_note-notes-form", function () {
		var form = $(this);
		if (form.find('textarea[name="description"]').val() === "") {
			return;
		}

		$.post(form.attr("action"), $(form).serialize()).done(function (
			rel_id
		) {
			// Reset the note textarea value
			form.find('textarea[name="description"]').val("");
			// Reload the notes
			let controller = form.attr("data-controller");
			if (controller?.length) {
				get_sales_notes(rel_id, controller);
			}
		});
		return false;
	});

	$("body").on("change", 'div.delivery_note input[name="date"]', function () {
		var date = $(this).val();
		do_prefix_year(date);

		// This function not work on edit
		if ($('input[name="isedit"]').length > 0) {
			return;
		}

		if ($("body").find("div.delivery_note").length > 0) {
			due_calc_url = admin_url + "delivery_notes/get_due_date";
			due_date_input_name = "expirydate";

			if (date === "") {
				$('input[name="' + due_date_input_name + '"]').val("");
			}

			if (date !== "") {
				$.post(due_calc_url, {
					date: date,
				}).done(function (formatted) {
					if (formatted) {
						$('input[name="' + due_date_input_name + '"]').val(
							formatted
						);
					}
				});
			}
		}
	});

	// Show send to email delivery_note modal
	$("body").on("click", ".delivery-note-send-to-client", function (e) {
		e.preventDefault();
		$("#delivery_note_send_to_client_modal").modal("show");
	});

	// Send templaate modal custom close function causing problems if is on pipeline view
	$("body").on("click", ".close-send-template-modal", function () {
		$("#delivery_note_send_to_client_modal").modal("hide");
	});
});

// Init single delivery_note
function init_delivery_note(id) {
	load_small_table_item(
		id,
		"#delivery_note",
		"delivery_noteid",
		"delivery_notes/get_delivery_note_data_ajax",
		".table-delivery_notes"
	);
}

function schedule_delivery_note_send(id) {
	$("#delivery_note").load(
		admin_url + "email_schedule_delivery_note/create/" + id
	);
}

function edit_delivery_note_scheduled_email(schedule_id) {
	$("#delivery_note").load(
		admin_url + "email_schedule_delivery_note/edit/" + schedule_id
	);
}
// Delete delivery_note attachment
function delete_delivery_note_attachment(id) {
	if (confirm_delete()) {
		requestGet("delivery_notes/delete_attachment/" + id)
			.done(function (success) {
				if (success == 1) {
					$("body")
						.find('[data-attachment-id="' + id + '"]')
						.remove();
					var rel_id = $("body")
						.find('input[name="_attachment_sale_id"]')
						.val();
					$("body").hasClass("delivery_notes-pipeline")
						? delivery_note_pipeline_open(rel_id)
						: init_delivery_note(rel_id);
				}
			})
			.fail(function (error) {
				alert_float("danger", error.responseText);
			});
	}
}

// Validates delivery_note add/edit form
function validate_delivery_note_form(selector) {
	selector =
		typeof selector == "undefined" ? "#delivery_note-form" : selector;

	appValidateForm($(selector), {
		clientid: {
			required: {
				depends: function () {
					var customerRemoved =
						$("select#clientid").hasClass("customer-removed");
					return !customerRemoved;
				},
			},
		},
		date: "required",
		currency: "required",
		number: {
			required: true,
		},
	});

	$("body")
		.find('input[name="number"]')
		.rules("add", {
			remote: {
				url: admin_url + "delivery_notes/validate_delivery_note_number",
				type: "post",
				data: {
					number: function () {
						return $('input[name="number"]').val();
					},
					isedit: function () {
						return $('input[name="number"]').data("isedit");
					},
					original_number: function () {
						return $('input[name="number"]').data(
							"original-number"
						);
					},
					date: function () {
						return $("body")
							.find('.delivery_note input[name="date"]')
							.val();
					},
				},
			},
			messages: {
				remote: app.lang.delivery_note_number_exists,
			},
		});
}

// Sort delivery_notes in the pipeline view / switching sort type by click
function delivery_notes_pipeline_sort(type) {
	kan_ban_sort(type, delivery_note_pipeline);
}

// Init delivery_notes pipeline
function delivery_note_pipeline() {
	init_kanban(
		"delivery_notes/get_pipeline",
		delivery_notes_pipeline_update,
		".pipeline-status",
		290,
		360
	);
}

// Used when delivery_note is updated from pipeline. eq changed order or moved to another status
function delivery_notes_pipeline_update(ui, object) {
	if (object === ui.item.parent()[0]) {
		var data = {
			delivery_noteid: $(ui.item).attr("data-delivery_note-id"),
			status: $(ui.item.parent()[0]).attr("data-status-id"),
			order: [],
		};

		$.each(
			$(ui.item).parents(".pipeline-status").find("li"),
			function (idx, el) {
				var id = $(el).attr("data-delivery_note-id");
				if (id) {
					data.order.push([id, idx + 1]);
				}
			}
		);

		check_kanban_empty_col("[data-delivery_note-id]");

		setTimeout(function () {
			$.post(admin_url + "delivery_notes/update_pipeline", data).done(
				function (response) {
					update_kan_ban_total_when_moving(ui, data.status);
					delivery_note_pipeline();
				}
			);
		}, 200);
	}
}

// Delivery note single open in pipeline
function delivery_note_pipeline_open(id) {
	if (id === "") {
		return;
	}
	requestGet("delivery_notes/pipeline_open/" + id).done(function (response) {
		var visible = $(".delivery_note-pipeline:visible").length > 0;
		$("#delivery_note").html(response);
		if (!visible) {
			$(".delivery_note-pipeline").modal({
				show: true,
				backdrop: "static",
				keyboard: false,
			});
		} else {
			$("#delivery_note")
				.find(".modal.delivery_note-pipeline")
				.removeClass("fade")
				.addClass("in")
				.css("display", "block");
		}
	});
}
