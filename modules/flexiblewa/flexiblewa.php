<?php

use Carbon\Carbon;


/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */
defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Flexible Workflow Automation
Description: This module automates tasks on Perfex CRM
Version: 1.0.1
Requires at least: 2.3.*
*/

define('FLEXIBLEWA_MODULE_NAME', 'flexiblewa');
define('FLEXIBLEWA_SET_ASSIGNED_TO_ACTION', 'set_assigned_to');
define('FLEXIBLEWA_SET_DUE_DATE_TO_ACTION', 'set_due_date_to');
define('FLEXIBLEWA_SET_PRIORITY_TO_ACTION', 'set_priority_to');
define('FLEXIBLEWA_ADD_NEW_CHECKLIST_ITEM_ACTION', 'add_new_checklist_item');
define('FLEXIBLEWA_ADD_NEW_REMINDER_ACTION', 'add_new_reminder');
define('FLEXIBLEWA_ADD_NEW_COMMENT_ACTION', 'add_new_comment');
define('FLEXIBLEWA_ADD_NEW_FOLLOWER_ACTION', 'add_new_follower');
define('FLEXIBLEWA_UPLOAD_FOLDER', FCPATH . 'uploads/' . FLEXIBLEWA_MODULE_NAME . '/');
define('FLEXIBLEWA_FILES_FOLDER', FLEXIBLEWA_UPLOAD_FOLDER . 'files/');
define('FLEXIBLEWA_ADD_NEW_FILE_ACTION', 'add_new_file');
define('FLEXIBLEWA_MOVE_TO_ANOTHER_RELATION_ACTION', 'move_to_another_relation');
define('FLEXIBLEWA_PROJECT_RELATION', 'project');
define('FLEXIBLEWA_INVOICE_RELATION', 'invoice');
define('FLEXIBLEWA_CUSTOMER_RELATION', 'customer');
define('FLEXIBLEWA_ESTIMATE_RELATION', 'estimate');
define('FLEXIBLEWA_CONTRACT_RELATION', 'contract');
define('FLEXIBLEWA_TICKET_RELATION', 'ticket');
define('FLEXIBLEWA_EXPENSE_RELATION', 'expense');
define('FLEXIBLEWA_LEAD_RELATION', 'lead');
define('FLEXIBLEWA_PROPOSAL_RELATION', 'proposal');
define('FLEXIBLEWA_MOVE_TO_SECTION_ACTION', 'move_to_section');
define('FLEXIBLEWA_MARK_AS_COMPLETE_ACTION', 'mark_as_complete');
define('FLEXIBLEWA_TASK_RELATION_TYPE', 'task');
hooks()->add_action('admin_init', FLEXIBLEWA_MODULE_NAME . '_permissions');
hooks()->add_action('admin_init', FLEXIBLEWA_MODULE_NAME . '_module_init_menu_items');
hooks()->add_action('task_status_changed', FLEXIBLEWA_MODULE_NAME . '_execute_task_actions');
hooks()->add_action('after_add_task', FLEXIBLEWA_MODULE_NAME . '_execute_new_task_actions');
/**
 * Register activation module hook
 */
register_activation_hook(FLEXIBLEWA_MODULE_NAME, FLEXIBLEWA_MODULE_NAME . '_module_activation_hook');

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(FLEXIBLEWA_MODULE_NAME, [FLEXIBLEWA_MODULE_NAME]);

function flexiblewa_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

function flexiblewa_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities(FLEXIBLEWA_MODULE_NAME, $capabilities, _l(FLEXIBLEWA_MODULE_NAME));
}

/**
 * Init flexible workflow automation module menu items in setup in admin_init hook
 * @return null
 */
function flexiblewa_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission(FLEXIBLEWA_MODULE_NAME, '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item(FLEXIBLEWA_MODULE_NAME, [
            'name' => _l(FLEXIBLEWA_MODULE_NAME),
            // The name if the item
            'href' => admin_url(FLEXIBLEWA_MODULE_NAME),
            // URL of the item
            'position' => 36,
            // The menu position, see below for default positions.
            'icon' => 'fa-solid fa-wand-sparkles',
            // Font awesome icon
        ]);
    }
}

