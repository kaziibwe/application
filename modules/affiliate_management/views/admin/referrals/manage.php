<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $columns = AffiliateManagementHelper::get_table_columns('referrals'); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                    <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                        <?php echo $title; ?>
                    </h4>
                    <button type="button" data-toggle="modal" data-target="#add_referral_modal" class="btn btn-primary">
                        <i class="fa fa-plus"></i> <?= _l('affiliate_management_add_new'); ?></button>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php render_datatable($columns, 'referrals'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_referral_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= _l('affiliate_management_add_new_modal'); ?></h4>
            </div>
            <div class="modal-body">
                <!-- contact selection -->
                <div class="f_client_id">
                    <div class="form-group select-placeholder">
                        <label for="clientid" class="control-label"><?php echo _l('client'); ?></label>
                        <select id="clientid" name="client_id" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                        </select>
                    </div>
                </div>

                <!-- Select affiliate -->
                <div class="select-placeholder form-group">
                    <label for="affiliate_id" class="control-label"><?= _l('affiliate_management_affiliate'); ?></label>

                    <select name="affiliate_id" class="form-control selectpicker" data-live-search="true">
                        <?php foreach ($affiliates as $affiliate) : ?>
                            <option value="<?= $affiliate->affiliate_id; ?>"><?= $affiliate->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-primary assign-client-affiliate" onclick="affiliateManagementAssign()"><?= _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>
    "use strict";
    $(function() {
        initDataTable('.table-referrals', window.location.href, undefined, [], undefined, [<?= count($columns) - 1; ?>,
            'desc'
        ]);
    });

    function affiliateManagementAssign() {
        const button = $("button.assign-client-affiliate");
        button.addClass("disabled");

        let modal = $("#add_referral_modal");

        let data = {
            client_id: $("#add_referral_modal select[name=client_id]").val(),
            affiliate_id: $("#add_referral_modal select[name=affiliate_id]").val(),
        };

        $.post("<?= admin_url('affiliate_management/assign_client_affiliate'); ?>", data)
            .done(function(response) {
                response = JSON.parse(response);
                if (response.status) {
                    alert_float(response.status, response.message);
                }
                if (response.status == 'success') {
                    $("button[data-dismiss='modal']").click();
                }
            }).always(function() {
                button.removeClass("disabled");
            });
    }
</script>
</body>

</html>