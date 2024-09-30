"use strict";

const affiliateCopyToClipboard = () => {
	var affiliateLink = document.getElementById("affiliateLink").value;
	var slug = document.getElementById("slug-id").value;
	var fullLink = affiliateLink + "/" + slug;

	var copyText = document.createElement("textarea");
	copyText.value = fullLink;
	document.body.appendChild(copyText);
	copyText.select();
	document.execCommand("copy");
	document.body.removeChild(copyText);

	document.getElementById("copySuccessMessage").style.display =
		"inline-block";

	setTimeout(function () {
		document.getElementById("copySuccessMessage").style.display = "none";
	}, 2000);
};

const affiliateToggleEdit = () => {
	var slugIdInput = document.querySelector("#slug-id");
	var editButton = document.querySelector("#edit-button");
	var saveButton = document.querySelector("#save-button");

	if (slugIdInput.readOnly) {
		// Activate editing
		slugIdInput.readOnly = false;
		editButton.style.display = "none";
		saveButton.style.display = "inline-block";
		slugIdInput.focus();
		slugIdInput.setSelectionRange(
			slugIdInput.value.length,
			slugIdInput.value.length
		); // Place cursor at the end
	} else {
		// Saving
		slugIdInput.readOnly = true;
		saveButton.style.display = "none";
		editButton.style.display = "inline-block";

		if (!slugIdInput.value.length) {
			slugIdInput.value = slugIdInput.dataset.value;
		}

		if (!slugIdInput.value.length) return;
		saveButton.setAttribute("disabled", "disabled");

		// Send to server
		$.post(slugIdInput.dataset.action, {
			affiliate_slug: slugIdInput.value,
		})
			.done((response) => {
				response = JSON.parse(response);

				$(".payouts .btn-dt-reload").click();
				if (response.status === "success") $("[data-dismiss]").click();
				saveButton.removeAttribute("disabled");

				slugIdInput.value = response.slug;
				alert_float(response.status, response.message);
			})
			.fail(function (error) {
				alert_float("danger", error.responseText);
				saveButton.removeAttribute("disabled");
			});
	}
};