function flexiblewa_get_task_status_name($status_id)
{
    $CI = &get_instance();
    $CI->load->model('Tasks_model');
    $statuses = $CI->tasks_model->get_statuses();
    

    $statuses = array_filter($statuses, function($element) use($status_id){
        return $element['id'] == $status_id;
    });

    if(empty($statuses)){
        throw new Exception('Task status not found');
    }

    return array_values($statuses)[0]['name'];
}

function flexiblewa_modals()
{
    $CI = &get_instance();
    $CI->load->view('partials/rule-modal');
    $CI->load->view('partials/action-modal');
}

function flexiblewa_get_staff_members(){
    $CI = &get_instance();
    $CI->load->model('staff_model');

    return $CI->staff_model->get();
}

function flexiblewa_get_rule_name($rule_id){
    return ucfirst(str_replace('_', ' ', $rule_id));
}

function flexiblewa_get_periods($include_hours = false){
    $periods = [];

    if($include_hours){
        array_push($periods, [
            'id' => 'hours',
            'name' => _l('flexiblewa_hours')
        ]);
    }
    
    array_push($periods, [
        'id' => 'days',
        'name' => _l('flexiblewa_days'),
    ],
    [
        'id' => 'weeks',
        'name' => _l('flexiblewa_weeks'),
    ],
    [
        'id' => 'months',
        'name' => _l('flexiblewa_months'),
    ]);

    return $periods;
}

function flexiblewa_create_storage_directory()
{
    flexiblewa_create_folder(FLEXIBLEWA_UPLOAD_FOLDER);
    flexiblewa_create_folder(FLEXIBLEWA_FILES_FOLDER);
}

function flexiblewa_get_upload_directory(){
    return FLEXIBLEWA_FILES_FOLDER;
}

function flexiblewa_create_folder($folder)
{
    if (!is_dir($folder)) {
        mkdir($folder, 0777);
        $fp = fopen(rtrim($folder, '/') . '/' . 'index.html', 'w');
        fclose($fp);
    }
}

/**
 * Upload a file and return it's path on success or false on failure
 *
 * @param string $input_name
 * @param integer $limit
 * @return string|bool
 */
function flexiblewa_upload_file($input_name, $limit = 1)
{
    $errors = [];
    $field = $input_name;
    $path = flexiblewa_get_upload_directory();

    $CI = &get_instance();

    if (
        isset($_FILES[$field]['name'])
        && ($_FILES[$field]['name'] != '' || is_array($_FILES[$field]['name']) && count($_FILES[$field]['name']) > 0)
    ) {
        if (!is_array($_FILES[$field]['name'])) {
            $_FILES[$field]['name'] = [$_FILES[$field]['name']];
            $_FILES[$field]['type'] = [$_FILES[$field]['type']];
            $_FILES[$field]['tmp_name'] = [$_FILES[$field]['tmp_name']];
            $_FILES[$field]['error'] = [$_FILES[$field]['error']];
            $_FILES[$field]['size'] = [$_FILES[$field]['size']];
        }

        for ($i = 0; $i < $limit; $i++) {
            $upload_file_name = $_FILES[$field]['name'][$i];

            if (_perfex_upload_error($_FILES[$field]['error'][$i])) {
                $errors[$upload_file_name] = _perfex_upload_error($_FILES[$field]['error'][$i]);

                continue;
            }

            // Get the temp file path
            $tmpFilePath = $_FILES[$field]['tmp_name'][$i];
            $filetype = $_FILES[$field]['type'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                _maybe_create_upload_path($path);
                $originalFilename = unique_filename($path, $upload_file_name);
                $filename = app_generate_hash() . '.' . get_file_extension($originalFilename);

                // In case client side validation is bypassed
                if (!_upload_extension_allowed($filename)) {
                    continue;
                }

                $new_file_path = $path . $filename;
                // Upload the file into the event uploads dir
                if (move_uploaded_file($tmpFilePath, $new_file_path)) {
                    // Return only the upload path; that's all we need.
                    return str_replace(FCPATH, '', $new_file_path);
                }
            }
        }
    }


    if (count($errors) > 0) {
        $message = '';
        foreach ($errors as $filename => $error_message) {
            $message .= $filename . ' - ' . $error_message . "\n";
        }

        throw new Exception($message);
    }

    return false;
}

