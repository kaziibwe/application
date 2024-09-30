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
class Contracts extends REST_Controller {
    function __construct() {
        // Construct the parent class
        parent::__construct();
    }

    /**
     * @api {get} api/contracts/:id Request Contract information
     * @apiVersion 0.3.0
     * @apiName GetContract
     * @apiGroup Contracts
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiParam {Number} id Contact unique ID
     *
     * @apiSuccess {Object} Contracts information.
     *
     * @apiSuccessExample Success-Response:
     *   HTTP/1.1 200 OK
     *	    {
     *	    "id": "1",
     *	    "content": "",
     *	    "description": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
     *	    "subject": "New Contract",
     *	    "client": "9",
     *	    "datestart": "2022-11-21",
     *	    "dateend": "2027-11-21",
     *	    "contract_type": "1",
     *	    "project_id": "0",
     *	    "addedfrom": "1",
     *	    "dateadded": "2022-11-21 12:45:58",
     *	    "isexpirynotified": "0",
     *	    "contract_value": "13456.00",
     *	    "trash": "0",
     *	    "not_visible_to_client": "0",
     *	    "hash": "31caaa36b9ea1f45a688c7e859d3ae70",
     *	    "signed": "0",
     *	    "signature": null,
     *	    "marked_as_signed": "0",
     *	    "acceptance_firstname": null,
     *	    "acceptance_lastname": null,
     *	    "acceptance_email": null,
     *	    "acceptance_date": null,
     *	    "acceptance_ip": null,
     *	    "short_link": null,
     *	    "name": "Development Contracts",
     *	    "userid": "9",
     *	    "company": "8web",
     *	    "vat": "",
     *	    "phonenumber": "",
     *	    "country": "0",
     *	    "city": "",
     *	    "zip": "",
     *	    "state": "",
     *	    "address": "",
     *	    "website": "",
     *	    "datecreated": "2022-08-11 14:07:26",
     *	    "active": "1",
     *	    "leadid": null,
     *	    "billing_street": "",
     *	    "billing_city": "",
     *	    "billing_state": "",
     *	    "billing_zip": "",
     *	    "billing_country": "0",
     *	    "shipping_street": "",
     *	    "shipping_city": "",
     *	    "shipping_state": "",
     *	    "shipping_zip": "",
     *	    "shipping_country": "0",
     *	    "longitude": null,
     *	    "latitude": null,
     *	    "default_language": "",
     *	    "default_currency": "0",
     *	    "show_primary_contact": "0",
     *	    "stripe_id": null,
     *	    "registration_confirmed": "1",
     *	    "type_name": "Development Contracts",
     *	    "attachments": [],
     *	    "customfields": [],
     *	    }
     */
    public function data_get($id = '') {
        // If the id parameter doesn't exist return all the
        $data = $this->Api_model->get_table('contracts', $id);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "contract", $id);
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    /**
     * @api {delete} api/contracts/:id Delete Contract
     * @apiVersion 0.3.0
     * @apiName DeleteContract
     * @apiGroup Contracts
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Contract Deleted Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Contract Deleted Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Contract Delete Fail
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Contract Delete Fail"
     *     }
     */
    public function data_delete($id = '') {
        $id = $this->security->xss_clean($id);
        if (empty($id) && !is_numeric($id)) {
            $message = array('status' => FALSE, 'message' => 'Invalid Contract ID');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->load->model('contracts_model');
            $is_exist = $this->contracts_model->get($id);
            if (is_object($is_exist)) {
                $output = $this->contracts_model->delete($id);
                if ($output === TRUE) {
                    // success
                    $message = array('status' => TRUE, 'message' => 'Contract Deleted Successfully');
                    $this->response($message, REST_Controller::HTTP_OK);
                } else {
                    // error
                    $message = array('status' => FALSE, 'message' => 'Contract Delete Fail');
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $message = array('status' => FALSE, 'message' => 'Invalid Contract ID');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * @api {post} api/contracts Add New Contract
     * @apiVersion 0.3.0
     * @apiName PostContract
     * @apiGroup Contracts
     *
     *  @apiHeader {String} Authorization Basic Access Authentication token.
     *
     *  @apiParam {String} subject                             Mandatory. Contract subject
     *	@apiParam {Date} datestart                             Mandatory. Contract start date
     *	@apiParam {Number} client                              Mandatory. Customer ID
     *	@apiParam {Date} dateend                               Optional.  Contract end date
     *	@apiParam {Number} contract_type                       Optional.  Contract type
     *  @apiParam {Number} contract_value             	 	   Optional.  Contract value
     *  @apiParam {String} description               	       Optional.  Contract description
     *  @apiParam {String} content              	 	       Optional.  Contract content
     *
     *  @apiParamExample {Multipart Form} Request-Example:
     *   [
     *		"subject"=>"Subject of the Contract,
     *		"datestart"=>"2022-11-11",
     *		"client"=>1,
     *		"dateend"=>"2023-11-11",
     *		"contract_type"=>1,
     *		"contract_value"=>12345,
     *		"description"=>"Lorem Ipsum is simply dummy text of the printing and typesetting industry",
     *		"content"=>"It has been the industry's standard dummy text ever since the 1500s"
     *	]
     *
     *
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Contracts Added Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Contract Added Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Contract add fail
     * @apiError {String} message The Start date field is required
     * @apiError {String} message The Subject field is required
     * @apiError {String} message The Customer ID field is required
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Contract ID Exists"
     *     }
     *
     * @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *    {
     *	    "status": false,
     *	    "error": {
     *	        "newitems[]": "The Start date field is required"
     *	    },
     *	    "message": "<p>The Start date field is required</p>\n"
     *	}
     *
     * @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *    {
     *	    "status": false,
     *	    "error": {
     *	        "subtotal": "The Subject field is required"
     *	    },
     *	    "message": "<p>The Subject field is required</p>\n"
     *	}
     *
     *  @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *    {
     *	    "status": false,
     *	    "error": {
     *	        "total": "The Customer ID is required"
     *	    },
     *	    "message": "<p>The Customer ID is required</p>\n"
     *	}
     *
     */
    public function data_post() {
        $data = $this->input->post();
        $this->form_validation->set_rules('id', 'Contract ID', 'trim|numeric|greater_than[0]');
        $this->form_validation->set_rules('content', 'Content', 'trim');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required');
        $this->form_validation->set_rules('client', 'Customer ID', 'trim|required|numeric|greater_than[0]');
        $this->form_validation->set_rules('contract_value', 'Contract Value', 'numeric');
        $this->form_validation->set_rules('datestart', 'Start date', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('dateend', 'End date', 'trim|max_length[255]');
        $this->form_validation->set_rules('contract_type', 'Contract type', 'trim|numeric|greater_than[0]');
        if ($this->form_validation->run() == FALSE) {
            $message = array('status' => FALSE, 'error' => $this->form_validation->error_array(), 'message' => validation_errors());
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->load->model('contracts_model');
            $output = $this->contracts_model->add($data);
            if ($output > 0 && !empty($output)) {
                // success
                $this->handle_contract_attachments_array($output);
                $message = array('status' => TRUE, 'message' => 'Contract Added Successfully');
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                // error
                $message = array('status' => FALSE, 'message' => 'Contract Add Fail');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * @api {post} api/contracts Add New Contract
     * @apiVersion 0.3.0
     * @apiName PostContract
     * @apiGroup Contracts
     *
     *  @apiHeader {String} Authorization Basic Access Authentication token.
     *
     *  @apiParam {String} subject                             Mandatory. Contract subject
     *	@apiParam {Date} datestart                             Mandatory. Contract start date
     *	@apiParam {Number} client                              Mandatory. Customer ID
     *	@apiParam {Date} dateend                               Optional.  Contract end date
     *	@apiParam {Number} contract_type                       Optional.  Contract type
     *  @apiParam {Number} contract_value             	 	   Optional.  Contract value
     *  @apiParam {String} description               	       Optional.  Contract description
     *  @apiParam {String} content              	 	       Optional.  Contract content
     *
     *  @apiParamExample {Multipart Form} Request-Example:
     *   [
     *		"subject"=>"Subject of the Contract,
     *		"datestart"=>"2022-11-11",
     *		"client"=>1,
     *		"dateend"=>"2023-11-11",
     *		"contract_type"=>1,
     *		"contract_value"=>12345,
     *		"description"=>"Lorem Ipsum is simply dummy text of the printing and typesetting industry",
     *		"content"=>"It has been the industry's standard dummy text ever since the 1500s"
     *	]
     *
     *
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Contracts Added Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Contract Added Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Contract add fail
     * @apiError {String} message The Start date field is required
     * @apiError {String} message The Subject field is required
     * @apiError {String} message The Customer ID field is required
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Contract ID Exists"
     *     }
     *
     * @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *    {
     *	    "status": false,
     *	    "error": {
     *	        "newitems[]": "The Start date field is required"
     *	    },
     *	    "message": "<p>The Start date field is required</p>\n"
     *	}
     *
     * @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *    {
     *	    "status": false,
     *	    "error": {
     *	        "subtotal": "The Subject field is required"
     *	    },
     *	    "message": "<p>The Subject field is required</p>\n"
     *	}
     *
     *  @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *    {
     *	    "status": false,
     *	    "error": {
     *	        "total": "The Customer ID is required"
     *	    },
     *	    "message": "<p>The Customer ID is required</p>\n"
     *	}
     *
     */
    public function data_put($id = '') {
        $_POST = json_decode($this->security->xss_clean(file_get_contents("php://input")), true);
        if (empty($_POST) || !isset($_POST)) {
            $this->load->library('parse_input_stream');
            $_POST = $this->parse_input_stream->parse_parameters();
            $_FILES = $this->parse_input_stream->parse_files();
            if (empty($_POST) || !isset($_POST)) {
                $message = array('status' => FALSE, 'message' => 'Data Not Acceptable OR Not Provided');
                $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
            }
        }
        $this->form_validation->set_data($_POST);
        if (empty($id) && !is_numeric($id)) {
            $message = array('status' => FALSE, 'message' => 'Invalid Lead ID');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $update_data = $this->input->post();
            $update_file = isset($update_data['file']) ? $update_data['file'] : null;
            unset($update_data['file']);

            $this->load->model('contracts_model');
            $output = $this->contracts_model->update($update_data, $id);
            if (!empty($update_file) && count($update_file)) {
                if ($output <= 0 || empty($output)) {
                    $output = $id;
                }
            }

            if ($output > 0 && !empty($output)) {
                // success
                $attachments = $this->contracts_model->get_contract_attachments('', $output);
                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        $this->contracts_model->delete_contract_attachment($attachment['id']);
                    }
                }
                $this->handle_contract_attachments_array($output);
                $message = array('status' => TRUE, 'message' => 'Contract Update Successfully');
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                // error
                $message = array('status' => FALSE, 'message' => 'Contract Update Fail');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    public function validate_contract_number($number, $contractid) {
        $isedit = 'false';
        if (!empty($contractid)) {
            $isedit = 'true';
        }
        $this->form_validation->set_message('validate_contract_number', 'The {field} is already in use');
        $original_number = null;
        $date = $this->input->post('date');
        if (!empty($contractid)) {
            $data = $this->Api_model->get_table('contracts', $contractid);
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
        if (total_rows(db_prefix() . 'contracts', ['YEAR(date)' => date('Y', strtotime(to_sql_date($date))), 'number' => $number, ]) > 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function handle_contract_attachments_array($contract_id, $index_name = 'file') {
        $path = get_upload_path_by_type('contract') . $contract_id . '/';
        $CI = & get_instance();
        if (isset($_FILES[$index_name]['name']) && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
            if (!is_array($_FILES[$index_name]['name'])) {
                $_FILES[$index_name]['name'] = [$_FILES[$index_name]['name']];
                $_FILES[$index_name]['type'] = [$_FILES[$index_name]['type']];
                $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
                $_FILES[$index_name]['error'] = [$_FILES[$index_name]['error']];
                $_FILES[$index_name]['size'] = [$_FILES[$index_name]['size']];
            }
            _file_attachments_index_fix($index_name);
            for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
                // Get the temp file path
                $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    if (_perfex_upload_error($_FILES[$index_name]['error'][$i]) || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
                        continue;
                    }
                    _maybe_create_upload_path($path);
                    $filename = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                    $newFilePath = $path . $filename;
                    // Upload the file into the temp dir
                    if (copy($tmpFilePath, $newFilePath)) {
                        unlink($tmpFilePath);
                        $data = [];
                        $data[] = ['file_name' => $filename, 'filetype' => $_FILES[$index_name]['type'][$i], ];
                        $this->add_attachment_to_database($contract_id, $data, false);
                    }
                }
            }
        }
        return true;
    }

    function add_attachment_to_database($contract_id, $attachment, $external = false) {
        $this->load->model('contracts_model');
        $this->load->model('misc_model');
        $this->misc_model->add_attachment_to_database($contract_id, 'contract', $attachment, $external);

        $contract         = $this->contracts_model->get($contract_id);
        $not_user_ids = [];
        if ($contract->addedfrom != get_staff_user_id()) {
            array_push($not_user_ids, $contract->addedfrom);
        }
        $notifiedUsers = [];
        foreach ($not_user_ids as $uid) {
            $notified = add_notification([
                'description'     => 'not_contract_added_attachment',
                'touserid'        => $uid,
                'link'            => '#contractid=' . $contract_id,
                'additional_data' => serialize([
                    $contract->subject,
                ]),
            ]);
            if ($notified) {
                array_push($notifiedUsers, $uid);
            }
        }
        pusher_trigger_notification($notifiedUsers);
    }
}
