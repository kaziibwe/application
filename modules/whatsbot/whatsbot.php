<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
    Module Name: WhatsBot
    Description: Elevate your customer relationship management and streamline your communication strategy with the power of WhatsApp
    Version: 1.1.0
    Requires at least: 3.0.*
    Module URI: https://codecanyon.net/item/whatsbot-whatsapp-marketing-bot-chat-module-for-perfex-crm/53052338
    Author: <a href="https://codecanyon.net/user/corbitaltech" target="_blank">Corbital Technologies<a/>
*/

define('WHATSBOT_MODULE', 'whatsbot');

/*
* Register language files, must be registered if the module is using languages
*/
register_language_files(WHATSBOT_MODULE, [WHATSBOT_MODULE]);

define('WHATSBOT_MODULE_UPLOAD_FOLDER', 'uploads/' . WHATSBOT_MODULE);
define('WHATSBOT_MODULE_UPLOAD_URL', base_url() . WHATSBOT_MODULE_UPLOAD_FOLDER . '/');

/*
 * Register activation module hook
 */
register_activation_hook(WHATSBOT_MODULE, 'whatsbot_module_activation_hook');
function whatsbot_module_activation_hook()
{
    $CI = &get_instance();
    require_once __DIR__ . '/install.php';

    $create_paths = [
        WHATSBOT_MODULE_UPLOAD_FOLDER,
        WHATSBOT_MODULE_UPLOAD_FOLDER . '/campaign',
        WHATSBOT_MODULE_UPLOAD_FOLDER . '/template',
        WHATSBOT_MODULE_UPLOAD_FOLDER . '/bot_files'
    ];

    array_map('_maybe_create_upload_path', $create_paths);
}

require_once __DIR__ . '/vendor/autoload.php';

get_instance()->load->helper(WHATSBOT_MODULE . '/whatsbot');


require_once __DIR__ . '/includes/sidebar_menu_links.php';
require_once __DIR__ . '/includes/assets.php';
require_once __DIR__ . '/includes/staff_permissions.php';

require_once __DIR__ . '/install.php';
get_instance()->config->load(WHATSBOT_MODULE . '/config');

/**
 * Add additional settings for this module in the module list area
 * @param  array $actions current actions
 * @return array
 */
hooks()->add_filter('module_whatsbot_action_links', function ($actions) {
    $actions[] = '<a href="https://docs.corbitaltech.dev/products/whatsbot/index.html" class="text-danger" target="_blank">' . _l('help') . '</a>';

    return $actions;
});

// add new created lead in campaign that is selected all leads
hooks()->add_action('lead_created', function ($id) {
    $campaigns = get_instance()->db->get_where(db_prefix() . 'wtc_campaigns', ['select_all' => '1', 'rel_type' => 'leads'])->result_array();
    foreach ($campaigns as $campaign) {
        if (0 == $campaign['is_sent']) {
            $template = wb_get_whatsapp_template($campaign['template_id']);
            get_instance()->db->insert(db_prefix() . 'wtc_campaign_data', [
                'campaign_id'       => $campaign['id'],
                'rel_id'            => $id,
                'rel_type'          => 'leads',
                'header_message'    => $template['header_data_text'],
                'body_message'      => $template['body_data'],
                'footer_message'    => $template['footer_data'],
                'status'            => 1,
            ]);
        }
    }
});

// delete campaign lead when lead deleted
hooks()->add_action('after_lead_deleted', function ($id) {
    get_instance()->db->delete(db_prefix() . 'wtc_campaign_data', ['rel_id' => $id, 'rel_type' => 'leads']);
});

// delete campaign contacts when contact deleted
hooks()->add_action('contact_deleted', function ($id, $result) {
    get_instance()->db->delete(db_prefix() . 'wtc_campaign_data', ['rel_id' => $id, 'rel_type' => 'contacts']);
}, 0, 2);

hooks()->add_filter('before_settings_updated', function ($data) {
    $data['settings']['whatsapp_auto_lead_settings'] = $data['settings']['whatsapp_auto_lead_settings'] ?? '0';
    $data['settings']['enable_webhooks'] = $data['settings']['enable_webhooks'] ?? '0';
    $data['settings']['enable_supportagent'] = $data['settings']['enable_supportagent'] ?? '0';
    $data['settings']['enable_wtc_notification_sound'] = $data['settings']['enable_wtc_notification_sound'] ?? '0';

    return $data;
});