function flexiblewa_get_relation_types(){
    return [
        [
            'id' => FLEXIBLEWA_PROJECT_RELATION,
            'name' => _l('project'),
        ],
        [
            'id' => FLEXIBLEWA_INVOICE_RELATION,
            'name' => _l('invoice'),
        ],
        [
            'id' => FLEXIBLEWA_CUSTOMER_RELATION,
            'name' => _l('client'),
        ],
        [
            'id' => FLEXIBLEWA_ESTIMATE_RELATION,
            'name' => _l('estimate'),
        ],
        [
            'id' => FLEXIBLEWA_CONTRACT_RELATION,
            'name' => _l('contract'),
        ],
        [
            'id' => FLEXIBLEWA_TICKET_RELATION,
            'name' => _l('ticket'),
        ],
        [
            'id' => FLEXIBLEWA_EXPENSE_RELATION,
            'name' => _l('expense'),
        ],
        [
            'id' => FLEXIBLEWA_LEAD_RELATION,
            'name' => _l('lead'),
        ],
        [
            'id' => FLEXIBLEWA_PROPOSAL_RELATION,
            'name' => _l('proposal'),
        ],
    ];
}

/**
 * Get relations based on relation type
 *
 * @param string $relation_type
 * @return array/mixed
 */
function flexiblewa_get_relations($relation_type){
    $CI = &get_instance();
    switch ($relation_type) {
        case FLEXIBLEWA_PROPOSAL_RELATION:
            $CI->load->model('proposals_model');
            $proposals = $CI->proposals_model->get();

            foreach($proposals as &$proposal){
                $proposal['name'] = $proposal['subject'];
            }
            
            return $proposals;
        
        case FLEXIBLEWA_LEAD_RELATION:
            $CI->load->model('leads_model');
            $leads = $CI->leads_model->get();
            
            return $leads;
        
        case FLEXIBLEWA_EXPENSE_RELATION:
            $CI->load->model('expenses_model');
            $expenses = $CI->expenses_model->get();

            foreach($expenses as &$expense){
                $expense['name'] = $expense['expense_name'];
            }
            
            return $expenses;
        
        case FLEXIBLEWA_TICKET_RELATION:
            $CI->load->model('tickets_model');
            $tickets = $CI->tickets_model->get();

            foreach($tickets as &$ticket){
                $ticket['name'] = $ticket['subject'];
                $ticket['id'] = $ticket['ticketid'];
            }
            
            return $tickets;
        
        case FLEXIBLEWA_CONTRACT_RELATION:
            $CI->load->model('contracts_model');
            $contracts = $CI->contracts_model->get();

            foreach($contracts as &$contract){
                $contract['name'] = $contract['subject'];
            }
            
            return $contracts;
        
        case FLEXIBLEWA_ESTIMATE_RELATION:
            $CI->load->model('estimates_model');
            $estimates = $CI->estimates_model->get();

            foreach($estimates as &$estimate){
                $estimate['name'] = sales_number_format($estimate['number'], $estimate['number_format'], $estimate['prefix'], $estimate['date']);
            }
            
            return $estimates;
        
        case FLEXIBLEWA_CUSTOMER_RELATION:
            $CI->load->model('clients_model');
            $customers = $CI->clients_model->get();

            foreach($customers as &$customer){
                $customer['name'] = $customer['company'];
                $customer['id'] = $customer['userid'];
            }

            return $customers;

        case FLEXIBLEWA_INVOICE_RELATION:
            $CI->load->model('invoices_model');
            $invoices = $CI->invoices_model->get();

            
            foreach($invoices as &$invoice){
                if ($invoice['status'] == Invoices_model::STATUS_DRAFT) {
                    $number = $invoice['prefix'] . 'DRAFT';
                } else {
                    $number = sales_number_format($invoice['number'], $invoice['number_format'], $invoice['prefix'], $invoice['date']);
                }

                $invoice['name'] = $number;
            }
            return $invoices;
            
        default:
            $CI->load->model('projects_model');
            return $CI->projects_model->get();
    }
}

/**
 * Get all task statuses except the one whose id is provided
 *
 * @param string $except
 * @return array
 */