// General function for all datatables serverside for client view
function initDataTableAffiliate(
	selector,
	url,
	notsearchable,
	notsortable,
	fnserverparams,
	defaultorder,
	language = {}
) {
	var table =
		typeof selector == "string"
			? $("body").find("table" + selector)
			: selector;

	if (table.length === 0) {
		return false;
	}

	fnserverparams =
		fnserverparams == "undefined" || typeof fnserverparams == "undefined"
			? []
			: fnserverparams;

	// If not order is passed order by the first column
	if (typeof defaultorder == "undefined") {
		defaultorder = [[0, "asc"]];
	} else {
		if (defaultorder.length === 1) {
			defaultorder = [defaultorder];
		}
	}

	var user_table_default_order = table.attr("data-default-order");

	if (!empty(user_table_default_order)) {
		var tmp_new_default_order = JSON.parse(user_table_default_order);
		var new_defaultorder = [];
		for (var i in tmp_new_default_order) {
			// If the order index do not exists will throw errors
			if (
				table.find("thead th:eq(" + tmp_new_default_order[i][0] + ")")
					.length > 0
			) {
				new_defaultorder.push(tmp_new_default_order[i]);
			}
		}
		if (new_defaultorder.length > 0) {
			defaultorder = new_defaultorder;
		}
	}

	var length_options = [10, 25, 50, 100];
	var length_options_names = [10, 25, 50, 100];

	app.options.tables_pagination_limit = parseFloat(
		app.options.tables_pagination_limit
	);

	if ($.inArray(app.options.tables_pagination_limit, length_options) == -1) {
		length_options.push(app.options.tables_pagination_limit);
		length_options_names.push(app.options.tables_pagination_limit);
	}

	length_options.sort(function (a, b) {
		return a - b;
	});
	length_options_names.sort(function (a, b) {
		return a - b;
	});

	length_options.push(-1);
	length_options_names.push(app.lang.dt_length_menu_all);

	var dtSettings = {
		language: {...app.lang.datatables, ...language},
		processing: true,
		retrieve: true,
		serverSide: true,
		paginate: true,
		searchDelay: 750,
		bDeferRender: true,
		autoWidth: false,
		dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'col-md-8 dataTables_paging'<'#colvis'><'.dt-page-jump'>p>>",
		pageLength: app.options.tables_pagination_limit,
		lengthMenu: [length_options, length_options_names],
		columnDefs: [
			{
				searchable: false,
				targets: notsearchable,
			},
			{
				sortable: false,
				targets: notsortable,
			},
		],
		fnDrawCallback: function (oSettings) {
			_table_jump_to_page(this, oSettings);
			if (oSettings.aoData.length === 0) {
				$(oSettings.nTableWrapper).addClass("app_dt_empty");
			} else {
				$(oSettings.nTableWrapper).removeClass("app_dt_empty");
			}
		},
		fnCreatedRow: function (nRow, aData, iDataIndex) {
			// If tooltips found
			$(nRow).attr("data-title", aData.Data_Title);
			$(nRow).attr("data-toggle", aData.Data_Toggle);
		},
		initComplete: function (settings, json) {
			var t = this;
			var $btnReload = $(".btn-dt-reload");
			$btnReload.attr("data-toggle", "tooltip");
			$btnReload.attr("title", app.lang.dt_button_reload);

			var $btnColVis = $(".dt-column-visibility");
			$btnColVis.attr("data-toggle", "tooltip");
			$btnColVis.attr("title", app.lang.dt_button_column_visibility);

			t.wrap('<div class="table-responsive"></div>');

			var dtEmpty = t.find(".dataTables_empty");
			if (dtEmpty.length) {
				dtEmpty.attr("colspan", t.find("thead th").length);
			}

			// Hide mass selection because causing issue on small devices
			if (
				is_mobile() &&
				$(window).width() < 400 &&
				t.find('tbody td:first-child input[type="checkbox"]').length > 0
			) {
				t.DataTable().column(0).visible(false, false).columns.adjust();
				$("a[data-target*='bulk_actions']").addClass("hide");
			}

			t.parents(".table-loading").removeClass("table-loading");
			t.removeClass("dt-table-loading");
			var th_last_child = t.find("thead th:last-child");
			var th_first_child = t.find("thead th:first-child");
			if (th_last_child.text().trim() == app.lang.options) {
				th_last_child.addClass("not-export");
			}
			if (th_first_child.find('input[type="checkbox"]').length > 0) {
				th_first_child.addClass("not-export");
			}
		},
		order: defaultorder,
		ajax: {
			url: url,
			type: "POST",
			data: function (d) {
				if (Array.isArray(d.order)) {
					d.order = d.order.map(function (order) {
						var tHead = table.find(
							"thead th:eq(" + order.column + ")"
						);
						if (tHead.length > 0) {
							if (tHead[0].dataset.customField == 1) {
								order.type = tHead[0].dataset.type;
							}
						}
						return order;
					});
				}

				if (typeof csrfData !== "undefined") {
					d[csrfData["token_name"]] = csrfData["hash"];
				}
				for (var key in fnserverparams) {
					d[key] = $(fnserverparams[key]).val();
				}
				if (table.attr("data-last-order-identifier")) {
					d["last_order_identifier"] = table.attr(
						"data-last-order-identifier"
					);
				}
			},
		},
		buttons: get_datatable_buttons(table),
	};

	table = table.dataTable(dtSettings);
	var tableApi = table.DataTable();

	var hiddenHeadings = table.find("th.not_visible");
	var hiddenIndexes = [];

	$.each(hiddenHeadings, function () {
		hiddenIndexes.push(this.cellIndex);
	});

	setTimeout(function () {
		for (var i in hiddenIndexes) {
			tableApi
				.columns(hiddenIndexes[i])
				.visible(false, false)
				.columns.adjust();
		}
	}, 10);

	if (table.hasClass("customizable-table")) {
		var tableToggleAbleHeadings = table.find("th.toggleable");
		var invisible = $("#hidden-columns-" + table.attr("id"));
		try {
			invisible = JSON.parse(invisible.text());
		} catch (err) {
			invisible = [];
		}

		$.each(tableToggleAbleHeadings, function () {
			var cID = $(this).attr("id");
			if ($.inArray(cID, invisible) > -1) {
				tableApi.column("#" + cID).visible(false);
			}
		});
	}

	// Fix for hidden tables colspan not correct if the table is empty
	if (table.is(":hidden")) {
		table
			.find(".dataTables_empty")
			.attr("colspan", table.find("thead th").length);
	}

	table.on("preXhr.dt", function (e, settings, data) {
		if (settings.jqXHR) settings.jqXHR.abort();
	});

	return tableApi;
}
