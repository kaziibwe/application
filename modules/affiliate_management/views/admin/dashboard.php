<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $allowed_referral_client_info = AffiliateManagementHelper::get_option('affiliate_management_save_referral_client_info') == '1'; ?>
<div id="wrapper">
    <div class="content">

        <div class="row tw-mb-8">

            <?php if ($pending_affiliates > 0) : ?>
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <?= _l('affiliate_management_pending_request_note', _l('affiliate_management_affiliate')); ?>
                    <a
                        href="<?= admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/affiliates'); ?>"><?= _l('affiliate_management_click_here_to_review'); ?></a>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($pending_payouts > 0) : ?>
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <?= _l('affiliate_management_pending_request_note', _l('affiliate_management_payout')); ?>
                    <a
                        href="<?= admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/payouts'); ?>"><?= _l('affiliate_management_click_here_to_review'); ?></a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php foreach ($stats as $stat) : ?>
            <div class="col-sm-6 col-md-3 tw-mb-2">
                <?php $tag = isset($stat['url']) ? 'a' : 'div'; ?>
                <<?= $tag; ?> <?= ($tag == 'a')   ? 'href="' . $stat['url'] . '"' : ''; ?>
                    class="tw-block top_stats_wrapper  <?= $stat['class'] ?? ''; ?>"
                    style="<?= $stat['style'] ?? ''; ?>">
                    <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                        <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center mr-2"> <i
                                class="<?= $stat['icon']; ?> tw-mr-2"></i> <?= $stat['label']; ?>
                        </div>
                        <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"><?= $stat['value']; ?></span>
                    </div>
                </<?= $tag; ?>>
            </div>
            <?php endforeach ?>
        </div>

        <div class="row tw-mt-8">

            <!-- top affiliates -->
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?= _l('affiliate_management_top_affiliates'); ?></h4>
                        <table class="table table-stripped">
                            <thead>
                                <tr>
                                    <th><?= _l('affiliate_management_affiliate_id'); ?></th>
                                    <th><?= _l('name'); ?></th>
                                    <th><?= _l('affiliate_management_total_referrals'); ?></th>
                                    <th><?= _l('affiliate_management_lifetime_earnings'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_affiliates as $affiliate) : ?>
                                <tr>
                                    <td><?= $affiliate->affiliate_slug; ?></td>
                                    <td><a
                                            href="<?= admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . "/view_affiliate/{$affiliate->affiliate_id}"); ?>"><?= $affiliate->name; ?></a>
                                    </td>
                                    <td><?= $affiliate->total_referrals; ?></td>
                                    <td><?= app_format_money($affiliate->total_earnings, $currency); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($top_affiliates)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center"><?= _l('affiliate_management_empty_data'); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- latest referrals -->
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?= _l('affiliate_management_latest_referrals'); ?></h4>
                        <table class="table table-stripped">
                            <thead>
                                <tr>
                                    <th><?= _l('affiliate_management_company'); ?></th>
                                    <?php if ($allowed_referral_client_info) : ?>
                                    <th><?= _l('affiliate_management_ip'); ?></th>
                                    <th><?= _l('affiliate_management_ua'); ?></th>
                                    <?php endif ?>
                                    <th><?= _l('affiliate_management_affiliate'); ?></th>
                                    <th><?= _l('date_created'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest_referrals as $referral) : ?>
                                <tr>
                                    <td>
                                        <a
                                            href="<?= admin_url('clients/client/' . $referral->userid); ?>"><?= $referral->company; ?></a>
                                    </td>
                                    <?php if ($allowed_referral_client_info) : ?>
                                    <td><?= $referral->ip; ?></td>
                                    <td><?= $referral->ua ?></td>
                                    <?php endif ?>
                                    <td>
                                        <a
                                            href="<?= admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/view_affiliate/' . $referral->affiliate_id); ?>"><?= $referral->affiliate; ?></a>
                                    </td>
                                    <td><?= $referral->created_at; ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($latest_referrals)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center"><?= _l('affiliate_management_empty_data'); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
    initDataTable('.table-affiliates', "<?= admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/affiliates'); ?>",
        undefined, [], undefined);
});
</script>
</body>

</html>