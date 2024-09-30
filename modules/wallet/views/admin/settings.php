<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="tw-flex tw-flex-col">

    <div class="tw-w-full text-right">
        <a class="btn btn-danger" href="<?= admin_url('settings?group=payment_gateways&tab=online_payments_' . WALLET_MODULE_GATEWAY_NAME . '_tab'); ?>">
            <?= _l(WALLET_MODULE_NAME . '_gateway_settings'); ?>
        </a>
    </div>

    <!-- min and max funding limit -->
    <?php foreach (['wallet_min_funding_amount', 'wallet_max_funding_amount'] as $key) {
        echo render_input('settings[' . $key . ']', $key == 'wallet_max_funding_amount' ? _l($key) . '<span class="tw-ml-2"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l($key . '_hint') . '"></i></span>' : $key, get_option($key), 'number');
    } ?>

    <?php $key = 'wallet_initial_credit_amount';
    $label = _l($key) . '<span class="tw-ml-2"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l($key . '_hint') . '"></i></span>';
    echo render_input('settings[' . $key . ']', $label, get_option($key), 'number', ['step' => 0.01]); ?>


    <!-- Taxes for founding invoice --->
    <div class="form-group">
        <label><?= _l('invoice_table_tax_heading'); ?></label>
        <?php
        $default_tax = get_option('wallet_taxname');
        $default_tax = empty($default_tax) ? [] : (array)json_decode($default_tax);
        $select      = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="settings[wallet_taxname][]" multiple data-none-selected-text="' . _l('no_tax') . '">';
        foreach ($taxes as $tax) {
            $selected = '';
            if (is_array($default_tax)) {
                if (in_array($tax['name'] . '|' . $tax['taxrate'], $default_tax)) {
                    $selected = ' selected ';
                }
            }
            $select .= '<option value="' . $tax['name'] . '|' . $tax['taxrate'] . '"' . $selected . 'data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
        }
        $select .= '</select>';
        echo $select;
        ?>
    </div>

    <!-- Payment gateway for wallet funding -->
    <div class="form-group tw-mb-8 <?= !empty($all_payment_modes) ? ' select-placeholder' : ''; ?>">
        <label for="allowed_payment_modes" class="control-label"><?= _l('wallet_funding_allowed_payment_modes'); ?></label>
        <?php
        $wallet_allowed_payment_modes = get_option('wallet_allowed_payment_modes');
        $wallet_allowed_payment_modes = empty($wallet_allowed_payment_modes) ? [] : (array)json_decode($wallet_allowed_payment_modes);
        $all_payment_modes = get_instance()->payment_modes_model->get();
        $select = '<select class="selectpicker display-block" data-actions-box="true" data-width="100%" name="settings[wallet_allowed_payment_modes][]" multiple data-none-selected-text="' . _l('dropdown_non_selected_tex') . '">';
        foreach ($all_payment_modes as $key => $pmode) {
            if ($pmode['id'] === WALLET_MODULE_GATEWAY_NAME) continue;
            $selected = (empty($wallet_allowed_payment_modes) && $pmode['selected_by_default'] == 1) || in_array($pmode['id'], $wallet_allowed_payment_modes ?? []) ? ' selected' : '';
            $select .= '<option value="' . $pmode['id'] . '"' . $selected . '>' . $pmode['name'] . '</option>';
        }
        $select .= '</select>';
        echo $select; ?>
    </div>

    <!-- Auto charging wallet for overdue invoice setting -->
    <?php $key = 'wallet_enable_overdue_invoice_auto_payment';
    render_yes_no_option($key, _l($key), _l($key . '_hint')); ?>

    <!-- Payment reversal -->
    <?php $key = 'wallet_enabled_reversal';
    render_yes_no_option($key, _l($key), _l($key . '_hint')); ?>

    <div class="tw-my-4">
        <hr />
    </div>
    <!-- Withdraw option -->
    <?php $key = 'wallet_allow_withdraw';
    render_yes_no_option($key, _l($key)); ?>

    <?php $key = 'wallet_withdrawal_methods';
    $label = _l($key) . '<span class="tw-ml-2"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l($key . '_hint') . '"></i></span>';
    echo render_input($key, $label, get_option($key)); ?>

    <div class="tw-my-4">
        <hr />
    </div>
    <!-- Funding option -->
    <?php $key = 'wallet_allow_funding';
    render_yes_no_option($key, _l($key)); ?>
</div>