function flexiblewa_get_task_statuses($except = ''){
    $CI = &get_instance();
    $CI->load->model('Tasks_model');

    if(empty($except)){
        return $CI->tasks_model->get_statuses();
    }

    $statuses = array_filter($CI->tasks_model->get_statuses(), function($status) use($except){
        return $status['id'] != $except;
    });

    return array_values($statuses);
}

function flexiblewa_get_task_data($task){
    return [
        'startdate' => $task->startdate,
        'repeat_every' => $task->repeat_every,
        'name' => $task->name,
        'duedate' => $task->duedate
    ];
}

function flexiblewa_get_file_url($file_name)
{
    return site_url($file_name);
}

/**
 * Execute workflow actions for given task-section combo
 *
 * @param array $data
 * @return void
 * @throws Exception
 */
function flexiblewa_execute_task_actions($data){
    $CI = &get_instance();
    $CI->load->model('flexiblewa/flexibleworkflow_model');
    $CI->load->model('tasks_model');

    $conditions = [
        'section_id' => $data['status']
    ];

    $workflows = $CI->flexibleworkflow_model->all($conditions);
    $task = $CI->tasks_model->get($data['task_id']);
    flexiblewa_execute_workflows_actions($CI, $workflows, $task);
}

/**
 * Execute workflow actions for newly created task
 *
 * @param integer $task_id
 * @return void
 * @throws Exception
 */
function flexiblewa_execute_new_task_actions($task_id){
    $CI = &get_instance();
    $CI->load->model('flexiblewa/flexibleworkflow_model');
    $CI->load->model('tasks_model');
    $task = $CI->tasks_model->get($task_id);
    $workflows = $CI->flexibleworkflow_model->all(['section_id' => $task->status]);
    try{
        flexiblewa_execute_workflows_actions($CI, $workflows, $task);
    }catch (Exception $e) {
        log_activity(FLEXIBLEWA_MODULE_NAME . $e->getMessage());
    }
}


/**
 * @param $CI
 * @param $workflows
 * @param $task
 * @return void
 * @throws Exception
 */
