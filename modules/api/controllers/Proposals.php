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
class Proposals extends REST_Controller {
    function __construct() {
        // Construct the parent class
        parent::__construct();
    }

    /**
     * @api {get} api/proposals Request Proposal information
     * @apiVersion 0.3.0
     * @apiName GetProposal
     * @apiGroup Proposals
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiParam {Number} id Proposal unique ID
     *
     * @apiSuccess {Object} Proposal information.
     *
     * @apiSuccessExample Success-Response:
     *   HTTP/1.1 200 OK
     *   {
     *	    "id": "1",
     *	    "subject": "Test Proposal",
     *	    "content": "{proposal_items}",
     *	    "addedfrom": "1",
     *	    "datecreated": "2021-08-01 13:38:08",
     *	    "total": "10.00",
     *	    "subtotal": "10.00",
     *	    "total_tax": "0.00",
     *	    "adjustment": "0.00",
     *	    "discount_percent": "0.00",
     *	    "discount_total": "0.00",
     *	    "discount_type": "",
     *	    "show_quantity_as": "1",
     *	    "currency": "1",
     *	    "open_till": "2021-08-08",
     *	    "date": "2021-08-01",
     *	    "rel_id": "1",
     *	    "rel_type": "customer",
     *	    "assigned": "0",
     *	    "hash": "9fc38e5ad2f8256b1b8430ee41069f75",
     *	    "proposal_to": "test",
     *	    "country": "102",
     *	    "zip": "30000202",
     *	    "state": "Test",
     *	    "city": "Test",
     *	    "address": "Test",
     *	    "email": "test@gmail.com",
     *	    "phone": "01324568903",
     *	    "allow_comments": "1",
     *	    "status": "6",
     *	    "estimate_id": null,
     *	    "invoice_id": null,
     *	    "date_converted": null,
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
     *	    "items": [
     *	        {
     *	            "id": "4",
     *	            "rel_id": "1",
     *	            "rel_type": "proposal",
     *	            "description": "item 1",
     *	            "long_description": "item 1 description",
     *	            "qty": "1.00",
     *	            "rate": "10.00",
     *	            "unit": "1",
     *	            "item_order": "1"
     *	        }
     *	    ],
     *	    "visible_attachments_to_customer_found": false,
     *	    "customfields": [
     *	        {
     *	            "label": "Custom Field",
     *	            "value": "Custom Field value"
     *	        }
     *	    ]
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
        $data = $this->Api_model->get_table('proposals', $id);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "proposal", $id);
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
    }

    /**
     * @api {get} api/proposals/search/:keysearch Search proposals information
     * @apiVersion 0.3.0
     * @apiName GetProposalSearch
     * @apiGroup Proposals
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {String} keysearch Search Keywords.
     *
     * @apiSuccess {Object} Proposals Information.
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *    {
     *  "id": "2",
     *  "subject": "Test 2",
     *  "content": "{proposal_items}",
     *  "addedfrom": "1",
     * "datecreated": "2021-08-01 13:43:49",
     *  "total": "10.00",
     *  "subtotal": "10.00",
     *  "total_tax": "0.00",
     *  "adjustment": "0.00",
     *  "discount_percent": "0.00",
     *  "discount_total": "0.00",
     *  "discount_type": "",
     *  "show_quantity_as": "1",
     *  "currency": "1",
     *  "open_till": "2021-08-08",
     *  "date": "2021-08-01",
     *  "rel_id": "1",
     *  "rel_type": "customer",
     *  "assigned": "0",
     *  "hash": "6fe6cd0bc66dff03663154660acc1a93",
     *  "proposal_to": "test",
     *  "country": "102",
     *  "zip": "300000",
     *  "state": "test",
     *  "city": "test",
     *  "address": "test",
     *  "email": "test@gmail.com",
     *  "phone": "01324568903",
     *  "allow_comments": "1",
     *  "status": "6",
     *  "estimate_id": null,
     *  "invoice_id": null,
     *  "date_converted": null,
     *  "pipeline_order": "0",
     *  "is_expiry_notified": "0",
     *  "acceptance_firstname": null,
     *  "acceptance_lastname": null,
     *  "acceptance_email": null,
     *  "acceptance_date": null,
     *  "acceptance_ip": null,
     *  "signature": null,
     *  "short_link": null,
     *  "symbol": "$",
     *  "name": "USD",
     *  "decimal_separator": ".",
     *  "thousand_separator": ",",
     *  "placement": "before",
     *  "isdefault": "1",
     *  "customfields": [
     *      {
     *          "label": "Custom Field",
     *	        "value": "Custom Field value"
     *      }
     *   ]
     * }
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
        $data = $this->Api_model->search('proposals', $key);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "proposal");
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
    }

    /**
     * @api {delete} api/proposals/:id Delete Proposal
     * @apiVersion 0.3.0
     * @apiName DeleteProposal
     * @apiGroup Proposals
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiParam {Number} id Proposal unique ID.
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Proposals Deleted Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Proposals Deleted Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Proposals Delete Fail
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Proposal Delete Fail"
     *     }
     */
    public function data_delete($id = '') {
        $id = $this->security->xss_clean($id);
        if (empty($id) && !is_numeric($id)) {
            $message = array('status' => FALSE, 'message' => 'Invalid Proposal ID');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->load->model('proposals_model');
            $is_exist = $this->proposals_model->get($id);
            if (is_object($is_exist)) {
                $output = $this->proposals_model->delete($id);
                if ($output === TRUE) {
                    // success
                    $message = array('status' => TRUE, 'message' => 'Proposal Deleted Successfully');
                    $this->response($message, REST_Controller::HTTP_OK);
                } else {
                    // error
                    $message = array('status' => FALSE, 'message' => 'Proposal Delete Fail');
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $message = array('status' => FALSE, 'message' => 'Invalid Proposal ID');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * @api {post} api/proposals Add New Proposals
     * @apiName PostProposals
     * @apiVersion 0.3.0
     * @apiGroup Proposals
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {String} subject                           	Mandatory. Proposal Subject Name.
     * @apiParam {string="lead","customer"} Related 			Mandatory. Proposal Related.
     * @apiParam {Number} rel_id                                Mandatory. Related ID.
     * @apiParam {string} proposal_to                           Mandatory. Lead / Customer name.
     * @apiParam {Date} date 		                            Mandatory. Proposal Start Date.
     * @apiParam {Date} open_till                               Optional. Proposal Open Till Date.
     * @apiParam {string} currency                              Mandatory. currency id.
     * @apiParam {string} discount_type                         Optional. Proposal Open Till Date.
     * @apiParam {string} status                                Optional. status id.
     * @apiParam {string} Assigned                              Optional. Assignee id.
     * @apiParam {string} Email                                 Mandatory. Email id.
     * @apiParam {Array} newitems                     			Mandatory. New Items to be added.
     *
     * @apiParamExample {Multipart Form} Request-Example:
     * [
     *	"subject" => proposal subject
     *	"rel_type" => customer
     *	"rel_id" => 1
     *	"proposal_to" => John Doe
     *	"email" => customer@mail.com
     *	"date" => 2021-08-19
     *	"newitems[0][description]" => item 1 description
     *	"newitems[0][long_description]" => item 1 long description
     *	"newitems[0][qty]" => 1
     *	"newitems[0][rate]" => 1200
     *	"newitems[0][order]" => 1
     *	"newitems[0][unit]" => 1
     *	"newitems[0][unit]" => 1
     * 	"newitems[0][custom_fields][items][1]" => custom field item
     *	"subtotal" => 1200.00
     *	"total" => 1200.00
     *	"currency" => 1
     *	"date" => 2021-08-19
     *	"status"  =>  6
     *	"custom_fields"[proposal][1]  =>  test
     *	....
     *]
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Proposal add successful.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Proposal add successful."
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Proposal add fail.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Proposal add fail."
     *     }
     *
     */
    public function data_post() {
		error_reporting(0);
        $data = $this->input->post();
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required|max_length[191]');
        $this->form_validation->set_rules('rel_type', 'Rel Type', 'trim|required|in_list[lead,customer]');
        $this->form_validation->set_rules('rel_id', 'Rel Id', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('proposal_to', 'Proposal to', 'trim|required|max_length[191]');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required|max_length[150]');
        $this->form_validation->set_rules('newitems[]', 'Items', 'required');
        $this->form_validation->set_rules('currency', 'Currency', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('date', 'date', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('subtotal', 'Sub Total', 'trim|required|decimal|greater_than[0]');
        $this->form_validation->set_rules('total', 'Total', 'trim|required|decimal|greater_than[0]');
        $data['address'] = $data['address']??"";
        if ($this->form_validation->run() == FALSE) {
            $message = array('status' => FALSE, 'error' => $this->form_validation->error_array(), 'message' => validation_errors());
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->load->model('proposals_model');
            $data['open_till'] = _d(date('Y-m-d', strtotime('+' . get_option('proposal_due_after') . ' DAY', strtotime(date('Y-m-d')))));
            $id = $this->proposals_model->add($data);
            if ($id > 0 && !empty($id)) {
                // success
                $message = array('status' => TRUE, 'message' => 'Proposal Added Successfully');
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                // error
                $message = array('status' => FALSE, 'message' => 'Proposal Add Fail');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * @api {put} api/proposal/:id Update a proposal
     * @apiVersion 0.3.0
     * @apiName PutProposal
     * @apiGroup Proposals
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {String} subject                       Mandatory. Proposal Subject Name.
     * @apiParam {string="lead","customer"} 			Mandatory. Proposal Related.
     * @apiParam {Number} rel_id                        Mandatory. Related ID.
     * @apiParam {string} proposal_to                   Mandatory. Lead / Customer name.
     * @apiParam {Date} date 		                    Mandatory. Proposal Start Date.
     * @apiParam {Date} open_till                       Optional. Proposal Open Till Date.
     * @apiParam {string} currency                      Mandatory. currency id.
     * @apiParam {string} discount_type                 Optional. Proposal Open Till Date.
     * @apiParam {string} status                        Optional. status id.
     * @apiParam {string} Assigned                      Optional. Assignee id.
     * @apiParam {string} Email                         Mandatory. Email id.
     * @apiParam {Array} newitems                     	Mandatory. New Items to be added.
     * @apiParam {Array} items                        	Optional. Existing items with Id
     * @apiParam {Array} removed_items				   	Optional. Items to be removed
     *
     *
     *  	@apiParamExample {json} Request-Example:
     *  	{
     *		"subject": "Test",
     *		"rel_type": "customer",
     *		"rel_id": 1,
     *		"proposal_to": "Trueline 1",
     *		"email": "test@mail.com",
     *		"date": "2021-08-19",
     *		"currency": 1,
     *		"status": 6,
     *		"items": {
     *		    "1": {
     *		        "itemid": "23",
     *		        "order": "1",
     *		        "description": "item description",
     *		        "long_description": "item long description",
     *		        "qty": "1",
     *		        "unit": "1",
     *		        "rate": "10.00",
     *		        "custom_fields":{
     *		            "items":{
     *		                "31":"test 12 item 1",
     *			            "32":"10",
     *			            "33":"Lorem Ipsum is simply dummy text of the printing and typesetting industry",*
     *			            "34":"Opti*on 1",*
     *			            "35":["Option 1","Option 2"],*
     *			            "36":["Option 1","Option 3"],
     *			            "37":"2021-05-06",
     *			            "38":"2021-05-06 00:23:25",
     *			            "39":"#ffffff",
     *			            "40":"<a href=\"url.com\" target=\"_blank\">Link</a>"
     *		            }
     *		        }
     *		    }
     *		},
     *		"newitems": {
     *		    "2": {
     *		        "order": "2",
     *		        "description": "updated item 2 description",
     *		        "long_description": "updated item 2 logn description",
     *		        "qty": "1",
     *		        "unit": "",
     *		        "rate": "100.00",
     *		        "custom_fields":{
     *		            "items":{
     *		                "31":"test 12 item 2",
     *			            "32":"10",
     *			            "33":"Lorem Ipsum is simply dummy text of the printing and typesetting industry",
     *			            "34":"Option 1",
     *			            "35":["Option 1","Option 2"],
     *			            "36":["Option 1","Option 3"],
     *			            "37":"2021-05-06",
     *			            "38":"2021-05-06 00:23:25",
     *			            "39":"#ffffff",
     *			            "40":"<a href=\"url.com\" target=\"_blank\">Link</a>"
     *		            }
     *		        }
     *		    }
     *		},
     *		"custom_fields":{
     *		    "proposal":{
     *		        "91":"test 12"
     *		    }
     *		},
     *		"subtotal":"110.00",
     *		"total":"110.00"
     *		}
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": false,
     *       "message": "Proposal Updated Successfully"
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Proposal Update Fail"
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
            $message = array('status' => FALSE, 'message' => 'Invalid Proposal ID');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->form_validation->set_rules('subject', 'Subject', 'trim|required|max_length[191]');
            $this->form_validation->set_rules('rel_type', 'Rel Type', 'trim|required|in_list[lead,customer]');
            $this->form_validation->set_rules('rel_id', 'Rel Id', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules('proposal_to', 'Proposal to', 'trim|required|max_length[191]');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required|max_length[150]');
            $this->form_validation->set_rules('items[]', 'Items', 'required');
            $this->form_validation->set_rules('currency', 'Currency', 'trim|required|max_length[255]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|numeric|greater_than[0]');
            $this->form_validation->set_rules('date', 'date', 'trim|required|max_length[255]');
            $this->form_validation->set_rules('subtotal', 'Sub Total', 'trim|required|decimal|greater_than[0]');
            $this->form_validation->set_rules('total', 'Total', 'trim|required|decimal|greater_than[0]');
            $_POST['address'] = $_POST['address']??"";
            if ($this->form_validation->run() == FALSE) {
                $message = array('status' => FALSE, 'error' => $this->form_validation->error_array(), 'message' => validation_errors());
                $this->response($message, REST_Controller::HTTP_CONFLICT);
            } else {
                $this->load->model('proposals_model');
                $is_exist = $this->proposals_model->get($id);
                if (!is_object($is_exist)) {
                    $message = array('status' => FALSE, 'message' => 'Proposal ID Doesn\'t Not Exist.');
                    $this->response($message, REST_Controller::HTTP_CONFLICT);
                }
                if (is_object($is_exist)) {
                    $data = $this->input->post();
                    $data['isedit'] = "";
                    $success = $this->proposals_model->update($data, $id);
                    if ($success == true) {
                        $message = array('status' => TRUE, 'message' => "Proposal Updated Successfully",);
                        $this->response($message, REST_Controller::HTTP_OK);
                    } else {
                        // error
                        $message = array('status' => FALSE, 'message' => 'Proposal Update Fail');
                        $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                    }
                } else {
                    $message = array('status' => FALSE, 'message' => 'Invalid Proposal ID');
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        }
    }
}
