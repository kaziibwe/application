<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Customers_api_model Class.
 *
 * This model class represents the API functionality for customers.
 */
class Customers_api_model extends App_Model
{
    /**
     * Customers_api_model constructor.
     *
     * Initialize the Customers_api_model class and load the required model dependencies.
     */
    public function __construct()
    {
        parent::__construct();
        register_language_files('customers_api');
        load_client_language();
    }

    /**
     * Get data from a table based on the table name and ID.
     *
     * @param string $name       the name of the table
     * @param int    $id         the ID of the record to retrieve
     * @param object $clientInfo the client information
     *
     * @return array the response with the retrieved data
     */
    public function getTable($name, $id, $clientInfo)
    {
        $where['clientid'] = $clientInfo->client_id;
        switch ($name) {
            case 'projects':
                $this->load->model('projects_model');

                $data = $this->projects_model->get($id, $where);

                // Check if data is not empty and ID is not empty
                if (!empty($data) && !empty($id)) {
                    $data->project_members              = $this->projects_model->get_project_members($id);
                    $data->status_name                  = get_project_status_by_id($data->status)['name'];
                    $data->addedfrom_name               = get_staff_full_name($data->addedfrom);
                    $data->settings->available_features = convertSerializeDataToObject($data->settings->available_features);

                    // Check if project members data is not empty
                    if (!empty($data->project_members)) {
                        foreach ($data->project_members as $key => $project_member) {
                            $data->project_members[$key]['staff_name'] = get_staff_full_name($project_member['staff_id']);
                        }
                    }
                }

                // Check if data is not empty and ID is empty
                if (!empty($data) && empty($id)) {
                    foreach ($data as $key => $project) {
                        $data[$key]['status_name']    = get_project_status_by_id($project['status'])['name'];
                        $data[$key]['addedfrom_name'] = get_staff_full_name($project['addedfrom']);
                    }
                }

                break;

            case 'invoices':
                $this->load->model('invoices_model');

                $data = $this->invoices_model->get($id, $where);

                // Check if data is not empty and ID is not empty
                if (!empty($data) && !empty($id)) {
                    $data->addedfrom_name = get_staff_full_name($data->addedfrom);
                    $data->status_name    = get_invoice_status_by_id($data->status);

                    // Check if project data is not empty
                    if (!empty($data->project_data)) {
                        $data->project_data->settings->available_features = convertSerializeDataToObject($data->project_data->settings->available_features);
                    }
                }

                // Check if data is not empty and ID is empty
                if (!empty($data) && empty($id)) {
                    foreach ($data as $key => $invoice) {
                        $project_name                 = !empty($invoice['project_id']) ? get_project($invoice['project_id'])->name : '';
                        $data[$key]['client_name']    = get_client($invoice['clientid'])->company;
                        $data[$key]['project_name']   = $project_name;
                        $data[$key]['addedfrom_name'] = get_staff_full_name($invoice['addedfrom']);
                        $data[$key]['status_name']    = get_invoice_status_by_id($invoice['status']);
                    }
                }

                break;

            case 'estimates':
                $this->load->model('estimates_model');
                $where['status !='] = '1';

                $data = $this->estimates_model->get($id, $where);

                // Check if data is not empty and ID is not empty
                if (!empty($data) && !empty($id)) {
                    $data->addedfrom_name = get_staff_full_name($data->addedfrom);
                    $data->status_name    = estimate_status_by_id($data->status);

                    // Check if project data is not empty
                    if (!empty($data->project_data)) {
                        $data->project_data->settings->available_features = convertSerializeDataToObject($data->project_data->settings->available_features);
                    }
                }

                // Check if data is not empty and ID is empty
                if (!empty($data) && empty($id)) {
                    foreach ($data as $key => $estimate) {
                        $project_name                 = !empty($estimate['project_id']) ? get_project($estimate['project_id'])->name : '';
                        $data[$key]['client_name']    = get_client($estimate['clientid'])->company;
                        $data[$key]['project_name']   = $project_name;
                        $data[$key]['addedfrom_name'] = get_staff_full_name($estimate['addedfrom']);
                        $data[$key]['status_name']    = estimate_status_by_id($estimate['status']);
                    }
                }

                break;

            case 'proposals':
                $this->load->model('proposals_model');
                $proposalWhere = [
                    'rel_id'    => $where['clientid'],
                    'rel_type'  => 'customer',
                    'status !=' => '6',
                ];

                $data = $this->proposals_model->get($id, $proposalWhere);

                // Check if data is not empty and ID is not empty
                if (!empty($data) && !empty($id)) {
                    $data->addedfrom_name = get_staff_full_name($data->addedfrom);
                    $data->status_name    = get_proposals_status_by_id($data->status);

                    // Check if project data is not empty
                    if (!empty($data->project_data)) {
                        $data->project_data->settings->available_features = convertSerializeDataToObject($data->project_data->settings->available_features);
                    }
                }

                // Check if data is not empty and ID is empty
                if (!empty($data) && empty($id)) {
                    foreach ($data as $key => $proposals) {
                        $project_name                 = !empty($proposals['project_id']) ? get_project($proposals['project_id'])->name : '';
                        $data[$key]['client_name']    = get_client($proposals['rel_id'])->company;
                        $data[$key]['project_name']   = $project_name;
                        $data[$key]['addedfrom_name'] = get_staff_full_name($proposals['addedfrom']);
                        $data[$key]['status_name']    = get_proposals_status_by_id($proposals['status']);
                    }
                }

                break;

            case 'contracts':
                $this->load->model('contracts_model');
                $contractWhere = [
                    'client'                => $where['clientid'],
                    'not_visible_to_client' => '0',
                ];

                $data = $this->contracts_model->get($id, $contractWhere);

                // Check if data is not empty and ID is not empty
                if (!empty($data) && !empty($id)) {
                    $project_name         = !empty($data->project_id) ? get_project($data->project_id)->name : '';
                    $data->project_name   = $project_name;
                    $data->addedfrom_name = get_staff_full_name($data->addedfrom);
                }

                // Check if data is not empty and ID is empty
                if (!empty($data) && empty($id)) {
                    foreach ($data as $key => $contract) {
                        $project_name                 = !empty($contract['project_id']) ? get_project($contract['project_id'])->name : '';
                        $data[$key]['project_name']   = $project_name;
                        $data[$key]['addedfrom_name'] = get_staff_full_name($contract['addedfrom']);
                    }
                }

                break;

            case 'tickets':
                $this->load->model('tickets_model');
                if (!empty($id)) {
                    // Get ticket data by ID and client ID
                    $data = $this->tickets_model->get_ticket_by_id($id, $where['clientid']);
                    if (!empty($data)) {
                        // Retrieve ticket replies
                        $data->ticket_replies = $this->tickets_model->get_ticket_replies($id);
                        $project_name         = !empty($data->project_id) ? get_project($data->project_id)->name : '';
                        $data->addedfrom_name = get_staff_full_name($data->addedfrom);
                        $data->project_name   = $project_name;
                        $data->status_name    = ticket_status_translate($data->status);
                    }
                } else {
                    // Get tickets data for a specific client
                    $data = $this->tickets_model->get('', [db_prefix().'tickets.userid' => $where['clientid']]);
                    if (!empty($data)) {
                        foreach ($data as $key => $ticket) {
                            $project_name                 = !empty($ticket['project_id']) ? get_project($ticket['project_id'])->name : '';
                            $data[$key]['addedfrom_name'] = get_staff_full_name($ticket['addedfrom']);
                            $data[$key]['project_name']   = $project_name;
                            $data[$key]['status_name']    = ticket_status_translate($ticket['status']);
                        }
                    }
                }

                break;

            default:
                return '';
        }

        return !empty($data) ? $this->successResponse($data) : $this->notFoundResponse();
    }

