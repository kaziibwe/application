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
 */
class Payments extends REST_Controller {
    function __construct() {
        // Construct the parent class
        parent::__construct();
        $this->load->model('payments_model');
        $this->load->model('Api_model');
    }

    /**
     * @api {get} api/payments/:id List all Payments
     * @apiVersion 0.3.0
     * @apiName GetPayment
     * @apiGroup Payments
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {Number} payment_id Optional payment unique ID <br/><i>Note : if you don't pass Payment id then it will list all payments records</i>
     *
     * @apiSuccess {Array} Payments List all Payment Records.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   [
     *       {
     *           "id": "3",
     *           "invoiceid": "7",
     *           "amount": "1000.00",
     *           "paymentmode": "3",
     *           "paymentmethod": "",
     *           "date": "2020-06-08",
     *           "daterecorded": "2020-06-08 20:29:54",
     *           "note": "",
     *           "transactionid": "000355795931",
     *           "name": "UPI",
     *           "description": "",
     *           "show_on_pdf": "0",
     *           "invoices_only": "0",
     *           "expenses_only": "0",
     *           "selected_by_default": "0",
     *           "active": "1",
     *           "paymentid": "1"
     *       },
     *       {
     *           "id": "4",
     *           "invoiceid": "12",
     *           "amount": "-3.00",
     *           "paymentmode": "4",
     *           "paymentmethod": "",
     *           "date": "2020-07-04",
     *           "daterecorded": "2020-07-04 15:32:59",
     *           "note": "",
     *           "transactionid": "P228210122733439",
     *           "name": "Stripe",
     *           "description": "",
     *           "show_on_pdf": "0",
     *           "invoices_only": "0",
     *           "expenses_only": "0",
     *           "selected_by_default": "0",
     *           "active": "1",
     *           "paymentid": "2"
     *       },
     *       {
     *           "id": "1",
     *           "invoiceid": "14",
     *           "amount": "8.00",
     *           "paymentmode": "1",
     *           "paymentmethod": "",
     *           "date": "2020-07-04",
     *           "daterecorded": "2020-07-04 15:47:30",
     *           "note": "",
     *           "transactionid": "000360166374",
     *           "name": "Bank",
     *           "description": null,
     *           "show_on_pdf": "0",
     *           "invoices_only": "0",
     *           "expenses_only": "0",
     *           "selected_by_default": "1",
     *           "active": "1",
     *           "paymentid": "3"
     *       },
     *       {
     *           "id": "2",
     *           "invoiceid": "13",
     *           "amount": "3.00",
     *           "paymentmode": "2",
     *           "paymentmethod": "Credit card",
     *           "date": "2020-07-04",
     *           "daterecorded": "2020-07-04 15:49:56",
     *           "note": "",
     *           "transactionid": "0124875873",
     *           "name": "paypal",
     *           "description": "",
     *           "show_on_pdf": "0",
     *           "invoices_only": "0",
     *           "expenses_only": "0",
     *           "selected_by_default": "0",
     *           "active": "1",
     *           "paymentid": "4"
     *       }
     *   ]
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
    public function data_get($id = '') {
        // If the id parameter doesn't exist return all the
        $data = $this->Api_model->payment_get($id);
        // Check if the data store contains
        if ($data) {
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
    }

    /**
     * @api {get} api/payments/search/:keysearch Search Payments Information
     * @apiVersion 0.3.0
     * @apiName GetPaymentSearch
     * @apiGroup Payments
     *
     * @apiHeader {String} Authorization Basic Access Authentication token
     *
     * @apiParam {String} keysearch Search Keywords
     *
     * @apiSuccess {Array} Payments Payments information
     *
     * @apiSuccessExample Success-Response:
     *   HTTP/1.1 200 OK
     *   [
     *       {
     *           "id": "3",
     *           "invoiceid": "14",
     *           "amount": "8.00",
     *           "paymentmode": "2",
     *           "paymentmethod": "",
     *           "date": "2020-07-04",
     *           "daterecorded": "2020-07-04 15:47:30",
     *           "note": "",
     *           "transactionid": "",
     *           ...
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
        // If the key parameter doesn't exist return all the
        $data = $this->Api_model->search('payments', $key);
        // Check if the data store contains
        if ($data) {
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
    }
}
