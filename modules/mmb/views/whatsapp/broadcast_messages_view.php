<?php
defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="alert alert-info">
                            <b>Hint: </b><?= _l("hint_message") ?>
                        </div>
                        <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'broadcast-form', 'class' => 'broadcast-form')); ?>
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <div class="form-group select-placeholder">
                                    <label for="rel_type" class="control-label"><?= _l('category'); ?></label>
                                    <select name="rel_type" id="rel_type" class="selectpicker" data-width="100%" data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <option value="leads"><?= _l('lead') ?></option>
                                        <option value="staff"><?= _l('staff') ?></option>
                                        <option value="customer"><?= _l('proposal_for_customer') ?></option>
                                    </select>
                                </div>
                                <hr />
                                <div id="rel_id_wrapper" class="hide" style="padding: 10px">
                                    <div class="checkbox checkbox-primary checkbox-inline">
                                        <input type="checkbox" name="rel_id_all" id="rel_id_all" class="form-control" checked value='1'>
                                        <label class="control-label" for="rel_id_all"><?= _l('send_to_all') ?><span class="rel_id_label"></span></label>
                                    </div>
                                    <div style="width: 100%; height: 10px; border-bottom: 1px solid #ddd; text-align: center; margin: 20px 0px">
                                        <span style="font-size: 15px; background-color: #fff; padding: 0 4px;">
                                            OR
                                        </span>
                                    </div>
                                    <div class="form-group select-placeholder">
                                        <label for="rel_id"><?= _l('send_to') ?>: <span class="rel_id_label"></span></label>
                                        <div id="rel_id_select">
                                            <select disabled name="rel_id[]" id="rel_id" multiple class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                            </select>
                                        </div>
                                    </div>
                                    <hr />
                                </div>
                                <?php echo render_select('template_name', get_template_list(), ['id', 'template'], _l('template_name')); ?>
                                <div id="image-upload" style="display: none;">
                                    <?php echo render_input('image', 'choose_image', '', 'file'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?= render_textarea('broadcast_message', 'broadcast_message_field', '', ['rows' => '20', 'cols' => '5'], [], '', 'mentionable'); ?>
                                <div class="checkbox checkbox-primary checkbox-inline">
                                    <input type="checkbox" name="debug_mode" id="debug_mode" class="form-control" value='1'>
                                    <label class="control-label" for="debug_mode"><?= _l('run_in_debug_mode') ?><span class="debug_mode_label"></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn send btn-primary pull-right"><?= _l('send') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    var _rel_id = $('#rel_id'),
        _rel_type = $('#rel_type'),
        _rel_id_wrapper = $('#rel_id_wrapper'),
        data = {};

    $(function() {
        $('.rel_id_label').html(_rel_type.find('option:selected').text());
        _rel_type.on('change', function() {
            var clonedSelect = _rel_id.html('').clone();
            _rel_id.selectpicker('destroy').remove();
            _rel_id = clonedSelect;
            $('#rel_id_select').append(clonedSelect);
            proposal_rel_id_select();
            if ($(this).val() != '') {
                _rel_id_wrapper.removeClass('hide');
            } else {
                _rel_id_wrapper.addClass('hide');
            }
            $('.rel_id_label').html(_rel_type.find('option:selected').text());
        });
        $("#rel_id_all").on('change', function() {
            _rel_id.prop("disabled", $(this).is(":checked")).selectpicker('refresh');
        })
        proposal_rel_id_select();
        validate_broadcast_form();

        $('#template_name').on('change', function(event) {
            event.preventDefault();
            var template_id = $(this).val();
            $.ajax({
                    url: `${admin_url}mmb/whatsapp_api/get_template_data/${template_id}`,
                    type: 'POST',
                    dataType: 'json',
                })
                .done(function(res) {
                    if (res.header_data_format !== 'IMAGE' && res.header_data_format !== '' && res.header_data_format !== "TEXT") {
                        alert_float("danger", `Right Now, we are not supporting ${res.header_data_format} template type for broadcast messages`, 10000);
                        $(".send").prop("disabled", true);
                        return false;
                    }
                    $(".send").prop("disabled", false);
                    if (res.body_params_count != '1' || body_data.trim() != "{{1}}") {
                        alert_float("warning", "The template you selected has more than one parameter or have some other content. But you can still send messages..", 12000);
                    }
                    if (res.header_data_format == 'IMAGE') {
                        $('#image-upload').show();
                    } else {
                        $('#image-upload').hide();
                    }
                });
        });
    });

    function validate_broadcast_form() {
        appValidateForm($('#broadcast-form'), {
            rel_type: 'required',
            template_name: 'required',
            broadcast_message: 'required',
            'rel_id[]': {
                required: {
                    depends: function(element) {
                        return !$("#rel_id_all").is(":checked");
                    }
                }
            }
        }, submitBrodcastForm);
    }

    function submitBrodcastForm(form) {
        var data = new FormData(form);
        var action = $(form).attr('action');
        $(".send").prop("disabled", true).html("<i class='fa fa-spin fa-spinner'></i>");
        $.ajax({
            type: 'POST',
            url: action,
            data: data,
            dataType: "json",
            cache: false,
            contentType: false,
            processData: false,
        }).done(function(res) {
            alert_float("success", "Message Sent");
            $(".send").prop("disabled", false).html("SEND");
            $(form).find(".selectpicker").each(function() {
                $(this).val('').selectpicker("refresh");
            });
            $("#broadcast_message").text("");
            $(form).trigger("reset");
        });
    }

    function proposal_rel_id_select() {
        var serverData = {};
        serverData.rel_id = _rel_id.val();
        data.type = _rel_type.val();
        init_ajax_search(_rel_type.val(), _rel_id, serverData);
    }

    refreshTribute();
</script>