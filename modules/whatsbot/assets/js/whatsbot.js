"use strict";
let wb_tribute;
var header_data;
var body_data;
var footer_data;

function wb_refreshTribute() {
    wb_tribute.attach(document.querySelectorAll(".mentionable"));
}

$(function () {
    wb_loadData();
});

function wb_loadData() {
    init_selectpicker();

    $('.sendnow').prop('disabled', true);

    // Load merge field
    var fields = _.filter(merge_fields, function (num) {
        return (
            typeof num["leads"] != "undefined" || typeof num["other"] != "undefined" || typeof num["client"] != "undefined"
        );
    });

    var rel_type = $('#rel_type').val();

    if (rel_type == 'leads') {
        rel_type = 'leads';
    } else if (rel_type == 'contacts') {
        rel_type = 'client';
    } else {
        rel_type = 'other';
    }

    var selected_index = _.findIndex(fields, function (data) {
        return _.allKeys(data)[0] == rel_type;
    });

    var options = [];

    if (fields[selected_index]) {
        fields[selected_index][rel_type].forEach((field) => {
            if (field.name != "") {
                options.push({ key: field.name, value: field.key });
            }
        });
    }
    if (rel_type != 'other') {
        fields[2]['other'].forEach((field) => {
            if (field.name != "") {
                options.push({ key: field.name, value: field.key });
            }
        });
    }

    wb_tribute = new Tribute({
        values: options,
        selectClass: "highlights",
    });
    wb_tribute.detach(document.querySelectorAll(".mentionable"));
    wb_tribute.attach(document.querySelectorAll(".mentionable"));
}

$(document).on('input change', '.header_input, .body_input, .footer_input', function () {
    var inputType = null;

    inputType = $(this).hasClass('header_input') ? 'header' :
        $(this).hasClass('body_input') ? 'body' :
            $(this).hasClass('footer_input') ? 'footer' :
                null;

    // Proceed only if inputType is found
    if (inputType) {
        var stringValue = $(this).attr('name');
        // Use a regular expression to extract the number inside the first square bracket
        var match = stringValue.match(/\[(\d+)\]/);
        var key = parseInt(match[1]);
        var value = $(this).val();

        var typeMap = {
            'header': {
                data: header_data,
                selector: '.header'
            },
            'body': {
                data: body_data,
                selector: '.body'
            },
            'footer': {
                data: footer_data,
                selector: '.footer'
            }
        };

        var dataInfo = typeMap[inputType];

        var regex = /{{\d+}}/g; // Regular expression to match '{{' followed by one or more digits and then '}}'
        var matches = dataInfo.data.match(regex);

        var count = matches ? matches.length : 0;

        for (let params = 1; params <= count; params++) {
            dataInfo.data = dataInfo.data.replace("{{" + params + "}}", ($(dataInfo.selector + '\\[' + params + '\\]').val() != "") ? $(dataInfo.selector + '\\[' + params + '\\]').val() : `{{${params}}}`)
        }

        $('.' + inputType + '_data').text(dataInfo.data);
    }
    wb_refreshTribute();
});

$(document).on('change', '#template_id', function (event) {
    var template_id = $('#template_id').val();

    // Related to campaign section : Start
    $('.totalleads').text($('#lead_ids\\[\\] option:selected').length);
    ($('#send_now').prop('checked')) ? $('.sendnow').prop('disabled', false) : $('.sendnow').prop('disabled', true);
    $('#preview_message').show();
    // Related to campaign section : Over

    $.ajax({
        url: `${admin_url}whatsbot/campaigns/get_template_map`,
        type: 'POST',
        dataType: 'html',
        data: {
            'template_id': template_id,
            'temp_id': $('.temp_id').val(),
        },
    })
        .done(function (response) {
            response = JSON.parse(response);
            $('.variableDetails').removeClass('hide');
            var content = (/\S/.test(response.view) != false) ? response.view : '<div class="alert alert-danger">Currently, the variable is not available for this template.</div>';
            $('.variables').html(content);
            $('.selectpicker').selectpicker('refresh');
            let preview_data = `
            <strong class='header_data'>${response.header_data ??= ''}</strong><br><br>
            <p class='body_data'>${response.body_data ??= ''}</p><br>
            <span class="text-muted tw-text-xs footer_data">${response.footer_data ??= ''}</span>
        `;
            let button_data = '';
            if (!empty(response.button_data)) {
                $.each(response.button_data.buttons, function(index, val) {
                    button_data += `<button class="btn btn-default btn-lg btn-block wtc_button">${val.text}</button>`;
                });
            }
            $('.previewBtn').html(button_data);
            $('.previewImage').html('<div id="header_image"></div>');
            $('.previewmsg').html(preview_data);
            header_data = response.header_data ??= '';
            body_data = response.body_data ??= '';
            footer_data = response.footer_data ??= '';
            $('.header_input, .body_input, .footer_input').trigger('input');
            $('.header_input, .body_input, .footer_input').trigger('change');
        })
});

$(document).on('change', '.header_image', function (event) {
    var imageAttachment = event.target.files[0];
    const maxAllowedSize = $('#maxFileSize').val() * 1024 * 1024;
    var imagePreview = !empty($('#image_url').val()) ? $('#image_url').val() : (imageAttachment != undefined ? URL.createObjectURL(imageAttachment) : '');
    var imagesSize = (imageAttachment != undefined) ? imageAttachment.size : 0;

    if (imagesSize > maxAllowedSize) {
        alert_float('danger', `Max file size upload ${$('#maxFileSize').val()} (MB)`);
        $('#header_image').empty();
        $(this).val('');
        return;
    }
    if (!empty(imagePreview)) {
        $('#header_image').html(`<img src="${imagePreview}" class="wtc_image">`);
    }
});

$(document).on('change', '#bot_file', function(event) {
    var imageAttachment = event.target.files[0];
    const maxAllowedSize = $('#maxFileSize').val() * 1024 * 1024;
    var imagesSize = (imageAttachment != undefined) ? imageAttachment.size : 0;

    if (imagesSize > maxAllowedSize) {
        alert_float('danger', `Max file size upload ${$('#maxFileSize').val()} (MB)`);
        $('#bot_file').empty();
        $(this).val('');
        return;
    }
});
