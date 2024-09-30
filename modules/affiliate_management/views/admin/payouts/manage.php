<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php

$columns = AffiliateManagementHelper::get_table_columns('payouts');
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body panel-table-full payouts">
                        <?php render_datatable($columns, 'payouts'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_instance()->load->view('admin/payouts/_payout_action_script'); ?>

<?php init_tail(); ?>
<script>
"use strict";
$(function() {
    initDataTable('.table-payouts', window.location.href, undefined, [], undefined);
});
</script>
</body>

</html>