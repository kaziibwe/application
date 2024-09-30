<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require __DIR__ . '/REST_Controller.php';

class Custom_fields extends REST_Controller {
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * @api {get} api/custom_fields/:FieldBelongsto/:id Request Values of Custom Fields
     * @apiVersion 0.2.0
     * @apiName GetCustomFieldswithValue
     * @apiGroup Custom Fields
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {string=<br/>"Company",<br/>"Leads",<br/>"Customers",<br/>"Contacts",<br/>"Staff",<br/>"Contracts",<br/>"Tasks",<br/>"Expenses",<br/>"Invoice",<br/>"Items",<br/>"Note",<br/>"Estimate",<br/>"Contract",<br/>"Proposal",<br/>"Projects",<br/>"Tickets"} FieldBelongsto Belongs to Mandatory Field Belongs to.
     *
     * @apiParam {Number}  [id]         Optional unique ID.
     *
     * @apiSuccess {Object} Custom Custom Fields information with values.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   [
     *       {
     *           "field_name": "custom_fields[invoice][1]",
     *           "custom_field_id": "1",
     *           "label": "Input 1",
     *           "required": "0",
     *           "type": "input",
     *           "value": "input1 data"
     *       },
     *       {
     *           "field_name": "custom_fields[invoice][2]",
     *           "custom_field_id": "2",
     *           "label": "Number 1",
     *           "required": "0",
     *           "type": "number",
     *           "value": "12"
     *       },
     *       {
     *           "field_name": "custom_fields[invoice][3]",
     *           "custom_field_id": "3",
     *           "label": "Textarea 1",
     *           "required": "0",
     *           "type": "textarea",
     *           "value": "textarea content"
     *       },
     *       {
     *           "field_name": "custom_fields[invoice][4]",
     *           "custom_field_id": "4",
     *           "label": "Select 1",
     *           "required": "0",
     *           "type": "select",
     *           "value": "[\"Option 1\"]",
     *           "options": "[\"Option 1\",\"Option 2\",\"Option 3\"]"
     *       },
     *       {
     *           "field_name": "custom_fields[invoice][5]",
     *           "custom_field_id": "5",
     *           "label": "Multiselect 1",
     *           "required": "0",
     *           "type": "multiselect",
     *           "value": "[\"Option 1\",\" Option 2\"]",
     *           "options": "[\"Option 1\",\"Option 2\",\"Option 3\"]"
     *       },
     *       {
     *           "field_name": "custom_fields[invoice][6]",
     *           "custom_field_id": "6",
     *           "label": "Checkbox 1",
     *           "required": "0",
     *           "type": "checkbox",
     *           "value": "[\"Option 1\",\" Option 2\"]",
     *           "options": "[\"Option 1\",\"Option 2\",\"Option 3\"]"
     *       },
     *       {
     *           "field_name": "custom_fields[invoice][7]",
     *           "custom_field_id": "7",
     *           "label": "Datepicker 1",
     *           "required": "0",
     *           "type": "date_picker",
     *           "value": "2021-05-16"
     *       },
     *       {
     *           "field_name": "custom_fields[invoice][8]",
     *           "custom_field_id": "8",
     *           "label": "Datetime Picker 1",
     *           "required": "0",
     *           "type": "date_picker_time",
     *           "value": "2021-05-25 23:06:00"
     *       },
     *       {
     *           "field_name": "custom_fields[invoice][9]",
     *           "custom_field_id": "9",
     *           "label": "Colorpicker 1",
     *           "required": "0",
     *           "type": "colorpicker",
     *           "value": "#8f1b1b"
     *       },
     *       {
     *           "field_name": "custom_fields[invoice][10]",
     *           "custom_field_id": "10",
     *           "label": "Hyperlink 1",
     *           "required": "0",
     *           "type": "link",
     *           "value": "<a href=\"https://google.com\" target=\"_blank\">google</a>"
     *       }
     *   ]
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data were found"
     *     }
     */
    public function data_get($type = "", $id = "") {
        $allowed_type = ["company", "leads", "customers", "contacts", "staff", "contracts", "tasks", "expenses", "invoice", "items", "credit_note", "estimate", "contract", "proposal", "projects", "tickets"];
        if (empty($type) || !in_array($type, $allowed_type)) {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'Not valid data'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
        $fields = get_custom_fields($type);
        $customfields = [];
        foreach ($fields as $key => $field) {
            $customfields[$key] = new stdclass();
            $customfields[$key]->field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
            $customfields[$key]->custom_field_id = $field['id'];
            $customfields[$key]->label = $field['name'];
            $customfields[$key]->required = $field['required'];
            $customfields[$key]->type = $field['type'];
            $customfields[$key]->value = get_custom_field_value($id, $field['id'], $type, false);
            if (!empty($field['options'])) {
                $customfields[$key]->value = json_encode(explode(',', $customfields[$key]->value));
                $customfields[$key]->options = json_encode(explode(',', $field['options']));
            }
        }
        $this->response($customfields, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code        
    }

    /**
     * @api {POST} N/A Add Custom Fields
     * @apiVersion 0.2.0
     * @apiDescription Submit URL for POST request of the custom fields remains the same for each endpoint (ie `api/contacts` for Contacts endpoint, `api/invoices` for Invoices endpoint, etc..)
     * <br> <h2>In this example, we will use the following form data which corresponds to the following custom field types:</h2>
     `custom_fields[invoice][1]`  = **Input Type**
     <br> `custom_fields[invoice][2]`  = **Number**
     <br> `custom_fields[invoice][3]`  = **Textarea**
     <br> `custom_fields[invoice][4]`  = **Radio**
     <br> `custom_fields[invoice][5]`  = **Checkbox**
     <br> `custom_fields[invoice][6]`  = **Multiselect**
     <br> `custom_fields[invoice][7]`  = **Date**
     <br> `custom_fields[invoice][8]`  = **Datetime**
     <br> `custom_fields[invoice][9]`  = **Color**
     <br> `custom_fields[invoice][10]` = **Link**
     * @apiName PostActionExample
     * @apiGroup Custom Fields
     *
     * @apiParam {string/array} custom_fields[customFieldType] Custom Field Key should be same as `field_name` returned from **Search custom field values' information**
     *
     *  @apiParamExample {Multipart Form} Request-Example:
     *   [
     *      custom_fields[invoice][1] => John Doe
     *      custom_fields[invoice][2] => 10
     *      custom_fields[invoice][3] => Lorem Ipsum is simply dummy text of the printing and typesetting industry.
     *      custom_fields[invoice][4] => Option 1
     *      custom_fields[invoice][5][] => Option 1
     *      custom_fields[invoice][5][] => Option 2
     *      custom_fields[invoice][6][] => Option 1
     *      custom_fields[invoice][6][] => Option 3
     *      custom_fields[invoice][7] => 2021-05-06
     *      custom_fields[invoice][8] => 2021-05-06 00:23:25
     *      custom_fields[invoice][9] => #FFFFFF
     *      custom_fields[invoice][10] => <a href="url.com" target="_blank">Link</a>
     *   ]
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *      Same as Original request
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     Same as Original request
     */

    /**
     * @api {PUT} N/A Update Custom Fields
     * @apiVersion 0.2.0
     * @apiDescription Submit URL for PUT request of the custom fields remains the same for each endpoint (ie `api/contacts` for Contacts endpoint, `api/invoices` for Invoices endpoint, etc..)
     * <br> <h2>In this example, we will use the following form data which corresponds to the following custom field types:</h2>
     `custom_fields[invoice][1]`  = **Input Type**
     <br> `custom_fields[invoice][2]`  = **Number**
     <br> `custom_fields[invoice][3]`  = **Textarea**
     <br> `custom_fields[invoice][4]`  = **Radio**
     <br> `custom_fields[invoice][5]`  = **Checkbox**
     <br> `custom_fields[invoice][6]`  = **Multiselect**
     <br> `custom_fields[invoice][7]`  = **Date**
     <br> `custom_fields[invoice][8]`  = **Datetime**
     <br> `custom_fields[invoice][9]`  = **Color**
     <br> `custom_fields[invoice][10]` = **Link**
     * @apiName PutActionExample
     * @apiGroup Custom Fields
     *
     * @apiParam {string/array} custom_fields[customFieldType] Custom Field JSON should be same as below with `field_name` and `custom_field_id` returned from **Search custom field values' information**
     *
     * @apiParamExample {json} Request-Example:
     *      {
     *          "custom_fields":{
     "invoice":{
     "1":"test 12 item 1",
     "2":"10",
     "3":"Lorem Ipsum is simply dummy text of the printing and typesetting industry",
     "4":"Option 1",
     "5":["Option 1","Option 2"],
     "6":["Option 1","Option 3"],
     "7":"2021-05-06",
     "8":"2021-05-06 00:23:25",
     "9":"#ffffff",
     "10":"<a href=\"url.com\" target=\"_blank\">Link</a>"
     }
     }
     *      }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *      Same as Original request
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     Same as Original request
     */

    /**
     * @api {GET} N/A Request Custom Fields
     * @apiVersion 0.2.0
     * @apiDescription Custom fields' data will be returned combined with other request's information during the initial GET request of each available endpoint (Contacts, Invoices etc) with their respective `label` and `value` key
     * @apiName GetActionExample
     * @apiGroup Custom Fields
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
        {
            "id": "1",
            "sent": "0",
            "datesend": null,
            "clientid": "1",
            "deleted_customer_name": null,
            "number": "10",
            "prefix": "INV-",
            "number_format": "1",
            "datecreated": "2021-05-14 00:44:52",
            "date": "2021-08-28",
            "duedate": "2021-09-27",
            "currency": "1",
            "subtotal": "110.00",
            "total_tax": "0.00",
            "total": "110.00",
            "adjustment": "0.00",
            "addedfrom": "0",
            "hash": "4222d2f53404324ea73535d3c0f2c3f0",
            "status": "1",
            "clientnote": "",
            "adminnote": "",
            "last_overdue_reminder": null,
            "cancel_overdue_reminders": "1",
            "allowed_payment_modes": "a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}",
            "token": null,
            "discount_percent": "0.00",
            "discount_total": "0.00",
            "discount_type": "",
            "recurring": "0",
            "recurring_type": null,
            "custom_recurring": "0",
            "cycles": "0",
            "total_cycles": "0",
            "is_recurring_from": null,
            "last_recurring_date": null,
            "terms": "",
            "sale_agent": "0",
            "billing_street": "billing address",
            "billing_city": "billing city name",
            "billing_state": "billing state name",
            "billing_zip": "billing zip code",
            "billing_country": "0",
            "shipping_street": "shipping address",
            "shipping_city": "city name",
            "shipping_state": "state name",
            "shipping_zip": "zip code",
            "shipping_country": "0",
            "include_shipping": "1",
            "show_shipping_on_invoice": "1",
            "show_quantity_as": "1",
            "project_id": "0",
            "subscription_id": "0",
            "short_link": null,
            "symbol": "$",
            "name": "USD",
            "decimal_separator": ".",
            "thousand_separator": ",",
            "placement": "before",
            "isdefault": "1",
            "currencyid": "1",
            "currency_name": "USD",
            "total_left_to_pay": "110.00",
            "items": [
                {
                    "id": "1",
                    "rel_id": "1",
                    "rel_type": "invoice",
                    "description": "item description",
                    "long_description": "item long description",
                    "qty": "1.00",
                    "rate": "10.00",
                    "unit": "1",
                    "item_order": "1",
                    "customfields": [
                        {
                            "label": "Input 1",
                            "value": "test 12 item 1"
                        },
                        {
                            "label": "Number 1",
                            "value": "10"
                        },
                        {
                            "label": "Textarea 1",
                            "value": "Lorem Ipsum is simply dummy text of the printing and typesetting industry"
                        },
                        {
                            "label": "Select 1",
                            "value": "Option 1"
                        },
                        {
                            "label": "Multiselect 1",
                            "value": "Option 1, Option 2"
                        },
                        {
                            "label": "Checkbox 1",
                            "value": "Option 1, Option 3"
                        },
                        {
                            "label": "Datepicker 1",
                            "value": "2021-05-06"
                        },
                        {
                            "label": "Datetime Picker 1",
                            "value": "2021-05-06 00:23:25"
                        },
                        {
                            "label": "Colorpicker 1",
                            "value": "#ffffff"
                        },
                        {
                            "label": "Hyperlink 1",
                            "value": "<a>Link</a>"
                        }
                    ]
                },
                {
                    "id": "2",
                    "rel_id": "1",
                    "rel_type": "invoice",
                    "description": "updated item 2 description",
                    "long_description": "updated item 2 logn description",
                    "qty": "1.00",
                    "rate": "100.00",
                    "unit": "",
                    "item_order": "2",
                    "customfields": [
                        {
                            "label": "Input 1",
                            "value": "test 12 item 2"
                        },
                        {
                            "label": "Number 1",
                            "value": "10"
                        },
                        {
                            "label": "Textarea 1",
                            "value": "Lorem Ipsum is simply dummy text of the printing and typesetting industry"
                        },
                        {
                            "label": "Select 1",
                            "value": "Option 1"
                        },
                        {
                            "label": "Multiselect 1",
                            "value": "Option 1, Option 2"
                        },
                        {
                            "label": "Checkbox 1",
                            "value": "Option 1, Option 3"
                        },
                        {
                            "label": "Datepicker 1",
                            "value": "2021-05-06"
                        },
                        {
                            "label": "Datetime Picker 1",
                            "value": "2021-05-06 00:23:25"
                        },
                        {
                            "label": "Colorpicker 1",
                            "value": "#ffffff"
                        },
                        {
                            "label": "Hyperlink 1",
                            "value": "<a>Link</a>"
                        }
                    ]
                }
            ],
            "attachments": [],
            "visible_attachments_to_customer_found": false,
            "client": {
                "userid": "1",
                "company": "updated company",
                "vat": "",
                "phonenumber": "",
                "country": "0",
                "city": "",
                "zip": "",
                "state": "",
                "address": "",
                "website": "",
                "datecreated": "2021-05-14 00:15:06",
                "active": "1",
                "leadid": null,
                "billing_street": "",
                "billing_city": "",
                "billing_state": "",
                "billing_zip": "",
                "billing_country": "0",
                "shipping_street": "",
                "shipping_city": "",
                "shipping_state": "",
                "shipping_zip": "",
                "shipping_country": "0",
                "longitude": null,
                "latitude": null,
                "default_language": "",
                "default_currency": "0",
                "show_primary_contact": "0",
                "stripe_id": null,
                "registration_confirmed": "1",
                "addedfrom": "0"
            },
            "payments": [],
            "scheduled_email": null,
            "customfields": [
                {
                    "label": "Input 1",
                    "value": "test 12"
                },
                {
                    "label": "Number 1",
                    "value": "10"
                },
                {
                    "label": "Textarea 1",
                    "value": "Lorem Ipsum is simply dummy text of the printing and typesetting industry"
                },
                {
                    "label": "Select 1",
                    "value": "Option 1"
                },
                {
                    "label": "Multiselect 1",
                    "value": "Option 1, Option 2"
                },
                {
                    "label": "Checkbox 1",
                    "value": "Option 1, Option 3"
                },
                {
                    "label": "Datepicker 1",
                    "value": "2021-05-06"
                },
                {
                    "label": "Datetime Picker 1",
                    "value": "2021-05-06 00:23:25"
                },
                {
                    "label": "Colorpicker 1",
                    "value": "#ffffff"
                },
                {
                    "label": "Hyperlink 1",
                    "value": "<a>Link</a>"
                }
            ]
        }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     Same as Original request
     */

    /**
     * @api {GET} N/A Search custom field values' information
     * @apiVersion 0.2.0
     * @apiDescription Custom fields' data will be returned combined with other request's information during the initial SEARCH request of each available endpoint (Contacts, Invoices etc) with their respective `label` and `value` key
     * @apiName SearchActionExample
     * @apiGroup Custom Fields
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
        [
            {
                "id": "1",
                "sent": "0",
                "datesend": null,
                "clientid": "1",
                "deleted_customer_name": null,
                "number": "10",
                "prefix": "INV-",
                "number_format": "1",
                "datecreated": "2021-05-14 00:15:06",
                "date": "2021-08-28",
                "duedate": "2021-09-27",
                "currency": "1",
                "subtotal": "110.00",
                "total_tax": "0.00",
                "total": "110.00",
                "adjustment": "0.00",
                "addedfrom": "0",
                "hash": "4222d2f53404324ea73535d3c0f2c3f0",
                "status": "1",
                "clientnote": "",
                "adminnote": "",
                "last_overdue_reminder": null,
                "cancel_overdue_reminders": "1",
                "allowed_payment_modes": "a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}",
                "token": null,
                "discount_percent": "0.00",
                "discount_total": "0.00",
                "discount_type": "",
                "recurring": "0",
                "recurring_type": null,
                "custom_recurring": "0",
                "cycles": "0",
                "total_cycles": "0",
                "is_recurring_from": null,
                "last_recurring_date": null,
                "terms": "",
                "sale_agent": "0",
                "billing_street": "",
                "billing_city": "",
                "billing_state": "",
                "billing_zip": "",
                "billing_country": "0",
                "shipping_street": "",
                "shipping_city": "",
                "shipping_state": "",
                "shipping_zip": "",
                "shipping_country": "0",
                "include_shipping": "1",
                "show_shipping_on_invoice": "1",
                "show_quantity_as": "1",
                "project_id": "0",
                "subscription_id": "0",
                "short_link": null,
                "userid": "1",
                "company": "updated company",
                "vat": "",
                "phonenumber": "",
                "country": "0",
                "city": "",
                "zip": "",
                "state": "",
                "address": "",
                "website": "",
                "active": "1",
                "leadid": null,
                "longitude": null,
                "latitude": null,
                "default_language": "",
                "default_currency": "0",
                "show_primary_contact": "0",
                "stripe_id": null,
                "registration_confirmed": "1",
                "invoiceid": "1",
                "customfields": [
                    {
                        "label": "Input 1",
                        "value": "test 12"
                    },
                    {
                        "label": "Number 1",
                        "value": "10"
                    },
                    {
                        "label": "Textarea 1",
                        "value": "Lorem Ipsum is simply dummy text of the printing and typesetting industry"
                    },
                    {
                        "label": "Select 1",
                        "value": "Option 1"
                    },
                    {
                        "label": "Multiselect 1",
                        "value": "Option 1, Option 2"
                    },
                    {
                        "label": "Checkbox 1",
                        "value": "Option 1, Option 3"
                    },
                    {
                        "label": "Datepicker 1",
                        "value": "2021-05-06"
                    },
                    {
                        "label": "Datetime Picker 1",
                        "value": "2021-05-06 00:23:25"
                    },
                    {
                        "label": "Colorpicker 1",
                        "value": "#ffffff"
                    },
                    {
                        "label": "Hyperlink 1",
                        "value": "<a>Link</a>"
                    }
                ]
            },
            {
                "id": "2",
                "sent": "0",
                "datesend": null,
                "clientid": "1",
                "deleted_customer_name": null,
                "number": "4",
                "prefix": "INV-",
                "number_format": "1",
                "datecreated": "2021-05-14 00:15:06",
                "date": "2021-05-28",
                "duedate": "2021-06-27",
                "currency": "1",
                "subtotal": "110.00",
                "total_tax": "0.00",
                "total": "110.00",
                "adjustment": "0.00",
                "addedfrom": "0",
                "hash": "630f8cc7ed2e6a70c4113ab24041bdf5",
                "status": "6",
                "clientnote": "",
                "adminnote": "",
                "last_overdue_reminder": null,
                "cancel_overdue_reminders": "1",
                "allowed_payment_modes": "a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}",
                "token": null,
                "discount_percent": "0.00",
                "discount_total": "0.00",
                "discount_type": "",
                "recurring": "0",
                "recurring_type": null,
                "custom_recurring": "0",
                "cycles": "0",
                "total_cycles": "0",
                "is_recurring_from": null,
                "last_recurring_date": null,
                "terms": "",
                "sale_agent": "0",
                "billing_street": "",
                "billing_city": "",
                "billing_state": "",
                "billing_zip": "",
                "billing_country": "0",
                "shipping_street": "",
                "shipping_city": "",
                "shipping_state": "",
                "shipping_zip": "",
                "shipping_country": "0",
                "include_shipping": "1",
                "show_shipping_on_invoice": "1",
                "show_quantity_as": "1",
                "project_id": "0",
                "subscription_id": "0",
                "short_link": null,
                "userid": "1",
                "company": "updated company",
                "vat": "",
                "phonenumber": "",
                "country": "0",
                "city": "",
                "zip": "",
                "state": "",
                "address": "",
                "website": "",
                "active": "1",
                "leadid": null,
                "longitude": null,
                "latitude": null,
                "default_language": "",
                "default_currency": "0",
                "show_primary_contact": "0",
                "stripe_id": null,
                "registration_confirmed": "1",
                "invoiceid": "2",
                "customfields": [
                    {
                        "label": "Input 1",
                        "value": "test 12"
                    },
                    {
                        "label": "Number 1",
                        "value": "10"
                    },
                    {
                        "label": "Textarea 1",
                        "value": "Lorem Ipsum is simply dummy text of the printing and typesetting industry"
                    },
                    {
                        "label": "Select 1",
                        "value": "Option 1"
                    },
                    {
                        "label": "Multiselect 1",
                        "value": "Option 1, Option 2"
                    },
                    {
                        "label": "Checkbox 1",
                        "value": "Option 1, Option 3"
                    },
                    {
                        "label": "Datepicker 1",
                        "value": "2021-05-06"
                    },
                    {
                        "label": "Datetime Picker 1",
                        "value": "2021-05-06 00:23:25"
                    },
                    {
                        "label": "Colorpicker 1",
                        "value": "#ffffff"
                    },
                    {
                        "label": "Hyperlink 1",
                        "value": "<a>Link</a>"
                    }
                ]
            }
        ]
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     Same as Original request
     */
    
    /**
     * @api {DELETE} N/A Delete Custom Fields
     * @apiVersion 0.2.0
     * @apiDescription To remove particular custom field value you can use **Update** action and an **empty** value in the custom field.<br /> Note: When you delete any record the corresponding custom field data will be **automatically deleted**.
     * @apiName DeleteActionExample
     * @apiGroup Custom Fields
     *
     */
}
/* End of file Custom_fields.php */
/* Location: ./application/controllers/Custom_fields.php */
