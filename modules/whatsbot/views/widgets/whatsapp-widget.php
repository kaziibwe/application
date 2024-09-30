<?php

$total_templates         = total_rows(db_prefix() . 'wtc_templates');
$total_approved_template = total_rows(db_prefix() . 'wtc_templates', ['status' => 'APPROVED']);
$total_leads             = total_rows(db_prefix() . 'leads');
$current_month_leads     = total_rows(db_prefix() . 'leads', ['MONTH(dateadded)' => date('m')]);
$message_bots            = total_rows(db_prefix() . 'wtc_bot');
$template_bots           = total_rows(db_prefix() . 'wtc_campaigns', ['is_bot' => 1]);
$total_bots              = $message_bots + $template_bots;
$total_contacts          = total_rows(db_prefix() . 'contacts');
$current_month_contacts  = total_rows(db_prefix() . 'contacts', ['MONTH(datecreated)' => date('m')]);
$total_message_bot_send = sum_from_table(db_prefix() . 'wtc_bot', ['field' => 'sending_count']);
$total_template_bot_send = sum_from_table(db_prefix() . 'wtc_campaigns', ['field' => 'sending_count', 'where' => ['is_bot' => 1]]);
$total_bot_send = $total_message_bot_send + $total_template_bot_send;

?>
<div class="widget relative mtop10 mbot10" id="widget-<?php echo basename(__FILE__, '.php'); ?>" data-name="<?php echo _l('whatsbot_stats'); ?>">
    <div class="widget-dragger"></div>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-default rounded-lg shadow-lg no-margin">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-8">
                            <span class="text-muted tw-font-semibold text-uppercase"><?php echo _l('templates'); ?></span>
                            <h4 class="tw-mt-2 tw-mb-0 tw-font-semibold tw-truncate"><?php echo $total_templates; ?>
                            </h4>
                            <p class="tw-mt-2 tw-mb-0 "><span class="tw-font-semibold"><?php echo $total_approved_template; ?></span>
                                <?php echo _l('approved'); ?></p>
                        </div>
                        <span class="circle numbertext circle_warning"><i class="fa-solid fa-scroll fa-xl"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-default rounded-lg shadow-lg no-margin">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-8">
                            <span class="text-muted tw-font-semibold text-uppercase"><?php echo _l('leads'); ?></span>
                            <h4 class="tw-mt-2 tw-mb-0 tw-font-semibold tw-truncate"><?php echo $total_leads; ?></h4>
                            <p class="tw-mt-2 tw-mb-0"><span class="tw-font-semibold"><?php echo $current_month_leads; ?></span><?php echo ' ' . _l('this_month'); ?>
                            </p>
                        </div>
                        <span class="circle numbertext circle_info"><i class="fa-solid fa-user fa-xl"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-default rounded-lg shadow-lg no-margin">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-8">
                            <span class="text-muted tw-font-semibold text-uppercase"><?php echo _l('contacts'); ?></span>
                            <h4 class="tw-mt-2 tw-mb-0 tw-font-semibold tw-truncate"><?php echo $total_contacts; ?></h4>
                            <p class="tw-mt-2 tw-mb-0"><span class="tw-font-semibold"><?php echo $current_month_contacts; ?></span><?php echo ' ' . _l('this_month'); ?>
                            </p>
                        </div>
                        <span class="circle numbertext circle_success"><i class="fa-solid fa-check fa-xl"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-default rounded-lg shadow-lg no-margin">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-8">
                            <span class="text-muted tw-font-semibold text-uppercase"><?php echo _l('bots'); ?></span>
                            <h4 class="tw-mt-2 tw-mb-0 tw-font-semibold tw-truncate"><?php echo $total_bots; ?></h4>
                            <p class="tw-mt-2 tw-mb-0"><span class="tw-font-semibold"><?php echo $total_bot_send; ?></span>
                                <?php echo _l('messages_sent'); ?></p>
                        </div>
                        <span class="circle numbertext circle_default"><i class="fa-solid fa-comment fa-xl"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
