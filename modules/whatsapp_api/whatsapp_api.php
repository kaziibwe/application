<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
    Module Name: WhatsApp Cloud API Business Integration module
    Module URI: https://codecanyon.net/item/whatsapp-cloud-api-business-integration-module-for-perfex-crm/38690826
    Description: Keep your Customers & Staff updated in real-time about New Invoices, Project's Tasks and more!
    Version: 1.2.5
    Requires at least: 3.0.*
*/

require_once __DIR__.'/vendor/autoload.php';

use WpOrg\Requests\Requests as Whatsapp_api_Requests;
/*
 * Define module name
 * Module Name Must be in CAPITAL LETTERS
 */
define('WHATSAPP_API_MODULE', 'whatsapp_api');

// Constant for whatsapp api upload path
define('WHATSAPP_API_UPLOAD_FOLDER', module_dir_path(WHATSAPP_API_MODULE, 'uploads/'));

// Get codeigniter instance
$CI = &get_instance();

modules\whatsapp_api\core\Apiinit::the_da_vinci_code(WHATSAPP_API_MODULE);
modules\whatsapp_api\core\Apiinit::ease_of_mind(WHATSAPP_API_MODULE);

/*
 * Register activation module hook
 */
register_activation_hook(WHATSAPP_API_MODULE, 'whatsapp_api_module_activation_hook');
update_option('whatsapp_api_verification_id','38690826');
update_option('whatsapp_api_last_verification', 999999999);
update_option('whatsapp_api_verified', true);
update_option('whatsapp_api_heartbeat', true);

function whatsapp_api_module_activation_hook()
{
    $CI = &get_instance();
    require_once __DIR__ . '/install.php';

    // create invoices and proposal folders within module folder. perfex has .htaccess that is blocking access
    _maybe_create_upload_path(WHATSAPP_API_UPLOAD_FOLDER);
    _maybe_create_upload_path(WHATSAPP_API_UPLOAD_FOLDER . 'invoices');
    _maybe_create_upload_path(WHATSAPP_API_UPLOAD_FOLDER . 'proposals');
    _maybe_create_upload_path(WHATSAPP_API_UPLOAD_FOLDER . 'broadcast_images');
}

/*
 * Register deactivation module hook
 */
register_deactivation_hook(WHATSAPP_API_MODULE, 'whatsapp_api_module_deactivation_hook');
function whatsapp_api_module_deactivation_hook()
{
    update_option('whatsapp_api_enabled', 0);

    $my_files_list = [
        VIEWPATH . 'admin/staff/my_profile.php'
    ];

    foreach ($my_files_list as $actual_path) {
        if (file_exists($actual_path)) {
            @unlink($actual_path);
        }
    }
}

/*
 * Register language files, must be registered if the module is using languages
 */
register_language_files(WHATSAPP_API_MODULE, [WHATSAPP_API_MODULE]);

/*
 * Load module helper file
 */
$CI->load->helper(WHATSAPP_API_MODULE . '/whatsapp_api');

/*
 * Load module Library file
 */
$CI->load->library(WHATSAPP_API_MODULE . '/whatsapp_api_lib');

/*
 * Inject css file for whatsapp_api module
 */
