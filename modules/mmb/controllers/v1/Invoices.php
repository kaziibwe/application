<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';
use CustomerApi\RestController;

class Invoices extends RestController
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

        if (!has_contact_permission('invoices', $this->clientInfo['data']->contact_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], 403);
        }
    }

    /**
     * @api {get} /mmb/v1/invoices List All Invoices
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetAllInvoices
     *
     * @apiGroup Invoices
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest /mmb/v1/invoices
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Invoices information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "status": true,
     *          "data": [
     *              {
     *                  "id": "4",
     *                  "sent": "0",
     *                  "datesend": null,
     *                  "clientid": "3",
     *                  "deleted_customer_name": null,
     *                  "number": "4",
     *                  "prefix": "INV-",
     *                  "number_format": "1",
     *                  "datecreated": "2023-05-17 15:50:13",
     *                  "date": "2023-05-17",
     *                  "duedate": "2023-06-16",
     *                  "currency": "1",
     *                  "subtotal": "500.00",
     *                  "total_tax": "0.00",
     *                  "total": "500.00",
     *                  "adjustment": "0.00",
     *                  "addedfrom": "1",
     *                  "hash": "e67647f69b1c74aa048020afdc1798d8",
     *                  "status": "1",
     *                  "clientnote": "Test client Note",
     *                  "adminnote": "Test",
     *                  "last_overdue_reminder": null,
     *                  "last_due_reminder": null,
     *                  "cancel_overdue_reminders": "0",
     *                  "allowed_payment_modes": "a:1:{i:0;s:1:\"1\";}",
     *                  "token": null,
     *                  "discount_percent": "0.00",
     *                  "discount_total": "0.00",
     *                  "discount_type": "",
     *                  "recurring": "0",
     *                  "recurring_type": null,
     *                  "custom_recurring": "0",
     *                  "cycles": "0",
     *                  "total_cycles": "0",
     *                  "is_recurring_from": null,
     *                  "last_recurring_date": null,
     *                  "terms": "Term & conditions ",
     *                  "sale_agent": "0",
     *                  "billing_street": "Test",
     *                  "billing_city": "Test",
     *                  "billing_state": "Test",
     *                  "billing_zip": "1234",
     *                  "billing_country": "1",
     *                  "shipping_street": null,
     *                  "shipping_city": null,
     *                  "shipping_state": null,
     *                  "shipping_zip": null,
     *                  "shipping_country": null,
     *                  "include_shipping": "0",
     *                  "show_shipping_on_invoice": "1",
     *                  "show_quantity_as": "1",
     *                  "project_id": "2",
     *                  "subscription_id": "0",
     *                  "short_link": null,
     *                  "symbol": "$",
     *                  "name": "USD",
     *                  "decimal_separator": ".",
     *                  "thousand_separator": ",",
     *                  "placement": "before",
     *                  "isdefault": "1",
     *                  "currencyid": "1",
     *                  "currency_name": "USD",
     *                  "client_name": "Barton PLC",
     *                  "project_name": "SEO Optimization",
     *                  "addedfrom_name": "admin main",
     *                  "status_name": "Unpaid"
     *              }
     *          ],
     *          "message": "Data retrieved successfully"
     *      }
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
     * @api {get} /mmb/v1/invoices/id/:id Request Invoice By ID
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetInvoiceById
     *
     * @apiGroup Invoices
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiParam {Number} id <span class="btn btn-xs btn-danger">Required</span> Invoice unique ID.
     *
     * @apiSampleRequest /mmb/v1/invoices/
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Object}  data    Invoice information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "data": {
     *             "id": "4",
     *             "sent": "0",
     *             "datesend": null,
     *             "clientid": "3",
     *             "deleted_customer_name": null,
     *             "number": "4",
     *             "prefix": "INV-",
     *             "number_format": "1",
     *             "datecreated": "2023-05-17 15:50:13",
     *             "date": "2023-05-17",
     *             "duedate": "2023-06-16",
     *             "currency": "1",
     *             "subtotal": "500.00",
     *             "total_tax": "0.00",
     *             "total": "500.00",
     *             "adjustment": "0.00",
     *             "addedfrom": "1",
     *             "hash": "e67647f69b1c74aa048020afdc1798d8",
     *             "status": "1",
     *             "clientnote": "Test client Note",
     *             "adminnote": "Test",
     *             "last_overdue_reminder": null,
     *             "last_due_reminder": null,
     *             "cancel_overdue_reminders": "0",
     *             "allowed_payment_modes": "a:1:{i:0;s:1:\"1\";}",
     *             "token": null,
     *             "discount_percent": "0.00",
     *             "discount_total": "0.00",
     *             "discount_type": "",
     *             "recurring": "0",
     *             "recurring_type": null,
     *             "custom_recurring": "0",
     *             "cycles": "0",
     *             "total_cycles": "0",
     *             "is_recurring_from": null,
     *             "last_recurring_date": null,
     *             "terms": "Term & conditions ",
     *             "sale_agent": "0",
     *             "billing_street": "Test",
     *             "billing_city": "Test",
     *             "billing_state": "Test",
     *             "billing_zip": "1234",
     *             "billing_country": "1",
     *             "shipping_street": null,
     *             "shipping_city": null,
     *             "shipping_state": null,
     *             "shipping_zip": null,
     *             "shipping_country": null,
     *             "include_shipping": "0",
     *             "show_shipping_on_invoice": "1",
     *             "show_quantity_as": "1",
     *             "project_id": "2",
     *             "subscription_id": "0",
     *             "short_link": null,
     *             "symbol": "$",
     *             "name": "USD",
     *             "decimal_separator": ".",
     *             "thousand_separator": ",",
     *             "placement": "before",
     *             "isdefault": "1",
     *             "currencyid": "1",
     *             "currency_name": "USD",
     *             "total_left_to_pay": "500.00",
     *             "items": [
     *                 {
     *                     "id": "12",
     *                     "rel_id": "4",
     *                     "rel_type": "invoice",
     *                     "description": "SEO Optimization",
     *                     "long_description": "Voluptate corporis ut dolores. Consequuntur qui ullam accusamus error minima incidunt consequatur.",
     *                     "qty": "1.00",
     *                     "rate": "500.00",
     *                     "unit": "1",
     *                     "item_order": "1"
     *                 }
     *             ],
     *             "attachments": [],
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
     *             "visible_attachments_to_customer_found": false,
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
     *             "payments": [],
     *             "scheduled_email": null,
     *             "addedfrom_name": "admin main",
     *             "status_name": "Unpaid"
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
    public function invoices_get()
    {
        if (!empty($this->get()) && !in_array('id', array_keys($this->get()))) {
            $this->response(['message' => _l('something_went_wrong')], 500);
        }

        $invoiceID   = $this->get('id');
        $invoiceData = $this->customers_api_model->getTable('invoices', $invoiceID, $this->clientInfo['data']);
        $this->response($invoiceData['response'], $invoiceData['response_code']);
    }
}

/* End of file Invoices.php */
