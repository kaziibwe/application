document.addEventListener("DOMContentLoaded", function () {
	let $selectField = $('select[name="fieldto"]');
	$selectField.on("change", function () {
		add_delivery_note_to_special_custom_fields($(this).val());
	});
	$selectField.trigger("change");
});

/**
 *
 * @param {string} selectedValue The selected value
 */
function add_delivery_note_to_special_custom_fields(selectedValue) {
	let field_name = "delivery_note";

	// Add the field to the list
	if (typeof pdf_fields !== "undefined" && !pdf_fields.includes(field_name))
		pdf_fields.push(field_name);

	if (
		typeof client_portal_fields !== "undefined" &&
		!client_portal_fields.includes(field_name)
	)
		client_portal_fields.push(field_name);

	// Show each checkbox and tick if neccessary
	let $selectField = $('select[name="fieldto"]');
	let $selectFieldForm = $selectField.closest("form");
	let new_admin_url =
		site_url + "delivery_notes/" + admin_url.replace(site_url, "");

	if (selectedValue == field_name) {
		let $selectOption = $selectField.find(
			'option[value="' + field_name + '"]'
		);
		let fields = ["show-on-pdf", "show-on-client-portal"];
		for (let index = 0; index < fields.length; index++) {
			const field = fields[index];
			$(`.checkbox.${field}`).show().removeClass("hide");

			let dataValue = $selectOption.data(field.replaceAll("-", "_"));
			dataValue = parseInt(dataValue);
			if (dataValue == 1)
				$(`.checkbox.${field} input[type=checkbox]`).prop(
					"checked",
					true
				);
		}

		// Update the form url to custom endpoint
		$selectFieldForm.attr(
			"action",
			$selectFieldForm.attr("action").replace(admin_url, new_admin_url)
		);
	} else {
		// Replace the form url with original url
		$selectFieldForm.attr(
			"action",
			$selectFieldForm.attr("action").replace(new_admin_url, admin_url)
		);
	}
}
