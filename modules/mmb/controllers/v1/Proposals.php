<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';

use CustomerApi\RestController;

class Proposals extends RestController
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

        if (!has_contact_permission('proposals', $this->clientInfo['data']->contact_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], 403);
        }
    }

    /**
     * @api {get} /mmb/v1/proposals List All Proposals
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetAllProposals
     *
     * @apiGroup Proposals
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest /mmb/v1/proposals
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Proposals information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "status": true,
     *          "data": [
     *              {
     *                  "id": "2",
     *                  "subject": "Proposal 2",
     *                  "content": "{proposal_items}",
     *                  "addedfrom": "1",
     *                  "datecreated": "2023-05-10 11:54:08",
     *                  "total": "20.00",
     *                  "subtotal": "20.00",
     *                  "total_tax": "0.00",
     *                  "adjustment": "0.00",
     *                  "discount_percent": "0.00",
     *                  "discount_total": "0.00",
     *                  "discount_type": "",
     *                  "show_quantity_as": "1",
     *                  "currency": "1",
     *                  "open_till": "2023-05-17",
     *                  "date": "2023-05-10",
     *                  "rel_id": "2",
     *                  "rel_type": "customer",
     *                  "assigned": "0",
     *                  "hash": "dfb1ad8e2f39f6b3b62b9c08f869c6a8",
     *                  "proposal_to": "Disha 123",
     *                  "project_id": "2",
     *                  "country": "0",
     *                  "zip": "",
     *                  "state": "",
     *                  "city": "",
     *                  "address": "",
     *                  "email": "Disha@123.com",
     *                  "phone": "",
     *                  "allow_comments": "1",
     *                  "status": "5",
     *                  "estimate_id": null,
     *                  "invoice_id": null,
     *                  "date_converted": null,
     *                  "pipeline_order": "1",
     *                  "is_expiry_notified": "0",
     *                  "acceptance_firstname": null,
     *                  "acceptance_lastname": null,
     *                  "acceptance_email": null,
     *                  "acceptance_date": null,
     *                  "acceptance_ip": null,
     *                  "signature": null,
     *                  "short_link": null,
     *                  "symbol": "$",
     *                  "name": "USD",
     *                  "decimal_separator": ".",
     *                  "thousand_separator": ",",
     *                  "placement": "before",
     *                  "isdefault": "1",
     *                  "currencyid": "1",
     *                  "currency_name": "USD",
     *                  "client_name": "Customer 2",
     *                  "project_name": "Client api project",
     *                  "addedfrom_name": "admin 123",
     *                  "status_name": "Revised"
     *              },
     *             ...
     *          ],
     *          "message": "Data retrieved successfully"
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message No data found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data found"
     *     }
     */

    /**
     * @api {get} /mmb/v1/proposals/id/:id Request Proposal By ID
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetProposalById
     *
     * @apiGroup Proposals
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiParam {Number} id <span class="btn btn-xs btn-danger">Required</span> Proposal unique ID.
     *
     * @apiSampleRequest /mmb/v1/proposals/
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Object}  data    Proposal information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "status": true,
     *          "data": {
     *              "id": "2",
     *              "subject": "Proposal 2",
     *              "content": "{proposal_items}",
     *              "addedfrom": "1",
     *              "datecreated": "2023-05-10 11:54:08",
     *              "total": "20.00",
     *              "subtotal": "20.00",
     *              "total_tax": "0.00",
     *              "adjustment": "0.00",
     *              "discount_percent": "0.00",
     *              "discount_total": "0.00",
     *              "discount_type": "",
     *              "show_quantity_as": "1",
     *              "currency": "1",
     *              "open_till": "2023-05-17",
     *              "date": "2023-05-10",
     *              "rel_id": "2",
     *              "rel_type": "customer",
     *              "assigned": "0",
     *              "hash": "dfb1ad8e2f39f6b3b62b9c08f869c6a8",
     *              "proposal_to": "Disha 123",
     *              "project_id": "2",
     *              "country": "0",
     *              "zip": "",
     *              "state": "",
     *              "city": "",
     *              "address": "",
     *              "email": "Disha@123.com",
     *              "phone": "",
     *              "allow_comments": "1",
     *              "status": "5",
     *              "estimate_id": null,
     *              "invoice_id": null,
     *              "date_converted": null,
     *              "pipeline_order": "1",
     *              "is_expiry_notified": "0",
     *              "acceptance_firstname": null,
     *              "acceptance_lastname": null,
     *              "acceptance_email": null,
     *              "acceptance_date": null,
     *              "acceptance_ip": null,
     *              "signature": null,
     *              "short_link": null,
     *              "symbol": "$",
     *              "name": "USD",
     *              "decimal_separator": ".",
     *              "thousand_separator": ",",
     *              "placement": "before",
     *              "isdefault": "1",
     *              "currencyid": "1",
     *              "currency_name": "USD",
     *              "attachments": [],
     *              "items": [
     *                  {
     *                      "id": "10",
     *                      "rel_id": "2",
     *                      "rel_type": "proposal",
     *                      "description": "lenovo legion 5",
     *                      "long_description": "desc",
     *                      "qty": "1.00",
     *                      "rate": "20.00",
     *                      "unit": "",
     *                      "item_order": "1"
     *                  }
     *              ],
     *              "visible_attachments_to_customer_found": false,
     *              "project_data": {
     *                  "id": "2",
     *                  "name": "Client api project",
     *                  "description": "",
     *                  "status": "2",
     *                  "clientid": "2",
     *                  "billing_type": "1",
     *                  "start_date": "2023-05-10",
     *                  "deadline": null,
     *                  "project_created": "2023-05-10",
     *                  "date_finished": null,
     *                  "progress": "0",
     *                  "progress_from_tasks": "1",
     *                  "project_cost": null,
     *                  "project_rate_per_hour": "0.00",
     *                  "estimated_hours": null,
     *                  "addedfrom": "1",
     *                  "contact_notification": "1",
     *                  "notify_contacts": "a:0:{}",
     *                  "shared_vault_entries": [],
     *                  "settings": {
     *                      "available_features": {
     *                          "project_overview": 1,
     *                          "project_tasks": 1,
     *                          "project_timesheets": 1,
     *                          "project_milestones": 1,
     *                          "project_files": 1,
     *                          "project_discussions": 1,
     *                          "project_gantt": 1,
     *                          "project_tickets": 1,
     *                          "project_contracts": 1,
     *                          "project_proposals": 1,
     *                          "project_estimates": 1,
     *                          "project_invoices": 1,
     *                          "project_subscriptions": 1,
     *                          "project_expenses": 1,
     *                          "project_credit_notes": 1,
     *                          "project_notes": 1,
     *                          "project_activity": 1
     *                      },
     *                      "view_tasks": "1",
     *                      "create_tasks": "1",
     *                      "edit_tasks": "1",
     *                      "comment_on_tasks": "1",
     *                      "view_task_comments": "1",
     *                      "view_task_attachments": "1",
     *                      "view_task_checklist_items": "1",
     *                      "upload_on_tasks": "1",
     *                      "view_task_total_logged_time": "1",
     *                      "view_finance_overview": "1",
     *                      "upload_files": "0",
     *                      "open_discussions": "0",
     *                      "view_milestones": "0",
     *                      "view_gantt": "0",
     *                      "view_timesheets": "0",
     *                      "view_activity_log": "1",
     *                      "view_team_members": "1",
     *                      "hide_tasks_on_main_tasks_table": "0"
     *                  },
     *                  "client_data": {
     *                      "userid": "2",
     *                      "company": "Customer 2",
     *                      "vat": "",
     *                      "phonenumber": "",
     *                      "country": "0",
     *                      "city": "",
     *                      "zip": "",
     *                      "state": "",
     *                      "address": "",
     *                      "website": "",
     *                      "datecreated": "2023-05-10 11:47:00",
     *                      "active": "1",
     *                      "leadid": null,
     *                      "billing_street": "",
     *                      "billing_city": "",
     *                      "billing_state": "",
     *                      "billing_zip": "",
     *                      "billing_country": "0",
     *                      "shipping_street": "",
     *                      "shipping_city": "",
     *                      "shipping_state": "",
     *                      "shipping_zip": "",
     *                      "shipping_country": "0",
     *                      "longitude": null,
     *                      "latitude": null,
     *                      "default_language": "",
     *                      "default_currency": "0",
     *                      "show_primary_contact": "0",
     *                      "stripe_id": null,
     *                      "registration_confirmed": "1",
     *                      "addedfrom": "1"
     *                  }
     *              },
     *              "addedfrom_name": "admin 123",
     *              "status_name": "Revised",
     *              "comments": [
     *                  {
     *                      "id": "3",
     *                      "content": "test",
     *                      "proposalid": "2",
     *                      "staffid": "0",
     *                      "dateadded": "2023-05-10 12:38:20"
     *                  },
     *                  {
     *                      "id": "4",
     *                      "content": "Test Comment",
     *                      "proposalid": "2",
     *                      "staffid": "0",
     *                      "dateadded": "2023-05-10 17:05:11"
     *                  },
     *                  {
     *                      "id": "14",
     *                      "content": "First proposals comments",
     *                      "proposalid": "2",
     *                      "staffid": "0",
     *                      "dateadded": "2023-05-16 15:52:30"
     *                  },
     *                  {
     *                      "id": "15",
     *                      "content": "First proposals comments",
     *                      "proposalid": "2",
     *                      "staffid": "0",
     *                      "dateadded": "2023-05-16 15:58:19"
     *                  },
     *                  {
     *                      "id": "16",
     *                      "content": "First proposals comments",
     *                      "proposalid": "2",
     *                      "staffid": "0",
     *                      "dateadded": "2023-05-16 18:09:15"
     *                  },
     *                  {
     *                      "id": "17",
     *                      "content": "First proposals comments",
     *                      "proposalid": "2",
     *                      "staffid": "0",
     *                      "dateadded": "2023-05-16 18:09:26"
     *                  }
     *              ]
     *          },
     *          "message": "Data retrieved successfully"
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
    public function proposals_get()
    {
        $parameters = $this->get();
        if (!empty($parameters) && !in_array('id', array_keys($parameters))) {
            $this->response(['message' => _l('something_went_wrong')], 500);
        }

        $proposalID  = $this->get('id');
        if (!empty($proposalID) && !is_numeric($proposalID)) {
            $this->response(['message' => _l('something_went_wrong')], 500);
        }

        $proposalData = $this->customers_api_model->getTable('proposals', $proposalID, $this->clientInfo['data']);

        if (!empty($proposalID) && 200 == $proposalData['response_code']) {
            $proposalData['response']['data']->comments = $this->proposals_model->get_comments($proposalID);
        }

        $this->response($proposalData['response'], $proposalData['response_code']);
    }

    /**
     * @api {post} /mmb/v1/proposals/id/:id/group/proposals_comment Add Proposal Comments
     *
     * @apiVersion 1.0.0
     *
     * @apiName AddProposalComments
     *
     * @apiGroup Proposals
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest off
     *
     * @apiParam {Number} id <span class="btn btn-xs btn-danger">Required</span> Proposal Unique ID.
     * @apiParam {String} group=proposals_comment <span class="btn btn-xs btn-danger">Required</span> Group Name
     *
     * @apiBody {String} content <span class="btn btn-xs btn-danger">Required</span> Comment
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "message": "Proposal comments added successfully."
     *     }
     *
     * @apiError {Boolean} status Response status.
     * @apiError {String} message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 403 Forbidden
     *     {
     *       "status": false,
     *       "message": "You do not have permission to perform this action"
     *     }
     */
    public function proposals_post()
    {
        $postData   = $this->post();
        $parameters = [
            'id'    => is_numeric($this->get('id')) ? $this->get('id') : '',
            'group' => $this->get('group'),
        ];

        if (!empty($parameters['group']) && !empty($parameters['id'])) {
            $data = $this->customers_api_model->addComments($postData, $parameters, $this->clientInfo['data']);
            $this->response($data['response'], $data['response_code']);
        }

        $this->response(['message' => _l('something_went_wrong')], 500);
    }
}