    /**
     * Get project groups based on the group name.
     *
     * @param int    $projectId  the ID of the project
     * @param string $groupName  the name of the group to retrieve
     * @param object $clientInfo the client information
     *
     * @return array the response with the retrieved data
     */
    public function getProjectGroups($projectId, $groupName, $clientInfo)
    {
        $projectData = $this->getTable('projects', $projectId, $clientInfo);

        // Check if project data is empty
        if (empty($projectData['response']['data'])) {
            return $this->notFoundResponse();
        }

        $visibleTabs       = $projectData['response']['data']->settings;
        $availableFeatures = $visibleTabs->available_features;

        switch ($groupName) {
            case 'tasks':
                $this->load->model('tasks_model');

                // Check if viewing tasks is allowed
                if ('1' !== $visibleTabs->view_tasks) {
                    return $this->forbiddenResponse();
                }

                $data = $this->projects_model->get_tasks($projectId);

                // Add additional information to each task
                if (!empty($data)) {
                    foreach ($data as $key => $task) {
                        $data[$key]['status_name']    = get_task_status_by_id($task['status'])['name'];
                        $data[$key]['addedfrom_name'] = get_staff_full_name($task['addedfrom']);
                        $data[$key]['assignees']      = $this->tasks_model->get_task_assignees($task['id']);
                        $data[$key]['followers_ids']  = $this->tasks_model->get_task_followers($task['id']);
                    }
                }
                break;

            case 'timesheets':
                // Check if viewing timesheets is allowed
                if ('1' !== $visibleTabs->view_timesheets) {
                    return $this->forbiddenResponse();
                }

                $data = $this->projects_model->get_timesheets($projectId);
                break;

            case 'milestones':
                // Check if viewing milestones is allowed
                if ('1' !== $visibleTabs->view_milestones) {
                    return $this->forbiddenResponse();
                }

                $data = $this->projects_model->get_milestones($projectId);
                break;

            case 'discussions':
                // Check if project discussions feature is available
                if ('1' !== $availableFeatures['project_discussions']) {
                    return $this->forbiddenResponse();
                }

                $data = $this->projects_model->get_discussions($projectId);
                break;

            case 'proposals':
                $this->load->model('proposals_model');

                // Check if project proposals feature is available
                if ('1' !== $availableFeatures['project_proposals']) {
                    return $this->forbiddenResponse();
                }

                $data = $this->proposals_model->get('', ['project_id' => $projectId]);
                break;

            case 'estimates':
                $this->load->model('estimates_model');

                // Check if project estimates feature is available
                if ('1' !== $availableFeatures['project_estimates']) {
                    return $this->forbiddenResponse();
                }

                $data = $this->estimates_model->get('', ['project_id' => $projectId]);
                break;

            case 'invoices':
                $this->load->model('invoices_model');

                // Check if project invoices feature is available
                if ('1' !== $availableFeatures['project_invoices']) {
                    return $this->forbiddenResponse();
                }

                $data = $this->invoices_model->get('', ['project_id' => $projectId]);
                break;

            case 'subscriptions':
                $this->load->model('subscriptions_model');

                // Check if project subscriptions feature is available
                if ('1' !== $availableFeatures['project_invoices']) {
                    return $this->forbiddenResponse();
                }

                $data = $this->subscriptions_model->get(['project_id' => $projectId]);
                break;

            case 'expenses':
                $this->load->model('expenses_model');

                // Check if project expenses feature is available
                if ('1' !== $availableFeatures['project_expenses']) {
                    return $this->forbiddenResponse();
                }

                $data = $this->expenses_model->get('', ['project_id' => $projectId]);
                break;

            case 'credit_notes':
                $this->load->model('credit_notes_model');

                // Check if project credit notes feature is available
                if ('1' !== $availableFeatures['project_credit_notes']) {
                    return $this->forbiddenResponse();
                }

                $data = $this->credit_notes_model->get('', ['project_id' => $projectId]);
                break;

            case 'create_task_data':
                // Get priority and project members for creating a task
                $data['priority']        = get_tasks_priorities();
                $data['project_members'] = $this->projects_model->get_project_members($projectId, true);
                break;

            default:
                break;
        }

        // Return appropriate response based on data availability
        return !empty($data) ? $this->successResponse($data) : $this->notFoundResponse();
    }

