<?php

use WpOrg\Requests\Requests as Webhooks_Requests;
/*
 * Define module name
 * Module Name Must be in CAPITAL LETTERS
 */
define('WEBHOOKS_MODULE', 'mmb');

/*
 *  Register language files, must be registered if the module is using languages
 */
register_language_files(MMB_MODULE, ["webhooks"]);

/*
 *  Load module helper file
 */
$CI->load->helper(WEBHOOKS_MODULE . '/webhooks');

/*
 *  Inject css file for webhooks module
 */
hooks()->add_action('app_admin_head', 'webhooks_add_head_components');
function webhooks_add_head_components()
{
    $CI = &get_instance();
    if ($CI->app_modules->is_active(MMB_MODULE)) {
        echo '<link href="' . module_dir_url(MMB_MODULE, 'assets/css/webhooks/webhooks.css') . '?v=' . $CI->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';
    }
}

/*
 *  Inject Javascript file for webhooks module
 */
hooks()->add_action('app_admin_footer', 'webhooks_load_js');
function webhooks_load_js()
{
    $CI = &get_instance();
    if ($CI->app_modules->is_active(MMB_MODULE)) {
        $CI->load->library('App_merge_fields');
        $merge_fields = $CI->app_merge_fields->all();
        echo '<script>var merge_fields = ' . json_encode($merge_fields) . '</script>';
        echo '<script src="' . module_dir_url(MMB_MODULE, 'assets/js/webhooks/webhooks.js') . '?v=' . $CI->app_scripts->core_version() . '"></script>';
    }
}

