<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-default rounded-lg shadow-lg">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-8">
                                <span class="text-muted tw-font-semibold text-uppercase"><?php echo _l('template'); ?></span>
                                <h5 class="tw-mt-2 tw-mb-0 tw-font-semibold"><?php echo $campaign['template_name']; ?></h5>
                                <p class="tw-mt-2 tw-mb-0">
                                    <?php echo _d($campaign['created_at']); ?>
                                </p>
                            </div>
                            <span class="circle numbertext circle_warning"><i class="fa-solid fa-scroll fa-xl"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-default rounded-lg shadow-lg">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-8">
                                <span class="text-muted tw-font-semibold text-uppercase"><?php echo _l($campaign['rel_type']); ?></span>
                                <h4 class="tw-mt-2 tw-mb-0 tw-font-semibold tw-truncate"><?php echo count(wb_get_campaign_data($campaign['id'])) ?? '0'; ?></h4>
                                <p class="tw-mt-2 tw-mb-0"><?php echo $total_percent; ?>% <?php echo _l('of_your') . $campaign['rel_type']; ?></p>
                            </div>
                            <span class="circle numbertext circle_info"><i class="fa-solid fa-user fa-xl"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-default rounded-lg shadow-lg">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-8">
                                <span class="text-muted tw-font-semibold text-uppercase"><?php echo _l('delivered_to'); ?></span>
                                <h4 class="tw-mt-2 tw-mb-0 tw-font-semibold tw-truncate"><?php echo $delivered_to_percent; ?>%</h4>
                                <p class="tw-mt-2 tw-mb-0">
                                    <?php echo $delivered_to_count . ' ' . _l($campaign['rel_type']); ?>
                                </p>
                            </div>
                            <span class="circle numbertext circle_success"><i class="fa-solid fa-check fa-xl"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-default rounded-lg shadow-lg">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-8">
                                <span class="text-muted tw-font-semibold text-uppercase"><?php echo _l('read_by'); ?></span>
                                <h4 class="tw-mt-2 tw-mb-0 tw-font-semibold tw-truncate"><?php echo $read_by_percent; ?> %</h4>
                                <p class="tw-mt-2 tw-mb-0 tw-text-nowrap">
                                    <!-- Set to dynamic value -->
                                    <?php echo $read_by_count; ?> of the <?php echo $delivered_to_count; ?> <?php echo $campaign['rel_type']; ?> messaged.
                                </p>
                            </div>
                            <span class="circle numbertext circle_default"><i class="fa-solid fa-comment fa-xl"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <h4 class="tw-mt-0 tw-mb-0 tw-font-semibold">
                                <?php echo _l('campaign_daily_task'); ?>
                            </h4>
                            <div class="<?= is_mobile() ? 'tw-flex tw-flex-col tw-gap-2' : '' ?>">
                                <a href="<?php echo admin_url('whatsbot/campaigns/pause_resume_campaign/' . $campaign['id']); ?>" class="btn btn-primary"><?php echo 1 == $campaign['pause_campaign'] ? "<i class='fa-solid fa-play mright5'></i>" . _l('resume_campaign') : "<i class='fa-solid fa-pause mright5'></i>" . _l('pause_campaign'); ?></a>
                                <a href="<?php echo admin_url('whatsbot/campaigns'); ?>" class="btn btn-primary"><i class="fa-solid fa-backward-step"></i> <?php echo _l('back'); ?></a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-separator">
                        <div class="panel-table-full">
                            <?php render_datatable([
                                _l('phone'),
                                _l('name'),
                                _l('message'),
                                _l('sent_status'),
                            ], 'campaign_daily_task_table'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    "use strict";
    initDataTable('.table-campaign_daily_task_table', `${admin_url}whatsbot/campaigns/get_table_data/campaign_daily_task_table/<?php echo $campaign['id'] . '/' . $campaign['rel_type']; ?>`);
</script>
