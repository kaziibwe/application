<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$withdraw_allowed = wallet_is_staff_logged_in() || (int)get_option('wallet_allow_withdraw');
$funding_allowed  = wallet_is_staff_logged_in() || (int)get_option('wallet_allow_funding');
$stat_cols = $withdraw_allowed || $funding_allowed ? '3' : '4';
?>

<h3 class="tw-font-semibold tw-mt-0 tw-mb-4 wallet-greetings"><?= _l('wallet_welcome', $contact->firstname); ?></h3>

<div class="row">
    <!-- Column -->
    <?php $stats = [
        ['label' => _l('wallet_total_credited'), 'text' => app_format_money($this->wallet->total_credit($contact_id), $currency), 'class' => 'bg-primary', 'icon' => 'fa fa-plus'],
        ['label' => _l('wallet_total_debited'), 'text' => app_format_money($this->wallet->total_debit($contact_id), $currency), 'class' => 'bg-warning', 'icon' => 'fa fa-minus'],
        ['label' => _l('wallet_balance'), 'text' => app_format_money($this->wallet->balance($contact_id), $currency), 'class' => 'bg-success', 'icon' => 'fa fa-wallet'],
    ]; ?>
    <?php foreach ($stats as $stat) : ?>
        <div class="col-md-<?= $stat_cols; ?>">
            <div class="panel_s">
                <div class="panel-body <?= isset($small_variant) ? 'tw-p-2' : ''; ?>">
                    <div class="tw-flex">
                        <div class="tw-flex tw-items-center"><span class="<?= $stat['class']; ?> <?= isset($small_variant) ? 'tw-px-3 tw-py-2' : 'tw-px-5 tw-py-4'; ?> tw-rounded-full"><i class="<?= $stat['icon']; ?>"></i></span></div>

                        <div class="tw-ml-4 align-self-center">
                            <h2 class="tw-mb-0 <?= isset($small_variant) ? 'tw-text-2xl' : ''; ?>">
                                <?= $stat['text']; ?>
                            </h2>
                            <h5 class="text-muted m-b-0"><?= $stat['label']; ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Column -->
    <?php if ($withdraw_allowed || $funding_allowed) : ?>
        <div class="col-md-<?= $stat_cols; ?>">
            <div class="panel_s">
                <div class="panel-body d-block d-md-flex justify-content-between  <?= isset($small_variant) ? 'tw-p-2' : ''; ?>" style="gap: 30px;">
                    <?= form_open(is_client_logged_in() ? base_url('wallet/transact') : base_url('wallet/transact/' . $contact_id), ['method' => 'POST', 'id' => 'wallet-form', 'class' => 'align-self-center w-full']) ?>
                    <div class="form-group m-y-20">
                        <div class="tw-flex tw-w-full tw-gap-2">
                            <div class="">
                                <select class="form-control tw-px-1" name="transaction_type" id="trans_type">
                                    <?php if ($funding_allowed) : ?>
                                        <option value="fund"><?= _l('wallet_fund'); ?></option>
                                    <?php endif; ?>
                                    <?php if ($withdraw_allowed) : ?>
                                        <option value="withdraw"><?= _l('wallet_withdraw'); ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="tw-w-full">
                                <input type="number" step="0.01" min="<?= $min_funding_amount; ?>" <?= $max_funding_amount != -1 ? 'max="' . $max_funding_amount . '"' : ''; ?> name="amount" id="amount" value="" required placeholder="<?= _l('wallet_amount'); ?> (<?= $currency_code; ?>)" class="form-control">
                            </div>
                        </div>

                    </div>
                    <?php if ($funding_allowed) : ?>
                        <button type="button" onclick="submitWalletForm()" class="btn btn-primary tw-w-full submitBtn fund">
                            <i class="fa fa-plus tw-mr-2"></i> <?= _l('wallet_add_fund'); ?>
                        </button>
                    <?php endif; ?>
                    <?php if ($withdraw_allowed) : ?>
                        <a href="#" class="btn btn-danger submitBtn withdraw tw-w-full" data-toggle="modal" data-target="#withdrawal-modal" <?php if ($funding_allowed) echo 'style="display: none;"'; ?>>
                            <i class="fa fa-minus tw-mr-2"></i> <?= _l('wallet_withdraw'); ?>
                        </a>
                    <?php endif; ?>

                    <!-- withdrawal modal-->
                    <div class="modal fade email-template" id="withdrawal-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">
                                        <?php echo _l('wallet_withdraw_info_title'); ?>
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><?= _l('wallet_withdraw_info_method'); ?></label>
                                                <select name="metadata[withdraw_method]" class="form-control">
                                                    <?php
                                                    $methods = trim(get_option('wallet_withdrawal_methods') ?? '');
                                                    $methods = empty($methods) ? 'Paypal,Bank' : $methods;
                                                    $methods = explode(',', $methods);
                                                    foreach ($methods as $method) {
                                                        $method = trim($method);
                                                        if (empty($method)) continue;
                                                        echo "<option>$method</option>";
                                                    }; ?>
                                                </select>
                                            </div>
                                            <?php echo render_textarea('metadata[withdraw_note]', _l('wallet_withdraw_info_details'), '', ['placeholder' => _l('wallet_withdraw_info_placeholder')]); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                    <button type="button" onclick="submitWalletForm()" class="btn btn-danger submitBtn withdraw">
                                        </i> <?= _l('submit'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 section-heading section-heading-invoices">
    <?php echo _l('wallet_transactions'); ?>
</h4>
<div class="panel_s">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 scroll table-responsive p-0">
                <table class="table dt-table table-invoices" data-order-col="5" data-order-type="desc">
                    <thead>
                        <tr>
                            <th><?= _l('invoice'); ?></th>
                            <th><?= _l('wallet_amount'); ?> ( <?= $currency->symbol; ?> )</th>
                            <th><?= _l('wallet_transaction_mode'); ?></th>
                            <th><?= _l('wallet_transaction_tag'); ?></th>
                            <th><?= _l('wallet_transcation_description'); ?></th>
                            <th><?= _l('date_created'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $reversal_enabled = get_option('wallet_enabled_reversal') != '0'; ?>
                        <?php foreach ($transactions as $key => $trans) :
                            $trans->metadata = empty($trans->metadata) ? [] : json_decode($trans->metadata, true);
                            $invoice_number = format_invoice_number($trans->invoice_id);
                        ?>
                            <tr>
                                <td>
                                    <?php if (!empty($invoice_number)) : ?>
                                        <a target="_blank" href="<?= !empty($trans->invoice_id) ? site_url(WALLET_MODULE_NAME . '/invoice/' . $trans->invoice_id) : '#'; ?>"><?= format_invoice_number($trans->invoice_id); ?></a>
                                    <?php else : ?>
                                        <?= ($trans->metadata['invoice_number'] ?? '') . ($trans->tag == Walletmanager::TAG_SYSTEM || $trans->mode == 'log' ? '-' : ' (' . trim(_l('deleted', '')) . ')'); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= $trans->amount; ?></td>
                                <td>
                                    <span class="badge bg-<?= $trans->mode == 'credit' ? 'success' : ($trans->mode == 'debit' ? 'danger' : 'warning'); ?>"><?= $trans->mode; ?></span>
                                    <?php if ($trans->mode === 'log' && $trans->tag === Walletmanager::TAG_FUNDING) : ?>
                                        <a class="tw-ml-2 text-danger" data-toggle="tooltip" title="<?= _l('wallet_revalidate_funding'); ?>" href="<?= site_url(WALLET_MODULE_NAME . '/revalidate_funding/' . $trans->id); ?>">
                                            <i class="fa fa-refresh"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= _l('wallet_tag_' . $trans->tag); ?>
                                    <?php
                                    if ($trans->tag === Walletmanager::TAG_CANCELLED_WITHDRAWAL && !empty($trans->metadata['withdraw_admin_note'])) {
                                        $admin_note = $trans->metadata['withdraw_admin_note'];
                                        if (!empty($admin_note)) {
                                    ?>
                                            <span class='tw-ml-2' data-title='<?= $admin_note; ?>' data-toggle='tooltip'><i class='fa fa-bell text-danger'></i></span>
                                    <?php }
                                    }; ?>
                                </td>
                                <td>
                                    <?= wallet_get_transaction_description($trans); ?>
                                    <?php if (!empty($trans->metadata['withdraw_method'])) : ?>
                                        <span data-toggle="tooltip" data-title="<?= $trans->metadata['withdraw_method']; ?> <?= empty($trans->metadata['withdraw_note']) ? '' : ' | ' . $trans->metadata['withdraw_note']; ?> <?= empty($trans->metadata['withdraw_admin_note']) ? '' : ' | ' . $trans->metadata['withdraw_admin_note']; ?>"><i class="fa fa-exclamation-circle"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $trans->created_at; ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    function submitWalletForm() {

        const form = $("#wallet-form");
        const amount = parseFloat($("#amount").val());
        const submitButton = $(".submitBtn");
        const currency_code = "<?= $currency_code; ?>";
        const transaction_type = $("#trans_type").val();
        const minFundingAmount = <?= $min_funding_amount; ?>;
        const maxFundingAmount = <?= $max_funding_amount; ?>;

        if (!amount || amount < minFundingAmount || (maxFundingAmount !== -1 && amount > maxFundingAmount)) {
            alert_float('danger',
                "<?= $max_funding_amount == -1 ?   _l('wallet_invalid_funding_amount_min', [$min_funding_amount, $currency_code]) :  _l('wallet_invalid_funding_amount_range', [$min_funding_amount, $max_funding_amount, $currency_code]) ?>"
            );
            return;
        }

        const withdraw_note = $("[name='metadata[withdraw_note]']").val();
        if (transaction_type === 'withdraw' && !withdraw_note?.length) {
            alert_float('danger', "<?= _l('wallet_withdrawal_note_required'); ?>");
            return;
        }


        if (transaction_type === 'fund') {
            if (confirm('<?= _l("wallet_funding_confirm_notice"); ?>')) {
                submitButton.attr('disabled', 'disabled');
                form.submit();
            }
            return;
        }

        submitButton.attr('disabled', 'disabled');
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        $("[name='transaction_type']").on('change', function() {
            let trans_type = $(this).val();
            $(`.submitBtn:not(.${trans_type})`).hide();
            $(`.submitBtn.${trans_type}`).show();
        })
    });
</script>