//inject permissions Feature and Capabilities for webhooks module
hooks()->add_filter('staff_permissions', 'webhooks_module_permissions_for_staff');
function webhooks_module_permissions_for_staff($permissions)
{
    $viewGlobalName =
        _l('permission_view') . '(' . _l('permission_global') . ')';
    $allPermissionsArray = [
        'view'   => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    $permissions['WEBHOOKS'] = [
        'name'         => _l('webhooks'),
        'capabilities' => $allPermissionsArray,
    ];

    return $permissions;
}

// Inject sidebar menu and links for webhooks module
hooks()->add_action('admin_init', 'webhooks_module_init_menu_items');
function webhooks_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('webhooks', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('webhooks', [
            'slug'     => 'webhooks',
            'name'     => _l('webhooks'),
            'icon'     => 'fa fa-handshake-o menu-icon fa-duotone fa-circle-nodes',
            'href'     => WEBHOOKS_MODULE,
            'position' => 30,
        ]);
    }

    if (has_permission('webhooks', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('webhooks', [
            'slug'     => 'webhooks',
            'name'     => _l('webhooks'),
            'icon'     => 'fa fa-compress',
            'href'     => admin_url(WEBHOOKS_MODULE . '/webhooks'),
            'position' => 1,
        ]);
    }

    if (has_permission('webhooks', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('webhooks', [
            'slug'     => 'webhook_log',
            'name'     => _l('webhook_log'),
            'icon'     => 'fa fa-history',
            'href'     => admin_url(WEBHOOKS_MODULE . '/webhooks/logs'),
            'position' => 5,
        ]);
    }
}

/*
 * Inject email template for webhooks module
 */
hooks()->add_action('after_email_templates', 'add_email_template_webhook');
function add_email_template_webhook()
{
    $data['hasPermissionEdit'] = has_permission('email_templates', '', 'edit');
    $data['webhooks']          = get_instance()->emails_model->get([
        'type'     => 'webhooks',
        'language' => 'english',
    ]);
    get_instance()->load->view(WEBHOOKS_MODULE . '/mail_lists/email_templates_list', $data, false);
}

hooks()->add_filter('other_merge_fields_available_for', 'add_other_merge_fields_for_webhook');

function add_other_merge_fields_for_webhook($available_for)
{
    $available_for[] = 'webhooks';

    return $available_for;
}

create_email_template('Webhook failed', '', 'webhooks', 'Webhook failed', 'webhook-failed');

register_merge_fields(MMB_MODULE . '/merge_fields/webhooks_merge_fields');

/* Contact webhooks : Start */
// Add new contact
hooks()->add_action('contact_created', 'wbhk_contact_added_hook');
function wbhk_contact_added_hook($contactID)
{
    $CI        = &get_instance();
    $tableData = new stdClass();
    $tableData->ContactData = $CI->clients_model->get_contact($contactID);
    $tableData->ClientData = $CI->clients_model->get($tableData->ContactData->userid);

    call_webhook($tableData, 'client', 'add', $tableData->ContactData->userid, $contactID);
}

// Update contact
hooks()->add_action('contact_updated', 'wbhk_contact_updated_hook');
function wbhk_contact_updated_hook($contactID)
{
    $CI        = &get_instance();
    $tableData = new stdClass();
    $tableData->ContactData = $CI->clients_model->get_contact($contactID);
    $tableData->ClientData = $CI->clients_model->get($tableData->ContactData->userid);

    call_webhook($tableData, 'client', 'edit', $tableData->ContactData->userid, $contactID);
}

// Delete contact
hooks()->add_action('before_delete_contact', 'wbhk_contact_deleted_hook');
function wbhk_contact_deleted_hook($contactID)
{
    $CI        = &get_instance();
    $tableData = new stdClass();
    $tableData->ContactData = $CI->clients_model->get_contact($contactID);
    $tableData->ClientData = $CI->clients_model->get($tableData->ContactData->userid);

    call_webhook($tableData, 'client', 'delete', $tableData->ContactData->userid, $contactID);
}
/* Contact webhooks : End */

/* Lead webhooks : Start */
// Add new lead
hooks()->add_action('lead_created', 'wbhk_lead_added_hook');
function wbhk_lead_added_hook($leadID)
{
    $CI        = &get_instance();
    //if lead created from web to lead form then leadid will be array
    if (is_array($leadID)) {
        $leadID = $leadID['lead_id'];
    }
    $tableData = $CI->leads_model->get($leadID);
    call_webhook($tableData, 'leads', 'add', $leadID);
}

// Lead status changed
hooks()->add_action('lead_status_changed', 'wbhk_lead_status_changed_hook');
function wbhk_lead_status_changed_hook($lead)
{
    $CI        = &get_instance();
    $tableData = $CI->leads_model->get($lead['lead_id']);
    call_webhook($tableData, 'leads', 'status_change', $lead['lead_id']);
}

// Delete lead
hooks()->add_action('before_lead_deleted', 'wbhk_lead_deleted_hook');
function wbhk_lead_deleted_hook($leadID)
{
    $CI        = &get_instance();
    $tableData = $CI->leads_model->get($leadID);
    call_webhook($tableData, 'leads', 'delete', $leadID);
}
/* Lead webhooks : End */

/* Invoice webhooks : Start */
// Add new invoice
hooks()->add_action('after_invoice_added', 'wbhk_invoice_added_hook');
function wbhk_invoice_added_hook($invoiceID)
{
    $CI        = &get_instance();
    $tableData = $CI->invoices_model->get($invoiceID);
    call_webhook($tableData, 'invoice', 'add', $invoiceID);
}

// Update invoice
hooks()->add_action('invoice_updated', 'wbhk_invoice_updated_hook');
function wbhk_invoice_updated_hook($invoice)
{
    $CI        = &get_instance();
    $tableData = $CI->invoices_model->get($invoice['id']);
    call_webhook($tableData, 'invoice', 'edit', $invoice['id']);
}

// Delete invoice
hooks()->add_action('before_invoice_deleted', 'wbhk_invoice_deleted_hook');
function wbhk_invoice_deleted_hook($invoiceID)
{
    $CI        = &get_instance();
    $tableData = $CI->invoices_model->get($invoiceID);
    call_webhook($tableData, 'invoice', 'delete', $invoiceID);
}
/* Invoice webhooks : End */

/* Task webhooks : Start */
// Add new task
hooks()->add_action('after_add_task', 'wbhk_task_added_hook');
function wbhk_task_added_hook($taskId)
{
    $CI        = &get_instance();
    $tableData = $CI->tasks_model->get($taskId);
    call_webhook($tableData, 'tasks', 'add', $taskId);
}

// Update task
hooks()->add_action('after_update_task', 'wbhk_task_updated_hook');
function wbhk_task_updated_hook($taskId)
{
    $CI        = &get_instance();
    $tableData = $CI->tasks_model->get($taskId);
    call_webhook($tableData, 'tasks', 'edit', $taskId);
}

// Delete task

/* Task webhooks : End */

/* Projects webhooks : Start */
// Add new project
hooks()->add_action('after_add_project', 'wbhk_project_added_hook');
function wbhk_project_added_hook($projectId)
{
    $CI        = &get_instance();
    $tableData = $CI->projects_model->get($projectId);
    call_webhook($tableData, 'projects', 'add', $projectId);
}

// Update project
hooks()->add_action('after_update_project', 'wbhk_project_updated_hook');
function wbhk_project_updated_hook($projectId)
{
    $CI        = &get_instance();
    $tableData = $CI->projects_model->get($projectId);
    call_webhook($tableData, 'projects', 'edit', $projectId);
}

// Delete project
hooks()->add_action('before_project_deleted', 'wbhk_project_deleted_hook');
function wbhk_project_deleted_hook($projectId)
{
    $CI        = &get_instance();
    $tableData = $CI->projects_model->get($projectId);
    call_webhook($tableData, 'projects', 'delete', $projectId);
}
/* Projects webhooks : End */

/* Proposal webhooks : Start */
// Add new proposal
hooks()->add_action('proposal_created', 'wbhk_proposal_added_hook');
function wbhk_proposal_added_hook($proposalId)
{
    $CI        = &get_instance();
    $tableData = $CI->proposals_model->get($proposalId);
    call_webhook($tableData, 'proposals', 'add', $proposalId);
}

// Update proposal
hooks()->add_action('after_proposal_updated', 'wbhk_proposal_updated_hook');
function wbhk_proposal_updated_hook($proposalId)
{
    $CI        = &get_instance();
    $tableData = $CI->proposals_model->get($proposalId);
    call_webhook($tableData, 'proposals', 'edit', $proposalId);
}

// Delete proposal
hooks()->add_action('before_proposal_deleted', 'wbhk_proposal_deleted_hook');
function wbhk_proposal_deleted_hook($proposalId)
{
    $CI        = &get_instance();
    $tableData = $CI->proposals_model->get($proposalId);
    call_webhook($tableData, 'proposals', 'delete', $proposalId);
}

// Accept proposal
hooks()->add_action('proposal_accepted', 'wbhk_proposal_accepted_hook');
function wbhk_proposal_accepted_hook($proposalId)
{
    $CI        = &get_instance();
    $tableData = $CI->proposals_model->get($proposalId);
    call_webhook($tableData, 'proposals', 'accept', $proposalId);
}

// Decline proposal
hooks()->add_action('proposal_declined', 'wbhk_proposal_declined_hook');
function wbhk_proposal_declined_hook($proposalId)
{
    $CI        = &get_instance();
    $tableData = $CI->proposals_model->get($proposalId);
    call_webhook($tableData, 'proposals', 'decline', $proposalId);
}

// Sent proposal
hooks()->add_action('proposal_sent', 'wbhk_proposal_sent_hook');
function wbhk_proposal_sent_hook($proposalId)
{
    $CI        = &get_instance();
    $tableData = $CI->proposals_model->get($proposalId);
    call_webhook($tableData, 'proposals', 'sent', $proposalId);
}
/* Proposal webhooks : End */

/* Ticket webhooks : Start */
// Add new ticket
hooks()->add_action('ticket_created', 'wbhk_ticket_added_hook');
function wbhk_ticket_added_hook($ticketId)
{
    $CI        = &get_instance();
    $tableData = $CI->tickets_model->get($ticketId);
    call_webhook($tableData, 'ticket', 'add', $ticketId);
}

// Update ticket
hooks()->add_action('ticket_settings_updated', 'wbhk_ticket_updated_hook');
function wbhk_ticket_updated_hook($ticket)
{
    $CI        = &get_instance();
    $tableData = $CI->tickets_model->get($ticket['ticket_id']);
    call_webhook($tableData, 'ticket', 'edit', $ticket['ticket_id']);
}

// Ticket status changed
hooks()->add_action('after_ticket_status_changed', 'wbhk_ticket_status_changed_hook');
function wbhk_ticket_status_changed_hook($ticket)
{
    $CI        = &get_instance();
    $tableData = $CI->tickets_model->get($ticket['id']);
    call_webhook($tableData, 'ticket', 'status_change', $ticket['id']);
}

// Delete ticket
hooks()->add_action('before_ticket_deleted', 'wbhk_ticket_deleted_hook');
function wbhk_ticket_deleted_hook($ticketID)
{
    $CI        = &get_instance();
    $tableData = $CI->tickets_model->get($ticketID);
    call_webhook($tableData, 'ticket', 'delete', $ticketID);
}
/* Ticket webhooks : End */

/* Payment webhooks : Start */
// Add new payment
hooks()->add_action('after_payment_added', 'wbhk_payment_added_hook');
function wbhk_payment_added_hook($paymentId)
{
    $CI        = &get_instance();
    $tableData = $CI->payments_model->get($paymentId);
    call_webhook($tableData, 'invoice_payments', 'add', $tableData->invoiceid, $paymentId);
}

// Update payment
hooks()->add_action('after_payment_updated', 'wbhk_payment_updated_hook');
function wbhk_payment_updated_hook($payment)
{
    $CI        = &get_instance();
    $tableData = $CI->payments_model->get($payment['id']);
    call_webhook($tableData, 'invoice_payments', 'edit', $tableData->invoiceid, $payment['id']);
}

// Delete payment
hooks()->add_action('before_payment_deleted', 'wbhk_payment_deleted_hook');
function wbhk_payment_deleted_hook($payment)
{
    $CI        = &get_instance();
    $tableData = $CI->payments_model->get($payment['paymentid']);
    call_webhook($tableData, 'invoice_payments', 'delete', $tableData->invoiceid, $payment['paymentid']);
}
/* Payment webhooks : End */

/* Staff webhooks : Start */
// Add new staff
hooks()->add_action('staff_member_created', 'wbhk_staff_added_hook');
function wbhk_staff_added_hook($staffid)
{
    $CI        = &get_instance();
    $tableData = $CI->staff_model->get($staffid);
    call_webhook($tableData, 'staff', 'add', $staffid);
}

// Update staff
hooks()->add_action('staff_member_updated', 'wbhk_staff_updated_hook');
function wbhk_staff_updated_hook($staffid)
{
    $CI        = &get_instance();
    $tableData = $CI->staff_model->get($staffid);
    call_webhook($tableData, 'staff', 'edit', $staffid);
}

// Delete staff
hooks()->add_action('before_delete_staff_member', 'wbhk_staff_deleted_hook');
function wbhk_staff_deleted_hook($staff)
{
    $CI        = &get_instance();
    $tableData = $CI->staff_model->get($staff['id']);
    call_webhook($tableData, 'staff', 'delete', $staff['id']);
}
/* Staff webhooks : End */

/* Contracts webhooks : Start */
// Add new contract
hooks()->add_action('after_contract_added', 'wbhk_contract_added_hook');
function wbhk_contract_added_hook($contractID)
{
    $CI        = &get_instance();
    $tableData = $CI->contracts_model->get($contractID);
    call_webhook($tableData, 'contract', 'add', $contractID);
}

// Update contract
hooks()->add_action('after_contract_updated', 'wbhk_contract_updated_hook');
function wbhk_contract_updated_hook($contractID)
{
    $CI        = &get_instance();
    $tableData = $CI->contracts_model->get($contractID);
    call_webhook($tableData, 'contract', 'edit', $contractID);
}

// Delete contract
hooks()->add_action('before_contract_deleted', 'wbhk_contract_deleted_hook');
function wbhk_contract_deleted_hook($contractID)
{
    $CI        = &get_instance();
    $tableData = $CI->contracts_model->get($contractID);
    call_webhook($tableData, 'contract', 'delete', $contractID);
}
/* Contracts webhooks : End */

/* Estimates webhooks : Start */
// Add new estimate
hooks()->add_action('after_estimate_added', 'wbhk_estimate_added_hook');
function wbhk_estimate_added_hook($estimateID)
{
    $CI        = &get_instance();
    $tableData = $CI->estimates_model->get($estimateID);
    call_webhook($tableData, 'estimate', 'add', $estimateID);
}

// Update estimate
hooks()->add_action('after_estimate_updated', 'wbhk_estimate_updated_hook');
function wbhk_estimate_updated_hook($estimateID)
{
    $CI        = &get_instance();
    $tableData = $CI->estimates_model->get($estimateID);
    call_webhook($tableData, 'estimate', 'edit', $estimateID);
}

// Delete estimate
hooks()->add_action('before_estimate_deleted', 'wbhk_estimate_deleted_hook');
function wbhk_estimate_deleted_hook($estimateID)
{
    $CI        = &get_instance();
    $tableData = $CI->estimates_model->get($estimateID);
    call_webhook($tableData, 'estimate', 'delete', $estimateID);
}
/* Estimates webhooks : End */
// Update calendar event
hooks()->add_filter('event_update_data', 'wbhk_calendar_event_updated_hook', 0, 2);
function wbhk_calendar_event_updated_hook($eventData, $eventID)
{
    $CI        = &get_instance();
    $eventData['end'] = $eventData['end'] ?? "";
    call_webhook((object)$eventData, 'event', 'edit', $eventID);
    return $eventData;
}
/* Calendar event webhooks : End */

hooks()->add_filter("staff_merge_fields", function ($fields, $data) {
    $fields['{staff_phonenumber}'] = $data['staff']->phonenumber;
    return $fields;
}, 10, 2);

hooks()->add_filter("available_merge_fields", function ($available) {
    $i = 0;
    foreach ($available as $fields) {
        $f = 0;
        // Fix for merge fields as custom fields not matching the names
        foreach ($fields as $key => $_fields) {
            if ($key == "staff") {
                $format = [
                    'base_name' => "staff_merge_fields",
                    'file'      => "merge_fields/staff_merge_fields",
                ];
                array_push($available[$i][$key], [
                    'name'      => "Staff Phone",
                    'key'       => '{staff_phonenumber}',
                    'available' => $available[$i][$key][$f]['available'],
                    'format'    => $format,
                ]);
            }
            if ($key == "client") {

                $format = [
                    'base_name' => "client_merge_fields",
                    'file'      => "merge_fields/client_merge_fields",
                ];

                $custom_fields = get_custom_fields("contacts", [], true);

                foreach ($custom_fields as $field) {
                    array_push($available[$i][$key], [
                        'name'      => $field['name'],
                        'key'       => '{' . $field['slug'] . '}',
                        'available' => $available[$i][$key][$f]['available'],
                        'format'    => $format,
                    ]);
                }
            }
            $f++;
        }
        $i++;
    }

    return $available;
});

function call_webhook($data, $webhook_for, $action, $data_id, $related_id = "")
{
    $CI = &get_instance();
    $CI->load->library('App_merge_fields');
    $CI->load->model(WEBHOOKS_MODULE . '/webhooks_model');

    if ($webhook_for == "ticket") {
        $merge_fields = $CI->app_merge_fields->format_feature("{$webhook_for}_merge_fields", 'new-ticket-opened-admin', $data_id);
    } elseif ($webhook_for == "client" && !empty($related_id)) {
        $merge_fields = $CI->app_merge_fields->format_feature("{$webhook_for}_merge_fields", $data_id, $related_id);
    } elseif (($webhook_for == "invoice" || $webhook_for == "client") && !empty($related_id)) {
        $merge_fields = $CI->app_merge_fields->format_feature("{$webhook_for}_merge_fields", $data_id, $related_id);
    } elseif ($webhook_for == "event") {
        $merge_fields = $CI->app_merge_fields->format_feature("{$webhook_for}_merge_fields", $data);
    } elseif ($webhook_for == "invoice_payments" && !empty($related_id)) {
        $merge_fields = $CI->app_merge_fields->format_feature("invoice_merge_fields", $data_id, $related_id);
    } else {
        $merge_fields = $CI->app_merge_fields->format_feature("{$webhook_for}_merge_fields", $data_id);
    }

    switch ($webhook_for) {
        case 'client':
            $customFieldTypes = ["customers", "contacts"];
            break;

        case 'invoice':
            $customFieldTypes = ["invoice", "items"];
            break;

        case 'proposals':
            $customFieldTypes = ["proposal"];
            break;

        case 'ticket':
            $customFieldTypes = ["tickets"];
            break;

        default:
            $customFieldTypes = [$webhook_for];
            break;
    }

    $CI->db->where('active', 1);
    $CI->db->where_in('fieldto', $customFieldTypes);

    $CI->db->order_by('field_order', 'asc');
    $fields = $CI->db->get(db_prefix() . 'customfields')->result_array();

    foreach ($fields as $key => $field) {
        $rel_id = $data_id;
        if ($field['fieldto'] == "contacts" && !empty($related_id)) {
            $rel_id = $related_id;
        }
        $data->{$field['slug']} = get_custom_field_value($rel_id, $field['id'], $field['fieldto'], false);
    }

    if ($webhook_for == "ticket") {
        $merge_fields['{staff_ticket_url}']  = site_url('clients/ticket/' . $data_id);
        $merge_fields['{client_ticket_url}'] = admin_url('tickets/ticket/' . $data_id);
    }

    if ($webhook_for == "tasks") {
        $CI->db->where('id', $data_id);
        $task = $CI->db->get(db_prefix() . 'tasks')->row();
        $merge_fields['{staff_task_link}'] = admin_url('tasks/view/' . $data_id);
        $merge_fields['{client_task_link}'] = site_url('clients/project/' . $task->rel_id . '?group=project_tasks&taskid=' . $data_id);
    }

    if ($webhook_for == "projects") {
        $merge_fields['{staff_project_link}'] = site_url('clients/project/' . $data_id);
        $merge_fields['{client_project_link}'] = admin_url('projects/view/' . $data_id);
    }

    if ($webhook_for == "projects") {
        $merge_fields['{staff_project_link}'] = admin_url('projects/view/' . $data_id);
        $merge_fields['{client_project_link}'] = site_url('clients/project/' . $data_id);
    }

    //get comman merge fields
    $other_merge_fields = $CI->app_merge_fields->format_feature(
        'other_merge_fields'
    );

    $merge_fields = array_merge($merge_fields, $other_merge_fields);

    $all_hooks = $CI->webhooks_model->getAll($webhook_for);
    foreach ($all_hooks as $webhook) {
        $webhook_action = json_decode($webhook->webhook_action, true);
        if (!in_array($action, $webhook_action)) {
            continue;
        }

        $headers = json_decode($webhook->request_header, true);
        $headers = array_map(static function ($header) use ($merge_fields) {
            $header_key = $header['header_choice'];
            if ('custom' === $header_key) {
                $header_key = $header['header_custom_choice'];
            }
            $header['value'] = preg_replace(
                '/@{(.*?)}/',
                '{$1}',
                $header['value']
            );
            foreach ($merge_fields as $key => $val) {
                $header['value'] =
                    false !== stripos($header['value'], $key)
                    ? str_replace($key, $val, $header['value'])
                    : str_replace($key, '', $header['value']);
            }

            return ['key' => trim($header_key), 'value' => trim($header['value'])];
        }, $headers);
        $headers = array_column($headers, 'value', 'key');

        $default_body = json_decode($webhook->request_body, true);
        $default_body = array_map(static function ($body) use ($merge_fields) {
            $body['value'] = preg_replace('/@{(.*?)}/', '{$1}', $body['value']);
            foreach ($merge_fields as $key => $val) {
                $body['value'] =
                    false !== stripos($body['value'], $key)
                    ? str_replace($key, $val, $body['value'])
                    : str_replace($key, '', $body['value']);
            }

            return [
                'key'   => trim($body['key']),
                'value' => trim($body['value']),
            ];
        }, $default_body);
        $default_body = array_column($default_body, 'value', 'key');

        $body_data = array_merge((array) $data, $default_body);
        if ('json' === strtolower($webhook->request_format) && 'GET' != $webhook->request_method && 'DELETE' != $webhook->request_method) {
            $body_data = json_encode($body_data);
        }

        // Send mail if webhook fails
        $CI->load->model('emails_model');

        $admin = $CI->staff_model->get('', ['admin' => '1', 'role' => NULL]);
        $admin_email = $admin[0]['email'];

        try {
            $request = Webhooks_Requests::request(
                $webhook->request_url,
                $headers,
                $body_data,
                $webhook->request_method
            );
            $response_code = $request->status_code;
            $response_data = htmlentities($request->body);

            $message = $response_code;

            if (($response_code >= 300 && $response_code <= 399) || ($response_code >= 400 && $response_code <= 499) || ($response_code >= 500 && $response_code <= 599)) {
                send_mail_template('webhooks_failed', MMB_MODULE, $admin_email, $message);
            }
        } catch (Exception $e) {
            $response_code = 'EXCEPTION';
            $response_data = $e->getMessage();
            send_mail_template('webhooks_failed', MMB_MODULE, $admin_email, $response_code);
        }
        if ($webhook->debug_mode) {
            $insert_data = [
                'webhook_action_name' => $webhook->name,
                'request_url'    => $webhook->request_url,
                'request_method' => $webhook->request_method,
                'request_format' => $webhook->request_format,
                'webhook_for'    => $webhook_for,
                'webhook_action' => json_encode([$action]),
                'request_header' => json_encode($headers),
                'request_body'   => is_array($body_data) ? json_encode($body_data) : $body_data,
                'response_code'  => $response_code,
                'response_data'  => $response_data,
            ];
            $CI->webhooks_model->add_log($insert_data);
        }
    }
}
