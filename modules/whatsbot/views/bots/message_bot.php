<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open_multipart(admin_url('whatsbot/bots/saveBots'), ['id' => 'whatsapp_bot_form'], ['id' => $bot['id'] ?? '']); ?>
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-text-lg tw-text-neutral-700"><?php echo (isset($bot)) ? _l('edit') . ' #' . $bot['name'] : _l('new_message_bot'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <?php echo render_input('name', 'bot_name', $bot['name'] ?? '', '', ['placeholder' => _l('enter_name'), 'autocomplete' => 'off'], [], 'col-md-12'); ?>
                            <?php echo render_select('rel_type', wb_get_rel_type(), ['key', 'name'], 'relation_type', $bot['rel_type'] ?? '', [], [], 'col-md-12'); ?>
                            <?php echo render_textarea('reply_text', 'reply_text', $bot['reply_text'] ?? '', ['rows' => '10', 'maxlength' => '1024'], [], 'col-md-12', 'mentionable'); ?>
                            <?php echo render_select('reply_type', wb_get_reply_type(), ['id', 'label'], 'reply_type', $bot['reply_type'] ?? '', [], [], 'col-md-12', '', '', false); ?>
                            <div class="col-md-12 alert_default_message hide">
                                <div class="alert alert-warning">
                                    <?= _l('default_message_note'); ?>
                                </div>
                            </div>
                            <div class="form-group col-md-12 trigger_input">
                                <label for="trigger" class="control-label"><?php echo _l('trigger_keyword'); ?></label>
                                <input type="text" class="tagsinput" id="trigger" name="trigger" value="<?= $bot['trigger'] ?? '' ?>" data-role="tagsinput">
                            </div>
                            <?php echo render_input('bot_header', 'header', $bot['bot_header'] ?? '', '', ['placeholder' => _l('enter_header')], [], 'col-md-12'); ?>
                            <?php echo render_input('bot_footer', 'footer_bot', $bot['bot_footer'] ?? '', '', ['placeholder' => _l('enter_footer'), 'maxlength' => '60'], [], 'col-md-12'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-text-lg tw-text-neutral-700"><?php echo _l('bot_with_reply_buttons'); ?></h4>
                        <div class="row">
                            <?php echo render_input('button1', 'button1', $bot['button1'] ?? '', '', ['placeholder' => _l('enter_button1')], [], 'col-md-6'); ?>
                            <?php echo render_input('button1_id', 'button1_id', $bot['button1_id'] ?? '', '', ['placeholder' => _l('enter_button1_id'), 'maxlength' => '256'], [], 'col-md-6'); ?>
                        </div>
                        <div class="row">
                            <?php echo render_input('button2', 'button2', $bot['button2'] ?? '', '', ['placeholder' => _l('enter_button2')], [], 'col-md-6'); ?>
                            <?php echo render_input('button2_id', 'button2_id', $bot['button2_id'] ?? '', '', ['placeholder' => _l('enter_button2_id'), 'maxlength' => '256'], [], 'col-md-6'); ?>
                        </div>
                        <div class="row">
                            <?php echo render_input('button3', 'button3', $bot['button3'] ?? '', '', ['placeholder' => _l('enter_button3')], [], 'col-md-6'); ?>
                            <?php echo render_input('button3_id', 'button3_id', $bot['button3_id'] ?? '', '', ['placeholder' => _l('enter_button3_id'), 'maxlength' => '256'], [], 'col-md-6'); ?>
                        </div>
                        <hr class="hr-panel-separator" />
                        <h4 class="tw-font-semibold tw-text-lg tw-text-neutral-700"><?php echo _l('option_2_bot_with_link'); ?></h4>
                        <div class="row">
                            <?php echo render_input('button_name', 'button_name', $bot['button_name'] ?? '', '', ['placeholder' => _l('enter_button_name')], [], 'col-md-12'); ?>
                            <?php echo render_input('button_url', 'button_link', $bot['button_url'] ?? '', '', ['placeholder' => _l('enter_button_url')], [], 'col-md-12'); ?>
                        </div>
                        <hr class="hr-panel-separator" />
                        <h4 class="tw-font-semibold tw-text-lg tw-text-neutral-700"><?php echo _l('option_3_file'); ?></h4>
                        <?php $allowd_extension = wb_get_allowed_extension(); ?>
                        <div class="row">
                            <div class="<?= (isset($bot) && empty($bot['filename'])) ? 'hide' : '' ?>">
                                <?php if (isset($bot)) : ?>
                                    <div class="col-md-9">
                                        <img src="<?= base_url(get_upload_path_by_type('bot_files') . $bot['filename']); ?>" class="img img-responsive" height="70%" width="70%">
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <a href="<?= admin_url(WHATSBOT_MODULE . '/bots/delete_bot_files/' . $bot['id']) ?>"><i class="fa fa-remove text-danger"></i></a>
                                    </div>
                                <?php endif ?>
                            </div>
                            <div class="<?= (isset($bot) && !empty($bot['filename'])) ? 'hide' : '' ?> col-md-12">
                                <input type="hidden" id="maxFileSize" value="<?= $allowd_extension['image']['size'] ?>">
                                <label for="bot_file" class="control-label">
                                    <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="<?= _l('maximum_file_size_should_be') . $allowd_extension['image']['size'] . ' MB' ?>"></i>
                                    <?= _l('image') ?>
                                    <small class="text-muted">( <?= _l('allowed_file_types') . $allowd_extension['image']['extension'] ?> )</small>
                                </label>
                                <input type="file" name="bot_file" id="bot_file" accept="<?= $allowd_extension['image']['extension'] ?>" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right tw-space-x-1">
                        <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    "use strict";
    $(function() {
        appValidateForm($('#whatsapp_bot_form'), {
            name: "required",
            reply_text: "required",
            reply_type: "required",
            rel_type: "required",
            button1: "alphanumericMaxlength",
            button2: "alphanumericMaxlength",
            button3: "alphanumericMaxlength",
            button_name: "alphanumericMaxlength",
            button_url: "url",
            trigger: {
                required: {
                    depends: function() {
                        return $('#trigger_input').hide() ? false : true;
                    },
                },
            },
        });
        $.validator.addMethod("alphanumericMaxlength", function(value, element) {
            // Check if value is alphanumeric with spaces and does not exceed 20 characters
            return this.optional(element) || /^[A-Za-z0-9\s]{1,20}$/.test(value);
        }, "Please enter only letters, numbers, or spaces and maximum 20 characters allowed.");
    });

    $(document).on('change', '#rel_type', function(event) {
        if ($(this).val() == "leads") {
            $('[for="reply_text"]').html(`<?php echo _l('reply_text', _l('leads')); ?>`);
        } else {
            $('[for="reply_text"]').html(`<?php echo _l('reply_text', _l('contacts')); ?>`);
        }
        wb_loadData();
    });

    $(document).on('change', '#reply_type', function(event) {
        $('.trigger_input').show();
        $('.alert_default_message').addClass('hide');
        if ($(this).val() == "3" || $(this).val() == "4") {
            $('.trigger_input').hide();
        }
        if ($(this).val() == "4") {
            $('.alert_default_message').removeClass('hide');
        }
    }).trigger("change");

    <?php if (isset($bot)) { ?>
        $('#rel_type').trigger('change');
        $('#reply_type').trigger('change');
    <?php } ?>
</script>