function flexiblewa_execute_workflows_actions($CI,$workflows,$task){
    $taskId = $task->id;
    if($workflows) {
        //let's set the user performing the automation to Automator Workflow User

        $currentStaffUserID = get_staff_user_id();
        $currentClientUserID = get_client_user_id();
        $automatorUserId = flexiblewa_automator_userid();
        $user_data = [
            'staff_user_id'   => $automatorUserId,
            'staff_logged_in'=> true
        ];
        $CI->session->set_userdata($user_data);
        //logout the client if logged in
        if($currentClientUserID){
            $CI->session->unset_userdata('client_user_id');
            $CI->session->unset_userdata('client_logged_in');
        }

        foreach($workflows as $workflow){
            $log_message = "Workflow Automation for {$task->name} task: ";

            switch ($workflow['rule_id']) {
                case FLEXIBLEWA_ADD_NEW_FILE_ACTION:
                    $attachment = [
                        [
                            'name' => basename($workflow['rule_value']),
                            'link' => flexiblewa_get_file_url($workflow['rule_value'])
                        ]
                    ];
                    $external = true;

                    if($CI->tasks_model->add_attachment_to_database($task->id, $attachment, $external)){
                        $log_message .= $workflow['rule_name'] . ' ' . $workflow['rule_value'];
                    }
                    break;
                case FLEXIBLEWA_MARK_AS_COMPLETE_ACTION:
                    if($CI->tasks_model->mark_as($workflow['rule_value'], $task->id)){
                        $log_message .= $workflow['rule_name'] . ' ' . flexiblewa_get_task_status_name($workflow['rule_value']);
                    }
                    break;

                case FLEXIBLEWA_MOVE_TO_ANOTHER_RELATION_ACTION:
                    //the value was joined with comma, so we need to split it
                    $relations = explode(',', $workflow['rule_value']);
                    $task_data = array_merge(flexiblewa_get_task_data($task), [
                        'rel_type' => $relations[0],
                        'rel_id' => $relations[1]
                    ]);
                    //let us check if the relation exists
                    $relation_exists = flexiblewa_get_relation($relations[0], $relations[1]);
                    if(!$relation_exists){
                        //we need to delete this rule as the relation does not exist
                        $CI->flexibleworkflow_model->delete(['id' => $workflow['id']]);
                    }else{
                        if($CI->tasks_model->update($task_data, $task->id)){
                            $log_message .= $workflow['rule_name'] . ' ' . $workflow['rule_value'];
                        }
                    }

                    break;
                case FLEXIBLEWA_MOVE_TO_SECTION_ACTION:
                    if($CI->tasks_model->mark_as($workflow['rule_value'], $task->id)){
                        $log_message .= $workflow['rule_name'] . ' ' . flexiblewa_get_task_status_name($workflow['rule_value']);
                    }
                    break;
                case FLEXIBLEWA_ADD_NEW_FOLLOWER_ACTION:
                    $followers = explode(',', $workflow['rule_value']);
                    $staff_names = [];

                    for ($i=0; $i < count($followers); $i++) {
                        $follower_data = [
                            'taskid' => $task->id,
                            'follower' => $followers[$i],
                        ];
                        //check if the staff exists
                        $staff = get_staff($followers[$i]);
                        if(!$staff) continue;
                        if($CI->tasks_model->add_task_followers($follower_data)){
                            $staff_names[] = get_staff_full_name($follower_data['follower']);
                        }
                    }

                    $log_message .= $workflow['rule_name'] . ' ' . implode(', ', $staff_names);

                    break;
                case FLEXIBLEWA_ADD_NEW_COMMENT_ACTION:
                    $comment_data = [
                        'taskid' => $task->id,
                        'content' => $workflow['rule_value'],
                    ];

                    $comment_added = $CI->tasks_model->add_task_comment($comment_data);

                    if($comment_added){
                        $log_message .= $workflow['rule_name'] . ' ' . $workflow['rule_value'];
                    }
                    break;
                case FLEXIBLEWA_ADD_NEW_REMINDER_ACTION:
                    $CI->load->model('misc_model');
                    $CI->load->model('staff_model');

                    $reminder_values = explode(',', $workflow['rule_value']);
                    //check if staff exists
                    $staff = get_staff($reminder_values[1]);
                    if($staff){
                        $duedate = new Carbon();
                        $duedate->add($reminder_values[0]);

                        $user_data = [
                            'staff_user_id'   => $reminder_values[1],
                            'staff_logged_in'=> true
                        ];

                        $CI->session->set_userdata($user_data);

                        $reminder_data = [
                            'rel_type' => FLEXIBLEWA_TASK_RELATION_TYPE,
                            'rel_id' => $task->id,
                            'date' => to_sql_date($duedate->format('Y-m-d')),
                            'staff' => $reminder_values[1],
                            'notify_by_email' => 1,
                            'description' => "Flexible Workflow Automation reminder for {$task->name} task"
                        ];

                        // We only pass the task id here because it's required by the method
                        $reminder_added = $CI->misc_model->add_reminder($reminder_data, $task->id);

                        if($reminder_added){
                            // We have to fetch the staff from the db as using the helper
                            // function picks the cached name of the previously logged in user
                            $staff = $CI->staff_model->get($reminder_values[1]);
                            $log_message .= $workflow['rule_name'] . ' ' . $reminder_values[0] . ' for ' . $staff->full_name;
                        }
                        //let's restore the user performing the automation to the automator user
                        //this is to help next action to be performed by the automator user
                        $user_data = [
                            'staff_user_id'   => $automatorUserId,
                            'staff_logged_in'=> true];
                        $CI->session->set_userdata($user_data);
                    }else{
                        //we need to delete this rule as the staff does not exist
                        $CI->flexibleworkflow_model->delete(['id' => $workflow['id']]);
                    };
                    break;
                case FLEXIBLEWA_ADD_NEW_CHECKLIST_ITEM_ACTION:
                    $checklists = explode(',', $workflow['rule_value']);
                    $checklist_added = false;
                    for($i = 0; $i < count($checklists); $i++){
                        $checklist_data = [
                            'taskid' => $task->id,
                            'description' => $checklists[$i],
                            'list_order' => ($i + 1)
                        ];

                        $checklist_added = $CI->tasks_model->add_checklist_item($checklist_data);
                    }

                    if($checklist_added){
                        $log_message .= $workflow['rule_name'] . ' ' . $workflow['rule_value'];
                    }

                    break;
                case FLEXIBLEWA_SET_PRIORITY_TO_ACTION:
                    $task_data = array_merge(flexiblewa_get_task_data($task), [
                        'duedate' => $task->duedate,
                        'priority' => $workflow['rule_value']
                    ]);

                    if($CI->tasks_model->update($task_data, $task->id)){
                        $log_message .= $workflow['rule_name'] . ' ' . task_priority($workflow['rule_value']);
                    }

                    break;
                case FLEXIBLEWA_SET_DUE_DATE_TO_ACTION:
                    $duedate = new Carbon();
                    $duedate->add($workflow['rule_value']);

                    $task_data = array_merge(flexiblewa_get_task_data($task), [
                        'duedate' => to_sql_date($duedate->format('Y-m-d'))
                    ]);

                    if($CI->tasks_model->update($task_data, $task->id)){
                        $log_message .= $workflow['rule_name'] . ' ' . $task_data['duedate'];
                    }

                    break;
                case FLEXIBLEWA_SET_ASSIGNED_TO_ACTION:
                    $task_data = [];
                    $assignees = explode(',', $workflow['rule_value']);

                    for($i = 0; $i < count($assignees); $i++){
                        $assignee_exists = $CI->tasks_model->get_task_assignees($assignees[$i]);

                        if(!$assignee_exists){
                            //let us check if the staff exists
                            $staff = get_staff($assignees[$i]);
                            if($staff){
                                $task_data = [
                                    'taskid' => $taskId,
                                    'assignee' => $assignees[$i]
                                ];
                                $CI->tasks_model->add_task_assignees($task_data);

                                $log_message .= $workflow['rule_name'] . ' ' . get_staff_full_name($assignees[$i]);
                            }
                        }
                    }
                    break;

                default:
                    # code...
                    break;
            }

            log_activity($log_message);
        }
        //let's restore the user performing the automation to the original user
        if($currentStaffUserID) {
            $user_data = [
                'staff_user_id' => $currentStaffUserID,
                'staff_logged_in' => true
            ];
            $CI->session->set_userdata($user_data);
        }
        if($currentClientUserID){
            $user_data = [
                'client_user_id' => $currentClientUserID,
                'client_logged_in' => true
            ];
            $CI->session->set_userdata($user_data);
        }
    }
}

