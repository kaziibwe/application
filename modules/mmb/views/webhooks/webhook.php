<?php
defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <?php echo form_open($this->uri->uri_string(), ['id' => 'webhook-form']); ?>
    <div class="content">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('tootltip_request_action_name'); ?>"></i>
                                <?php echo render_input('webhook_name', '<b>'._l('webhook_feed_name').'</b>', $webhook->name ?? ''); ?>
                            </div>
                            <div class="col-md-5">
                                <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('tootltip_request_webhook_for'); ?>"></i>
                                <?php echo render_select('webhook_for', get_webhook_triggers(), ['value', 'label', 'subtext'], '<b>'._l('webhook_for').'</b>', $webhook->webhook_for ?? ''); ?>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group chk">
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('selectwebhookcondition'); ?>" data-original-title="" title=""></i>
                                    <label for="request_url" class="control-label"><?php echo _l('webhook_action');
                                     ?></label>
                                    <br/><span class="valign"></span>
                                    <div class="checkbox checkbox-inline">
                                        <input class="field_checkbox" value="add" id="webhook_action_add" type="checkbox" name="webhook_action[]" <?php echo !empty($webhook) && in_array('add', $webhook->webhook_action) ? 'checked' : ''; ?> >
                                        <label for="webhook_action_add" class="chk-label"><?php echo _l('permission_create'); ?></label>
                                    </div>
                                    <div class="checkbox checkbox-inline">
                                        <input class="field_checkbox" value="edit" id="webhook_action_edit" type="checkbox" name="webhook_action[]" <?php echo !empty($webhook) && in_array('edit', $webhook->webhook_action) ? 'checked' : ''; ?> >
                                        <label for="webhook_action_edit" class="chk-label"><?php echo _l('webhook_permission_update'); ?></label>
                                    </div>
                                    <div class="checkbox checkbox-inline">
                                        <input class="field_checkbox" value="delete" id="webhook_action_delete" type="checkbox" name="webhook_action[]" <?php echo !empty($webhook) && in_array('delete', $webhook->webhook_action) ? 'checked' : ''; ?> >
                                        <label for="webhook_action_delete" class="chk-label"><?php echo _l('permission_delete'); ?></label>
                                    </div>
                                    <div class="status_change_checkbox checkbox checkbox-inline hide">
                                        <input class="field_checkbox" value="status_change" id="webhook_action_status_change" type="checkbox" name="webhook_action[]" <?php echo !empty($webhook) && in_array('status_change', $webhook->webhook_action) ? 'checked' : ''; ?> >
                                        <label for="webhook_action_status_change" class="chk-label"><?php echo _l('webhook_permission_status_change'); ?></label>
                                    </div>
                                    <div class="accept_checkbox checkbox checkbox-inline hide">
                                        <input class="field_checkbox" value="accept" id="webhook_action_accept" type="checkbox" name="webhook_action[]" <?php echo !empty($webhook) && in_array('accept', $webhook->webhook_action) ? 'checked' : ''; ?> >
                                        <label for="webhook_action_accept" class="chk-label"><?php echo _l('webhook_permission_accept'); ?></label>
                                    </div>
                                    <div class="decline_checkbox checkbox checkbox-inline hide">
                                        <input class="field_checkbox" value="decline" id="webhook_action_decline" type="checkbox" name="webhook_action[]" <?php echo !empty($webhook) && in_array('decline', $webhook->webhook_action) ? 'checked' : ''; ?> >
                                        <label for="webhook_action_decline" class="chk-label"><?php echo _l('webhook_permission_decline'); ?></label>
                                    </div>
                                    <div class="sent_checkbox checkbox checkbox-inline hide">
                                        <input class="field_checkbox" value="sent" id="webhook_action_sent" type="checkbox" name="webhook_action[]" <?php echo !empty($webhook) && in_array('sent', $webhook->webhook_action) ? 'checked' : ''; ?> >
                                        <label for="webhook_action_sent" class="chk-label"><?php echo _l('webhook_permission_sent'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel_s">
                    <div class="panel-body">
                        <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('tootltip_request_url'); ?>"></i>
                        <?php echo render_input('request_url', '<b>'._l('request_url').'</b>', $webhook->request_url ?? ''); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('tootltip_request_method'); ?>"></i>
                                <?php echo render_select('request_method', get_request_method(), ['label', 'value'], _l('request_method'), $webhook->request_method ?? 'GET', [], [], '', '', false); ?>
                            </div>
                            <div class="col-md-6">
                                <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('tootltip_request_format'); ?>"></i>
                                <?php echo render_select('request_format', get_request_format(), ['label', 'value'], _l('request_format'), $webhook->request_format ?? 'FORM', [], [], '', '', false); ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="control-label">
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('tootltip_request_headers'); ?>" data-original-title="" title=""></i>
                                    <b><?php echo _l('request_headers'); ?></b>
                                </label>


                            </div>
                        </div>
                        <div class="request_header_label">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="control-label"><?php echo '<b>'._l('name').'<b/>'; ?></label>
                                </div>
                                <div class="col-md-7">
                                    <label class="control-label"><?php echo '<b>'._l('Value').'<b/>'; ?> </label>

                                </div>
                            </div>
                        </div>
                        <div class="request_header_row" id="req_header_0">
                            <div class="row">
                                <div class="col-md-4">
                                    <?php echo render_select('header[0][header_choice]', get_header_choices(), ['label', 'value'], '', '', [], [], '', 'header_choice'); ?>
                                    <?php echo render_input('header[0][header_custom_choice]', '', '', '', [], ['style' => 'display: none'], 'header_custom_choice'); ?>
                                    <span style="display: none;" class="header_custom_choice_span" id="header_custom_choice_span_0"><i class="fa fa-times"></i></span>
                                </div>
                                <div class="col-md-7">
                                    <?php echo render_input('header[0][value]', '', '', 'text', ['placeholder'=>_l('press_@_key')], [], '', 'mentionable'); ?>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-success add_row"><i class="fa fa-plus"></i></button>
                                    <button type="button" class="btn btn-sm btn-danger remove_row hidden" data-count="0"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        <?php echo $webhook->request_header_html ?? ''; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-md-12" app-field-wrapper="p_category_name">
                                <label class="control-label">
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('tootltip_request_body'); ?>" data-original-title="" title=""></i>
                                    <b><?php echo _l('request_body'); ?></b>
                                </label>
                            </div>
                        </div>
                        <span class="label label-warning"><?php echo _l('field_value'); ?></span>
                        <div class="request_body_label">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="control-label"><?php echo '<b>'._l('Key').'<b/>'; ?></label>
                                </div>
                                <div class="col-md-7">
                                    <label class="control-label"><?php echo '<b>'._l('Value').'<b/>'; ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="request_body_row" id="req_body_0">
                            <div class="row">
                                <div class="col-md-4">
                                    <?php echo render_input('body[0][key]'); ?>
                                </div>
                                <div class="col-md-7">
                                    <?php echo render_input('body[0][value]', '', '', 'text', ['placeholder'=>_l('press_@_key')], [], '', 'mentionable'); ?>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-success add_body_row" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                                    <button type="button" class="btn btn-sm btn-danger remove_body_row hidden" data-toggle="tooltip" data-count="0"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        <?php echo $webhook->request_body_html ?? ''; ?>
                   </div>
               </div>
           </div>
       </div>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel_s">
            <div class="panel-body">
                <button group="submit" id="webhook_submit" class="btn btn-primary" data-toggle="tooltip"><?php echo _l('submit'); ?></button>
                <a class="btn btn-default" data-toggle="tooltip" href="<?php echo admin_url(WEBHOOKS_MODULE . '/webhooks'); ?>"><?php echo _l('close'); ?></a>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
</div>
</div>

</div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
    $(function() {
        "use strict";
        $(".add_row").click( function(event) {
            event.preventDefault();
            var total_element = $(".request_header_row").length;
            var last_id = $(".request_header_row:last").attr('id').split("_");
            var next_id = Number(last_id[2]) + 1;
            $(`#req_header_0 .header_choice`).selectpicker('destroy');
            $("#req_header_0").clone()
            .attr('id', `req_header_${next_id}`)
            .html((i, OldHtml) => {
                OldHtml = OldHtml.replaceAll("header[0][header_choice]",`header[${next_id}][header_choice]`);
                OldHtml = OldHtml.replaceAll("header[0][header_custom_choice]",`header[${next_id}][header_custom_choice]`);
                OldHtml = OldHtml.replaceAll("header_custom_choice_span_0",`header_custom_choice_span_${next_id}`);
                OldHtml = OldHtml.replaceAll("header[0][value]",`header[${next_id}][value]`);
                return OldHtml;
            })
            .appendTo($(".request_header_row:last").parent());
            $(`#req_header_${next_id} .add_row`).remove();
            $(`#req_header_${next_id} :input`).val("");
            $(`#req_header_0 .header_choice`).selectpicker('refresh');
            $(`#req_header_${next_id} .header_choice`).selectpicker('refresh').parents(".form-group").show();
            $(`#req_header_${next_id} .header_custom_choice`).hide();
            $(`#req_header_${next_id} #header_custom_choice_span_${next_id}`).hide();
            $(`#req_header_${next_id} .remove_row`).removeClass('hidden').data('count',next_id);

            refreshTribute();
        });
        $(".add_body_row").click( function(event) {
            event.preventDefault();
            var total_element = $(".request_body_row").length;
            var last_id = $(".request_body_row:last").attr('id').split("_");
            var next_id = Number(last_id[2]) + 1;
            $("#req_body_0").clone()
            .attr('id', `req_body_${next_id}`)
            .html((i, OldHtml) => {
                OldHtml = OldHtml.replaceAll("body[0][key]",`body[${next_id}][key]`);
                OldHtml = OldHtml.replaceAll("body[0][value]",`body[${next_id}][value]`);
                return OldHtml;
            })
            .appendTo($(".request_body_row:last").parent());
            $(`#req_body_${next_id} .add_body_row`).remove();
            $(`#req_body_${next_id} :input`).val("");
            $(`#req_body_${next_id} .remove_body_row`).removeClass('hidden').data('count',next_id);

            refreshTribute();

        });
        $(document).on('click', '.remove_row', function(event) {
            event.preventDefault();
            $('#req_header_'+$(this).data('count')).remove();
        });
        $(document).on('click', '.remove_body_row', function(event) {
            event.preventDefault();
            $('#req_body_'+$(this).data('count')).remove();
        });
        $(document).on('change', '.header_choice', function(event) {
            event.preventDefault();
            if ($(this).val() == "custom") {
                $(this).parents('.form-group').hide();
                $(this).parents('.form-group').siblings('.header_custom_choice').show();
                $(this).parents('.form-group').siblings('.header_custom_choice_span').show();
            }
        });
        $(document).on('click', '.header_custom_choice_span', function(event) {
            $(this).parent().find('.header_custom_choice').val("").hide();
            $(this).parent().find('.header_choice').selectpicker("val","").selectpicker("refresh").parents(".form-group").show();
            $(this).hide();
        });

        $(document).on('change', '#request_method, #request_format', function(event) {
            event.preventDefault();
            if (($("#request_method").val() == "GET" || $("#request_method").val() == "DELETE") && $("#request_format").val() == "JSON") {
                alert_float("warning", "Reminder: GET / DELETE methods do not support JSON format");
                $("#webhook_submit").prop('disabled', true);
            } else {
                $("#webhook_submit").prop('disabled', false);
            }
        });

    });
</script>
