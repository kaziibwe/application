<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$groups = AffiliateManagementHelper::get_affiliate_groups();
$referral_link_hint =  _l(
    'affiliate_management_link_id_hint',
    [
        AffiliateManagementHelper::URL_IDENTIFIER,
        $affiliate->affiliate_slug,
        base_url() . '?' . AffiliateManagementHelper::URL_IDENTIFIER . '=' . $affiliate->affiliate_slug,
        base_url('forms/wtl/xx') . '?styled=1&' . AffiliateManagementHelper::URL_IDENTIFIER . '=' . $affiliate->affiliate_slug
    ]
);
$payouts_cols = AffiliateManagementHelper::get_table_columns('payouts', true);
$referrals_cols = AffiliateManagementHelper::get_table_columns('referrals', true);
$commissions_cols = AffiliateManagementHelper::get_table_columns('commissions', true);
?>
<div class="content affiliate-view">
    <div class="row">
        <?php foreach ($stats as $stat) : ?>
            <div class="col-sm-6 col-md-3 tw-mb-2">
                <div class="tw-bg-white tw-px-6 tw-py-5 tw-pt-3 top_stats_wrapper tw-shadow-sm tw-rounded-lg tw-border tw-border-neutral-200 tw-border-solid top_stats_wrapper">
                    <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                        <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center mr-2"> <i class="<?= $stat['icon']; ?> tw-mr-2"></i> <?= $stat['label']; ?>
                        </div>
                        <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"><?= $stat['value']; ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <!-- Affiliate link and slug update -->
    <div class="panel panel-default tw-mt-8">
        <div class="panel-body">

            <!-- profile info -->
            <?php if (has_permission('customers', '', 'view') && !is_client_logged_in()) : ?>
                <div class="tw-flex tw-flex-col tw-justify-center">
                    <div class="text-center">
                        <h5 class="tw-mb-0"><?= $affiliate->name ?></h5>
                        <p class="tw-m-0"><span class="text-muted"><?= $affiliate->email ?></span></p>

                        <?php if (is_admin()) : ?>
                            <div class="tw-my-3">
                                <?= form_open(admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . "/update_affiliate_group/{$affiliate->affiliate_id}"), ['method' => 'POST']); ?>
                                <div class="input-group tw-max-w-sm tw-mx-auto">
                                    <select class="selectpicker form-control" name="group_id">
                                        <?php foreach ($groups as $key => $value) : ?>
                                            <option value="<?= $key; ?>" <?= $key == $affiliate->group_id ? 'selected' : ''; ?>>
                                                <?= $value['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary group-update _delete" id="edit-group-button" data-toggle="tooltip" data-title="<?= _l('save'); ?>" title="<?= _l('save'); ?>"><i class="fa fa-save"></i></button>
                                    </span>
                                </div>
                                <?= form_close(); ?>
                            </div>
                            <a class="btn btn-primary" href="<?= admin_url('clients/login_as_client/' . $affiliate->userid); ?>" target="_blank">
                                <i class="fa-regular fa-share-from-square"></i>
                                <?= _l('login_as_client'); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="tw-mt-2 tw-mb-2">
                    <hr />
                </div>
            <?php endif; ?>

            <?php if ($affiliate->status === AffiliateManagementHelper::STATUS_ACTIVE) : ?>
                <!-- affiliate link -->
                <div class="form-group" data-title="<?= $referral_link_hint; ?>" data-toggle="tooltip">
                    <label for="slug-id"><?= _l('affiliate_management_link_id'); ?>:</label>
                    <div class="tw-flex tw-gap-6">
                        <div class="tw-flex tw-flex-col tw-grow">
                            <div class="input-group">
                                <input type="text" class="form-control" id="affiliateLink" value="<?= rtrim(base_url('register'), '/'); ?>" readonly>
                                <span class="input-group-addon">/</span>
                                <input type="text" class="form-control" id="slug-id" data-value="<?= $affiliate->affiliate_slug; ?>" data-action="<?= base_url('clients/' . AFFILIATE_MANAGEMENT_MODULE_NAME . '/update_affiliate_slug'); ?>" value="<?= $affiliate->affiliate_slug; ?>" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-default slug-update" id="edit-button" data-toggle="tooltip" data-title="<?= _l('edit'); ?>" <?php if (!is_client_logged_in()) echo 'disabled'; ?>><i class="fa fa-pen"></i></button>
                                    <button class="btn btn-success slug-update" style="display: none;" id="save-button" data-toggle="tooltip" data-title="<?= _l('save'); ?>" <?php if (!is_client_logged_in()) echo 'disabled'; ?>><i class="fa fa-save"></i></button>
                                    <button class="btn btn-primary copy"><?= _l('copy'); ?></button>
                                </span>
                            </div>
                            <div class="tw-w-full text-right">
                                <small class="text-muted mt-2" id="copySuccessMessage" style="display: none;"><?= _l('affiliate_management_link_copied'); ?></small>
                            </div>
                        </div>
                        <div>
                            <a href="<?= base_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/join/' . (is_admin() ? $affiliate->affiliate_id : '')); ?>" target="_blank" data-toggle="tooltip" data-title="<?= _l('info'); ?>" type="button" class="btn btn-info tw-rounded-full focus"><i class="fa fa-info"></i></a>

                        </div>
                    </div>
                </div>
                <!-- end affiliate link -->
            <?php endif; ?>

            <?php if ($affiliate->status === AffiliateManagementHelper::STATUS_PENDING) : ?>
                <div class="alert alert-info">
                    <?= _l('affiliate_management_pending_affiliate_note'); ?>
                </div>
            <?php endif; ?>

            <?php if ($affiliate->status === AffiliateManagementHelper::STATUS_INACTIVE) : ?>
                <div class="alert alert-danger">
                    <?= _l('affiliate_management_inactive_affiliate_note'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payouts -->
    <h4 class="tw-mt-8"><?= _l('affiliate_management_payouts'); ?></h4>
    <div class="row">
        <div class="col-md-4 col-md-push-8">
            <div class="panel_s">
                <div class="panel-body">

                    <div class="text-center tw-flex tw-flex-col tw-mb-4">
                        <span class="tw-text-2xl tw-font-bold text-success"><?= app_format_money($affiliate->balance, $currency); ?></span>
                        <span><?= _l('affiliate_management_balance'); ?></span>
                    </div>

                    <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>

                    <?php echo form_open(base_url('clients/' . AFFILIATE_MANAGEMENT_MODULE_NAME . '/payout'), ['id' => 'payout_form']); ?>
                    <?php $affiliate_payout_min = AffiliateManagementHelper::get_payout_min($affiliate); ?>
                    <?= render_input('amount', _l('affiliate_management_amount') . ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l('affiliate_management_payout_min_note', app_format_money($affiliate_payout_min, $currency)) . '"></i>', '', 'number', ['step' => '0.01', 'min' => $affiliate_payout_min, 'max' => $affiliate->balance, 'required' => true]); ?>
                    <?= render_select('payout_method', AffiliateManagementHelper::get_allowed_payout_methods($affiliate), ['key', ['label']], 'affiliate_management_payout_methods', '', [], [], '', '', true); ?>
                    <?= render_textarea('note_for_admin', 'affiliate_management_note_for_admin', '', ['rows' => 2, 'placeholder' => _l('affiliate_management_note_for_admin_placeholder')]); ?>

                    <div class="text-right">
                        <button type="submit" data-loading-text="..." data-form="#affiliates_form" class="btn btn-primary mtop15 mbot15" <?php if (!is_client_logged_in() || $affiliate->status === AffiliateManagementHelper::STATUS_PENDING) echo 'disabled'; ?>><?php echo _l('affiliate_management_request_payout'); ?></button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-md-pull-4">
            <div class="panel_s" style="<?= !is_mobile() ? 'min-height:420px;' : ''; ?>">
                <div class="panel-body panel-table-full">
                    <?php render_datatable($payouts_cols, 'payouts'); ?>
                </div>
            </div>
        </div>

    </div>

    <div class="row tw-mt-4">
        <!-- commission -->
        <div class="col-md-12">
            <h4><?= _l('affiliate_management_commissions'); ?></h4>
            <div class="panel_s">
                <div class="panel-body panel-table-full">
                    <?php render_datatable($commissions_cols, 'commissions'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row tw-mt-4">
        <!-- referrals/commission -->
        <div class="col-md-12">
            <h4><?= _l('affiliate_management_referrals'); ?></h4>
            <div class="panel_s">
                <div class="panel-body panel-table-full">
                    <?php render_datatable($referrals_cols, 'referrals'); ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php get_instance()->load->view('admin/payouts/_payout_action_script'); ?>

<script src="<?= module_dir_url(AFFILIATE_MANAGEMENT_MODULE_NAME, 'assets/js/affiliate.js'); ?>"></script>

<script>
    "use strict";
    document.addEventListener('DOMContentLoaded', function() {

        $(".select-placeholder").removeClass('select-placeholder');

        app.user_language = "<?= get_client_default_language(); ?>";

        initDataTableAffiliate('.table-payouts', window.location.href + "?table=payouts", undefined, [], undefined,
            [<?= count($payouts_cols) - 1; ?>, 'desc']);

        // For this table, set language option with empty infoFiltered. 
        // This is because the CRM does not include $sGroupBy in the total items count query
        initDataTableAffiliate('.table-commissions', window.location.href + "?table=commissions", undefined, [],
            undefined, [<?= count($commissions_cols) - 1; ?>, 'desc'], {
                infoFiltered: ""
            });

        initDataTableAffiliate('.table-referrals', window.location.href + "?table=referrals", undefined, [],
            undefined, [<?= count($referrals_cols) - 1; ?>, 'desc']);

        // Form validation
        appValidateForm($("#payout_form"), {
            amount: "required",
            payout_method: "required"
        });

        $(".slug-update").on('click', function() {
            affiliateToggleEdit();
        });

        $("button.copy").on('click', function() {
            affiliateCopyToClipboard();
        });
    });
</script>