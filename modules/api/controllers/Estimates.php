<?php

defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions

/** @noinspection PhpIncludeInspection */
require __DIR__ . '/REST_Controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Estimates extends REST_Controller {
    function __construct() {
        // Construct the parent class
        parent::__construct();
    }

    /**
     * @api {get} api/estimates/:id Request Estimate information
     * @apiVersion 0.3.0
     * @apiName GetEstimate
     * @apiGroup Estimates
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiParam {Number} id Contact unique ID
     *
     * @apiSuccess {Object} Estimates information.
     *
     * @apiSuccessExample Success-Response:
     *   HTTP/1.1 200 OK
     *   {
     *	    "id": "1",
     *	    "sent": "0",
     *	    "datesend": null,
     *	    "clientid": "1",
     *	    "deleted_customer_name": null,
     *	    "project_id": "0",
     *	    "number": "1",
     *	    "prefix": "EST-",
     *	    "number_format": "1",
     *	    "hash": "b12ae9de6471d0cf153d7846f05128af",
     *	    "datecreated": "2021-07-31 11:06:49",
     *	    "date": "2021-07-31",
     *	    "expirydate": "2021-08-07",
     *	    "currency": "1",
     *	    "subtotal": "1200.00",
     *	    "total_tax": "0.00",
     *	    "total": "1200.00",
     *	    "adjustment": "0.00",
     *	    "addedfrom": "1",
     *	    "status": "1",
     *	    "clientnote": "",
     *	    "adminnote": "",
     *	    "discount_percent": "0.00",
     *	    "discount_total": "0.00",
     *	    "discount_type": "",
     *	    "invoiceid": null,
     *	    "invoiced_date": null,
     *	    "terms": "",
     *	    "reference_no": "",
     *	    "sale_agent": "0",
     *	    "billing_street": "Thangadh, Gujarat, India<br />\r\nShipping",
     *	    "billing_city": "Thangadh",
     *	    "billing_state": "Gujarat",
     *	    "billing_zip": "363630",
     *	    "billing_country": "102",
     *	    "shipping_street": "Thangadh, Gujarat, India<br />\r\nShipping",
     *	    "shipping_city": "Thangadh",
     *	    "shipping_state": "Gujarat",
     *	    "shipping_zip": "363630",
     *	    "shipping_country": "102",
     *	    "include_shipping": "1",
     *	    "show_shipping_on_estimate": "1",
     *	    "show_quantity_as": "1",
     *	    "pipeline_order": "0",
     *	    "is_expiry_notified": "0",
     *	    "acceptance_firstname": null,
     *	    "acceptance_lastname": null,
     *	    "acceptance_email": null,
     *	    "acceptance_date": null,
     *	    "acceptance_ip": null,
     *	    "signature": null,
     *	    "short_link": null,
     *	    "symbol": "$",
     *	    "name": "USD",
     *	    "decimal_separator": ".",
     *	    "thousand_separator": ",",
     *	    "placement": "before",
     *	    "isdefault": "1",
     *	    "currencyid": "1",
     *	    "currency_name": "USD",
     *	    "attachments": [],
     *	    "visible_attachments_to_customer_found": false,
     *	    "items": [
     *	        {
     *	            "id": "2",
     *	            "rel_id": "1",
     *	            "rel_type": "estimate",
     *	            "description": "test",
     *	            "long_description": "test",
     *	            "qty": "1.00",
     *	            "rate": "1200.00",
     *	            "unit": "1",
     *	            "item_order": "1"
     *	        }
     *	    ],
     *	    "client": {
     *	        "userid": "1",
     *	        "company": "test",
     *	        "vat": "",
     *	        "phonenumber": "01324568903",
     *	        "country": "102",
     *	        "city": "test",
     *	        "zip": "3000",
     *	        "state": "Test",
     *	        "address": "Test",
     *	        "website": "",
     *	        "datecreated": "2021-07-30 16:29:46",
     *	        "active": "1",
     *	        "leadid": null,
     *	        "billing_street": "Test",
     *	        "billing_city": "Test",
     *	        "billing_state": "Test",
     *	        "billing_zip": "3000",
     *	        "billing_country": "102",
     *	        "shipping_street": "Test",
     *	        "shipping_city": "Test",
     *	        "shipping_state": "Test",
     *	        "shipping_zip": "3000",
     *	        "shipping_country": "102",
     *	        "longitude": null,
     *	        "latitude": null,
     *	        "default_language": "",
     *	        "default_currency": "0",
     *	        "show_primary_contact": "0",
     *	        "stripe_id": null,
     *	        "registration_confirmed": "1",
     *	        "addedfrom": "1"
     *	    },
     *	    "scheduled_email": null,
     *	    "customfields": []
     *	}
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data were found"
     *     }
     */
    public function data_get($id = '') {
        // If the id parameter doesn't exist return all the
        $data = $this->Api_model->get_table('estimates', $id);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "estimate", $id);
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
    }

    /**
     * @api {get} api/estimates/search/:keysearch Search Estimate information
     * @apiVersion 0.3.0
     * @apiName GetEstimateSearch
     * @apiGroup Estimates
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {String} keysearch Search Keywords.
     *
     * @apiSuccess {Object} Estimate Information.
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *   {
     *      "id": "2",
     *      "sent": "0",
     *      "datesend": null,
     *      "clientid": "1",
     *      "deleted_customer_name": null,
     *      "project_id": "0",
     *      "number": "2",
     *      "prefix": "EST-",
     *      "number_format": "1",
     *      "hash": "ac754972999f948ade369c70bb44d696",
     *      "datecreated": "2021-07-30 16:29:46",
     *      "date": "2021-08-01",
     *      "expirydate": "2021-08-08",
     *      "currency": "1",
     *      "subtotal": "1200.00",
     *      "total_tax": "0.00",
     *      "total": "1200.00",
     *      "adjustment": "0.00",
     *      "addedfrom": "1",
     *      "status": "1",
     *      "clientnote": "",
     *      "adminnote": "adminnote",
     *      "discount_percent": "0.00",
     *      "discount_total": "0.00",
     *      "discount_type": "",
     *      "invoiceid": null,
     *      "invoiced_date": null,
     *      "terms": "",
     *      "reference_no": "",
     *      "sale_agent": "0",
     *      "billing_street": "Test",
     *      "billing_city": "Test",
     *      "billing_state": "Test",
     *      "billing_zip": "3000",
     *      "billing_country": "102",
     *      "shipping_street": "Test",
     *      "shipping_city": "Test",
     *      "shipping_state": "Test",
     *      "shipping_zip": "3000",
     *      "shipping_country": "102",
     *      "include_shipping": "1",
     *      "show_shipping_on_estimate": "1",
     *      "show_quantity_as": "1",
     *      "pipeline_order": "0",
     *      "is_expiry_notified": "0",
     *      "acceptance_firstname": null,
     *      "acceptance_lastname": null,
     *      "acceptance_email": null,
     *      "acceptance_date": null,
     *      "acceptance_ip": null,
     *      "signature": null,
     *      "short_link": null,
     *      "userid": "1",
     *      "company": "test",
     *      "vat": "",
     *      "phonenumber": "01324568903",
     *      "country": "102",
     *      "city": "Test",
     *      "zip": "3000",
     *      "state": "Test",
     *      "address": "Test",
     *      "website": "",
     *      "active": "1",
     *      "leadid": null,
     *      "longitude": null,
     *      "latitude": null,
     *      "default_language": "",
     *      "default_currency": "0",
     *      "show_primary_contact": "0",
     *      "stripe_id": null,
     *      "registration_confirmed": "1",
     *      "estimateid": "2",
     *     "customfields": []
     *  }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No Data Were Found"
     *     }
     */
    public function data_search_get($key = '') {
        $data = $this->Api_model->search('estimates', $key);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "estimate");
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
    }

    /**
     * @api {delete} api/estimates/:id Delete Estimate
     * @apiVersion 0.3.0
     * @apiName DeleteEstimate
     * @apiGroup Estimates
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Estimates Deleted Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Estimate Deleted Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Estimate Delete Fail
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Estimate Delete Fail"
     *     }
     */
    public function data_delete($id = '') {
        $id = $this->security->xss_clean($id);
        if (empty($id) && !is_numeric($id)) {
            $message = array('status' => FALSE, 'message' => 'Invalid Estimate ID');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->load->model('estimates_model');
            $is_exist = $this->estimates_model->get($id);
            if (is_object($is_exist)) {
                $output = $this->estimates_model->delete($id);
                if ($output === TRUE) {
                    // success
                    $message = array('status' => TRUE, 'message' => 'Estimate Deleted Successfully');
                    $this->response($message, REST_Controller::HTTP_OK);
                } else {
                    // error
                    $message = array('status' => FALSE, 'message' => 'Estimate Delete Fail');
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $message = array('status' => FALSE, 'message' => 'Invalid Estimate ID');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * @api {post} api/estimates Add New Estimates
     * @apiVersion 0.3.0
     * @apiName PostEstimates
     * @apiGroup Estimates
     *
     *  @apiHeader {String} Authorization Basic Access Authentication token.
     *
     *  @apiParam {Number}  clientid                       Mandatory. Customer id
     *	@apiParam {Number}  number                         Mandatory. Estimates Number
     *	@apiParam {Date}    date                           Mandatory. Estimates Date
     *	@apiParam {Date}    [duedate]                      Optional.  Expiry Date of Estimates
     *	@apiParam {Number}  currency                       Mandatory. currency field
     *  @apiParam {Array}   newitems               	 	   Mandatory. New Items to be added
     *  @apiParam {Decimal}   subtotal               	   Mandatory. calculation based on item Qty, Rate and Tax
     *  @apiParam {Decimal}   total               	 	   Mandatory. calculation based on subtotal, Discount and Adjustment
     *	@apiParam {String}  billing_street                 Optional. Street Address
     *	@apiParam {String}  [billing_city]                 Optional. City Name for billing
     *	@apiParam {String}  [billing_state]                Optional. Name of state for billing
     *	@apiParam {Number}  [billing_zip]                  Optional. Zip code
     *	@apiParam {Number}  [billing_country]              Optional. Country code
     *	@apiParam {String}  [shipping_street]              Optional. Address of shipping
     *	@apiParam {String}  [shipping_city]                Optional. City name for shipping
     *	@apiParam {String}  [shipping_state]               Optional. Name of state for shipping
     *	@apiParam {Number}  [shipping_zip]                 Optional. Zip code for shipping
     *	@apiParam {Number}  [shipping_country]             Optional. Country code
     *	@apiParam {String}  [tags]                         Optional. TAGS comma separated
     *	@apiParam {Number}  [status]                   	   Optional. Status id (default status is Accepted)
     *	@apiParam {String}  [Reference]                    Optional. Reference name
     *	@apiParam {Number}  [sale_agent]                   Optional. Sale Agent name
     *	@apiParam {String}  [adminnote]                    Optional. notes by admin
     *	@apiParam {String} 	[clientnote]       			   Optional. client notes
     *	@apiParam {String} 	[terms]                   	   Optional. Terms
     *
     *  @apiParamExample {Multipart Form} Request-Example:
     *   [
     *		"clientid"=>1,
     *		"number"=>"00001",
     *		"date"=>"2020-09-07",
     *		"currency"=>1,
     *		"newitems[0][description]"=>"item 1 description",
     *		"newitems[0][long_description]"=>"item 1 long description",
     *		"newitems[0][qty]"=>1,
     *		"newitems[0][rate]"=>100,
     *		"newitems[0][order]"=>1,
     * 		"newitems[0][taxname][]"=>CGST|9.00,
     * 		"newitems[0][taxname][]"=>SGST|9.00,
     * 		"newitems[0][unit]"=>"",
     * 		"newitems[1][description]"=>"item 2 description",
     *		"newitems[1][long_description]"=>"item 2 long description",
     *		"newitems[1][qty]"=>1,
     *		"newitems[1][rate]"=>100,
     *		"newitems[1][order]"=>1,
     * 		"newitems[1][taxname][]"=>CGST|9.00,
     * 		"newitems[1][taxname][]"=>SGST|9.00,
     * 		"newitems[1][unit]"=>"",
     *		"subtotal"=>236.00,
     *		"total"=>236.00,
     *		"status"=>1,
     * 		....
     *	]
     *
     *
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Estimates Added Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Estimates Added Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Estimates add fail
     * @apiError {String} newitems[] The Items field is required
     * @apiError {String} number The Estimates number is already in use
     * @apiError {String} subtotal The Sub Total field is required
     * @apiError {String} total The Total field is required
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Estimates Add Fail"
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 409 Conflict
     *     {
     *       "status": false,
     *       "error": {
     *			"number":"The Estimates number is already in use"
     *		},
     * 		"message": "The Estimates number is already in use"
     *     }
     *
     * @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *    {
     *	    "status": false,
     *	    "error": {
     *	        "newitems[]": "The Items field is required"
     *	    },
     *	    "message": "<p>The Items field is required</p>\n"
     *	}
     *
     * @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *    {
     *	    "status": false,
     *	    "error": {
     *	        "subtotal": "The Sub Total field is required"
     *	    },
     *	    "message": "<p>The Sub Total field is required</p>\n"
     *	}
     *
     *  @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *    {
     *	    "status": false,
     *	    "error": {
     *	        "total": "The Total field is required"
     *	    },
     *	    "message": "<p>The Total field is required</p>\n"
     *	}
     *
     */
    public function data_post() {
		error_reporting(0);
        $data = $this->input->post();
        $this->form_validation->set_rules('clientid', 'Customer', 'trim|required|numeric|greater_than[0]');
        $this->form_validation->set_rules('project_id', 'Project', 'trim|numeric|greater_than[0]');
        $this->form_validation->set_rules('include_shipping', 'Include Shipping', 'trim|numeric|greater_than_equal_to[0]|less_than_equal_to[1]');
        $this->form_validation->set_rules('show_shipping_on_estimate', 'Show shipping on estimate', 'trim|numeric|greater_than_equal_to[0]|less_than_equal_to[1]');
        $this->form_validation->set_rules('currency', 'Currency', 'trim|required|numeric|greater_than[0]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|numeric|greater_than[0]');
        $this->form_validation->set_rules('date', 'Estimate date', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('quantity', 'Quantity', 'trim|numeric|greater_than[0]');
        $this->form_validation->set_rules('newitems[]', 'Items', 'required');
        $this->form_validation->set_rules('subtotal', 'Sub Total', 'trim|required|decimal|greater_than[0]');
        $this->form_validation->set_rules('total', 'Total', 'trim|required|decimal|greater_than[0]');
        $this->form_validation->set_rules('billing_street', 'Street', 'trim|required|max_length[200]');
        $this->form_validation->set_rules('number', 'Estimate Number', 'trim|required|numeric|callback_validate_estimate_number[0]');
        if ($this->form_validation->run() == FALSE) {
            $message = array('status' => FALSE, 'error' => $this->form_validation->error_array(), 'message' => validation_errors());
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->load->model('estimates_model');
            $data['expirydate'] = _d(date('Y-m-d', strtotime('+' . get_option('estimate_due_after') . ' DAY', strtotime(date('Y-m-d')))));
            $id = $this->estimates_model->add($data);
            if ($id > 0 && !empty($id)) {
                // success
                $message = array('status' => TRUE, 'message' => 'Estimate Added Successfully');
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                // error
                $message = array('status' => FALSE, 'message' => 'Estimate Add Fail');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    public function validate_estimate_number($number, $estimateid) {
        $isedit = 'false';
        if (!empty($estimateid)) {
            $isedit = 'true';
        }
        $this->form_validation->set_message('validate_estimate_number', 'The {field} is already in use');
        $original_number = null;
        $date = $this->input->post('date');
        if (!empty($estimateid)) {
            $data = $this->Api_model->get_table('estimates', $estimateid);
            $original_number = $data->number;
            if (empty($date)) {
                $date = $data->date;
            }
        }
        $number = trim($number);
        $number = ltrim($number, '0');
        if ($isedit == 'true') {
            if ($number == $original_number) {
                return TRUE;
            }
        }
        if (total_rows(db_prefix() . 'estimates', ['YEAR(date)' => date('Y', strtotime(to_sql_date($date))), 'number' => $number, ]) > 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * @api {put} api/estimates/:id Update a estimate
     * @apiVersion 0.3.0
     * @apiName PutEstimate
     * @apiGroup Estimates
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {String} clientid                   Mandatory. Customer.
     * @apiParam {String} billing_street          	Mandatory. Street Address
     * @apiParam {String}  [billing_city]            Optional. City Name for billing
     * @apiParam {String}  [billing_state]           Optional. Name of state for billing
     * @apiParam {Number}  [billing_zip]             Optional. Zip code
     * @apiParam {Number}  [billing_country]         Optional. Country code
     * @apiParam {boolean} [include_shipping="no"]   Optional. set yes if you want add Shipping Address
     * @apiParam {boolean} [show_shipping_on_estimate]    Optional. Shows shipping details in estimate.
     * @apiParam {String}  [shipping_street]          Optional. Address of shipping
     * @apiParam {String}  [shipping_city]            Optional. City name for shipping
     * @apiParam {String}  [shipping_state]           Optional. Name of state for shipping
     * @apiParam {Number}  [shipping_zip]             Optional. Zip code for shipping
     * @apiParam {Number}  [shipping_country]         Optional. Country code
     * @apiParam {Number}  number                     Mandatory. Estimate Number
     * @apiParam {Date}    date                       Mandatory. Estimate Date
     * @apiParam {Date}    [expirydate]               Optional. Expiry Date of Estimate
     * @apiParam {String}  [tags]                     Optional. TAGS comma separated
     * @apiParam {Number}  currency                   Mandatory. currency field
     * @apiParam {Number}  status                     Mandatory. Estimate Status(eg. Draft, Sent)
     * @apiParam {String}  [reference_no]             Optional. Reference #
     * @apiParam {Number}  [sale_agent]               Optional. Sale Agent name
     * @apiParam {String}  [discount_type]            Optional. before_tax / after_tax discount type
     * @apiParam {String}  [adminnote]                Optional. notes by admin
     * @apiParam {Array}   [items]                    Mandatory. Existing items with Id
     * @apiParam {Array}   [removed_items]			 Optional. Items to be removed
     * @apiParam {Array}   [newitems]       	 	     Optional. New Items to be added
     * @apiParam {Decimal}   subtotal           	     Mandatory. calculation based on item Qty, Rate and Tax
     * @apiParam {Decimal}   total               	 Mandatory. calculation based on subtotal, Discount and Adjustment
     * @apiParam {String}  [clientnote]       		 Optional. client notes
     * @apiParam {String}  [terms]                    Optional. Terms
     *
     *  	@apiParamExample {json} Request-Example:
     *  	{
     *	    "clientid": 1,
     *	    "billing_street": "new 1 update",
     *	    "number": 2,
     *	    "status": 2,
     *	    "date": "2021-08-19",
     *	    "currency": 1,
     *	    "items": {
     *	        "1": {
     *	            "itemid": "24",
     *	            "order": "1",
     *	            "description": "item description",
     *	            "long_description": "item long description",
     *	            "qty": "1",
     *	            "unit": "1",
     *	            "rate": "10.00",
     *	            "custom_fields":{
     *	                "items":{
     *	                    "31":"test 12 item 1",
     *			            "32":"10",
     *			            "33":"Lorem Ipsum is simply dummy text of the printing and typesetting industry",
     *			            "34":"Option 1",
     *			            "35":["Option 1","Option 2"],
     *			            "36":["Option 1","Option 3"],
     *			            "37":"2021-05-06",
     *			            "38":"2021-05-06 00:23:25",
     *			            "39":"#ffffff",
     *			            "40":"<a href=\"url.com\" target=\"_blank\">Link</a>"
     *	                }
     *	            }
     *	        }
     *	    },
     *	    "newitems": {
     *	        "2": {
     *	            "order": "2",
     *	            "description": "updated item 2 description",
     *	            "long_description": "updated item 2 logn description",
     *	            "qty": "1",
     *	            "unit": "",
     *	            "rate": "100.00",
     *	            "custom_fields":{
     *	                "items":{
     *	                    "31":"test 12 item 2",
     *			            "32":"10",
     *			            "33":"Lorem Ipsum is simply dummy text of the printing and typesetting industry",
     *			            "34":"Option 1",
     *			            "35":["Option 1","Option 2"],
     *			            "36":["Option 1","Option 3"],
     *			            "37":"2021-05-06",
     *			            "38":"2021-05-06 00:23:25",
     *			            "39":"#ffffff",
     *			            "40":"<a href=\"url.com\" target=\"_blank\">Link</a>"
     *	                }
     *	            }
     *	        }
     *	    },
     *	    "custom_fields":{
     *	        "estimate":{
     *	            "92":"test 1254"
     *	        }
     *	    },
     *	    "subtotal":"110.00",
     *	    "total":"110.00"
     *	}
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": false,
     *       "message": "Estimate Updated Successfully"
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Estimate Update Fail"
     *     }
     *
     *  @apiError {String} number The Estimate number is already in use
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 409 Conflict
     *     {
     *       "status": false,
     *       "error": {
     *			"number":"The Estimate number is already in use"
     *		},
     * 		"message": "The Estimate number is already in use"
     *     }
     *
     *
     */
    public function data_put($id = "") {
        $_POST = json_decode($this->security->xss_clean(file_get_contents("php://input")), true);
        if (empty($_POST) || !isset($_POST)) {
            $message = array('status' => FALSE, 'message' => 'Data Not Acceptable OR Not Provided');
            $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
        }
        $this->form_validation->set_data($_POST);
        if (empty($id) && !is_numeric($id)) {
            $message = array('status' => FALSE, 'message' => 'Invalid Estimate ID');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->form_validation->set_rules('clientid', 'Customer', 'trim|required|numeric|greater_than[0]');
            $this->form_validation->set_rules('project_id', 'Project', 'trim|numeric|greater_than[0]');
            $this->form_validation->set_rules('include_shipping', 'Include Shipping', 'trim|numeric|greater_than_equal_to[0]|less_than_equal_to[1]');
            $this->form_validation->set_rules('show_shipping_on_estimate', 'Show shipping on estimate', 'trim|numeric|greater_than_equal_to[0]|less_than_equal_to[1]');
            $this->form_validation->set_rules('currency', 'Currency', 'trim|required|numeric|greater_than[0]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|numeric|greater_than[0]');
            $this->form_validation->set_rules('date', 'Estimate date', 'trim|required|max_length[255]');
            $this->form_validation->set_rules('quantity', 'Quantity', 'trim|numeric|greater_than[0]');
            $this->form_validation->set_rules('items[]', 'Items', 'required');
            $this->form_validation->set_rules('subtotal', 'Sub Total', 'trim|required|decimal|greater_than[0]');
            $this->form_validation->set_rules('total', 'Total', 'trim|required|decimal|greater_than[0]');
            $this->form_validation->set_rules('billing_street', 'Street', 'trim|required|max_length[200]');
            $this->form_validation->set_rules('number', 'Estimate Number', 'trim|required|numeric|callback_validate_estimate_number[' . $id . ']');
            $_POST['shipping_street'] = $_POST['shipping_street']??"";
            if ($this->form_validation->run() == FALSE) {
                $message = array('status' => FALSE, 'error' => $this->form_validation->error_array(), 'message' => validation_errors());
                $this->response($message, REST_Controller::HTTP_CONFLICT);
            } else {
                $this->load->model('estimates_model');
                $is_exist = $this->estimates_model->get($id);
                if (!is_object($is_exist)) {
                    $message = array('status' => FALSE, 'message' => 'Estimate ID Doesn\'t Not Exist.');
                    $this->response($message, REST_Controller::HTTP_CONFLICT);
                }
                if (is_object($is_exist)) {
                    $data = $this->input->post();
                    $data['isedit'] = "";
                    $success = $this->estimates_model->update($data, $id);
                    if ($success == true) {
                        $message = array('status' => TRUE, 'message' => "Estimate Updated Successfully",);
                        $this->response($message, REST_Controller::HTTP_OK);
                    } else {
                        // error
                        $message = array('status' => FALSE, 'message' => 'Estimate Update Fail');
                        $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                    }
                } else {
                    $message = array('status' => FALSE, 'message' => 'Invalid Estimate ID');
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        }
    }
}