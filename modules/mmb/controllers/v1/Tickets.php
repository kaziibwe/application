<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';

class Tickets extends \CustomerApi\RestController
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

        if (!has_contact_permission('support', $this->clientInfo['data']->contact_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], 403);
        }
    }

    /**
     * @api {get} /mmb/v1/tickets List All Tickets
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetAllTickets
     *
     * @apiGroup Support
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest /mmb/v1/tickets
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Tickets information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *       {
     *       "status": true,
     *       "data": [
     *           {
     *               "ticketid": "2",
     *               "adminreplying": "0",
     *               "userid": "3",
     *               "contactid": "3",
     *               "merged_ticket_id": null,
     *               "email": "nick83@example.org",
     *               "name": "Medium",
     *               "department": "1",
     *               "priority": "2",
     *               "status": "1",
     *               "service": "0",
     *               "ticketkey": "d16df256b79647c1396a5a338b7d1c66",
     *               "subject": "Test Ticket 2",
     *               "message": "<p>Test</p>",
     *               "admin": "1",
     *               "date": "2023-05-18 11:01:23",
     *               "project_id": "2",
     *               "lastreply": null,
     *               "clientread": "0",
     *               "adminread": "1",
     *               "assigned": "1",
     *               "staff_id_replying": null,
     *               "cc": "test@gmail.com",
     *               "departmentid": "1",
     *               "imap_username": "",
     *               "email_from_header": "0",
     *               "host": "",
     *               "password": "$2a$08$epiHbbi567hPL03zSwBzn.3Ie3slCBVdlzPeF3kctLmykR6lhYYfe",
     *               "encryption": "",
     *               "folder": "INBOX",
     *               "delete_after_import": "0",
     *               "calendar_id": null,
     *               "hidefromclient": "0",
     *               "ticketstatusid": "1",
     *               "isdefault": "1",
     *               "statuscolor": "#ff2d42",
     *               "statusorder": "1",
     *               "serviceid": null,
     *               "company": "Barton PLC",
     *               "vat": "",
     *               "phonenumber": null,
     *               "country": "66",
     *               "city": "Vickyport",
     *               "zip": "47469-3292",
     *               "state": "Ohio",
     *               "address": "7600 Tanya Burg",
     *               "website": "yost.com",
     *               "datecreated": "2023-05-11 08:00:22",
     *               "active": "1",
     *               "leadid": null,
     *               "billing_street": "",
     *               "billing_city": "",
     *               "billing_state": "",
     *               "billing_zip": "",
     *               "billing_country": "0",
     *               "shipping_street": "",
     *               "shipping_city": "",
     *               "shipping_state": "",
     *               "shipping_zip": "",
     *               "shipping_country": "0",
     *               "longitude": null,
     *               "latitude": null,
     *               "default_language": null,
     *               "default_currency": "1",
     *               "show_primary_contact": "0",
     *               "stripe_id": null,
     *               "registration_confirmed": "1",
     *               "addedfrom": "1",
     *               "id": "3",
     *               "is_primary": "1",
     *               "firstname": "admin",
     *               "lastname": "main",
     *               "title": "Travel Guide",
     *               "new_pass_key": null,
     *               "new_pass_key_requested": null,
     *               "email_verified_at": "2023-05-12 16:18:34",
     *               "email_verification_key": null,
     *               "email_verification_sent_at": null,
     *               "last_ip": "162.158.170.180",
     *               "last_login": "2023-05-18 10:22:27",
     *               "last_password_change": null,
     *               "profile_image": null,
     *               "direction": null,
     *               "invoice_emails": "1",
     *               "estimate_emails": "1",
     *               "credit_note_emails": "1",
     *               "contract_emails": "1",
     *               "task_emails": "1",
     *               "project_emails": "1",
     *               "ticket_emails": "1",
     *               "staffid": "1",
     *               "facebook": null,
     *               "linkedin": null,
     *               "skype": null,
     *               "last_activity": "2023-05-18 11:01:24",
     *               "role": null,
     *               "media_path_slug": null,
     *               "is_not_staff": "0",
     *               "hourly_rate": "0.00",
     *               "two_factor_auth_enabled": "0",
     *               "two_factor_auth_code": null,
     *               "two_factor_auth_code_requested": null,
     *               "email_signature": null,
     *               "google_auth_secret": null,
     *               "priorityid": "2",
     *               "from_name": null,
     *               "ticket_email": null,
     *               "department_name": " Department Name 1",
     *               "priority_name": "Medium",
     *               "service_name": null,
     *               "status_name": "Open",
     *               "user_firstname": "Geovanny",
     *               "user_lastname": "Mraz",
     *               "staff_firstname": "admin",
     *               "staff_lastname": "main",
     *               "addedfrom_name": "admin main",
     *               "project_name": "SEO Optimization"
     *           }
     *       ],
     *       "message": "Data retrieved successfully"
     *   }
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
     * @api {get} /mmb/v1/tickets/id/:id Request Tickets By ID
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetTicketsById
     *
     * @apiGroup Support
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiParam {Number} id <span class="btn btn-xs btn-danger">Required</span> Ticket unique ID.
     *
     * @apiSampleRequest /mmb/v1/tickets/
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Object}  data    Ticket information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *       {
     *       "status": true,
     *       "data": {
     *           "ticketid": "2",
     *           "adminreplying": "0",
     *           "userid": "3",
     *           "contactid": "3",
     *           "merged_ticket_id": null,
     *           "email": "nick83@example.org",
     *           "name": "Medium",
     *           "department": "1",
     *           "priority": "2",
     *           "status": "1",
     *           "service": "0",
     *           "ticketkey": "d16df256b79647c1396a5a338b7d1c66",
     *           "subject": "Test Ticket 2",
     *           "message": "<p>Test</p>",
     *           "admin": "1",
     *           "date": "2023-05-18 11:01:23",
     *           "project_id": "2",
     *           "lastreply": null,
     *           "clientread": "0",
     *           "adminread": "1",
     *           "assigned": "1",
     *           "staff_id_replying": null,
     *           "cc": "test@gmail.com",
     *           "departmentid": "1",
     *           "imap_username": "",
     *           "email_from_header": "0",
     *           "host": "",
     *           "password": "$2a$08$Q0VrrNw1/ErpYP4NNM9Wp.I8CII2XJMByUvVsJR/nW9FBpDCP75fu",
     *           "encryption": "",
     *           "folder": "INBOX",
     *           "delete_after_import": "0",
     *           "calendar_id": null,
     *           "hidefromclient": "0",
     *           "ticketstatusid": "1",
     *           "isdefault": "1",
     *           "statuscolor": "#ff2d42",
     *           "statusorder": "1",
     *           "serviceid": null,
     *           "company": "Barton PLC",
     *           "vat": "",
     *           "phonenumber": "",
     *           "country": "66",
     *           "city": "Vickyport",
     *           "zip": "47469-3292",
     *           "state": "Ohio",
     *           "address": "7600 Tanya Burg",
     *           "website": "yost.com",
     *           "datecreated": "2023-05-12 16:18:34",
     *           "active": "1",
     *           "leadid": null,
     *           "billing_street": "",
     *           "billing_city": "",
     *           "billing_state": "",
     *           "billing_zip": "",
     *           "billing_country": "0",
     *           "shipping_street": "",
     *           "shipping_city": "",
     *           "shipping_state": "",
     *           "shipping_zip": "",
     *           "shipping_country": "0",
     *           "longitude": null,
     *           "latitude": null,
     *           "default_language": null,
     *           "default_currency": "1",
     *           "show_primary_contact": "0",
     *           "stripe_id": null,
     *           "registration_confirmed": "1",
     *           "addedfrom": "1",
     *           "staffid": "1",
     *           "firstname": "Geovanny",
     *           "lastname": "Mraz",
     *           "facebook": null,
     *           "linkedin": null,
     *           "skype": null,
     *           "profile_image": null,
     *           "last_ip": "108.162.227.71",
     *           "last_login": "2023-05-17 15:12:58",
     *           "last_activity": "2023-05-18 11:01:24",
     *           "last_password_change": "2023-05-13 15:35:50",
     *           "new_pass_key": null,
     *           "new_pass_key_requested": null,
     *           "role": null,
     *           "direction": "",
     *           "media_path_slug": null,
     *           "is_not_staff": "0",
     *           "hourly_rate": "0.00",
     *           "two_factor_auth_enabled": "0",
     *           "two_factor_auth_code": null,
     *           "two_factor_auth_code_requested": null,
     *           "email_signature": null,
     *           "google_auth_secret": null,
     *           "id": "3",
     *           "is_primary": "1",
     *           "title": "Travel Guide",
     *           "email_verified_at": "2023-05-12 16:18:34",
     *           "email_verification_key": null,
     *           "email_verification_sent_at": null,
     *           "invoice_emails": "1",
     *           "estimate_emails": "1",
     *           "credit_note_emails": "1",
     *           "contract_emails": "1",
     *           "task_emails": "1",
     *           "project_emails": "1",
     *           "ticket_emails": "1",
     *           "priorityid": "2",
     *           "from_name": null,
     *           "ticket_email": null,
     *           "department_name": " Department Name 1",
     *           "priority_name": "Medium",
     *           "service_name": null,
     *           "status_name": "Open",
     *           "user_firstname": "Geovanny",
     *           "user_lastname": "Mraz",
     *           "staff_firstname": "admin",
     *           "staff_lastname": "main",
     *           "submitter": "Geovanny Mraz",
     *           "opened_by": "admin main",
     *           "attachments": [],
     *           "ticket_replies": [],
     *           "addedfrom_name": "admin main",
     *           "project_name": "SEO Optimization"
     *       },
     *       "message": "Data retrieved successfully"
     *   }
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
    public function tickets_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], 500);
        }

        $ticketsID  = $this->get('id');
        $ticketData = $this->customers_api_model->getTable('tickets', $ticketsID, $this->clientInfo['data']);
        $this->response($ticketData['response'], $ticketData['response_code']);
    }

    /**
     * @api {post} /mmb/v1/tickets Add New Ticket
     *
     * @apiName AddNewTicket
     *
     * @apiGroup Support
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest off
     *
     * @apiBody {String} subject      <span class="btn btn-xs btn-danger">Required</span> Subject Name
     * @apiBody {Number} department   <span class="btn btn-xs btn-danger">Required</span> Department ID
     * @apiBody {Number} priority     <span class="btn btn-xs btn-danger">Required</span> Ticket priority ID
     * @apiBody {Number} [project]    Project unique ID
     * @apiBody {String} [TicketBody] Ticket Body
     * @apiBody {File}   [Attachment] Attachment
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "message": "Ticket added successfully."
     *     }
     */

    /**
     * @api {post} /mmb/v1/tickets/add_reply/:id Add New Reply To A Ticket
     *
     * @apiName AddReplyToTicket
     *
     * @apiGroup Support
     *
     * @apiVersion 1.0.0
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiParam {Number} add_reply <span class="btn btn-xs btn-danger">Required</span> Ticket unique ID.
     *
     * @apiSampleRequest off
     *
     * @apiBody {String} Message      <span class="btn btn-xs btn-danger">Required</span> Ticket Reply Massage
     * @apiBody {File}   [Attachment] Attachment
     *
     * @apiSuccess {Boolean} status  Response status
     * @apiSuccess {String}  message Success message
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "message": "Reply added successfully"
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     */
    public function tickets_post()
    {
        $ticketID = $this->get('add_reply');

        $postData = $this->post();
        if (empty($ticketID)) {
            $data = $this->customers_api_model->addTicket($postData, $this->clientInfo['data']);
        }

        if (!empty($ticketID)) {
            $data = $this->customers_api_model->addReplyToATicket($postData, $this->clientInfo['data'], $ticketID);
        }

        $this->response($data['response'], $data['response_code']);
    }
}