    /**
     * Add comments to a specific group.
     *
     * @param array  $comments       the comments to be added
     * @param array  $additionalData additional data related to the comments
     * @param object $clientInfo     the client information
     *
     * @return array the response with the result of adding the comments
     */
    public function addComments($comments, $additionalData, $clientInfo)
    {
        $requiredData = [
            'content' => '',
        ];

        $postData = $comments;
        $postData = array_merge($requiredData, $postData);

        $this->form_validation->set_data($postData);
        $this->form_validation->set_rules('content', 'Content', 'trim|required');

        // Validate the comment data
        if (false == $this->form_validation->run()) {
            return [
                'response' => [
                    'message' => validation_errors(),
                ],
                'response_code' => 422,
            ];
        }

        switch ($additionalData['group']) {
            case 'contract_comment':
                $this->load->model('contracts_model');

                // Get contracts related to the client
                $contracts    = $this->db->get_where(db_prefix().'contracts', ['client' => $clientInfo->client_id, 'not_visible_to_client' => '0'])->result_array();
                $contract_ids = array_column($contracts, 'id');

                // Check if the contract is logged in client's contract
                if (!in_array($additionalData['id'], $contract_ids)) {
                    return $this->forbiddenResponse();
                }

                $insertData = [
                    'contract_id' => $additionalData['id'],
                    'content'     => $comments['content'],
                ];

                $success = $this->contracts_model->add_comment($insertData, true);

                return [
                    'response' => [
                        'message' => ($success) ? _l('contract_comments_added_success') : _l('something_went_wrong'),
                    ],
                    'response_code' => ($success) ? 200 : 500,
                ];

                break;

            case 'proposals_comment':
                $this->load->model('proposals_model');

                // Get proposals related to the client
                $proposals    = $this->db->get_where(db_prefix().'proposals', ['rel_id' => $clientInfo->client_id, 'rel_type' => 'customer'])->result_array();
                $proposal_ids = array_column($proposals, 'id');

                // Check if the proposal is logged in client's proposal
                if (!in_array($additionalData['id'], $proposal_ids)) {
                    return $this->forbiddenResponse();
                }

                $insertData = [
                    'proposalid' => $additionalData['id'],
                    'content'    => $comments['content'],
                ];

                $success = $this->proposals_model->add_comment($insertData, true);

                return [
                    'response' => [
                        'message' => ($success) ? _l('proposal_comments_added_success') : _l('something_went_wrong'),
                    ],
                    'response_code' => ($success) ? 200 : 500,
                ];

                break;

            default:
                return $this->errorResponse();
                break;
        }
    }

