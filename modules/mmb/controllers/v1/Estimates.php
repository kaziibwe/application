<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';
use CustomerApi\RestController;

class Estimates extends RestController
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

        if (!has_contact_permission('estimates', $this->clientInfo['data']->contact_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], 403);
        }
    }

    /**
     * @api {get} /mmb/v1/estimates List All Estimates
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetAllEstimates
     *
     * @apiGroup Estimates
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest /mmb/v1/estimates
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Estimates information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "data": [
     *             {
     *                 "id": "5",
     *                 "sent": "1",
     *                 "datesend": "2023-05-17 16:35:51",
     *                 "clientid": "3",
     *                 "deleted_customer_name": null,
     *                 "project_id": "2",
     *                 "number": "5",
     *                 "prefix": "EST-",
     *                 "number_format": "1",
     *                 "hash": "c8698772970e7f3dbdcc2196a8a003fe",
     *                 "datecreated": "2023-05-17 16:34:19",
     *                 "date": "2023-05-17",
     *                 "expirydate": "2023-05-24",
     *                 "currency": "1",
     *                 "subtotal": "500.00",
     *                 "total_tax": "0.00",
     *                 "total": "500.00",
     *                 "adjustment": "0.00",
     *                 "addedfrom": "1",
     *                 "status": "2",
     *                 "clientnote": "Test Client Note",
     *                 "adminnote": "Test Note",
     *                 "discount_percent": "0.00",
     *                 "discount_total": "0.00",
     *                 "discount_type": "",
     *                 "invoiceid": null,
     *                 "invoiced_date": null,
     *                 "terms": "Test Terms & Conditions",
     *                 "reference_no": "",
     *                 "sale_agent": "0",
     *                 "billing_street": "Test",
     *                 "billing_city": "Test",
     *                 "billing_state": "Test",
     *                 "billing_zip": "12345",
     *                 "billing_country": "1",
     *                 "shipping_street": null,
     *                 "shipping_city": null,
     *                 "shipping_state": null,
     *                 "shipping_zip": null,
     *                 "shipping_country": null,
     *                 "include_shipping": "0",
     *                 "show_shipping_on_estimate": "1",
     *                 "show_quantity_as": "1",
     *                 "pipeline_order": "1",
     *                 "is_expiry_notified": "0",
     *                 "acceptance_firstname": null,
     *                 "acceptance_lastname": null,
     *                 "acceptance_email": null,
     *                 "acceptance_date": null,
     *                 "acceptance_ip": null,
     *                 "signature": null,
     *                 "short_link": null,
     *                 "symbol": "$",
     *                 "name": "USD",
     *                 "decimal_separator": ".",
     *                 "thousand_separator": ",",
     *                 "placement": "before",
     *                 "isdefault": "1",
     *                 "currencyid": "1",
     *                 "currency_name": "USD",
     *                 "client_name": "Barton PLC",
     *                 "project_name": "SEO Optimization",
     *                 "addedfrom_name": "admin main",
     *                 "status_name": "Sent"
     *             }
     *         ],
     *         "message": "Data retrieved successfully"
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
     * @api {get} /mmb/v1/estimates/id/:id Request Estimates By ID
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetEstimatesById
     *
     * @apiGroup Estimates
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiParam {Number} id <span class="btn btn-xs btn-danger">Required</span> Estimate unique ID.
     *
     * @apiSampleRequest /mmb/v1/estimates/
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Object}  data    Estimate information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "data": {
     *             "id": "5",
     *             "sent": "1",
     *             "datesend": "2023-05-17 16:35:51",
     *             "clientid": "3",
     *             "deleted_customer_name": null,
     *             "project_id": "2",
     *             "number": "5",
     *             "prefix": "EST-",
     *             "number_format": "1",
     *             "hash": "c8698772970e7f3dbdcc2196a8a003fe",
     *             "datecreated": "2023-05-17 16:34:19",
     *             "date": "2023-05-17",
     *             "expirydate": "2023-05-24",
     *             "currency": "1",
     *             "subtotal": "500.00",
     *             "total_tax": "0.00",
     *             "total": "500.00",
     *             "adjustment": "0.00",
     *             "addedfrom": "1",
     *             "status": "2",
     *             "clientnote": "Test Client Note",
     *             "adminnote": "Test Note",
     *             "discount_percent": "0.00",
     *             "discount_total": "0.00",
     *             "discount_type": "",
     *             "invoiceid": null,
     *             "invoiced_date": null,
     *             "terms": "Test Terms & Conditions",
     *             "reference_no": "",
     *             "sale_agent": "0",
     *             "billing_street": "Test",
     *             "billing_city": "Test",
     *             "billing_state": "Test",
     *             "billing_zip": "12345",
     *             "billing_country": "1",
     *             "shipping_street": null,
     *             "shipping_city": null,
     *             "shipping_state": null,
     *             "shipping_zip": null,
     *             "shipping_country": null,
     *             "include_shipping": "0",
     *             "show_shipping_on_estimate": "1",
     *             "show_quantity_as": "1",
     *             "pipeline_order": "1",
     *             "is_expiry_notified": "0",
     *             "acceptance_firstname": null,
     *             "acceptance_lastname": null,
     *             "acceptance_email": null,
     *             "acceptance_date": null,
     *             "acceptance_ip": null,
     *             "signature": null,
     *             "short_link": null,
     *             "symbol": "$",
     *             "name": "USD",
     *             "decimal_separator": ".",
     *             "thousand_separator": ",",
     *             "placement": "before",
     *             "isdefault": "1",
     *             "currencyid": "1",
     *             "currency_name": "USD",
     *             "attachments": [],
     *             "visible_attachments_to_customer_found": false,
     *             "items": [
     *                 {
     *                     "id": "13",
     *                     "rel_id": "5",
     *                     "rel_type": "estimate",
     *                     "description": "SEO Optimization",
     *                     "long_description": "Voluptate corporis ut dolores. Consequuntur qui ullam accusamus error minima incidunt consequatur.",
     *                     "qty": "1.00",
     *                     "rate": "500.00",
     *                     "unit": "1",
     *                     "item_order": "1"
     *                 }
     *             ],
     *             "project_data": {
     *                 "id": "2",
     *                 "name": "SEO Optimization",
     *                 "description": "<p>I should understand that better,' Alice said very humbly; 'I won't indeed!' said Alice, 'how am I then? Tell me that first, and then another confusion of voices--'Hold up his.<br><br>Alice looked at it uneasily, shaking it every now and then, and holding it to his son, 'I feared it might tell her something about the right size, that it felt quite relieved to see what was the Rabbit began. Alice thought to herself, 'Which way? Which way?', holding her hand on the floor, and a great crash, as if a dish or kettle had been all the time when I learn music.' 'Ah! that accounts for it,' said the Duchess; 'I.</p>",
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
     *                 "shared_vault_entries": [],
     *                 "settings": {
     *                     "available_features": {
     *                         "project_overview": 1,
     *                         "project_tasks": 1,
     *                         "project_timesheets": 1,
     *                         "project_milestones": 1,
     *                         "project_files": 1,
     *                         "project_discussions": 1,
     *                         "project_gantt": 1,
     *                         "project_tickets": 1,
     *                         "project_contracts": 1,
     *                         "project_proposals": 1,
     *                         "project_estimates": 1,
     *                         "project_invoices": 1,
     *                         "project_subscriptions": 1,
     *                         "project_expenses": 1,
     *                         "project_credit_notes": 1,
     *                         "project_notes": 1,
     *                         "project_activity": 1
     *                     },
     *                     "view_tasks": "1",
     *                     "create_tasks": "1",
     *                     "edit_tasks": "1",
     *                     "comment_on_tasks": "1",
     *                     "view_task_comments": "1",
     *                     "view_task_attachments": "1",
     *                     "view_task_checklist_items": "1",
     *                     "upload_on_tasks": "1",
     *                     "view_task_total_logged_time": "1",
     *                     "view_finance_overview": "1",
     *                     "upload_files": "1",
     *                     "open_discussions": "1",
     *                     "view_milestones": "1",
     *                     "view_gantt": "1",
     *                     "view_timesheets": "1",
     *                     "view_activity_log": "1",
     *                     "view_team_members": "1",
     *                     "hide_tasks_on_main_tasks_table": "0"
     *                 },
     *                 "client_data": {
     *                     "userid": "3",
     *                     "company": "Barton PLC",
     *                     "vat": "",
     *                     "phonenumber": "+1 (704) 220-4245",
     *                     "country": "66",
     *                     "city": "Vickyport",
     *                     "zip": "47469-3292",
     *                     "state": "Ohio",
     *                     "address": "7600 Tanya Burg",
     *                     "website": "yost.com",
     *                     "datecreated": "2023-05-12 16:17:22",
     *                     "active": "1",
     *                     "leadid": null,
     *                     "billing_street": "",
     *                     "billing_city": "",
     *                     "billing_state": "",
     *                     "billing_zip": "",
     *                     "billing_country": "0",
     *                     "shipping_street": "",
     *                     "shipping_city": "",
     *                     "shipping_state": "",
     *                     "shipping_zip": "",
     *                     "shipping_country": "0",
     *                     "longitude": null,
     *                     "latitude": null,
     *                     "default_language": "",
     *                     "default_currency": "1",
     *                     "show_primary_contact": "0",
     *                     "stripe_id": null,
     *                     "registration_confirmed": "1",
     *                     "addedfrom": "1"
     *                 }
     *             },
     *             "client": {
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
     *             "scheduled_email": null,
     *             "addedfrom_name": "admin main",
     *             "status_name": "Sent"
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
    public function estimates_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], 500);
        }

        $estimateID   = $this->get('id');
        $estimateData = $this->customers_api_model->getTable('estimates', $estimateID, $this->clientInfo['data']);
        $this->response($estimateData['response'], $estimateData['response_code']);
    }
}

/* End of file Estimates.php */
