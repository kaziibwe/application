<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo _l('connect_whatsapp_business'); ?>
                </h4>
                <h4 class="tw-mt-0 tw-font-semibold tw-text-sm text-muted">
                    <?php echo _l('connect_your_whatsapp_account'); ?>
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?php echo form_open('', ['id' => 'connect_whatsapp_form'], []); ?>
                <div class="panel_s mbot20">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="text-success no-margin"><?php echo _l('whatsapp'); ?></h3>
                                <p><span class="badge rounded-circle bg-success tw-mt-0.5 tw-mr-1"> </span>
                                    <?php echo _l('one_click_account_connection'); ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row mtop15">
                                    <div class="col-md-12">
                                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="<?php echo _l('business_account_id_description'); ?>" data-placement="left"></i>
                                        <?php echo render_input('wac_business_account_id', _l('whatsapp_business_account_id'), get_option('wac_business_account_id')); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="<?php echo _l('access_token_description'); ?>"></i>
                                        <?php echo render_input('wac_access_token', _l('whatsapp_access_token'), get_option('wac_access_token')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5><?php echo _l('webhook_callback_url'); ?></h5>
                                <div class="tw-break-words">
                                    <a href="<?php echo site_url('whatsbot/whatsapp_webhook'); ?>" class="copyText"><?php echo site_url('whatsbot/whatsapp_webhook'); ?></a>
                                    <span class="badge rounded-circle tw-mt-0.5 tw-mr-1 pull-right btn copyBtn"><?php echo _l('copy'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h5><?php echo _l('verify_token'); ?></h5>
                                <span class="copyText"><?php echo get_option('wac_verify_token'); ?></span>
                                <span class="badge rounded-circle tw-mt-0.5 tw-mr-1 pull-right btn copyBtn"><?php echo _l('copy'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <?php if (!$is_connected) { ?>
                            <button type="submit" name="submit" value="submit" class="btn btn-success" id="submitForm"><i class="fa-solid fa-link tw-mr-1"></i><?php echo _l('connect'); ?></button>
                        <?php } else { ?>
                            <button type="submit" name="submit" value="update" class="btn btn-success"><i class="fa-regular fa-pen-to-square tw-mr-1"></i><?php echo _l('update_details'); ?></button>
                            <button type="submit" class="btn btn-danger" formaction="<?php echo admin_url('whatsbot/disconnect'); ?>"><i class="fa-solid fa-link-slash tw-mr-1"></i><?php echo _l('disconnect'); ?></button>
                        <?php } ?>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="col-md-6">
                <?php foreach ($phone_numbers as $phone) {
                    $isDefault = ($phone->id == get_option('wac_phone_number_id')); ?>
                    <div class="panel <?php echo ($isDefault) ? 'panel-success' : 'panel-info'; ?>">
                        <div class="panel-heading">
                            <span>
                                <i class="fa-solid fa-phone tw-mr-1"></i>
                                <strong><?php echo $phone->display_phone_number; ?></strong>
                            </span>
                        </div>
                        <div class="panel-body">
                            <p><i class="fa-solid fa-address-book tw-mr-1 text-info"></i><strong><?php echo _l('verified_name'); ?>
                                    :</strong> <?php echo $phone->verified_name; ?></p>
                            <p><i class="fa-regular fa-circle-check tw-mr-1 text-success"></i><strong><?php echo _l('number_id'); ?>
                                    :</strong> <?php echo $phone->id; ?></p>
                            <p><i class="fa-regular fa-star tw-mr-1 text-success"></i><strong><?php echo _l('quality'); ?>
                                    :</strong><span> <?php echo $phone->quality_rating; ?></span></p>
                            <p><i class="fa-solid fa-spinner tw-mr-1 text-warning"></i><strong><?php echo _l('status'); ?>
                                    :</strong>
                                <span> <?php echo $phone->code_verification_status; ?></span>
                            </p>
                        </div>
                        <div class="panel-footer">
                            <?php if ($isDefault) { ?>
                                <span class="label label-success"><?php echo _l('currently_using_this_number'); ?></span>
                            <?php } else { ?>
                                <a href="#" class="btn btn-info mark_as_default" data-phone_number_id="<?php echo $phone->id; ?>" data-default-phone-number="<?php echo $phone->display_phone_number; ?>">
                                    <i class="fa-solid fa-check tw-mr-1"></i>
                                    <?php echo _l('mark_as_default'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php
                } ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    "use strict";
    $(function() {
        $('.copyBtn').on('click', function() {
            var textToCopy = $(this).prev('.copyText').text();
            var tempInput = $('<textarea>');
            tempInput.val(textToCopy);
            $('body').append(tempInput);
            tempInput.select();
            tempInput[0].setSelectionRange(0, 99999);
            document.execCommand('copy');
            tempInput.remove();
            $(this).text('<?php echo _l('copied'); ?>');
            setTimeout(() => {
                $(this).text('<?php echo _l('copy'); ?>');
            }, 1000);
        });

        $('.mark_as_default').on('click', function() {
            $.ajax({
                url: `${admin_url}whatsbot/set_default_number_phone_number_id`,
                data: {
                    wac_phone_number_id: $(this).data('phone_number_id'),
                    wac_default_phone_number: $(this).data('default-phone-number')
                },
                dataType: 'json',
                type: 'POST'
            }).done(function(res) {
                location.reload();
            });
        });

    });
</script>