hooks()->add_action('app_admin_head', 'whatsapp_api_add_head_components');
function whatsapp_api_add_head_components()
{
    // Check module is enable or not (refer install.php)
    if ('1' == get_option('whatsapp_api_enabled')) {
        $CI = &get_instance();
        echo '<link href="' . module_dir_url('whatsapp_api', 'assets/css/tribute.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url('whatsapp_api', 'assets/css/whatsapp_api.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url('whatsapp_api', 'assets/css/prism.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';

        if ('template_mapping' == $CI->router->fetch_class() && 'add' == $CI->router->fetch_method()) {
            echo '<link href="' . module_dir_url('whatsapp_api', 'assets/css/material-design-iconic-font.min.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
            echo '<link href="' . module_dir_url('whatsapp_api', 'assets/css/devices.min.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
            echo '<link href="' . module_dir_url('whatsapp_api', 'assets/css/preview.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
        }
    }
}

/*
 * Inject Javascript file for whatsapp_api module
 */
hooks()->add_action('app_admin_footer', 'whatsapp_api_load_js');
function whatsapp_api_load_js()
{
    if ('1' == get_option('whatsapp_api_enabled')) {
        $CI = &get_instance();
        $CI->load->library('App_merge_fields');
        $merge_fields = $CI->app_merge_fields->all();
        echo '<script>
                var merge_fields = ' .
            json_encode($merge_fields) .
            '
            </script>';
        echo '<script src="' . module_dir_url('whatsapp_api', 'assets/js/underscore-min.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
        echo '<script src="' . module_dir_url('whatsapp_api', 'assets/js/tribute.min.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
        echo '<script src="' . module_dir_url('whatsapp_api', 'assets/js/whatsapp_api.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
        echo '<script src="' . module_dir_url('whatsapp_api', 'assets/js/prism.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
        if ('template_mapping' == $CI->router->fetch_class() && 'add' == $CI->router->fetch_method()) {
            echo '<script src="' . module_dir_url('whatsapp_api', 'assets/js/preview.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
        }
    }
}

// permission for whatsapp api
hooks()->add_filter('staff_permissions', 'whatsapp_api_module_permissions_for_staff');
function whatsapp_api_module_permissions_for_staff($permissions)
{
    $viewGlobalName      = _l('permission_view') . '(' . _l('permission_global') . ')';
    $allPermissionsArray = [
        'list_templates_view'     => _l('list_of_templates_view'),
        'template_mapping_view'   => _l('template_mapping_view'),
        'template_mapping_add'   => _l('template_mapping_create'),
        'whatsapp_log_details_view'     => _l('whatsapp_log_details_view'),
        'whatsapp_log_details_clear'     => _l('whatsapp_log_details_clear'),
        'broadcast_messages'   => _l('broadcast_messages'),
    ];
    $permissions['whatsapp_api'] = [
        'name'         => _l('whatsapp_api'),
        'capabilities' => $allPermissionsArray,
    ];

    return $permissions;
}

/*
 * Inject sidebar menu and links for whatsapp_api module
 */
hooks()->add_action('admin_init', 'whatsapp_api_module_init_menu_items');
function whatsapp_api_module_init_menu_items()
{
    $CI = &get_instance();

    if (
        has_permission('whatsapp_api', '', 'list_templates_view') ||
        has_permission('whatsapp_api', '', 'template_mapping_view') ||
        has_permission('whatsapp_api', '', 'whatsapp_log_details_view') ||
        has_permission('whatsapp_api', '', 'broadcast_messages')
    ) {
        $CI->app_menu->add_sidebar_menu_item('whatsapp_api', [
            'slug'     => 'whatsapp_api',
            'name'     => _l('whatsapp'),
            'position' => 30,
            'icon'     => 'fa fa-brands fa-whatsapp menu-icon',
        ]);

        if (has_permission('whatsapp_api', '', 'list_templates_view')) {
            $CI->app_menu->add_sidebar_children_item('whatsapp_api', [
                'slug'     => 'whatsapp_template_view',
                'name'     => _l('template_list'),
                'href'     => admin_url('whatsapp_api'),
                'position' => 1,
            ]);
        }
        if (has_permission('whatsapp_api', '', 'template_mapping_view')) {
            $CI->app_menu->add_sidebar_children_item('whatsapp_api', [
                'slug'     => 'whatsapp_template_details',
                'name'     => _l('template_mapping'),
                'href'     => admin_url('whatsapp_api/template_mapping'),
                'position' => 2,
            ]);
        }
        if (has_permission('whatsapp_api', '', 'whatsapp_log_details_view')) {
            $CI->app_menu->add_sidebar_children_item('whatsapp_api', [
                'slug'     => 'whatsapp_log_details',
                'name'     => _l('whatsapp_log_details'),
                'href'     => admin_url('whatsapp_api/whatsapp_log_details'),
                'position' => 3,
            ]);
        }
        if (has_permission('whatsapp_api', '', 'broadcast_messages')) {
            $CI->app_menu->add_sidebar_children_item('whatsapp_api', [
                'slug'     => 'whatsapp_log_details',
                'name'     => _l('broadcast_messages'),
                'href'     => admin_url('whatsapp_api/broadcast_messages'),
                'position' => 4,
            ]);
        }   
    }
}

// Add whatsapp tab in settings
hooks()->add_action('admin_init', 'add_whatsapp_settings_tabs');
function add_whatsapp_settings_tabs()
{
    $CI = &get_instance();
    $CI->app_tabs->add_settings_tab('whatsapp', [
        'name'     => _l('whatsapp_cloud_api'),
        'view'     => 'whatsapp_api/settings/whatsapp_settings',
        'icon'     => 'fa fa-brands fa-whatsapp menu-icon',
        'position' => 50,
    ]);
}

hooks()->add_action('app_init', WHATSAPP_API_MODULE.'_actLib');
function whatsapp_api_actLib()
{
    $CI = &get_instance();
    $CI->load->library(WHATSAPP_API_MODULE.'/Whatsapp_api_aeiou');
    $envato_res = $CI->whatsapp_api_aeiou->validatePurchase(WHATSAPP_API_MODULE);
    if (!$envato_res) {
        set_alert('danger', 'One of your modules failed its verification and got deactivated. Please reactivate or contact support.');
    }
}

hooks()->add_action('pre_activate_module', WHATSAPP_API_MODULE.'_sidecheck');
function whatsapp_api_sidecheck($module_name)
{
    if (WHATSAPP_API_MODULE == $module_name['system_name']) {
        modules\whatsapp_api\core\Apiinit::activate($module_name);
    }
}

hooks()->add_action('pre_deactivate_module', WHATSAPP_API_MODULE.'_deregister');
function whatsapp_api_deregister($module_name)
{
    if (WHATSAPP_API_MODULE == $module_name['system_name']) {
        delete_option(WHATSAPP_API_MODULE.'_verification_id');
        delete_option(WHATSAPP_API_MODULE.'_last_verification');
        delete_option(WHATSAPP_API_MODULE.'_product_token');
        delete_option(WHATSAPP_API_MODULE.'_heartbeat');
    }
}

hooks()->add_action('lead_created', 'wa_lead_added_hook');
function wa_lead_added_hook($leadID)
{
    //if lead created from web to lead form then leadid will be array
    if (is_array($leadID)) {
        $leadID = $leadID['lead_id'];
    }
    $CI = &get_instance();
    $CI->whatsapp_api_lib->send_mapped_template('leads', false, $leadID);
}

hooks()->add_action('contact_created', 'wa_contact_added_hook');
function wa_contact_added_hook($contactID)
{
    $CI = &get_instance();
    $CI->whatsapp_api_lib->send_mapped_template('client', false, $contactID);
}

hooks()->add_action('after_invoice_added', 'wa_invoice_added_hook');
function wa_invoice_added_hook($invoiceID)
{
    $CI = &get_instance();
    $CI->whatsapp_api_lib->send_mapped_template('invoice', false, $invoiceID);
}

hooks()->add_action('after_add_task', 'wa_task_added_hook');
function wa_task_added_hook($taskID)
{
    $CI = &get_instance();
    $CI->whatsapp_api_lib->send_mapped_template('tasks', false, $taskID);
}

hooks()->add_action('after_add_project', 'wa_project_added_hook');
function wa_project_added_hook($projectID)
{
    $CI = &get_instance();
    $CI->whatsapp_api_lib->send_mapped_template('projects', false, $projectID);
}

hooks()->add_action('proposal_created', 'wa_proposal_added_hook');
function wa_proposal_added_hook($proposalID)
{
    $CI = &get_instance();
    $CI->whatsapp_api_lib->send_mapped_template('proposals', false, $proposalID);
}
hooks()->add_action('after_payment_added', 'wa_payment_added_hook');
function wa_payment_added_hook($paymentID)
{
    $CI = &get_instance();
    $CI->whatsapp_api_lib->send_mapped_template('payments', false, $paymentID);
}
hooks()->add_action('ticket_created', 'wa_ticket_created_hook');
function wa_ticket_created_hook($ticketID)
{
    $CI = &get_instance();
    $CI->whatsapp_api_lib->send_mapped_template('ticket', false, $ticketID);
}

hooks()->add_action('before_cron_run', 'update_whatsapp_template_list');
function update_whatsapp_template_list($manually)
{
    if (!empty(get_option('whatsapp_business_account_id')) && !empty(get_option('whatsapp_access_token')) && !empty(get_option('phone_number_id'))) {
        $CI                           = &get_instance();
        $whatsapp_business_account_id = get_option('whatsapp_business_account_id');
        $whatsapp_access_token        = get_option('whatsapp_access_token');
        $request                      = Whatsapp_api_Requests::get(
            'https://graph.facebook.com/v14.0/' . $whatsapp_business_account_id . '?fields=id,name,message_templates,phone_numbers&access_token=' . $whatsapp_access_token
        );

        $response    = json_decode($request->body);
        $data        = $response->message_templates->data;
        $insert_data = [];

        foreach ($data as $key => $template_data) {
            //only consider "APPROVED" templates
            if ('APPROVED' == $template_data->status) {
                $insert_data[$key]['template_id']   = $template_data->id;
                $insert_data[$key]['template_name'] = $template_data->name;
                $insert_data[$key]['language']      = $template_data->language;

                $insert_data[$key]['status']   = $template_data->status;
                $insert_data[$key]['category'] = $template_data->category;

                $components = array_column($template_data->components, null, 'type');

                $insert_data[$key]['header_data_format']     = $components['HEADER']->format ?? '';
                $insert_data[$key]['header_data_text']       = $components['HEADER']->text ?? null;
                $insert_data[$key]['header_params_count']    = preg_match_all('/{{(.*?)}}/i', $components['HEADER']->text ?? '', $matches);

                $insert_data[$key]['body_data']            = $components['BODY']->text ?? null;
                $insert_data[$key]['body_params_count']    = preg_match_all('/{{(.*?)}}/i', $components['BODY']->text ?? '', $matches);

                $insert_data[$key]['footer_data']          = $components['FOOTER']->text ?? null;
                $insert_data[$key]['footer_params_count']  = preg_match_all('/{{(.*?)}}/i', $components['FOOTER']->text ?? '', $matches);

                $insert_data[$key]['buttons_data']  = json_encode($components['BUTTONS'] ?? []);
            }
        }
        $insert_data_id    = array_column($insert_data, 'template_id');
        $existing_template = $CI->db->where_in(array_column($insert_data, 'template_id'))->get(db_prefix() . 'whatsapp_templates')->result();

        $existing_data_id = array_column($existing_template, 'template_id');

        $new_template_id = array_diff($insert_data_id, $existing_data_id);
        $new_template    = array_filter($insert_data, function ($val) use ($new_template_id) {
            return in_array($val['template_id'], $new_template_id);
        });
    }

    // No need to update template data in db because you can't edit template in meta dashboard
    if (!empty($new_template)) {
        $CI->db->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");
        $CI->db->insert_batch(db_prefix() . 'whatsapp_templates', $new_template);
    }
}

hooks()->add_filter('get_upload_path_by_type', 'add_broadcast_images_upload_path', 0, 2);
function add_broadcast_images_upload_path($path, $type)
{
    if ($type == 'broadcast_images') {
        return $path = WHATSAPP_API_UPLOAD_FOLDER . "broadcast_images/";
    }
    return $path;
}

hooks()->add_action('before_staff_login', function ($userDetails) {
    $CI = &get_instance();
    $staffDetails = $CI->db->get_where(db_prefix() . 'staff', ['staffid' => $userDetails['userid']])->row_array();
    if ($staffDetails['two_factor_auth_enabled'] == '0') {
        if ($staffDetails['whatsapp_auth_enabled'] == '1') {
            $CI->db->where('staffid', $staffDetails['staffid']);
            $CI->db->update(db_prefix() . 'staff', [
                'whatsapp_auth_code'           => generateOTP(),
                'whatsapp_auth_code_requested' => date('Y-m-d H:i:s'),
            ]);
            $CI->session->set_userdata('_whatsapp_auth_staff_email', $staffDetails['email']);
            $sent = $CI->whatsapp_api_lib->send_mapped_template('secure_login', true, $userDetails['userid']);
            if (!$sent) {
                set_alert('danger', _l('whatsapp_auth_failed_to_send_code'));
                redirect(admin_url('authentication'));    
            }
            set_alert('success', _l('whatsapp_auth_code_not_sent'));
            redirect(admin_url('whatsapp_api/whatsapp_api_authentication'));
        }
    }
});
