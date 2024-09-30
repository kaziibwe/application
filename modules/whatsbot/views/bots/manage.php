<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php if ('text' == $type) { ?>
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="tw-flex tw-justify-between tw-items-center">
                                <h4 class="tw-my-0 tw-font-semibold">
                                    <?php echo _l('message_bot'); ?>
                                </h4>
                                <div>
                                    <?php if (staff_can('create', 'wtc_message_bot')) { ?>
                                        <a href="<?php echo admin_url('whatsbot/bots/bot/text'); ?>" class="btn btn-primary">
                                            <?php echo _l('create_message_bot'); ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="hr-panel-separator">
                            <div class="panel-table-full">
                                <?php render_datatable([
                                    _l('name'),
                                    _l('type'),
                                    _l('trigger_keyword'),
                                    _l('relation_type'),
                                    _l('active'),
                                    _l('actions'),
                                ], 'message_bot_table'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if ('template' == $type) { ?>
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="tw-flex tw-justify-between tw-items-center">
                                <h4 class="tw-my-0 tw-font-semibold">
                                    <?php echo _l('template_bot'); ?>
                                </h4>
                                <div>
                                    <?php if (staff_can('create', 'wtc_template_bot')) { ?>
                                        <a href="<?php echo admin_url('whatsbot/bots/bot/template'); ?>" class="btn btn-primary pull-right">
                                            <?php echo _l('create_template_base_bot'); ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="hr-panel-separator">
                            <div class="panel-table-full">
                                <?php render_datatable([
                                    _l('name'),
                                    _l('type'),
                                    _l('trigger_keyword'),
                                    _l('relation_type'),
                                    _l('active'),
                                    _l('actions'),
                                ], 'template_bot_table'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    "use strict";
    initDataTable('.table-message_bot_table', `${admin_url}whatsbot/bots/table/message_bot_table`);
    initDataTable('.table-template_bot_table', `${admin_url}whatsbot/bots/table/template_bot_table`)

    $(document).on('click', '#delete_message_bot', function(event) {
        var id = $(this).data('id');
        var type = $(this).data('type');
        $.ajax({
            url: `${admin_url}whatsbot/bots/deleteMessageBot/${type}/${id}`,
            type: 'post',
            dataType: 'json',
        }).done(function(res) {
            alert_float(res.type, res.message);
            $('.table-message_bot_table').DataTable().ajax.reload();
            $('.table-template_bot_table').DataTable().ajax.reload();
        })
    });
</script>
</body>

</html>
