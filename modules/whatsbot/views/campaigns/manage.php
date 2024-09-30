<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <h4 class="tw-my-0 tw-font-semibold"><?php echo _l('campaigns'); ?></h4>
                            <div class="">
                                <?php if (staff_can('create', 'wtc_campaign')) { ?>
                                    <a href="<?php echo admin_url('whatsbot/campaigns/campaign'); ?>" class="btn btn-primary"><?php echo _l('send_new_campaign'); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-separator">
                        <div class="panel-table-full">
                            <div class="">
                                <?php echo render_datatable([
                                    _l('the_number_sign'),
                                    _l('campaign_name'),
                                    _l('template'),
                                    _l('relation_type'),
                                    _l('total'),
                                    _l('delivered_to'),
                                    _l('read_by'),
                                    _l('actions'),
                                ], 'campaigns_table'); ?>
                            </div>
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
    "use strict";
    initDataTable('.table-campaigns_table', `${admin_url}whatsbot/campaigns/get_table_data/campaigns`);
</script>