    /**
     * Add a task to a project.
     *
     * @param int    $projectID  the ID of the project
     * @param array  $postData   the data of the task to be added
     * @param object $clientInfo the client information
     *
     * @return array the response with the result of the task addition
     */
    public function addTask($projectID, $postData, $clientInfo)
    {
        $this->load->model('tasks_model');

        $requiredData = [
            'name' => '',
            'priority' => '',
            'startdate' => '',
            'duedate' => ''
        ];

        $data = $postData;
        $data = array_merge($requiredData, $postData);
        $this->form_validation->set_data($data);

        // Check for validation
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('priority', 'Priority', 'trim|required');
        $this->form_validation->set_rules('startdate', 'Startdate', 'trim|required');
        $this->form_validation->set_rules('duedate', 'Duedate', 'trim|required');

        // Validate the task data
        if (false == $this->form_validation->run()) {
            return [
                'response' => [
                    'message' => validation_errors(),
                ],
                'response_code' => 422,
            ];
        }

        // Verify projects
        $projects    = $this->db->get_where(db_prefix().'projects', ['clientid' => $clientInfo->client_id])->result_array();
        $project_ids = array_column($projects, 'id');

        // Check if the project is client's project
        if (!in_array($projectID, $project_ids)) {
            return $this->forbiddenResponse();
        }

        // Verify permission
        $projectData = $this->getTable('projects', $projectID, $clientInfo);

        // Check if the user has permission to create tasks
        if ('1' !== $projectData['response']['data']->settings->create_tasks) {
            return $this->forbiddenResponse();
        }

        // Check if assignees are provided
        if (empty($postData['assignees'])) {
            return [
                'response' => [
                    'message' => _l('assignee_is_required'),
                ],
                'response_code' => 422,
            ];
        }

        // Assignees must be project members
        $projectMembers = $this->projects_model->get_project_members($projectID);
        foreach ($postData['assignees'] as $value) {
            if (!in_array($value, array_column($projectMembers, 'staff_id'))) {
                return [
                    'response' => [
                        'message' => _l('assignee_must_be_selected_project_member'),
                    ],
                    'response_code' => 422,
                ];
            }
        }

        // Prepare the task data for insertion
        $insertData = [
            'name'                  => $postData['name'],
            'priority'              => $postData['priority'],
            'milestone'             => $postData['milestone'] ?? '',
            'startdate'             => $postData['startdate'],
            'duedate'               => $postData['duedate'],
            'assignees'             => $postData['assignees'],
            'description'           => $postData['description'] ?? '',
            'rel_id'                => $projectID,
            'rel_type'              => 'project',
        ];

        // Add the task
        $output = $this->tasks_model->add($insertData, true);

        return [
            'response' => [
                'message' => $output ? _l('task_added_success') : _l('something_went_wrong'),
            ],
            'response_code' => $output ? 200 : 500,
        ];
    }

