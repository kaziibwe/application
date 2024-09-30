<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME . '_affiliates', '', 'create')) { ?>
                    <div class="tw-mb-2 sm:tw-mb-4">
                        <a href="<?php echo admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/add_affiliate'); ?>" class="btn btn-primary">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('affiliate_management_add_new_affiliate'); ?>
                        </a>
                    </div>
                <?php } ?>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php render_datatable([
                            _l('affiliate_management_affiliate_id'),
                            _l('affiliate_management_group'),
                            _l('affiliate_management_name'),
                            _l('affiliate_management_email'),
                            _l('affiliate_management_status'),
                            _l('affiliate_management_total_referrals'),
                            _l('affiliate_management_lifetime_earnings'),
                            _l('affiliate_management_lifetime_payouts'),
                            _l('affiliate_management_current_balance'),
                            _l('affiliate_management_date_enrolled'),
                            _l('options'),
                        ], 'affiliates'); ?>
                    </div>
                </div>
                <script>
                    "use strict";
                    document.addEventListener('DOMContentLoaded', function() {
                        initDataTable('.table-affiliates',
                            "<?= admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/affiliates'); ?>",
                            undefined, [], undefined);
                    });
                </script>

            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>

</html>