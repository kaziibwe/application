<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <!-- Column -->
            <?php $stats = [
                ['label' => _l('wallet_total_credited'), 'text' => app_format_money($total_credited, $currency), 'class' => 'bg-primary', 'icon' => 'fa fa-plus'],
                ['label' => _l('wallet_total_debited'), 'text' => app_format_money($total_debited, $currency), 'class' => 'bg-warning', 'icon' => 'fa fa-minus'],
                ['label' => _l('wallet_total_balance'), 'text' => app_format_money($total_balance, $currency), 'class' => 'bg-success', 'icon' => 'fa fa-wallet'],
            ]; ?>
            <?php foreach ($stats as $stat) : ?>
                <div class="col-md-4">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="tw-flex">
                                <div class="tw-flex tw-items-center"><span class="<?= $stat['class']; ?> tw-px-5 tw-py-4 tw-rounded-full"><i class="<?= $stat['icon']; ?>"></i></span></div>

                                <div class="tw-ml-4 align-self-center">
                                    <h2 class="tw-mb-0">
                                        <?= $stat['text']; ?>
                                    </h2>
                                    <h5 class="text-muted m-b-0"><?= $stat['label']; ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="row">

            <?php if ($pending_withdrawals > 0) : ?>
                <div class="col-md-12 withdraws">
                    <div class="alert alert-danger">
                        <?php echo _l('wallet_pending_withdrawals', $pending_withdrawals); ?>
                    </div>
                    <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 section-heading section-heading-invoices">
                        <?php echo _l('wallet_withdrawal_requests'); ?>
                    </h4>
                    <div class="panel_s">
                        <div class="panel-body panel-table-full">
                            <?php render_datatable([
                                _l('id'),
                                _l('conatact'),
                                _l('wallet_amount') . ' ( ' . $currency->symbol . ' )',
                                _l('wallet_withdraw_info_method'),
                                _l('wallet_withdraw_info_details'),
                                _l('date_created'),
                                _l('action'),
                            ], 'withdrawals'); ?>
                        </div>
                    </div>
                </div>
                <?php require_once('includes/withdraw_script.php'); ?>
            <?php endif; ?>

            <div class="col-md-4">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 section-heading section-heading-invoices">
                    <?php echo _l('wallet'); ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php render_datatable([
                            _l('contact'),
                            _l('wallet_balance') . ' ( ' . $currency->symbol . ' )',
                            _l('wallet_updated_at'),
                        ], 'wallets'); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 section-heading section-heading-invoices">
                    <?php echo _l('wallet_transactions'); ?>
                </h4>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php render_datatable([
                            _l('invoice'),
                            _l('company'),
                            _l('wallet_amount') . ' ( ' . $currency->symbol . ' )',
                            _l('wallet_transaction_mode'),
                            _l('wallet_transaction_tag'),
                            _l('description'),
                            _l('date_created'),
                        ], 'wallet_transaction'); ?>
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
        initDataTable('.table-wallet_transaction', window.location.href, undefined, undefined, undefined, [6,
            "desc"
        ]);
        initDataTable('.table-wallets', '<?= admin_url(WALLET_MODULE_NAME . '/wallet_admin/index/wallets'); ?>',
            undefined, undefined, undefined, [
                2,
                "desc"
            ]);
        initDataTable('.table-withdrawals',
            '<?= admin_url(WALLET_MODULE_NAME . '/wallet_admin/index/withdrawals'); ?>',
            undefined, undefined, undefined, [
                4,
                "desc"
            ]);
    });
</script>
</body>

</html>