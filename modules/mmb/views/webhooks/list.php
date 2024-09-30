<?php
defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url(WEBHOOKS_MODULE . '/webhooks/webhook'); ?>" class="btn btn-primary pull-left"><?php echo _l('new_web_hook'); ?></a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('webhook_feed_name'),
                            _l('request_url'),
                            _l('custom_field_add_edit_active'),
                            _l('debug_mode'),
                            _l('actions'),
                        ], 'webhooks');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    initDataTable('.table-webhooks', window.location.href);
</script>