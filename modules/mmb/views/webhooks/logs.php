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
                            <div class="row">
                                <div class="col-md-6">
                                    <h4><?php echo $title; ?></h4>
                                </div>
                                <div class="col-md-6">
                                    <a href="<?php echo admin_url(WEBHOOKS_MODULE . '/webhooks/clear_webhook_log'); ?>" class="btn btn-danger pull-right"><?php echo _l('clear_activity_log'); ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('webhook_feed_name'),
                            _l('response_code'),
                            _l('recorded_on'),
                            _l('actions'),
                        ], 'webhooks-logs');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    initDataTable('.table-webhooks-logs', window.location.href, undefined, undefined, undefined, [2, 'desc']);
</script>