function flexiblewa_automator_userid()
{
    //option to set the user id for the automator
    return (get_option('flexiblewa_automator_userid')) ? get_option('flexiblewa_automator_userid') : 0;
}

function flexiblewa_create_automator_bot(){
    if(!get_option('flexiblewa_automator_userid')){
        $CI = &get_instance();
        $CI->load->model('Staff_model');
    
        $staff_data = [
            'firstname' => 'Workflow',
            'lastname' => 'Automator(Bot) ',
            'email' => 'workflow@automatorproductivity.com',
            'password' => 'secretpassword123',
        ];

        $automator_user_id = $CI->staff_model->add($staff_data);

        if($automator_user_id){
            add_option('flexiblewa_automator_userid', $automator_user_id);
        }
    }
}

function flexiblewa_get_display_value($rule_id, $rule_value){
    switch ($rule_id) {
        case FLEXIBLEWA_ADD_NEW_FILE_ACTION:
            $name = basename($rule_value);
            $link = flexiblewa_get_file_url($rule_value);
            $label = _l('flexiblewa_view_file');
            return "<a href='$link' title='$name' target='blank' />$label</a>";

        case FLEXIBLEWA_MARK_AS_COMPLETE_ACTION:
            return $rule_value ? _l('flexiblewa_true') : _l('flexiblewa_false');
        
        case FLEXIBLEWA_MOVE_TO_ANOTHER_RELATION_ACTION:
            //the value was joined with comma, so we need to split it
            list($relation_type, $relation_id) = explode(',', $rule_value);
            $relation = (array)flexiblewa_get_relation($relation_type, $relation_id);
            if(!$relation) return "";
            $relation_type = ucfirst($relation_type);
            $type_label = _l('flexiblewa_relation_type');
            $name_label = _l('flexiblewa_relation_name');
            $name = $relation['name'];
            return "<p><strong>$type_label: </strong>$relation_type</p> <p><strong>$name_label: </strong>$name</p>";

        case FLEXIBLEWA_MOVE_TO_SECTION_ACTION:
            return flexiblewa_get_task_status_name($rule_value);

        case FLEXIBLEWA_ADD_NEW_FOLLOWER_ACTION:
            $followers = explode(',', $rule_value);
            $staff_names = [];

            for ($i=0; $i < count($followers); $i++) {
                $staff_names[] = get_staff_full_name($followers[$i]);
            }

            return implode(', ', $staff_names);

        case FLEXIBLEWA_ADD_NEW_COMMENT_ACTION:
            return $rule_value;

        case FLEXIBLEWA_ADD_NEW_REMINDER_ACTION:
            $reminder_values = explode(',', $rule_value);

            return $reminder_values[0];

        case FLEXIBLEWA_ADD_NEW_CHECKLIST_ITEM_ACTION:
            return str_replace(',', '<br>', $rule_value);

        case FLEXIBLEWA_SET_PRIORITY_TO_ACTION:
            return task_priority($rule_value);

        case FLEXIBLEWA_SET_DUE_DATE_TO_ACTION:
            return $rule_value;

        case FLEXIBLEWA_SET_ASSIGNED_TO_ACTION:
            $assignees = explode(',', $rule_value);
            $assignee_names = [];

            for($i = 0; $i < count($assignees); $i++){
                $assignee_names[] = get_staff_full_name($assignees[$i]);
            }

            return implode(', ', $assignee_names);
        
        default:
            # code...
            break;
    }
}

