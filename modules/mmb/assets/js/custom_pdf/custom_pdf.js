var pickers = $('#items_table .colorpicker-input');

// Wait for the document to be fully loaded before executing the code
$(function () {
    // When a click event occurs on an element with the class "tinymce-merge-field"
    $(".tinymce-merge-field").on("click", function (e) {
        // Prevent the default behavior of the click event (e.g., following a link)
        e.preventDefault();

        // Get the text content of the clicked element and remove leading/trailing whitespace
        var mergeField = $(this).text().trim();

        // Find the target TinyMCE editor element based on the 'data-to' attribute
        thisEditor = $(`#${$(this).data('to')}`);

        // Get the current content of the TinyMCE editor and append the merge field text
        // Then, set the updated content back into the TinyMCE editor
        tinyMCE.editors[thisEditor.attr('id')].setContent(`${tinyMCE.editors[thisEditor.attr('id')].getContent()}\n${mergeField}`);
    });

    $.each(pickers, function () {
        $(this).colorpicker({
            format: "hex"
        });
        $(this).colorpicker().on('changeColor', function (e) {
            var color = e.color.toHex();
            var _class = 'custom_style_' + $(this).find('input').data('id');
            var val = $(this).find('input').val();
            if (val == '') {
                $('.' + _class).remove();
                return false;
            }
            var append_data = '';
            var additional = $(this).data('additional');
            additional = additional.split('+');
            if (additional.length > 0 && additional[0] != '') {
                $.each(additional, function (i, add) {
                    add = add.split('|');
                    append_data += add[0] + '{' + add[1] + ':' + color + ' !important;}';
                });
            }
            append_data += $(this).data('target') + '{' + $(this).data('css') + ':' + color +
                ' !important;}';
            console.log($(this).data('target'));
            if ($('head').find('.' + _class).length > 0) {
                $('head').find('.' + _class).html(append_data);
            } else {
                $("<style />", {
                    class: _class,
                    type: 'text/css',
                    html: append_data
                }).appendTo("head");
            }
        });
    });
});

