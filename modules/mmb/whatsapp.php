<?php

use WpOrg\Requests\Requests as Whatsapp_api_Requests;

define('WHATSAPP_API_MODULE', 'mmb');

/*
 * Register deactivation module hook
 */
register_deactivation_hook(MMB_MODULE, 'mmb_module_deactivation_hook');
function mmb_module_deactivation_hook()
{
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
 * Register language files for whatsapp module
 */
register_language_files(MMB_MODULE, ["whatsapp_api"]);

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
    $CI = &get_instance();
    if ($CI->app_modules->is_active(MMB_MODULE)) {
        echo '<link href="' . module_dir_url(MMB_MODULE, 'assets/css/whatsapp/whatsapp_api.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';

        if ('template_mapping' == $CI->router->fetch_class() && 'add' == $CI->router->fetch_method()) {
            echo '<link href="' . module_dir_url(MMB_MODULE, 'assets/css/whatsapp/material-design-iconic-font.min.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
            echo '<link href="' . module_dir_url(MMB_MODULE, 'assets/css/whatsapp/devices.min.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
            echo '<link href="' . module_dir_url(MMB_MODULE, 'assets/css/whatsapp/preview.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
        }
    }
}

/*
 * Inject Javascript file for whatsapp_api module
 */
hooks()->add_action('app_admin_footer', 'whatsapp_api_load_js');
function whatsapp_api_load_js()
{
    $CI = &get_instance();
    if ($CI->app_modules->is_active(MMB_MODULE)) {
        echo '<script src="' . module_dir_url(MMB_MODULE, 'assets/js/whatsapp/whatsapp_api.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
        if ('template_mapping' == $CI->router->fetch_class() && 'add' == $CI->router->fetch_method()) {
            echo '<script src="' . module_dir_url(MMB_MODULE, 'assets/js/whatsapp/preview.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
        }
    }
}

// permission for whatsapp api
hooks()->add_filter('staff_permissions', 'whatsapp_api_module_permissions_for_staff');
function whatsapp_api_module_permissions_for_staff($permissions)
{
    $viewGlobalName      = _l('permission_view') . '(' . _l('permission_global') . ')';
    $allPermissionsArray = [
        'list_templates_view'        => _l('list_of_templates_view'),
        'template_mapping_view'      => _l('template_mapping_view'),
        'template_mapping_add'       => _l('template_mapping_create'),
        'whatsapp_log_details_view'  => _l('whatsapp_log_details_view'),
        'whatsapp_log_details_clear' => _l('whatsapp_log_details_clear'),
        'broadcast_messages'         => _l('broadcast_messages'),
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
                'href'     => admin_url(WHATSAPP_API_MODULE . '/whatsapp_api'),
                'position' => 1,
            ]);
        }
        if (has_permission('whatsapp_api', '', 'template_mapping_view')) {
            $CI->app_menu->add_sidebar_children_item('whatsapp_api', [
                'slug'     => 'whatsapp_template_details',
                'name'     => _l('template_mapping'),
                'href'     => admin_url(WHATSAPP_API_MODULE . '/template_mapping'),
                'position' => 2,
            ]);
        }
        if (has_permission('whatsapp_api', '', 'whatsapp_log_details_view')) {
            $CI->app_menu->add_sidebar_children_item('whatsapp_api', [
                'slug'     => 'whatsapp_log_details',
                'name'     => _l('whatsapp_log_details'),
                'href'     => admin_url(WHATSAPP_API_MODULE . '/log_details'),
                'position' => 3,
            ]);
        }
        if (has_permission('whatsapp_api', '', 'broadcast_messages')) {
            $CI->app_menu->add_sidebar_children_item('whatsapp_api', [
                'slug'     => 'whatsapp_log_details',
                'name'     => _l('broadcast_messages'),
                'href'     => admin_url(WHATSAPP_API_MODULE . '/whatsapp_api/broadcast_messages'),
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
        'view'     => WHATSAPP_API_MODULE . '/whatsapp/settings/settings',
        'icon'     => 'fa fa-brands fa-whatsapp menu-icon',
        'position' => 50,
    ]);
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
        $request                      = Mmb_api_Requests::get(
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
            redirect(admin_url(WHATSAPP_API_MODULE . '/authentication'));
        }
    }
});

hooks()->add_action('before_update_contact', function ($data, $id) {
    $postData = get_instance()->input->post();

    $data['client_message']  = isset($postData['client_message']) ? 1 :0;
    $data['invoice_message']  = isset($postData['invoice_message']) ? 1 :0;
    $data['tasks_message']     = isset($postData['tasks_message']) ? 1 :0;
    $data['projects_message']  = isset($postData['projects_message']) ? 1 :0;
    $data['proposals_message'] = isset($postData['proposals_message']) ? 1 :0;
    $data['payments_message'] = isset($postData['payments_message']) ? 1 :0;
    $data['ticket_message']  = isset($postData['ticket_message']) ? 1 :0;

    $data['client_forward_phone']  = isset($postData['client_forward_phone']) ? $postData['client_forward_phone'] :null;
    $data['invoice_forward_phone']  = isset($postData['invoice_forward_phone']) ? $postData['invoice_forward_phone'] :null;
    $data['tasks_forward_phone']     = isset($postData['tasks_forward_phone']) ? $postData['tasks_forward_phone'] :null;
    $data['projects_forward_phone']  = isset($postData['projects_forward_phone']) ? $postData['projects_forward_phone'] :null;
    $data['proposals_forward_phone'] = isset($postData['proposals_forward_phone']) ? $postData['proposals_forward_phone'] :null;
    $data['payments_forward_phone'] = isset($postData['payments_forward_phone']) ? $postData['payments_forward_phone'] :null;
    $data['ticket_forward_phone']  = isset($postData['ticket_forward_phone']) ? $postData['ticket_forward_phone'] :null;

    return $data;
}, 10, 2);

hooks()->add_action('after_contact_modal_content_loaded', "whatsapp_api_updates");
hooks()->add_action('after_client_profile_form_loaded', "whatsapp_api_updates");

function whatsapp_api_updates() {
    $contact_id = get_contact_user_id();
    if(is_staff_member()){
        if(get_instance()->uri->segment(3) == "form_contact"){
            $contact_id = get_instance()->uri->segment(5);
        }
    }
    if(!is_primary_contact($contact_id)){
        return;
    }
    $contact = get_instance()->clients_model->get_contact($contact_id);
    $html = "";
    $html .= '<hr />';
    $html .= '<p class="bold email-notifications-label">' . _l("whatsapp_updates") . '</p>';
    $html .= render_checkbox_forward($contact, "client");
    if (has_contact_permission("invoices", $contact_id)) {
        $html .= render_checkbox_forward($contact, "invoice");
    }
    if (has_contact_permission("projects", $contact_id)) {
        $html .= render_checkbox_forward($contact, "tasks");
        $html .= render_checkbox_forward($contact, "projects");
    }
    if (has_contact_permission("proposals", $contact_id)) {
        $html .= render_checkbox_forward($contact, "proposals");
    }
    if (has_contact_permission("invoices", $contact_id)) {
        $html .= render_checkbox_forward($contact, "payments");
    }
    if (has_contact_permission("support", $contact_id)) {
        $html .= render_checkbox_forward($contact, "ticket");
    }
    echo $html;
};
