<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';
require_once __DIR__.'/../../vendor/autoload.php';
use CustomerApi\RestController;

class Logout extends RestController
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
     * @api {post} /mmb/v1/logout Logout
     *
     * @apiName Logout
     *
     * @apiGroup Authentication
     *
     * @apiHeader {String} Authorization <span class="btn btn-xs btn-danger">Required</span> Basic Access Authentication token.
     *
     * @apiVersion 1.0.0
     *
     * @apiSampleRequest off
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "message": "You've logged out successfully"
     *     }
     */
    public function logout_get()
    {
        $this->db->update(db_prefix() . 'contacts', ['customer_api_key' => NULL], ['id' => $this->clientInfo['data']->contact_id]);
        $this->response(['message' => _l('logged_out_successfully')], 200);
    }
}
/* End of file Authentication.php */
