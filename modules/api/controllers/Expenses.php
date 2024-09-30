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
class Expenses extends REST_Controller {
    function __construct() {
        // Construct the parent class
        parent::__construct();
    }

    /**
     * @api {get} api/expenses/:id Request Expense information
     * @apiVersion 0.3.0
     * @apiName GetExpense
     * @apiGroup Expenses
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiParam {Number} id Expense unique ID.
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiSuccess {Array} Expense Expense information.
     * @apiSuccessExample Success-Response:
     *   [
     *       {
     *           "id": "1",
     *           "category": "1",
     *           "currency": "1",
     *           "amount": "50.00",
     *           "tax": "0",
     *           "tax2": "0",
     *           "reference_no": "012457893",
     *           "note": "AWS server hosting charges",
     *           "expense_name": "Cloud Hosting",
     *           "clientid": "1",
     *           "project_id": "0",
     *           "billable": "0",
     *           "invoiceid": null,
     *           "paymentmode": "2",
     *           "date": "2021-09-01",
     *           "recurring_type": "month",
     *           "repeat_every": "1",
     *           "recurring": "1",
     *           "cycles": "12",
     *           "total_cycles": "0",
     *           "custom_recurring": "0",
     *           "last_recurring_date": null,
     *           "create_invoice_billable": "0",
     *           "send_invoice_to_customer": "0",
     *           "recurring_from": null,
     *           "dateadded": "2021-09-01 12:26:34",
     *           "addedfrom": "1",
     *           "is_expense_created_in_xero": "0",
     *           "userid": "1",
     *           "company": "Company A",
     *           "vat": "",
     *           "phonenumber": "",
     *           "country": "0",
     *           "city": "",
     *           "zip": "",
     *           "state": "",
     *           "address": "",
     *           "website": "",
     *           "datecreated": "2020-05-25 22:55:49",
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
     *           "default_language": "",
     *           "default_currency": "0",
     *           "show_primary_contact": "0",
     *           "stripe_id": null,
     *           "registration_confirmed": "1",
     *           "name": "Hosting Management",
     *           "description": "server space and other settings",
     *           "show_on_pdf": "0",
     *           "invoices_only": "0",
     *           "expenses_only": "0",
     *           "selected_by_default": "0",
     *           "taxrate": null,
     *           "category_name": "Hosting Management",
     *           "payment_mode_name": "Paypal",
     *           "tax_name": null,
     *           "tax_name2": null,
     *           "taxrate2": null,
     *           "expenseid": "1",
     *           "customfields": []
     *       }
     *   ]
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
        $data = $this->Api_model->get_table('expenses', $id);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "expenses", $id);
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    /**
     * @api {get} api/expenses/search/:keysearch Search Expenses information
     * @apiVersion 0.3.0
     * @apiName GetExpenseSearch
     * @apiGroup Expenses
     *
     * @apiHeader {String} Authorization Basic Access Authentication token
     *
     * @apiParam {String} keysearch Search Keywords
     *
     * @apiSuccess {Array} Expenses Expenses Information
     *
     * @apiSuccessExample Success-Response:
     *   [
     *       {
     *           "id": "1",
     *           "category": "1",
     *           "currency": "1",
     *           "amount": "50.00",
     *           "tax": "0",
     *           "tax2": "0",
     *           "reference_no": "012457893",
     *           "note": "AWS server hosting charges",
     *           "expense_name": "Cloud Hosting",
     *           "clientid": "1",
     *           "project_id": "0",
     *           "billable": "0",
     *           "invoiceid": null,
     *           "paymentmode": "2",
     *           "date": "2021-09-01",
     *           "recurring_type": "month",
     *           "repeat_every": "1",
     *           "recurring": "1",
     *           "cycles": "12",
     *           "total_cycles": "0",
     *           "custom_recurring": "0",
     *           "last_recurring_date": null,
     *           "create_invoice_billable": "0",
     *           "send_invoice_to_customer": "0",
     *           "recurring_from": null,
     *           "dateadded": "2021-09-01 12:26:34",
     *           "addedfrom": "1",
     *           "is_expense_created_in_xero": "0",
     *           "userid": "1",
     *           "company": "Company A",
     *           "vat": "",
     *           "phonenumber": "",
     *           "country": "0",
     *           "city": "",
     *           "zip": "",
     *           "state": "",
     *           "address": "",
     *           "website": "",
     *           "datecreated": "2020-05-25 22:55:49",
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
     *           "default_language": "",
     *           "default_currency": "0",
     *           "show_primary_contact": "0",
     *           "stripe_id": null,
     *           "registration_confirmed": "1",
     *           "name": "Hosting Management",
     *           "description": "server space and other settings",
     *           "show_on_pdf": "0",
     *           "invoices_only": "0",
     *           "expenses_only": "0",
     *           "selected_by_default": "0",
     *           "taxrate": null,
     *           "category_name": "Hosting Management",
     *           "payment_mode_name": "Paypal",
     *           "tax_name": null,
     *           "tax_name2": null,
     *           "taxrate2": null,
     *           "expenseid": "1",
     *           "customfields": []
     *       }
     *   ]
     *
     * @apiError {Boolean} status Request status
     * @apiError {String} message No data were found
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data were found"
     *     }
     */
    public function data_search_get($key = '') {
        $data = $this->Api_model->search('expenses', $key);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "expenses");
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    /**
     * @api {delete} api/expenses/:id Delete Expense
     * @apiVersion 0.3.0
     * @apiName DeleteExpenses
     * @apiGroup Expenses
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Expense Deleted Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Expense Deleted Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Expense Delete Fail
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Expense Delete Fail"
     *     }
     */
    public function data_delete($id = '') {
        $id = $this->security->xss_clean($id);
        if (empty($id) && !is_numeric($id)) {
            $message = array('status' => FALSE, 'message' => 'Invalid Expense ID');
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->load->model('expenses_model');
            $is_exist = $this->expenses_model->get($id);
            if (is_object($is_exist)) {
                $output = $this->expenses_model->delete($id);
                if ($output === TRUE) {
                    // success
                    $message = array('status' => TRUE, 'message' => 'Expense Deleted Successfully');
                    $this->response($message, REST_Controller::HTTP_OK);
                } else {
                    // error
                    $message = array('status' => FALSE, 'message' => 'Expense Delete Fail');
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $message = array('status' => FALSE, 'message' => 'Invalid Expense ID');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * @api {post} api/expenses Add Expense
     * @apiVersion 0.3.0
     * @apiName AddExpense
     * @apiGroup Expenses
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {String}  [expense_name]               Optional. Expanse Name
     * @apiParam {String}  [note]                       Optional. Expanse Note
     * @apiParam {Number}  category                     Mandatory. Expense Category
     * @apiParam {Decimal} amount                       Mandatory. Expense Amount
     * @apiParam {Date}    date                         Mandatory. Expense Date
     * @apiParam {Number}  clientid                     Optional. Customer id
     * @apiParam {Number}  currency                     Mandatory. Currency Field
     * @apiParam {Number}  tax                          Optional. Tax 1
     * @apiParam {Number}  tax2                         Optional. Tax 2
     * @apiParam {Number}  paymentmode                  Optional. Payment mode
     * @apiParam {String}  [reference_no]               Optional. Reference #
     * @apiParam {String}  [recurring]                  Optional. recurring 1 to 12 or custom
     * @apiParam {Number}  [repeat_every_custom]        Optional. if recurring is custom set number gap
     * @apiParam {String}  [repeat_type_custom]         Optional. if recurring is custom set gap option day/week/month/year
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *       "expense_name": "Test51",
     *       "note": "Expanse note",
     *       "category": 300,
     *       "date": "2021-08-20",
     *       "amount": "1200.00",
     *       "billable": 1,
     *       "clientid": 1,
     *       "currency": 1,
     *       "tax": 1,
     *       "tax2": 1,
     *       "paymentmode": 2,
     *       "reference_no": 5874,
     *       "repeat_every": "6-month",
     *       "cycles": 5,
     *       "create_invoice_billable": 0,
     *       "send_invoice_to_customer": 1,
     *       "custom_fields":
     *       {
     *           "expenses":
     *           {
     *               "94": "test 1254"
     *           }
     *       }
     *   }
     *
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Expense Added Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Expense Added Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Expense Update Fail
     * @apiError {String} category The Expense Category is not found.
     * @apiError {String} date The Expense date field is required.
     * @apiError {String} amount The Amount field is required.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Expense Add Fail"
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 409 Conflict
     *     {
     *       "status": false,
     *       "error": {
     *          "category":"The Expense Category is not found"
     *      },
     *      "message": "The Expense Category is not found"
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 409 Conflict
     *     {
     *       "status": false,
     *       "error": {
     *          "date":"The Expense date field is required."
     *      },
     *      "message": "The Expense date field is required."
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 409 Conflict
     *     {
     *       "status": false,
     *       "error": {
     *          "amount":"The Amount field is required."
     *      },
     *      "message": "The Amount field is required."
     *     }
     *
     */
    public function data_post() {
        $data = $this->input->post();
        $this->form_validation->set_rules('category', 'Expense Category', 'trim|required|max_length[255]|callback_validate_category');
        $this->form_validation->set_rules('date', 'Expense date', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('category', 'Expense Category', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('date', 'Invoice date', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('currency', 'Currency', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|decimal|greater_than[0]');
        $data['note'] = $data['note'] ?? "";
        if ($this->form_validation->run() == FALSE) {
            $message = array('status' => FALSE, 'error' => $this->form_validation->error_array(), 'message' => validation_errors());
            $this->response($message, REST_Controller::HTTP_CONFLICT);
        } else {
            $this->load->model('expenses_model');
            $id = $this->expenses_model->add($data);
            if ($id > 0 && !empty($id)) {
                // success
                $this->handle_expense_attachments_array($id);
                $message = array('status' => TRUE, 'message' => 'Expense added successfully.');
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                // error
                $message = array('status' => FALSE, 'message' => 'Expense add fail.');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * @api {put} api/expenses Update a Expense
     * @apiVersion 0.3.0
     * @apiName PutExpense
     * @apiGroup Expenses
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {String}  [expense_name]                 Optional. Name
     * @apiParam {String}  [note]                          Optional. Note
     * @apiParam {Number}  category                       Mandatory. Expense Category
     * @apiParam {Decimal} amount                       Mandatory. Expense Amount
     * @apiParam {Date}    date                           Mandatory. Expense Date
     * @apiParam {Number}  clientid                       Optional. Customer id
     * @apiParam {Number}  currency                       Mandatory. currency field
     * @apiParam {Number}  tax                              Optional. Tax 1
     * @apiParam {Number}  tax2                             Optional. Tax 2
     * @apiParam {Number}  paymentmode                    Optional. Payment mode
     * @apiParam {String}  [reference_no]                   Optional. Reference #
     * @apiParam {String}  [recurring]                    Optional. recurring 1 to 12 or custom
     * @apiParam {Number}  [repeat_every_custom]          Optional. if recurring is custom set number gap
     * @apiParam {String}  [repeat_type_custom]           Optional. if recurring is custom set gap option day/week/month/year
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *       "expense_name": "Test51",
     *       "note": "exp note",
     *       "category": 300,
     *       "date": "2021-08-20",
     *       "amount": "1200.00",
     *       "billable": 1,
     *       "clientid": 1,
     *       "currency": 1,
     *       "tax": 1,
     *       "tax2": 1,
     *       "paymentmode": 2,
     *       "reference_no": 5874,
     *       "repeat_every": "6-month",
     *       "cycles": 5,
     *       "create_invoice_billable": 0,
     *       "send_invoice_to_customer": 1,
     *       "custom_fields":
     *       {
     *           "expenses":
     *           {
     *               "94": "test 1254"
     *           }
     *       }
     *   }
     *
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Expense Updated Successfully
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Expense Updated Successfully"
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Expense Update Fail
     * @apiError {String} category The Expense Category is not found.
     * @apiError {String} date The Expense date field is required.
     * @apiError {String} amount The Amount field is required.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Expense Update Fail"
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 409 Conflict
     *     {
     *       "status": false,
     *       "error": {
     *          "category":"The Expense Category is not found"
     *      },
     *      "message": "The Expense Category is not found"
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 409 Conflict
     *     {
     *       "status": false,
     *       "error": {
     *          "date":"The Expense date field is required."
     *      },
     *      "message": "The Expense date field is required."
     *     }
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 409 Conflict
     *     {
     *       "status": false,
     *       "error": {
     *          "amount":"The Amount field is required."
     *      },
     *      "message": "The Amount field is required."
     *     }
     *
     */
    public function data_put($id = "") {
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
            $this->load->model('expenses_model');
            $is_exist = $this->expenses_model->get($id);
            if (!is_object($is_exist)) {
                $message = array('status' => FALSE, 'message' => 'Expense ID Doesn\'t Not Exist.');
                $this->response($message, REST_Controller::HTTP_CONFLICT);
            }
            if (is_object($is_exist)) {
                $update_data = $this->input->post();
                $update_file = isset($update_data['file']) ? $update_data['file'] : null;
                unset($update_data['file']);
                
                $output = $this->expenses_model->update($update_data, $id);
                if (!empty($update_file) && count($update_file)) {
                    if ($output <= 0 || empty($output)) {
                        $output = $id;
                    }
                }

                if ($output > 0 && !empty($output)) {
                    $this->expenses_model->delete_expense_attachment($output);
                    $this->handle_expense_attachments_array($output);
                    $message = array('status' => TRUE, 'message' => "Expense Updated Successfully",);
                    $this->response($message, REST_Controller::HTTP_OK);
                } else {
                    // error
                    $message = array('status' => FALSE, 'message' => 'Expense Update Fail');
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $message = array('status' => FALSE, 'message' => 'Invalid Expense ID');
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }
    
    public function validate_category($value) {
        $this->form_validation->set_message('validate_category', 'The {field} is not found.');
        $this->load->model('expenses_model');
        $is_exist = $this->expenses_model->get_category($value);
        if ($is_exist) {
            return TRUE;
        }
        return FALSE;
    }

    function handle_expense_attachments_array($expense_id, $index_name = 'file') {
        $path = get_upload_path_by_type('expense') . $expense_id . '/';
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
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $CI = & get_instance();
                        $CI->load->model('expenses_model');
                        $data = [];
                        $data[] = ['file_name' => $filename, 'filetype' => $_FILES[$index_name]['type'][$i], ];
                        $this->add_attachment_to_database($expense_id, $data, false);
                    }
                }
            }
        }
        return true;
    }

    function add_attachment_to_database($expense_id, $attachment, $external = false, $form_activity = false) {
        $this->misc_model->add_attachment_to_database($expense_id, 'expense', $attachment, $external);

        // No notification when attachment is imported from web to lead form
        if ($form_activity == false) {
            $this->load->model('expenses_model');
            $expense         = $this->expenses_model->get($expense_id);
            $not_user_ids = [];
            if ($expense->addedfrom != get_staff_user_id()) {
                array_push($not_user_ids, $expense->addedfrom);
            }
            $notifiedUsers = [];
            foreach ($not_user_ids as $uid) {
                $notified = add_notification([
                    'description'     => 'not_expense_added_attachment',
                    'touserid'        => $uid,
                    'link'            => '#expenseid=' . $expense_id,
                    'additional_data' => serialize([
                        $expense->expense_name,
                    ]),
                ]);
                if ($notified) {
                    array_push($notifiedUsers, $uid);
                }
            }
            pusher_trigger_notification($notifiedUsers);
        }
    }
}
/* End of file Expenses.php */
