<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';

use CustomerApi\RestController;

class Contracts extends RestController
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

        if (!has_contact_permission('contracts', $this->clientInfo['data']->contact_id)) {
            $this->response(['message' => _l('not_permission_to_perform_this_action')], 403);
        }
    }

    /**
     * @api {get} /mmb/v1/contracts List All Contracts
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetAllContracts
     *
     * @apiGroup Contracts
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest /mmb/v1/contracts
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Contracts information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "status": true,
     *          "data": [
     *              {
     *                  "id": "2",
     *                  "content": null,
     *                  "description": "desc",
     *                  "subject": "Customer to first contract",
     *                  "client": "2",
     *                  "datestart": "2023-05-10",
     *                  "dateend": null,
     *                  "contract_type": "1",
     *                  "project_id": "2",
     *                  "addedfrom": "1",
     *                  "dateadded": "2023-05-10 11:56:39",
     *                  "isexpirynotified": "0",
     *                  "contract_value": "0.00",
     *                  "trash": "0",
     *                  "not_visible_to_client": "0",
     *                  "hash": "cf42ccef24e496d571bae70bbd7456c9",
     *                  "signed": "0",
     *                  "signature": null,
     *                  "marked_as_signed": "0",
     *                  "acceptance_firstname": null,
     *                  "acceptance_lastname": null,
     *                  "acceptance_email": null,
     *                  "acceptance_date": null,
     *                  "acceptance_ip": null,
     *                  "short_link": null,
     *                  "name": "Type 1",
     *                  "userid": "2",
     *                  "company": "Customer 2",
     *                  "vat": "",
     *                  "phonenumber": "",
     *                  "country": "0",
     *                  "city": "",
     *                  "zip": "",
     *                  "state": "",
     *                  "address": "",
     *                  "website": "",
     *                  "datecreated": "2023-05-10 11:47:00",
     *                  "active": "1",
     *                  "leadid": null,
     *                  "billing_street": "",
     *                  "billing_city": "",
     *                  "billing_state": "",
     *                  "billing_zip": "",
     *                  "billing_country": "0",
     *                  "shipping_street": "",
     *                  "shipping_city": "",
     *                  "shipping_state": "",
     *                  "shipping_zip": "",
     *                  "shipping_country": "0",
     *                  "longitude": null,
     *                  "latitude": null,
     *                  "default_language": "",
     *                  "default_currency": "0",
     *                  "show_primary_contact": "0",
     *                  "stripe_id": null,
     *                  "registration_confirmed": "1",
     *                  "type_name": "Type 1",
     *                  "attachments": [],
     *                  "project_name": "Client api project",
     *                  "addedfrom_name": "admin 123"
     *              },
     *             ...
     *          ],
     *          "message": "Data retrieved successfully"
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
     * @api {get} /mmb/v1/contracts/id/:id Request Contract By ID
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetcontractById
     *
     * @apiGroup Contracts
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiParam {Number} id  <span class="btn btn-xs btn-danger">Required</span> Contract unique ID.
     *
     * @apiSampleRequest /mmb/v1/contracts/
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Object}  data    Contract information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "status": true,
     *           "data": {
     *               "id": "2",
     *               "content": "",
     *               "description": "desc",
     *               "subject": "Customer to first contract",
     *               "client": "2",
     *               "datestart": "2023-05-10",
     *               "dateend": null,
     *               "contract_type": "1",
     *               "project_id": "2",
     *               "addedfrom": "1",
     *               "dateadded": "2023-05-10 11:56:39",
     *               "isexpirynotified": "0",
     *               "contract_value": "0.00",
     *               "trash": "0",
     *               "not_visible_to_client": "0",
     *               "hash": "cf42ccef24e496d571bae70bbd7456c9",
     *               "signed": "0",
     *               "signature": null,
     *               "marked_as_signed": "0",
     *               "acceptance_firstname": null,
     *               "acceptance_lastname": null,
     *               "acceptance_email": null,
     *               "acceptance_date": null,
     *               "acceptance_ip": null,
     *               "short_link": null,
     *               "name": "Type 1",
     *               "userid": "2",
     *               "company": "Customer 2",
     *               "vat": "",
     *               "phonenumber": "",
     *               "country": "0",
     *               "city": "",
     *               "zip": "",
     *               "state": "",
     *               "address": "",
     *               "website": "",
     *               "datecreated": "2023-05-10 11:47:00",
     *               "active": "1",
     *               "leadid": null,
     *               "billing_street": "",
     *               "billing_city": "",
     *               "billing_state": "",
     *               "billing_zip": "",
     *               "billing_country": "0",
     *               "shipping_street": "",
     *               "shipping_city": "",
     *               "shipping_state": "",
     *               "shipping_zip": "",
     *               "shipping_country": "0",
     *               "longitude": null,
     *               "latitude": null,
     *               "default_language": "",
     *               "default_currency": "0",
     *               "show_primary_contact": "0",
     *               "stripe_id": null,
     *               "registration_confirmed": "1",
     *               "type_name": "Type 1",
     *               "attachments": [],
     *               "project_name": "Client api project",
     *               "addedfrom_name": "admin 123",
     *               "comments": [
     *                   {
     *                       "id": "3",
     *                       "content": "test",
     *                       "contract_id": "2",
     *                       "staffid": "0",
     *                       "dateadded": "2023-05-10 12:37:18"
     *                   },
     *                   {
     *                       "id": "4",
     *                       "content": "Contract comment for client side",
     *                       "contract_id": "2",
     *                       "staffid": "0",
     *                       "dateadded": "2023-05-10 16:55:34"
     *                   },
     *                   {
     *                       "id": "5",
     *                       "content": "Contract comment for client side",
     *                       "contract_id": "2",
     *                       "staffid": "0",
     *                       "dateadded": "2023-05-10 16:57:37"
     *                   },
     *                   {
     *                       "id": "6",
     *                       "content": "Test Client Comment",
     *                       "contract_id": "2",
     *                       "staffid": "0",
     *                       "dateadded": "2023-05-10 17:01:02"
     *                   },
     *                   {
     *                       "id": "7",
     *                       "content": "Test Client Comment",
     *                       "contract_id": "2",
     *                       "staffid": "0",
     *                       "dateadded": "2023-05-10 17:02:00"
     *                   },
     *                   {
     *                       "id": "18",
     *                       "content": "Test Client Comment",
     *                       "contract_id": "2",
     *                       "staffid": "0",
     *                       "dateadded": "2023-05-16 15:45:16"
     *                   },
     *                   {
     *                       "id": "19",
     *                       "content": "Test Client Comment",
     *                       "contract_id": "2",
     *                       "staffid": "0",
     *                       "dateadded": "2023-05-16 15:50:22"
     *                   },
     *                   {
     *                       "id": "20",
     *                       "content": "Test Client Comment",
     *                       "contract_id": "2",
     *                       "staffid": "0",
     *                       "dateadded": "2023-05-16 18:16:15"
     *                   },
     *                   {
     *                       "id": "21",
     *                       "content": "Test Client Comment",
     *                       "contract_id": "2",
     *                       "staffid": "0",
     *                       "dateadded": "2023-05-16 18:16:32"
     *                   },
     *                   {
     *                       "id": "22",
     *                       "content": "Test Client Comment",
     *                       "contract_id": "2",
     *                       "staffid": "0",
     *                       "dateadded": "2023-05-16 18:17:21"
     *                   }
     *               ]
     *           },
     *           "message": "Data retrieved successfully"
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
    public function contracts_get()
    {
        $parameters = $this->get();
        if (!empty($parameters) && !in_array('id', array_keys($parameters))) {
            $this->response(['message' => _l('something_went_wrong')], 500);
        }

        $contractID = $this->get('id');
        if (!empty($contractID) && !is_numeric($contractID)) {
            $this->response(['message' => _l('something_went_wrong')], 500);
        }

        $contractData = $this->customers_api_model->getTable('contracts', $contractID, $this->clientInfo['data']);

        if (!empty($contractID) && '200' == $contractData['response_code']) {
            $contractData['response']['data']->comments = $this->contracts_model->get_comments($contractID);
        }

        $this->response($contractData['response'], $contractData['response_code']);
    }

    /**
     * @api {post} /mmb/v1/contracts/id/:id/group/contract_comment Add Contract Comments
     *
     * @apiVersion 1.0.0
     *
     * @apiName AddContractComments
     *
     * @apiGroup Contracts
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest off
     *
     * @apiParam {Number} id <span class="btn btn-xs btn-danger">Required</span> Contract unique ID.
     * @apiParam {String} group=contract_comment <span class="btn btn-xs btn-danger">Required</span> Group Name
     *
     * @apiBody {String} content <span class="btn btn-xs btn-danger">Required</span> Comment
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "message": "Contract comments added successfully."
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 403 Forbidden
     *     {
     *       "status": false,
     *       "message": "You do not have permission to perform this action"
     *     }
     */
    public function contracts_post()
    {
        $postData = $this->post();

        $parameters = [
            'id'    => is_numeric($this->get('id')) ? $this->get('id') : '',
            'group' => $this->get('group'),
        ];

        if (!empty($parameters['group']) && !empty($parameters['id'])) {
            $data = $this->customers_api_model->addComments($postData, $parameters, $this->clientInfo['data']);
            $this->response($data['response'], $data['response_code']);
        }

        $this->response(['message' => _l('something_went_wrong')], 500);
    }
}
