<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';
require_once __DIR__.'/../../vendor/autoload.php';
use CustomerApi\RestController;

class Authentication extends RestController
{
    public function __construct()
    {
        parent::__construct();
        register_language_files('mmb', ["customers_api"]);
        load_client_language();

        $this->load->helper('mmb/customers_api');
        if (checkModuleStatus()) {
            $this->response(checkModuleStatus()['response'], checkModuleStatus()['response_code']);
        }
    }

    /**
     * @api {post} /mmb/v1/authentication Login
     *
     * @apiName Login
     *
     * @apiGroup Authentication
     *
     * @apiVersion 1.0.0
     *
     * @apiSampleRequest off
     *
     * @apiBody {String} email    <span class="btn btn-xs btn-danger">Required</span> Customer's Email
     * @apiBody {String} password <span class="btn btn-xs btn-danger">Required</span> Customer's Password
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {String}  data    Logged in customers details.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "data": {
     *             "client_id": "1",
     *             "contact_id": "1",
     *             "client_logged_in": true,
     *             "API_TIME": 1684385965,
     *             "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJjbGllbnRfaWQiOiIxIiwiY29udGFjdF9pZCI6IjEiLCJjbGllbnRfbG9nZ2VkX2luIjp0cnVlLCJBUElfVElNRSI6MTY4NDM4NTk2NX0.y2rJF2OjwDair7ObPD5NbergZTFZo6zB3JzvNOhEOQaRvp0oDKR1eV6-pLyrInGTUACxAKODxdV2E6YjaGWfwA"
     *         },
     *         "message": "You've logged in successfully"
     *     }
     */
    public function authentication_post()
    {
        $requiredData = [
            'password' => '',
            'email'    => '',
        ];

        $postData = $this->post();
        $postData = array_merge($requiredData, $postData);

        $this->load->library('form_validation');

        $this->form_validation->set_data($postData);

        $this->form_validation->set_rules('password', _l('clients_login_password'), 'required');
        $this->form_validation->set_rules('email', _l('clients_login_email'), 'trim|required|valid_email');

        if (false === $this->form_validation->run()) {
            $this->response(['message' => strip_tags(validation_errors())], 422);
        }

        $this->load->model('Authentication_model');

        $success = $this->Authentication_model->login(
            $postData['email'],
            $postData['password'],
            false,
            false
        );

        if (is_array($success) && isset($success['memberinactive'])) {
            $this->response(['message' => _l('inactive_account')], 400);
        } elseif (false == $success) {
            $this->response(['message' => _l('client_invalid_username_or_password')], 400);
        }

        $table = db_prefix().'contacts';

        $this->db->where('email', $postData['email']);
        $client = $this->db->get($table)->row();

        $client_data = [
            'client_id'        => $client->userid, // Client's ID
            'contact_id'       => $client->id, // Contact ID
            'client_logged_in' => true,
            'API_TIME'         => time(),
        ];

        $this->load->helper('mmb/jwt');
        $token                = $this->authorization_token->generateToken($client_data);
        $client_data['token'] = $token;

        $this->db->update(db_prefix() . 'contacts', ['customer_api_key' => $token], ['id' => $client->id]);

        $this->response(['data' => $client_data, 'message' => _l('logged_in_successfully')], 200);
    }
}
/* End of file Authentication.php */
