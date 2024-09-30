<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';
use CustomerApi\RestController;

class Miscellaneous extends RestController
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
    }

    /**
     * @api {get} /mmb/v1/miscellaneous/group/client_menu Get client menu
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetClientMenu
     *
     * @apiGroup Miscellaneous
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest /mmb/v1/miscellaneous/group/client_menu
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Client menu information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "status": true,
     *          "data": [
     *              {
     *                  "id": 1,
     *                  "name": "Invoices",
     *                  "short_name": "invoices"
     *              },
     *              {
     *                  "id": 2,
     *                  "name": "Estimates",
     *                  "short_name": "estimates"
     *              },
     *              {
     *                  "id": 3,
     *                  "name": "Contracts",
     *                  "short_name": "contracts"
     *              },
     *              {
     *                  "id": 4,
     *                  "name": "Proposals",
     *                  "short_name": "proposals"
     *              },
     *              {
     *                  "id": 5,
     *                  "name": "Support",
     *                  "short_name": "support"
     *              },
     *              {
     *                  "id": 6,
     *                  "name": "Projects",
     *                  "short_name": "projects"
     *              }
     *          ],
     *          "message": "Data retrieved successfully"
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *         "status": false,
     *         "message": "No data found"
     *     }
     */

    /**
     * @api {get} /mmb/v1/miscellaneous/group/departments List all departments
     *
     * @apiVersion 1.0.0
     *
     * @apiName ListAllDepartments
     *
     * @apiGroup Miscellaneous
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest /mmb/v1/miscellaneous/group/departments
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Departments information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "data": [
     *             {
     *                 "departmentid": "1",
     *                 "name": " Department Name 1"
     *             },
     *             {
     *                 "departmentid": "2",
     *                 "name": " Department Name 2"
     *             }
     *         ],
     *         "message": "Data retrieved successfully"
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *         "status": false,
     *         "message": "No data found"
     *     }
     */

    /**
     * @api {get} /mmb/v1/miscellaneous/group/get_ticket_priorities List ticket priorities
     *
     * @apiVersion 1.0.0
     *
     * @apiName ListTicketPriorities
     *
     * @apiGroup Miscellaneous
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest /mmb/v1/miscellaneous/group/get_ticket_priorities
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Ticket priorities information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "data": [
     *             {
     *                 "priorityid": "1",
     *                 "name": "Low"
     *             },
     *             {
     *                 "priorityid": "2",
     *                 "name": "Medium"
     *             },
     *             {
     *                 "priorityid": "3",
     *                 "name": "High"
     *             }
     *         ],
     *         "message": "Data retrieved successfully"
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *         "status": false,
     *         "message": "No data found"
     *     }
     */

    /**
     * @api {get} /mmb/v1/miscellaneous/group/get_services List all services
     *
     * @apiVersion 1.0.0
     *
     * @apiName ListAllServices
     *
     * @apiGroup Miscellaneous
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiSampleRequest /mmb/v1/miscellaneous/group/get_services
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Services information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "data": [
     *             {
     *                 "serviceid": "1",
     *                 "name": "service 1"
     *             },
     *             {
     *                 "serviceid": "2",
     *                 "name": "service 2"
     *             }
     *         ],
     *         "message": "Data retrieved successfully"
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *         "status": false,
     *         "message": "No data found"
     *     }
     */
    public function Miscellaneous_get()
    {
        $group = $this->get('group');
        if (!empty($group)) {
            $data = $this->customers_api_model->getMiscellaneousGroups($group, $this->clientInfo['data']);
            $this->response($data['response'], $data['response_code']);
        }

        $this->response(['message' => _l('something_went_wrong')], 500);
    }
}

/* End of file Miscellaneous.php */
