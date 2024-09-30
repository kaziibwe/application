<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <h4 class="tw-mt-0 tw-font-semibold">
                                <?php echo _l('templates'); ?>
                            </h4>
                            <div class="<?= is_mobile() ? 'tw-flex tw-flex-col tw-gap-2' : '' ?>">
                                <?php if (staff_can('load_template', 'wtc_template')) { ?>
                                    <button class="btn btn-primary load_templates"><?php echo _l('load_templates'); ?></button>
                                <?php } ?>
                                <a href="https://business.facebook.com/wa/manage/message-templates/" class="btn btn-primary <?= is_mobile() ? '' : 'tw-ml-1'?>" target="_blank"><?php echo _l('template_management'); ?></a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-separator">
                        <?php render_datatable([
                            _l('the_number_sign'),
                            _l('template_name'),
                            _l('language'),
                            _l('category'),
                            _l('status'),
                            _l('body_data'),
                        ], 'templates'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script type="text/javascript">
    "use strict";
    initDataTable('.table-templates', `${admin_url}whatsbot/templates/get_table_data`, [], [], [], [2, 'ASC']);

    $('.load_templates').on('click', function() {
        $.ajax({
            url: `${admin_url}whatsbot/templates/load_templates`,
            type: 'POST',
            dataType: 'json'
        }).done(function(res) {
            if (res.success == true) {
                alert_float('success', res.message);
                $('.table-templates').DataTable().ajax.reload();
            } else {
                alert_float('danger', res.message);
                $('.table-templates').DataTable().ajax.reload();
            }
        });
    });
</script>