function flexiblewa_get_relation($relation_type, $relation_id){
    $CI = &get_instance();
    switch ($relation_type) {
        case FLEXIBLEWA_PROPOSAL_RELATION:
            $CI->load->model('proposals_model');
            $proposal = $CI->proposals_model->get($relation_id);
            if(!$proposal) return [];
            $proposal['name'] = $proposal['subject'];
            return $proposal;
        
        case FLEXIBLEWA_LEAD_RELATION:
            $CI->load->model('leads_model');
            $lead = $CI->leads_model->get($relation_id);
            if(!$lead) return [];
            return $lead;
        
        case FLEXIBLEWA_EXPENSE_RELATION:
            $CI->load->model('expenses_model');
            $expense =  $CI->expenses_model->get($relation_id);
            if(!$expense) return [];
            return $expense;

        case FLEXIBLEWA_TICKET_RELATION:
            $CI->load->model('tickets_model');
            $ticket = $CI->tickets_model->get($relation_id);
            if(!$ticket) return [];
            $ticket['name'] = $ticket['subject'];
            $ticket['id'] = $ticket['ticketid'];
            return $ticket;
        
        case FLEXIBLEWA_CONTRACT_RELATION:
            $CI->load->model('contracts_model');
            $contract = $CI->contracts_model->get($relation_id);
            if(!$contract) return [];
            $contract['name'] = $contract['subject'];
            return $contract;
        
        case FLEXIBLEWA_ESTIMATE_RELATION:
            $CI->load->model('estimates_model');
            $estimate = $CI->estimates_model->get($relation_id);
            if(!$estimate) return [];
            $estimate['name'] = sales_number_format($estimate['number'], $estimate['number_format'], $estimate['prefix'], $estimate['date']);
            
            return $estimate;
        
        case FLEXIBLEWA_CUSTOMER_RELATION:
            $CI->load->model('clients_model');
            $customer = (array)$CI->clients_model->get($relation_id);
            if(!$customer) return [];
            $customer['name'] = $customer['company'];
            $customer['id'] = $customer['userid'];
            return $customer;

        case FLEXIBLEWA_INVOICE_RELATION:
            $CI->load->model('invoices_model');
            $invoice = $CI->invoices_model->get($relation_id);
            if(!$invoice) return [];
            if ($invoice['status'] == Invoices_model::STATUS_DRAFT) {
                $number = $invoice['prefix'] . 'DRAFT';
            } else {
                $number = sales_number_format($invoice['number'], $invoice['number_format'], $invoice['prefix'], $invoice['date']);
            }

            $invoice['name'] = $number;
            return $invoice;
            
        default:
            $CI->load->model('projects_model');
            $project =  $CI->projects_model->get($relation_id);
            if(!$project) return [];
            return $project;
    }
}
