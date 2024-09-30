<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$currency = get_base_currency();
$settings = $group['settings'];
$true_false_options = [
    ['key' => '1', 'label' => _l('settings_yes')],
    ['key' => '0', 'label' => _l('settings_no')],
];
?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="tw-mb-2 sm:tw-mb-4">
                    <h4><?= $title; ?></h4>
                </div>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php echo form_open(uri_string(), ['method' => 'POST', 'id' => 'affiliate-groups']); ?>
                        <div class="tw-flex tw-flex-col">

                            <?= render_input('name', _l('affiliate_mangement_group_name'), $group['name'] ?? '', 'text', ['required' => true]); ?>

                            <div class="tw-mt-4 tw-mb-4">
                                <hr />
                            </div>

                            <?php $key = 'affiliate_management_commission_enabled'; ?>
                            <?= render_select("settings[$key]", $true_false_options, ['key', ['label']], _l($key), $settings[$key]); ?>

                            <?php $key = 'affiliate_management_commission_rule'; ?>
                            <?= render_select("settings[$key]", AffiliateManagementHelper::get_commission_rules(), ['key', ['label']], _l($key), $settings[$key] ?? '',  [], [], '', '', false); ?>

                            <?php $key = 'affiliate_management_commission_type'; ?>
                            <?= render_select("settings[$key]", AffiliateManagementHelper::get_commission_types(), ['key', ['label']], _l($key), $settings[$key] ?? '', [], [], '', '', false); ?>

                            <?php $key = 'affiliate_management_commission_amount'; ?>
                            <?= render_input("settings[$key]", _l($key), $settings[$key] ?? '', 'number', ['step' => '0.1']); ?>

                            <?php $key = 'affiliate_management_commission_cap'; ?>
                            <?php $label = _l($key) . "({$currency->symbol})" . ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l($key . '_hint') . '"></i>'; ?>
                            <?= render_input("settings[$key]", $label, $settings[$key] ?? '', 'number', ['step' => '0.1']); ?>

                            <div class="tw-mt-4 tw-mb-4">
                                <hr />
                            </div>

                            <?php $key = 'affiliate_management_payout_min'; ?>
                            <?= render_input("settings[$key]", _l($key), $settings[$key] ?? '', 'number', ['step' => '0.1']); ?>

                            <?php $key = 'affiliate_management_payout_methods';
                            $label = _l($key) . '<span class="tw-ml-2"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l($key . '_hint') . '"></i></span>';
                            echo render_input("settings[$key]", $label, $settings[$key] ?? '', 'text', ['required' => true]); ?>
                        </div>
                        <div class="text-right mtop15">
                            <button type="submit" autocomplete="off" data-form="#affiliate-groups"
                                data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-primary">
                                <?php echo _l('submit'); ?>
                            </button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<script>
appValidateForm($("#affiliate-groups"), {
    name: "required",
    "settings[affiliate_management_payout_methods]": "required",
});
</script>
</body>

</html>