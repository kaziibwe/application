<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$CI->load->model('emails_model');
$email_templates = $CI->emails_model->get(["`slug` LIKE" => "affiliate_management%", 'language' => 'english'], 'result');
?>

<div class="tw-flex tw-flex-col">

    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
        </div>
        <div>
            <a class="btn btn-danger"
                href="<?= admin_url('settings?group=payment_gateways&tab=online_payments_' . AFFILIATE_MANAGEMENT_MODULE_NAME . '_gateway_tab'); ?>">
                <?= _l(AFFILIATE_MANAGEMENT_MODULE_NAME . '_gateway_settings'); ?>
            </a>
        </div>
    </div>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>

    <?php $key = 'affiliate_management_affiliate_model'; ?>
    <?php $label = _l($key) . ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l($key . '_hint') . '"></i>'; ?>
    <?= render_select("settings[$key]", AffiliateManagementHelper::get_affiliate_models(), ['key', ['label']], $label, AffiliateManagementHelper::get_option($key),  [], [], '', '', false); ?>

    <?php $key = 'affiliate_management_auto_approve_signup'; ?>
    <?= render_yes_no_option($key, _l($key), _l($key . '_hint')); ?>


    <?php $key = 'affiliate_management_save_referral_client_info'; ?>
    <?= render_yes_no_option($key, _l($key), $key . '_hint'); ?>

    <?php
    $key = 'affiliate_management_join_page_content';
    $default = AffiliateManagementHelper::default_affiliate_page();
    $value = AffiliateManagementHelper::get_option($key);
    $value = empty($value) ? $default['content'] : $value;
    ?>
    <?= render_textarea("settings[$key]", _l($key), $value, ['rows' => 20], [], 'tinymce tinymce-manual'); ?>

    <div class="tw-flex tw-justify-between">
        <div class=""><?= _l('tags'); ?>: <?= implode(", ", array_keys($default['tags'])); ?></div>
        <div class="tw-flex">
            <button class="btn btn-default tw-mr-2" type="button" onclick="affiliateSwitchEditor();"><i
                    class="fa fa-code"></i></button>
            <button class="btn btn-danger" type="button"
                onclick="affiliateResetJoinPageContent();"><?= _l('reset'); ?></button>
        </div>
    </div>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <?php $key = 'affiliate_management_show_commission_info_on_referral_table';
    echo render_yes_no_option($key, $key);
    ?>
    <?php
    $key = 'affiliate_management_commission_date_format';
    $label = $key;
    $value = AffiliateManagementHelper::get_option($key);
    $value = empty($value) ? 'time_ago' : $value;
    echo render_select("settings[$key]", [
        [
            'key' => 'datetime',
            'label' => _l('datetime', false),
        ],
        [
            'key' => 'date',
            'label' => _l('date', false),
        ],
        [
            'key' => 'time_ago',
            'label' => _l('time_ago', false),
        ]
    ], ['key', ['label']], $label, $value,  [], [], '', '', false);
    ?>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <?php 
    $key = 'affiliate_management_enable_referral_removal';
    echo render_yes_no_option($key, $key);
    echo '<small class="text-danger">'._l($key.'_hint').'</small>';
    ?>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <div class="">
        <label><?= _l('email_templates'); ?></label>
        <ul class="tw-mt-4">
            <?php foreach ($email_templates as $t) : ?>
            <li class="tw-mb-2">
                <a href="<?= admin_url('emails/email_template/' . $t->emailtemplateid); ?>" target="_blank">
                    <i class="fa fa-pen"></i> <?= $t->name ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
function affiliateSwitchEditor() {
    if (tinymce.activeEditor) {
        tinymce.activeEditor.destroy();
    } else {
        init_editor('[name="settings[affiliate_management_join_page_content]"]');
    }
}

function affiliateResetJoinPageContent() {
    if (confirm("<?= _l('confirm_action_prompt'); ?>")) {
        tinymce?.activeEditor?.setContent(`<?= addslashes($default['content']); ?>`);
    }
}
</script>