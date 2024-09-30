<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';

class Projects extends \CustomerApi\RestController
{
    protected $clientInfo;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mmb/customers_api_model');
        $this->load->helper('mmb/customers_api');

        if (!isset(isAuthorized()['status'])) {
            $this->response(isAuthorized()['response'], isAuthorized()['response_code']);
        }

        $this->clientInfo = isAuthorized();

        if (!has_contact_permission('projects', $this->clientInfo['data']->contact_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], 403);
        }
    }

    /**
     * @api {get} /mmb/v1/projects/id/:id Request Project By ID
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetProjectById
     *
     * @apiGroup Projects
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiParam {Number} id <span class="btn btn-xs btn-danger">Required</span> Project unique ID.
     *
     * @apiSampleRequest /mmb/v1/projects/
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Object}  data    Project information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "data": {
     *             "id": "2",
     *             "name": "SEO Optimization",
     *             "description": "<p>I should understand that better</p>",
     *             "status": "2",
     *             "clientid": "3",
     *             "billing_type": "3",
     *             "start_date": "2023-05-12",
     *             "deadline": "2023-05-19",
     *             "project_created": "2023-05-12",
     *             "date_finished": null,
     *             "progress": "0",
     *             "progress_from_tasks": "1",
     *             "project_cost": "0.00",
     *             "project_rate_per_hour": "0.00",
     *             "estimated_hours": null,
     *             "addedfrom": "1",
     *             "contact_notification": "1",
     *             "notify_contacts": "a:0:{}",
     *             "shared_vault_entries": [],
     *             "settings": {
     *                 "available_features": {
     *                     "project_overview": 1,
     *                     "project_tasks": 1,
     *                     "project_timesheets": 1,
     *                     "project_milestones": 1,
     *                     "project_files": 1,
     *                     "project_discussions": 1,
     *                     "project_gantt": 1,
     *                     "project_tickets": 1,
     *                     "project_contracts": 1,
     *                     "project_proposals": 1,
     *                     "project_estimates": 1,
     *                     "project_invoices": 1,
     *                     "project_subscriptions": 1,
     *                     "project_expenses": 1,
     *                     "project_credit_notes": 1,
     *                     "project_notes": 1,
     *                     "project_activity": 1
     *                 },
     *                 "view_tasks": "1",
     *                 "create_tasks": "1",
     *                 "edit_tasks": "1",
     *                 "comment_on_tasks": "1",
     *                 "view_task_comments": "1",
     *                 "view_task_attachments": "1",
     *                 "view_task_checklist_items": "1",
     *                 "upload_on_tasks": "1",
     *                 "view_task_total_logged_time": "1",
     *                 "view_finance_overview": "1",
     *                 "upload_files": "1",
     *                 "open_discussions": "1",
     *                 "view_milestones": "1",
     *                 "view_gantt": "1",
     *                 "view_timesheets": "1",
     *                 "view_activity_log": "1",
     *                 "view_team_members": "1",
     *                 "hide_tasks_on_main_tasks_table": "0"
     *             },
     *             "client_data": {
     *                 "userid": "3",
     *                 "company": "Barton PLC",
     *                 "vat": "",
     *                 "phonenumber": "+1 (704) 220-4245",
     *                 "country": "66",
     *                 "city": "Vickyport",
     *                 "zip": "47469-3292",
     *                 "state": "Ohio",
     *                 "address": "7600 Tanya Burg",
     *                 "website": "yost.com",
     *                 "datecreated": "2023-05-12 16:17:22",
     *                 "active": "1",
     *                 "leadid": null,
     *                 "billing_street": "",
     *                 "billing_city": "",
     *                 "billing_state": "",
     *                 "billing_zip": "",
     *                 "billing_country": "0",
     *                 "shipping_street": "",
     *                 "shipping_city": "",
     *                 "shipping_state": "",
     *                 "shipping_zip": "",
     *                 "shipping_country": "0",
     *                 "longitude": null,
     *                 "latitude": null,
     *                 "default_language": "",
     *                 "default_currency": "1",
     *                 "show_primary_contact": "0",
     *                 "stripe_id": null,
     *                 "registration_confirmed": "1",
     *                 "addedfrom": "1"
     *             },
     *             "project_members": [
     *                 {
     *                     "email": "kameron34@example.org",
     *                     "project_id": "2",
     *                     "staff_id": "4",
     *                     "staff_name": "Coty Wehner"
     *                 },
     *                 {
     *                     "email": "darion.considine@example.org",
     *                     "project_id": "2",
     *                     "staff_id": "3",
     *                     "staff_name": "Alford Jakubowski"
     *                 }
     *             ],
     *             "status_name": "In Progress",
     *             "addedfrom_name": "admin main"
     *         },
     *         "message": "Data retrieved successfully"
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data found"
     *     }
     */

    /**
     * @api {get} /mmb/v1/projects List All Projects
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetAllProjects
     *
     * @apiGroup Projects
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest /mmb/v1/projects
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Projects information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "data": [
     *             {
     *                 "id": "2",
     *                 "name": "SEO Optimization",
     *                 "description": "<p>I should understand that better</p>"
     *                 "status": "2",
     *                 "clientid": "3",
     *                 "billing_type": "3",
     *                 "start_date": "2023-05-12",
     *                 "deadline": "2023-05-19",
     *                 "project_created": "2023-05-12",
     *                 "date_finished": null,
     *                 "progress": "0",
     *                 "progress_from_tasks": "1",
     *                 "project_cost": "0.00",
     *                 "project_rate_per_hour": "0.00",
     *                 "estimated_hours": null,
     *                 "addedfrom": "1",
     *                 "contact_notification": "1",
     *                 "notify_contacts": "a:0:{}",
     *                 "userid": "3",
     *                 "company": "Barton PLC",
     *                 "vat": "",
     *                 "phonenumber": "+1 (704) 220-4245",
     *                 "country": "66",
     *                 "city": "Vickyport",
     *                 "zip": "47469-3292",
     *                 "state": "Ohio",
     *                 "address": "7600 Tanya Burg",
     *                 "website": "yost.com",
     *                 "datecreated": "2023-05-12 16:17:22",
     *                 "active": "1",
     *                 "leadid": null,
     *                 "billing_street": "",
     *                 "billing_city": "",
     *                 "billing_state": "",
     *                 "billing_zip": "",
     *                 "billing_country": "0",
     *                 "shipping_street": "",
     *                 "shipping_city": "",
     *                 "shipping_state": "",
     *                 "shipping_zip": "",
     *                 "shipping_country": "0",
     *                 "longitude": null,
     *                 "latitude": null,
     *                 "default_language": "",
     *                 "default_currency": "1",
     *                 "show_primary_contact": "0",
     *                 "stripe_id": null,
     *                 "registration_confirmed": "1",
     *                 "status_name": "In Progress",
     *                 "addedfrom_name": "admin main"
     *             }
     *             ...
     *         ],
     *         "message": "Projects retrieved successfully"
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data found"
     *     }
     */

    /**
     * @api {get} /mmb/v1/projects/id/:id/group/:groupname Request Project Groups Information
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetProjectGroups
     *
     * @apiGroup Projects
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiParam {Number} id <span class="btn btn-xs btn-danger">Required</span> Project unique ID.
     * @apiParam {string="tasks","timesheets","milestones","discusssions","proposals","estimates","invoices","subscriptions","expenses","credit_notes","create_task_data"} group <span class="btn btn-xs btn-danger">Required</span> Project group name.
     *
     * @apiSampleRequest off
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Project group information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "data": [
     *             {
     *                 "id": "5",
     *                 "name": "Add a Test Case for the GCI Website",
     *                 "description": "",
     *                 "priority": "2",
     *                 "dateadded": "2023-05-12 17:28:35",
     *                 "startdate": "2023-05-12",
     *                 "duedate": null,
     *                 "datefinished": null,
     *                 "addedfrom": "1",
     *                 "is_added_from_contact": "0",
     *                 "status": "4",
     *                 "recurring_type": null,
     *                 "repeat_every": null,
     *                 "recurring": "0",
     *                 "is_recurring_from": null,
     *                 "cycles": "0",
     *                 "total_cycles": "0",
     *                 "custom_recurring": "0",
     *                 "last_recurring_date": null,
     *                 "rel_id": "2",
     *                 "rel_type": "project",
     *                 "is_public": "0",
     *                 "billable": "1",
     *                 "billed": "0",
     *                 "invoice_id": "0",
     *                 "hourly_rate": "25.00",
     *                 "milestone": "0",
     *                 "kanban_order": "1",
     *                 "milestone_order": "0",
     *                 "visible_to_client": "1",
     *                 "deadline_notified": "0",
     *                 "milestone_name": null,
     *                 "total_logged_time": null,
     *                 "assignees_ids": null,
     *                 "status_name": "In Progress",
     *                 "addedfrom_name": "admin main",
     *                 "assignees": [],
     *                 "followers_ids": []
     *             },
     *             {
     *                 "id": "7",
     *                 "name": "Test task 1",
     *                 "description": "",
     *                 "priority": "2",
     *                 "dateadded": "2023-05-15 10:19:24",
     *                 "startdate": "2023-05-15",
     *                 "duedate": null,
     *                 "datefinished": null,
     *                 "addedfrom": "3",
     *                 "is_added_from_contact": "1",
     *                 "status": "1",
     *                 "recurring_type": null,
     *                 "repeat_every": null,
     *                 "recurring": "0",
     *                 "is_recurring_from": null,
     *                 "cycles": "0",
     *                 "total_cycles": "0",
     *                 "custom_recurring": "0",
     *                 "last_recurring_date": null,
     *                 "rel_id": "2",
     *                 "rel_type": "project",
     *                 "is_public": "0",
     *                 "billable": "0",
     *                 "billed": "0",
     *                 "invoice_id": "0",
     *                 "hourly_rate": "0.00",
     *                 "milestone": "0",
     *                 "kanban_order": "1",
     *                 "milestone_order": "0",
     *                 "visible_to_client": "1",
     *                 "deadline_notified": "0",
     *                 "milestone_name": null,
     *                 "total_logged_time": null,
     *                 "assignees_ids": "4, 3",
     *                 "status_name": "Not Started",
     *                 "addedfrom_name": "Alford Jakubowski",
     *                 "assignees": [
     *                     {
     *                         "id": "6",
     *                         "assigneeid": "3",
     *                         "assigned_from": "3",
     *                         "firstname": "Alford",
     *                         "lastname": "Jakubowski",
     *                         "full_name": "Alford Jakubowski",
     *                         "is_assigned_from_contact": "1"
     *                     },
     *                     {
     *                         "id": "5",
     *                         "assigneeid": "4",
     *                         "assigned_from": "3",
     *                         "firstname": "Coty",
     *                         "lastname": "Wehner",
     *                         "full_name": "Coty Wehner",
     *                         "is_assigned_from_contact": "1"
     *                     }
     *                 ],
     *                 "followers_ids": []
     *             }
     *         ],
     *         "message": "Data retrived successfully"
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data found"
     *     }
     */
    public function projects_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], 500);
        }

        // Get the project ID and group parameters from the API request.
        $projectID = $this->get('id');
        $group     = $this->get('group');

        // Retrieve project data for the given project ID and client information.
        $projectData = $this->customers_api_model->getTable('projects', $projectID, $this->clientInfo['data']);

        // If both the project ID and group parameters are provided, retrieve project data for the given project ID and group.
        if (!empty($group) && !empty($projectID)) {
            $projectData = $this->customers_api_model->getProjectGroups($projectID, $group, $this->clientInfo['data']);
            // Return the retrieved project data and response code as a response.
            $this->response($projectData['response'], $projectData['response_code']);
        }

        // If the group parameter is provided but the project ID is not, return an error response.
        if (!empty($group) && empty($projectID)) {
            $this->response(['message' => _l('something_went_wrong')], 500);
        }

        // Return the retrieved project data and response code as a response.
        $this->response($projectData['response'], $projectData['response_code']);
    }

    /**
     * @api {post} /mmb/v1/projects/id/:id/group/tasks Add task to a project
     *
     * @apiName AddTaskToAProject
     *
     * @apiGroup Task
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiParam {Number} id <span class="btn btn-xs btn-danger">Required</span> Project unique ID.
     * @apiParam {String} group=tasks <span class="btn btn-xs btn-danger">Required</span> Group Name.
     *
     * @apiSampleRequest off
     *
     * @apiBody {String} name          <span class="btn btn-xs btn-danger">Required</span> Task Name
     * @apiBody {Int}    priority      <span class="btn btn-xs btn-danger">Required</span> Priority ID
     * @apiBody {Date}   startdate     <span class="btn btn-xs btn-danger">Required</span> Task Startdate <code>(MySQL Date Format: YYYY-MM-DD) </code>
     * @apiBody {Date} duedate         <span class="btn btn-xs btn-danger">Required</span> Task Duedate  <code>(MySQL Date Format: YYYY-MM-DD) </code>
     * @apiBody {Array}  assignees[]   <span class="btn btn-xs btn-danger">Required</span> Task Assignees ID
     * @apiBody {String} [description] Task description
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "message": "Task added successfully."
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 403 Forbidden
     *     {
     *       "status": false,
     *       "message": "You do not have permission to perform this action"
     *     }
     */
    public function projects_post()
    {
        $projectID = $this->get('id');
        $group     = $this->get('group');

        $postData = $this->post();

        if (!empty($group)) {
            switch ($group) {
                case 'tasks':
                    $data = $this->customers_api_model->addTask($projectID, $postData, $this->clientInfo['data']);
                    $this->response($data['response'], $data['response_code']);
                    break;

                default:
                    break;
            }
        }

        $this->response(['message' => _l('something_went_wrong')], 500);
    }

    /**
     * @api {put} /mmb/v1/projects/id/:id/group/tasks Update task
     *
     * @apiName UpdateTask
     *
     * @apiGroup Task
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiParam {Number} id <span class="btn btn-xs btn-danger">Required</span> Project Unique ID.
     * @apiParam {String} group=tasks <span class="btn btn-xs btn-danger">Required</span> Group Name
     *
     * @apiSampleRequest off
     *
     * @apiBody {Number} id            <span class="btn btn-xs btn-danger">Required</span> Task ID
     * @apiBody {String} name          <span class="btn btn-xs btn-danger">Required</span> Task Name
     * @apiBody {Number} priority      <span class="btn btn-xs btn-danger">Required</span> Priority ID
     * @apiBody {Date} startdate       <span class="btn btn-xs btn-danger">Required</span> Task Startdate <code>(MySQL Date Format: YYYY-MM-DD) </code>
     * @apiBody {Date} duedate         <span class="btn btn-xs btn-danger">Required</span> Task Duedate  <code>(MySQL Date Format: YYYY-MM-DD) </code>
     * @apiBody {Array}  assignees[]   <span class="btn btn-xs btn-danger">Required</span> Task Assignees ID
     * @apiBody {String} [description] Task description
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {String}  message Success message
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "message": "Task updated successfully."
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 403 Forbidden
     *     {
     *       "status": false,
     *       "message": "You do not have permission to perform this action"
     *     }
     */
    public function projects_put()
    {
        $projectID = $this->get('id');
        $group     = $this->get('group');

        $updateData = $this->put();

        if (!empty($group)) {
            switch ($group) {
                case 'tasks':
                    $data = $this->customers_api_model->updateTask($projectID, $updateData, $this->clientInfo['data']);
                    $this->response($data['response'], $data['response_code']);
                    break;

                default:
                    break;
            }
        }

        $this->response(['message' => _l('something_went_wrong')], 500);
    }
}
