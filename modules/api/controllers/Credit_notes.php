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
class Credit_notes extends REST_Controller {
    function __construct() {
        // Construct the parent class
        parent::__construct();
    }

    /**
     * @api {get} api/credit_notes/:id Request Credit notes information
     * @apiVersion 0.3.0
     * @apiName GetCreditNotes
     * @apiGroup Credit Notes
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiSuccess {Object} Credit notes information.
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *	        "id": "2",
     *	        "clientid": "1",
     *	        "deleted_customer_name": null,
     *	        "number": "2",
     *         "prefix": "CN-",
     *	        "number_format": "1",
     *	        "datecreated": "2021-07-30 16:29:46",
     *	        "date": "2021-08-02",
     *	        "adminnote": "adminnote2",
     *         "terms": "",
     *	        "clientnote": "",
     *	        "currency": "1",
     *	        "subtotal": "1200.00",
     *	        "total_tax": "0.00",
     *	        "total": "1200.00",
     *	        "adjustment": "0.00",
     *	        "addedfrom": "1",
     *	        "status": "1",
     *	        "project_id": "0",
     *	        "discount_percent": "0.00",
     *	        "discount_total": "0.00",
     *	        "discount_type": "",
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
     *	        "include_shipping": "1",
     *	        "show_shipping_on_credit_note": "1",
     *	        "show_quantity_as": "1",
     *	        "reference_no": "",
     *	        "userid": "1",
     *	        "company": "Test",
     *	        "vat": "",
     *	        "phonenumber": "01324568903",
     *	        "country": "102",
     *	        "city": "Test",
     *	        "zip": "3000",
     *	        "state": "Test",
     *	        "address": "Test",
     *	        "website": "",
     *	        "active": "1",
     *	        "leadid": null,
     *	        "longitude": null,
     *	        "latitude": null,
     *	        "default_language": "",
     *	        "default_currency": "0",
     *	        "show_primary_contact": "0",
     *	        "stripe_id": null,
     *	        "registration_confirmed": "1",
     *	        "credit_note_id": "2",
     *	        "customfields": []
     *	    }
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
        $data = $this->Api_model->get_table('creditnotes', $id);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "credit_note", $id);
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
    }

    /**
     * @api {get} api/credit_notes/search/:keysearch Search credit notes item information
     * @apiVersion 0.3.0
     * @apiName GetCreditNotesSearch
     * @apiGroup Credit Notes
     *
     * @apiHeader {String} Authorization Basic Access Authentication token
     *
     * @apiParam {String} keysearch Search Keywords
     *
     * @apiSuccess {Object} credit notes Information
     *
     * @apiSuccessExample Success-Response:
     *	HTTP/1.1 200 OK
     *     {
     *	        "id": "2",
     *	        "clientid": "1",
     *	        "deleted_customer_name": null,
     *	        "number": "2",
     *         "prefix": "CN-",
     *	        "number_format": "1",
     *	        "datecreated": "2021-07-30 16:29:46",
     *	        "date": "2021-08-02",
     *	        "adminnote": "adminnote2",
     *         "terms": "",
     *	        "clientnote": "",
     *	        "currency": "1",
     *	        "subtotal": "1200.00",
     *	        "total_tax": "0.00",
     *	        "total": "1200.00",
     *	        "adjustment": "0.00",
     *	        "addedfrom": "1",
     *	        "status": "1",
     *	        "project_id": "0",
     *	        "discount_percent": "0.00",
     *	        "discount_total": "0.00",
     *	        "discount_type": "",
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
     *	        "include_shipping": "1",
     *	        "show_shipping_on_credit_note": "1",
     *	        "show_quantity_as": "1",
     *	        "reference_no": "",
     *	        "userid": "1",
     *	        "company": "test",
     *	        "vat": "",
     *	        "phonenumber": "01324568903",
     *	        "country": "102",
     *	        "city": "Test",
     *	        "zip": "3000",
     *	        "state": "Test",
     *	        "address": "Test",
     *	        "website": "",
     *	        "active": "1",
     *	        "leadid": null,
     *	        "longitude": null,
     *	        "latitude": null,
     *	        "default_language": "",
     *	        "default_currency": "0",
     *	        "show_primary_contact": "0",
     *	        "stripe_id": null,
     *	        "registration_confirmed": "1",
     *	        "credit_note_id": "2",
     *	        "customfields": []
     *	    }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data were found"
     *     }
     */
    public function data_search_get($key = '') {
        $data = $this->Api_model->search('creditnotes', $key);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "credit_note");
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
    }

    /**
     * @api {delete} api/credit_notes/:id Delete Credit Note
     * @apiVersion 0.3.0
     * @apiName DeleteCreditNote
     * @apiGroup Credit Notes
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Credit Note Deleted Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Credit Note Deleted Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Credit Note Delete Fail
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Credit Note Delete Fail"
     *     }
     */
    public function data_delete($id = '') {
        $id = $this->security->xss_clean($id);
        if (empty($id) && !is_numeric($id)) {
            $message = array('status' => FALSE, 'message' => 'Invalid Credit Note ID');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->load->model('credit_notes_model');
            $is_exist = $this->credit_notes_model->get($id);
            if (is_object($is_exist)) {
                $output = $this->credit_notes_model->delete($id);
                if ($output === TRUE) {
                    // success
                    $message = array('status' => TRUE, 'message' => 'Credit Note Deleted Successfully');
                    $this->response($message, REST_Controller::HTTP_OK);
                } else {
                    // error
                    $message = array('status' => FALSE, 'message' => 'Credit Note Delete Fail');
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $message = array('status' => FALSE, 'message' => 'Invalid Credit Note ID');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * @api {post} api/credit_notes Add New Credit Notes
     * @apiVersion 0.3.0
     * @apiName PostCredit_notes
     * @apiGroup Credit Notes
     *
     *  @apiHeader {String} Authorization Basic Access Authentication token.
     *
     *  @apiParam {Number}  clientid                       Mandatory. Customer id
     *	@apiParam {Date}    date                           Mandatory. Credit Note Date
     *	@apiParam {Number}  number                         Mandatory. Credit Note Number
     *	@apiParam {Number}  currency                       Mandatory. currency field
     *  @apiParam {Array}   newitems               	 	   Mandatory. New Items to be added
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
     *	@apiParam {String}  [discount_type]                Optional. before_tax / after_tax discount type
     *	@apiParam {String} 	[Admin Note]                   Optional. Admin Note
     *	@apiParam {Decimal} subtotal               	   	   Mandatory. calculation based on item Qty, Rate and Tax
     *  @apiParam {Decimal} total               	 	   Mandatory. calculation based on subtotal, Discount and
     *  @apiParam {String} 	[clientnote]       			   Optional. client notes
     *	@apiParam {String} 	[terms]                   	   Optional. Terms
     *
     *
     *  @apiParamExample {Multipart Form} Request-Example:
     *  [
     *		"clientid" => 2
     *		"date" => 2021-08-20
     *		"number" => 2
     *		"newitems[0][description]" => item 1 description
     *		"newitems[0][long_description]" => item 1 long description
     *		"newitems[0][qty]" => 1
     *		"newitems[0][rate]" => 1200
     *		"newitems[0][order]" => 1
     *		"newitems[0][unit]" =>
     *		"newitems[0][unit]" =>
     *		"newitems[0][custom_fields][items][1]" => "new condition"
     *		"subtotal" => 1200.00
     *		"total" => 1200.00
     *		"currency" => 1
     *		"custom_fields"[credit_note][1]" => customfield_value
     * ]
     *
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Credit Note Added Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Credit Note Added Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Credit Note add fail
     * @apiError {String} newitems[] The Items field is required
     * @apiError {String} number The Credit Note number is already in use
     * @apiError {String} subtotal The Sub Total field is required
     * @apiError {String} total The Total field is required
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Credit Note Add Fail"
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 409 Conflict
     *     {
     *       "status": false,
     *       "error": {
     *			"number":"The Credit Note number is already in use"
     *		},
     * 		"message": "The Credit Note number is already in use"
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
        $data = $this->input->post();
        $this->form_validation->set_rules('clientid', 'Customer', 'trim|required|numeric|greater_than[0]');
        $this->form_validation->set_rules('project_id', 'Project', 'trim|numeric|greater_than[0]');
        $this->form_validation->set_rules('currency', 'Currency', 'trim|required|numeric|greater_than[0]');
        $this->form_validation->set_rules('date', 'Credit Note Date', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('newitems[]', 'Items', 'required');
        $this->form_validation->set_rules('subtotal', 'Sub Total', 'trim|required|decimal|greater_than[0]');
        $this->form_validation->set_rules('total', 'Total', 'trim|required|decimal|greater_than[0]');
        $this->form_validation->set_rules('number', 'Credit Note Number', 'trim|required|numeric|callback_validate_creditnotes_number[0]');
        if ($this->form_validation->run() == FALSE) {
            $message = array('status' => FALSE, 'error' => $this->form_validation->error_array(), 'message' => validation_errors());
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->load->model('credit_notes_model');
            $id = $this->credit_notes_model->add($data);
            if ($id > 0 && !empty($id)) {
                // success
                $message = array('status' => TRUE, 'message' => 'Credit Note Added Successfully');
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                // error
                $message = array('status' => FALSE, 'message' => 'Credit Note Add Fail');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }
    public function validate_creditnotes_number($number, $credit_notes_id) {
        $isedit = 'false';
        if (!empty($credit_notes_id)) {
            $isedit = 'true';
        }
        $this->form_validation->set_message('validate_creditnotes_number', 'The {field} is already in use');
        $original_number = null;
        $date = $this->input->post('date');
        if (!empty($credit_notes_id)) {
            $data = $this->Api_model->get_table('creditnotes', $credit_notes_id);
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
        if (total_rows(db_prefix() . 'creditnotes', ['YEAR(date)' => date('Y', strtotime(to_sql_date($date))), 'number' => $number, ]) > 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * @api {put} api/credit_notes Update a Credit Note
     * @apiVersion 0.3.0
     * @apiName PutCredit_notes
     * @apiGroup Credit Notes
     *
     *  @apiHeader {String} Authorization Basic Access Authentication token.
     *
     *  @apiParam {Number}  clientid                       Mandatory. Customer id
     *	@apiParam {Date}    date                           Mandatory. Credit Note Date
     *	@apiParam {Number}  number                         Mandatory. Credit Note Number
     *	@apiParam {Number}  currency                       Mandatory. currency field
     *  @apiParam {Array}   newitems               	 	   Mandatory. New Items to be added
     * @apiParam {Array}   items                    	   Mandatory. Existing items with Id
     * @apiParam {Array}   removed_items			 	   Optional. Items to be removed
     * @apiParam {Array}   newitems       	 	     	   Optional. New Items to be added
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
     *	@apiParam {String}  [discount_type]                Optional. before_tax / after_tax discount type
     *	@apiParam {String} 	[Admin Note]                   Optional. Admin Note
     *	@apiParam {Decimal} subtotal               	   	   Mandatory. calculation based on item Qty, Rate and Tax
     *  @apiParam {Decimal} total               	 	   Mandatory. calculation based on subtotal, Discount and
     *  @apiParam {String} 	[clientnote]       			   Optional. client notes
     *	@apiParam {String} 	[terms]                   	   Optional. Terms
     *
     *
     *  @apiParamExample {json} Request-Example:
     *	{
     *	    "clientid": 1,
     *	    "date": "2021-08-20",
     *	    "number": 1,
     *	    "items":
     *	    {
     *	        "1":
     *	        {
     *	            "itemid": "25",
     *	            "order": "1",
     *	            "description": "item description",
     *	            "long_description": "item long description",
     *	            "qty": "1",
     *	            "unit": "1",
     *	            "rate": "10.00",
     *	            "custom_fields":
     *	            {
     *	                "items":
     *	                {
     *	                    "31": "test 12 item 1",
     *	                    "32": "10",
     *	                    "33": "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
     *	                    "34": "Option 1",
     *	                    "35":
     *	                    [
     *	                        "Option 1",
     *	                        "Option 2"
     *	                    ],
     *	                    "36":
     *	                    [
     *	                        "Option 1",
     *	                        "Option 3"
     *	                    ],
     *	                    "37": "2021-05-06",
     *	                    "38": "2021-05-06 00:23:25",
     *	                    "39": "#ffffff",
     *	                    "40": "<a href=\"url.com\" target=\"_blank\">Link</a>"
     *	                }
     *	            }
     *	        }
     *	    },
     *	    "newitems":
     *	    {
     *	        "2":
     *	        {
     *	            "order": "2",
     *	            "description": "updated item 2 description",
     *	            "long_description": "updated item 2 logn description",
     *	            "qty": "1",
     *	            "unit": "",
     *	            "rate": "100.00",
     *	            "custom_fields":
     *	            {
     *	                "items":
     *	                {
     *	                    "31": "test 12 item 2",
     *	                    "32": "10",
     *	                    "33": "Lorem Ipsum is simply dummy text of the printing and  typesetting industry",
     *	                    "34": "Option 1",
     *	                    "35":
     *	                    [
     *	                        "Option 1",
     *	                        "Option 2"
     *	                    ],
     *	                    "36":
     *	                    [
     *	                        "Option 1",
     *	                        "Option 3"
     *	                    ],
     *	                    "37": "2021-05-06",
     *	                    "38": "2021-05-06 00:23:25",
     *	                    "39": "#ffffff",
     *	                    "40": "<a href=\"url.com\" target=\"_blank\">Link</a>"
     *	                }
     *	            }
     *	        }
     *	    },
     *	    "custom_fields":
     *	    {
     *	        "credit_note":
     *	        {
     *	            "93": "test 1254"
     *	        }
     *	    },
     *	    "subtotal": "1200.00",
     *	    "total": "1200.00",
     *	    "currency": 1
     *	}
     *
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Credit Note Updated Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Credit Note Updated Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Credit Note Update Fail
     * @apiError {String} newitems[] The Items field is required
     * @apiError {String} number The Credit Note number is already in use
     * @apiError {String} subtotal The Sub Total field is required
     * @apiError {String} total The Total field is required
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Credit Note Update Fail"
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 409 Conflict
     *     {
     *       "status": false,
     *       "error": {
     *			"number":"The Credit Note number is already in use"
     *		},
     * 		"message": "The Credit Note number is already in use"
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
    public function data_put($id = "") {
        $_POST = json_decode($this->security->xss_clean(file_get_contents("php://input")), true);
        if (empty($_POST) || !isset($_POST)) {
            $message = array('status' => FALSE, 'message' => 'Data Not Acceptable OR Not Provided');
            $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
        }
        $this->form_validation->set_data($_POST);
        if (empty($id) && !is_numeric($id)) {
            $message = array('status' => FALSE, 'message' => 'Invalid Credit Note ID');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->form_validation->set_rules('clientid', 'Customer', 'trim|required|numeric|greater_than[0]');
            $this->form_validation->set_rules('project_id', 'Project', 'trim|numeric|greater_than[0]');
            $this->form_validation->set_rules('currency', 'Currency', 'trim|required|numeric|greater_than[0]');
            $this->form_validation->set_rules('date', 'Credit Note Date', 'trim|required|max_length[255]');
            $this->form_validation->set_rules('items[]', 'Items', 'required');
            $this->form_validation->set_rules('subtotal', 'Sub Total', 'trim|required|decimal|greater_than[0]');
            $this->form_validation->set_rules('total', 'Total', 'trim|required|decimal|greater_than[0]');
            $this->form_validation->set_rules('number', 'Credit Note Number', 'trim|required|numeric|callback_validate_creditnotes_number[' . $id . ']');
            if ($this->form_validation->run() == FALSE) {
                $message = array('status' => FALSE, 'error' => $this->form_validation->error_array(), 'message' => validation_errors());
                $this->response($message, REST_Controller::HTTP_CONFLICT);
            } else {
                $this->load->model('credit_notes_model');
                $is_exist = $this->credit_notes_model->get($id);
                if (!is_object($is_exist)) {
                    $message = array('status' => FALSE, 'message' => 'Credit Note ID Doesn\'t Not Exist.');
                    $this->response($message, REST_Controller::HTTP_CONFLICT);
                }
                if (is_object($is_exist)) {
                    $data = $this->input->post();
                    $data['isedit'] = "";
                    $success = $this->credit_notes_model->update($data, $id);
                    if ($success == true) {
                        $message = array('status' => TRUE, 'message' => "Credit Note Updated Successfully",);
                        $this->response($message, REST_Controller::HTTP_OK);
                    } else {
                        // error
                        $message = array('status' => FALSE, 'message' => 'Credit Note Update Fail');
                        $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                    }
                } else {
                    $message = array('status' => FALSE, 'message' => 'Invalid Credit Note ID');
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        }
    }
}