    /**
     * Update a task for a specific project.
     *
     * @param int    $projectID  the ID of the project
     * @param array  $postData   the data for updating the task
     * @param object $clientInfo the client information object
     *
     * @return array the response array
     */
    public function updateTask($projectID, $postData, $clientInfo)
    {
        $requiredData = [
            'id' => '',
            'name' => '',
            'priority' => '',
            'startdate' => '',
            'duedate' => ''
        ];

        $data = $postData;
        $data = array_merge($requiredData, $postData);
        $this->form_validation->set_data($data);

        // Set the form validation rules and data
        $this->form_validation->set_rules('id', 'Id', 'required');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('priority', 'Priority', 'trim|required');
        $this->form_validation->set_rules('startdate', 'Startdate', 'trim|required');
        $this->form_validation->set_rules('duedate', 'Duedate', 'trim|required');

        // Validate the task data
        if (false == $this->form_validation->run()) {
            return [
                'response' => [
                    'message' => validation_errors(),
                ],
                'response_code' => 422,
            ];
        }

        // Get the projects of the client
        $projects    = $this->db->get_where(db_prefix().'projects', ['clientid' => $clientInfo->client_id])->result_array();
        $project_ids = array_column($projects, 'id');

        // Check if the project is client's project
        if (!in_array($projectID, $project_ids)) {
            return $this->forbiddenResponse();
        }

        // Get the tasks related to the project added by the client
        $tasks    = $this->db->get_where(db_prefix().'tasks', ['rel_id' => $projectID, 'rel_type' => 'project', 'is_added_from_contact' => 1])->result_array();
        $task_ids = array_column($tasks, 'id');

        // Get project data and check if tasks can be edited
        $projectData = $this->getTable('projects', $projectID, $clientInfo);
        if ('1' !== $projectData['response']['data']->settings->edit_tasks) {
            return $this->forbiddenResponse();
        }

        $this->load->model('tasks_model');

        // Prepare the task data for update
        $insertData = [
            'name'              => $postData['name'],
            'priority'          => $postData['priority'],
            'milestone'         => $postData['milestone'] ?? '',
            'startdate'         => date('Y-m-d', strtotime($postData['startdate'])),
            'duedate'           => date('Y-m-d', strtotime($postData['duedate'])),
            'description'       => $postData['description'] ?? '',
            'rel_id'            => $projectID,
            'rel_type'          => 'project',
            'visible_to_client' => 1,
        ];

        // Update the task
        $output = $this->tasks_model->update($insertData, $postData['id'], true);

        return [
            'response' => [
                'message' => $output ? _l('task_update_success') : _l('no_data_updated'),
            ],
            'response_code' => 200,
        ];
    }

    /**
     * Add a new ticket.
     *
     * @param array  $postData   the data for the new ticket
     * @param object $clientInfo the client information object
     *
     * @return array the response array
     */
    public function addTicket($postData, $clientInfo)
    {
        $requiredData = [
            'subject' => '',
            'department' => '',
            'priority' => ''
        ];

        $data = $postData;
        $data = array_merge($requiredData, $postData);
        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('subject', 'Subject', 'trim|required');
        $this->form_validation->set_rules('department', 'Department', 'trim|required');
        $this->form_validation->set_rules('priority', 'Priority', 'trim|required');

        // Validate the ticket data
        if (false == $this->form_validation->run()) {
            return [
                'response' => [
                    'message' => validation_errors(),
                ],
                'response_code' => 422,
            ];
        }

        $insertData = [
            'userid'     => $clientInfo->client_id,
            'contactid'  => $clientInfo->contact_id,
            'subject'    => $postData['subject'],
            'department' => $postData['department'],
            'priority'   => $postData['priority'],
            'project_id' => $postData['project_id'] ?? '',
            'service'    => $postData['service'] ?? '',
            'message'    => $postData['message'] ?? '',
            'status'     => 1,
            'date'       => date('Y-m-d H:i:s'),
            'clientread' => 1,
        ];

        $this->load->model('tickets_model');
        // Add the ticket
        $res = $this->tickets_model->add($insertData);

        return [
            'response' => [
                'message' => $res ? _l('ticket_added_success') : _l('something_went_wrong'),
            ],
            'response_code' => $res ? 200 : 500,
        ];
    }

