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
class Items extends REST_Controller {
    function __construct() {
        // Construct the parent class
        parent::__construct();
    }

    /**
     * @api {get} api/items/items/:id Request items information
     * @apiVersion 0.1.0
     * @apiName GetItem
     * @apiGroup Items
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiSuccess {Object} Item item information.
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *	  "itemid": "1",
     *        "rate": "100.00",
     *        "taxrate": "5.00",
     *        "taxid": "1",
     *        "taxname": "PAYPAL",
     *        "taxrate_2": "9.00",
     *        "taxid_2": "2",
     *        "taxname_2": "CGST",
     *        "description": "JBL Soundbar",
     *        "long_description": "The JBL Cinema SB110 is a hassle-free soundbar",
     *        "group_id": "0",
     *        "group_name": null,
     *        "unit": ""
     *     }
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
        $data = $this->Api_model->get_table('invoice_items', $id);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "items", $id);
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
    }

    /**
     * @api {get} api/items/search/:keysearch Search invoice item information
     * @apiVersion 0.1.0
     * @apiName GetItemSearch
     * @apiGroup Items
     *
     * @apiHeader {String} Authorization Basic Access Authentication token
     *
     * @apiParam {String} keysearch Search Keywords
     *
     * @apiSuccess {Object} Item  Item Information
     *
     * @apiSuccessExample Success-Response:
     *	HTTP/1.1 200 OK
     *	{
     *	  "rate": "100.00",
     *	  "id": "1",
     *	  "name": "(100.00) JBL Soundbar",
     *	  "subtext": "The JBL Cinema SB110 is a hassle-free soundbar..."
     *	}
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
        $data = $this->Api_model->search('invoice_items', $key);
        // Check if the data store contains
        if ($data) {
            $data = $this->Api_model->get_api_custom_data($data, "items");
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            
        } else {
            // Set the response and exit
            $this->response(['status' => FALSE, 'message' => 'No data were found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            
        }
    }
}