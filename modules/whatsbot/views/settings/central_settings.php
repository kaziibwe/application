<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<div class="horizontal-scrollable-tabs panel-full-width-tabs">
    <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
    <div class="horizontal-tabs">
        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
            <li role="presentation" class="active">
                <a href="#whatsapp_auto_lead" aria-controls="whatsapp_auto_lead" role="tab" data-toggle="tab">
                    <?php echo _l('whatsapp_auto_lead'); ?>
                </a>
            </li>
            <li role="presentation" class="">
                <a href="#webhooks" aria-controls="webhooks" role="tab" data-toggle="tab">
                    <?php echo _l('webhooks'); ?>
                </a>
            </li>
            <li role="presentation" class="">
                <a href="#supportagent" aria-controls="supportagent" role="tab" data-toggle="tab">
                    <?php echo _l('supportagent'); ?>
                </a>
            </li>
            <li role="presentation" class="">
                <a href="#notification_sound" aria-controls="notification_sound" role="tab" data-toggle="tab">
                    <?php echo _l('notification_sound'); ?>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="tab-content mtop15">
    <div role="tabpanel" class="tab-pane active" id="whatsapp_auto_lead">
        <div class="mbot15">
            <label for="whatsapp_auto_lead_settings"><?php echo _l('convert_whatsapp_message_to_lead'); ?></label>
            <div class="onoffswitch">
                <input type="checkbox" value="1" class="onoffswitch-checkbox" id="whatsapp_auto_lead_settings" name="settings[whatsapp_auto_lead_settings]" <?php echo ('1' == get_option('whatsapp_auto_lead_settings')) ? 'checked' : ''; ?>>
                <label class="onoffswitch-label" for="whatsapp_auto_lead_settings"></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php echo render_select('settings[whatsapp_auto_leads_status]', $leads_statuses, ['id', 'name'], 'leads_status', get_option('whatsapp_auto_leads_status'), [], [], '', '', false); ?>
            </div>
            <div class="col-md-4">
                <?php echo render_select('settings[whatsapp_auto_leads_source]', $leads_sources, ['id', 'name'], 'leads_source', get_option('whatsapp_auto_leads_source'), [], [], '', '', false); ?>
            </div>
            <div class="col-md-4">
                <?php echo render_select('settings[whatsapp_auto_leads_assigned]', wb_get_all_staff(), ['staffid', ['firstname', 'lastname']], 'leads_assigned', get_option('whatsapp_auto_leads_assigned'), [], [], '', '', false); ?>
            </div>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="webhooks">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="enable_webhooks"><?php echo _l('enable_webhooks'); ?></label>
                <div class="onoffswitch">
                    <input type="checkbox" value="1" class="onoffswitch-checkbox" id="enable_webhooks" name="settings[enable_webhooks]" <?php echo ('1' == get_option('enable_webhooks')) ? 'checked' : ''; ?>>
                    <label class="onoffswitch-label" for="enable_webhooks"></label>
                </div>
            </div>
            <div class="form-group col-md-12">
                <label for="settings[webhooks_url]" class="control-label"><?php echo _l('webhooks_label'); ?></label>
                <input type="text" id="settings[webhooks_url]" name="settings[webhooks_url]" class="form-control" value="<?php echo get_option('webhooks_url'); ?>">
            </div>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="supportagent">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="enable_supportagnet"><?php echo _l('assign_chat_permission_to_support_agent'); ?></label>
                <div class="onoffswitch">
                    <input type="checkbox" value="1" class="onoffswitch-checkbox" id="enable_supportagent" name="settings[enable_supportagent]" <?php echo ('1' == get_option('enable_supportagent')) ? 'checked' : ''; ?>>
                    <label class="onoffswitch-label" for="enable_supportagent"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <?= _l('support_agent_note'); ?>
                </div>
            </div>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="notification_sound">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="enable_notification_sound"><?php echo _l('enable_whatsapp_notification_sound'); ?></label>
                <div class="onoffswitch">
                    <input type="checkbox" value="1" class="onoffswitch-checkbox" id="enable_wtc_notification_sound" name="settings[enable_wtc_notification_sound]" <?php echo ('1' == get_option('enable_wtc_notification_sound')) ? 'checked' : ''; ?>>
                    <label class="onoffswitch-label" for="enable_wtc_notification_sound"></label>
                </div>
            </div>
        </div>
    </div>
</div>