    /**
     * Add a reply to a ticket.
     *
     * @param array  $postData   the data for the ticket reply
     * @param object $clientInfo the client information object
     * @param int    $ticketID   the ID of the ticket
     *
     * @return array the response array
     */
    public function addReplyToATicket($postData, $clientInfo, $ticketID)
    {
        $requiredData = [
            'message' => ''
        ];

        $data = $postData;
        $data = array_merge($requiredData, $postData);
        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('message', 'Ticket Reply', 'trim|required');

        // Validate the ticket reply message
        if (false == $this->form_validation->run()) {
            return [
                'response' => [
                    'message' => validation_errors(),
                ],
                'response_code' => 422,
            ];
        }

        // Get tickets for the client
        $tickets    = $this->db->get_where(db_prefix().'tickets', ['userid' => $clientInfo->client_id])->result_array();
        $ticket_ids = array_column($tickets, 'ticketid');

        // Check if the provided ticket ID belongs to the client
        if (!in_array($ticketID, $ticket_ids)) {
            return $this->forbiddenResponse();
        }

        $this->load->model('tickets_model');
        // Add the reply to the ticket
        $res = $this->tickets_model->add_reply([
            'message'   => $postData['message'],
            'contactid' => $clientInfo->contact_id,
            'userid'    => $clientInfo->client_id,
        ], $ticketID);

        return [
            'response' => [
                'message' => $res ? _l('reply_added_success') : _l('something_went_wrong'),
            ],
            'response_code' => $res ? 200 : 500,
        ];
    }

    /**
     * Get miscellaneous groups based on the given group name and client information.
     *
     * @param string $groupName  the name of the group to retrieve
     * @param object $clientInfo the client information object
     *
     * @return array the response array
     */
    public function getMiscellaneousGroups($groupName, $clientInfo)
    {
        // Set the client ID in the where condition
        $where['clientid'] = $clientInfo->client_id;
        // Initialize the data array
        $data = [];

        switch ($groupName) {
            case 'client_menu':
                // Get contact permissions items based on the client ID
                $items = $this->db->get_where(db_prefix().'contact_permissions', ['userid' => $where['clientid']])->result_array();
                // Get the list of available contact permissions
                $permissions = get_contact_permissions();

                if (!empty($items)) {
                    // Iterate through each permission
                    foreach ($permissions as $permission) {
                        // Check if the permission ID exists in the items array
                        if (in_array($permission['id'], array_column($items, 'permission_id'))) {
                            // Add the permission details to the data array
                            $data[] = [
                                'id'         => $permission['id'],
                                'name'       => $permission['name'],
                                'short_name' => $permission['short_name'],
                            ];
                        }
                    }
                }

                break;

            case 'departments':
                // Get departments that are not hidden from the client
                $departments = $this->db->get_where(db_prefix().'departments', ['hidefromclient' => 0])->result_array();

                if (!empty($departments)) {
                    // Iterate through each department
                    foreach ($departments as $value) {
                        // Add the department details to the data array
                        $data[] = [
                            'departmentid' => $value['departmentid'],
                            'name'         => $value['name'],
                        ];
                    }
                }

                break;

            case 'get_ticket_priorities':
                // Load the tickets model and retrieve the priority
                $this->load->model('tickets_model');
                $data = $this->tickets_model->get_priority();

                break;

            case 'get_services':
                // Load the tickets model and retrieve the services
                $this->load->model('tickets_model');
                $data = $this->tickets_model->get_service();

                break;

            default:
                // Return an error response for an invalid group name
                return $this->errorResponse();
        }

        // Return the success response if data is not empty, otherwise return the not found response
        return (!empty($data)) ? $this->successResponse($data) : $this->notFoundResponse();
    }

    /**
     * Generate a success response with the provided data.
     *
     * @param mixed $data the data to include in the response
     *
     * @return array the success response array
     */
    private function successResponse($data)
    {
        return [
            'response' => [
                'data'    => $data,
                'message' => _l('data_retrived_success'),
            ],
            'response_code' => 200,
        ];
    }

    /**
     * Generate a not found response.
     *
     * @return array the not found response array
     */
    private function notFoundResponse()
    {
        return [
            'response' => [
                'message' => _l('data_not_found'),
            ],
            'response_code' => 404,
        ];
    }

    /**
     * Generate a forbidden response.
     *
     * @return array the forbidden response array
     */
    private function forbiddenResponse()
    {
        return [
            'response' => [
                'message' => _l('not_permission_to_perform_this_action'),
            ],
            'response_code' => 403,
        ];
    }

    /**
     * Generate an error response.
     *
     * @return array the error response array
     */
    private function errorResponse()
    {
        return [
            'response' => [
                'message' => _l('something_went_wrong'),
            ],
            'response_code' => 500,
        ];
    }
}