// custom hook for whatsapp auto lead create if not available
hooks()->add_filter('ctl_auto_lead_creation', function ($contact_number, $name) {
    if (1 == get_option('whatsapp_auto_lead_settings')) {
        $lead_data = [
            'phonenumber' => $contact_number,
            'name'        => $name,
            'status'      => get_option('whatsapp_auto_leads_status'),
            'source'      => get_option('whatsapp_auto_leads_source'),
            'assigned'    => get_option('whatsapp_auto_leads_assigned'),
            'dateadded'   => date('Y-m-d H:i:s'),
            'description' => '',
            'address'     => '',
            'email'       => '',
        ];
        get_instance()->load->model('leads_model');

        return get_instance()->leads_model->add($lead_data);
    }

    return false;
}, 10, 2);

// add new created contact in campaign that is select all contacts
hooks()->add_action('contact_created', function ($id) {
    $campaigns = get_instance()->db->get_where(db_prefix() . 'wtc_campaigns', ['select_all' => '1', 'rel_type' => 'contacts'])->result_array();
    foreach ($campaigns as $campaign) {
        if (0 == $campaign['is_sent']) {
            $template = wb_get_whatsapp_template($campaign['template_id']);
            get_instance()->db->insert(db_prefix() . 'wtc_campaign_data', [
                'campaign_id'       => $campaign['id'],
                'rel_id'            => $id,
                'rel_type'          => 'contacts',
                'header_message'    => $template['header_data_text'],
                'body_message'      => $template['body_data'],
                'footer_message'    => $template['footer_data'],
                'status'            => 1,
            ]);
        }
    }
});

hooks()->add_action('after_cron_run', 'send_campaign');
function send_campaign()
{
    $scheduledData = get_instance()->db
        ->select(db_prefix() . 'wtc_campaigns.*, ' . db_prefix() . 'wtc_templates.*, ' . db_prefix() . 'wtc_campaign_data.*')
        ->join(db_prefix() . 'wtc_campaigns', db_prefix() . 'wtc_campaigns.id = ' . db_prefix() . 'wtc_campaign_data.campaign_id', 'left')
        ->join(db_prefix() . 'wtc_templates', db_prefix() . 'wtc_campaigns.template_id = ' . db_prefix() . 'wtc_templates.id', 'left')
        ->where(db_prefix() . 'wtc_campaigns.scheduled_send_time <= NOW()')
        ->where(db_prefix() . 'wtc_campaigns.pause_campaign', 0)
        ->where(db_prefix() . 'wtc_campaign_data.status', 1)
        ->where(db_prefix() . 'wtc_campaigns.is_bot', 0)
        ->get(db_prefix() . 'wtc_campaign_data')->result_array();

    if (!empty($scheduledData)) {
        get_instance()->load->model(WHATSBOT_MODULE . '/whatsbot_model');
        get_instance()->whatsbot_model->send_campaign($scheduledData);
    }
}

// add widgets
hooks()->add_filter('get_dashboard_widgets', function ($widgets) {
    $new_widgets = [];
    $new_widgets[] = [
        'path'      => WHATSBOT_MODULE . '/widgets/whatsapp-widget',
        'container' => 'top-12',
    ];

    return array_merge($new_widgets, $widgets);
});

if (!is_dir(WHATSBOT_MODULE_UPLOAD_FOLDER)) {
    if (!mkdir(WHATSBOT_MODULE_UPLOAD_FOLDER, 0755, true)) {
        exit('Failed to create directory: ' . WHATSBOT_MODULE_UPLOAD_FOLDER);
    }
    $fp = fopen(WHATSBOT_MODULE_UPLOAD_FOLDER . '/index.html', 'w');
    fclose($fp);
}

hooks()->add_filter('get_upload_path_by_type', 'add_whatsbot_files_upload_path', 0, 2);
function add_whatsbot_files_upload_path($path, $type)
{
    switch ($type) {
        case 'bot_files':
            $path = WHATSBOT_MODULE_UPLOAD_FOLDER . '/bot_files/';
            break;
        case 'campaign':
            $path = WHATSBOT_MODULE_UPLOAD_FOLDER . '/campaign/';
            break;
        case 'template':
            $path = WHATSBOT_MODULE_UPLOAD_FOLDER . '/template/';
            break;
        default:
            $path = $path;
            break;
    }
    return $path;
}
