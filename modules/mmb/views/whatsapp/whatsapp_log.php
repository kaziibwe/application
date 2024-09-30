<?php
defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="staff_logged_time">
            <div class="alert alert-info">
                <?= _l("update_timing_message") ?>
            </div>
            <dl class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-5 tw-gap-3 sm:tw-gap-5">
                <div class="tw-border tw-border-solid tw-border-neutral-200 tw-rounded-md tw-bg-white">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-medium text-success">
                            <?php echo _l('total_daily_limits'); ?>
                        </dt>
                        <dd class="tw-mt-1 tw-flex tw-items-baseline tw-justify-between md:tw-block lg:tw-flex">
                            <div class="tw-flex tw-items-baseline tw-text-base tw-font-semibold tw-text-primary-600">
                                1000
                            </div>
                        </dd>
                    </div>
                </div>
                <div class="tw-border tw-border-solid tw-border-neutral-200 tw-rounded-md tw-bg-white">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-medium text-info">
                            <?php echo _l('used'); ?>
                        </dt>
                        <dd class="tw-mt-1 tw-flex tw-items-baseline tw-justify-between md:tw-block lg:tw-flex">
                            <div class="tw-flex tw-items-baseline tw-text-base tw-font-semibold tw-text-primary-600">
                                <?= $sent ?>
                            </div>
                        </dd>
                    </div>
                </div>
                <div class="tw-border tw-border-solid tw-border-neutral-200 tw-rounded-md tw-bg-white">
                    <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                        <dt class="tw-font-medium text-danger">
                            <?php echo _l('reamining'); ?>
                        </dt>
                        <dd class="tw-mt-1 tw-flex tw-items-baseline tw-justify-between md:tw-block lg:tw-flex">
                            <div class="tw-flex tw-items-baseline tw-text-base tw-font-semibold tw-text-primary-600">
                            <?= 1000 - $sent ?>
                            </div>
                        </dd>
                    </div>
                </div>
            </dl>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4><?php echo _l('whatsapp_log_details'); ?> </h4>
                                </div>
                                <?php
                                if (has_permission('whatsapp_api', '', 'whatsapp_log_details_clear')) {
                                ?>
                                    <div class="col-md-6">
                                        <a href="<?php echo admin_url(WHATSAPP_API_MODULE . '/log_details/clear_webhook_log'); ?>" class="btn btn-danger pull-right"><?php echo _l('clear_activity_log'); ?></a>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('template_name'),
                            _l('response_code'),
                            _l('recorded_on'),
                            _l('actions'),
                        ], 'whatsapp_api_logs');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    initDataTable('.table-whatsapp_api_logs', admin_url + "mmb/log_details/whatsapp_log_details_table", undefined, undefined, undefined, [2, 'Desc']);
</script>