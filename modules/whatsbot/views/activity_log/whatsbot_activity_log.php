<?php
defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="staff_logged_time">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="">
                                <div class="tw-flex tw-justify-between tw-items-center">
                                    <h4 class="tw-my-0 tw-font-semibold"><?php echo _l('whatsapp_logs'); ?></h4>
                                    <?php if (staff_can('clear_log', 'wtc_log_activity')) { ?>
                                        <a href="<?php echo admin_url('whatsbot/clear_log'); ?>" class="btn btn-danger"><?php echo _l('clear_log'); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="hr-panel-heading" />
                            <div class="clearfix"></div>
                            <?php
                            echo render_datatable([
                                _l('the_number_sign'),
                                _l('category'),
                                _l('name'),
                                _l('template_name'),
                                _l('response_code'),
                                _l('type'),
                                _l('recorded_on'),
                                _l('actions'),
                            ], 'wtc_activity_logs');
                            ?>
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
    $(function() {
        initDataTable('.table-wtc_activity_logs', `${admin_url}whatsbot/activity_log_table`, [], [], [], [0, 'desc']);
    });
</script>
