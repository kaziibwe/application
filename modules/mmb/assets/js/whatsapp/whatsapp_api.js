let wa_tribute;
$(function () {
  "use strict";
  $(document.body).on("change", "#category, #rel_type", function (event) {
    var selectedValue = $(this).val();
    if (selectedValue == 'payments') {
      selectedValue='invoice';
    }
    var fields = _.filter(merge_fields, function (num) {
      return (
        typeof num[selectedValue] != "undefined" ||
        typeof num["other"] != "undefined" ||
        typeof num["staff"] != "undefined" ||
        typeof num["client"] != "undefined"
      );
    });

    var other_index = _.findIndex(fields, function (data) {
      return _.allKeys(data)[0] == "other";
    });
    var staff_index = _.findIndex(fields, function (data) {
      return _.allKeys(data)[0] == "staff";
    });
    var client_index = _.findIndex(fields, function (data) {
      return _.allKeys(data)[0] == "client";
    });
    var selected_index = _.findIndex(fields, function (data) {
      return _.allKeys(data)[0] == selectedValue;
    });

    var options = [];

    if (fields[selected_index]) {
      fields[selected_index][selectedValue].forEach((field) => {
        if (field.name != "") {
          options.push({ key: field.name, value: field.key });
        }
      });
    }
    if (fields[other_index]) {
      fields[other_index]["other"].forEach((field) => {
        if (field.name != "") {
          options.push({ key: field.name, value: field.key });
        }
      });
    }
    if (fields[staff_index] && ["client"].indexOf($(this).val()) === -1) {
      fields[staff_index]["staff"].forEach((field) => {
        if (field.name != "") {
          options.push({ key: field.name, value: field.key });
        }
      });
      options.push({
        "key": 'Whatsapp OTP Code',
        "value": '{whatsapp_auth_code}',
      });
    }
    if (fields[client_index] && ["client", "leads"].indexOf($(this).val()) === -1) {
      fields[client_index]["client"].forEach((field) => {
        if (field.name != "") {
          options.push({ key: field.name, value: field.key });
        }
      });
    }

    wa_tribute = new Tribute({
      values: options,
      selectClass: "highlights",
    });
    wa_tribute.detach(document.querySelectorAll(".mentionable"));
    wa_tribute.attach(document.querySelectorAll(".mentionable"));
  });
  $("#category").trigger("change");

  appValidateForm($("#template_mapping_form"), {
    category: "required",
    template_name: "required",
    send_to: "required",
  });
});

function refreshTribute() {
  "use strict";
  if ($("#category").val() != "") {
    wa_tribute.attach(document.querySelectorAll(".mentionable"));
  }
}
