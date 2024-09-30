<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';
require_once __DIR__.'/../../vendor/autoload.php';
use CustomerApi\RestController;

class Register extends RestController
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
     * @api {post} /mmb/v1/register Register
     *
     * @apiVersion 1.0.0
     *
     * @apiName Register
     *
     * @apiGroup Authentication
     *
     * @apiSampleRequest off
     *
     * @apiBody {String} company               <span class="btn btn-xs btn-danger">Required</span> Company
     * @apiBody {String} firstname             <span class="btn btn-xs btn-danger">Required</span> Firstname
     * @apiBody {String} lastname              <span class="btn btn-xs btn-danger">Required</span> Lastname
     * @apiBody {String} email                 <span class="btn btn-xs btn-danger">Required</span> Email
     * @apiBody {String} password              <span class="btn btn-xs btn-danger">Required</span> Password
     * @apiBody {String} passwordr             <span class="btn btn-xs btn-danger">Required</span> Repeat Password
     * @apiBody {String} [vat]                 VAT Number
     * @apiBody {Number} [phonenumber]         Phone
     * @apiBody {Number} [contact_phonenumber] Contact Phonenumber
     * @apiBody {String} [website]             Website
     * @apiBody {String} [position]            Position
     * @apiBody {Number} [country]             Country
     * @apiBody {String} [city]                City
     * @apiBody {String} [address]             Address
     * @apiBody {Number} [zip]                 Zip Code
     * @apiBody {String} [state]               State
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "status": true,
     *         "message": "Thank your for registering."
     *     }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "status": false,
     *       "message": "Registration not allowed"
     *     }
     */
    public function register_post()
    {
        /* Check is registration allowed */
        if (1 != get_option('allow_register_api')) {
            $this->response(['message' => _l('registration_not_enabled_using_api')], 200);
        }
        $requiredData = [
            'firstname' => '',
            'lastname'  => '',
            'email'     => '',
            'password'  => '',
            'passwordr' => '',
        ];

        if (1 == get_option('company_is_required')) {
            $this->form_validation->set_rules('company', _l('client_company'), 'required');
            $requiredData['company'] = '';
        }

        $postData   = $this->post();
        $postData   = array_merge($requiredData, $postData);

        $this->load->library('form_validation');

        $this->form_validation->set_data($postData);

        $this->form_validation->set_rules('firstname', _l('client_firstname'), 'required');
        $this->form_validation->set_rules('lastname', _l('client_lastname'), 'required');
        $this->form_validation->set_rules('email', _l('client_email'), 'trim|required|is_unique['.db_prefix().'contacts.email]|valid_email');
        $this->form_validation->set_rules('password', _l('clients_register_password'), 'required');
        $this->form_validation->set_rules('passwordr', _l('clients_register_password_repeat'), 'required|matches[password]');

        if (false === $this->form_validation->run()) {
            $this->response(['message' => strip_tags(validation_errors())], 422);
        }

        $this->load->model('clients_model');

        $countryId = !empty($postData['country']) && is_numeric($postData['country']) ? $postData['country'] : 0;

        if (is_automatic_calling_codes_enabled()) {
            $customerCountry = get_country($countryId);

            if ($customerCountry) {
                $callingCode = '+'.ltrim($customerCountry->calling_code, '+');

                if (startsWith($postData['contact_phonenumber'], $customerCountry->calling_code)) { // with calling code but without the + prefix
                    $postData['contact_phonenumber'] = '+'.$postData['contact_phonenumber'];
                } elseif (!startsWith($postData['contact_phonenumber'], $callingCode)) {
                    $postData['contact_phonenumber'] = $callingCode.$postData['contact_phonenumber'];
                }
            }
        }

        define('CONTACT_REGISTERING', true);

        $clientid = $this->clients_model->add([
                'billing_street'      => $postData['address'] ?? '',
                'billing_city'        => $postData['city'] ?? '',
                'billing_state'       => $postData['state'] ?? '',
                'billing_zip'         => $postData['zip'] ?? '',
                'billing_country'     => $countryId,
                'firstname'           => $postData['firstname'],
                'lastname'            => $postData['lastname'],
                'email'               => $postData['email'],
                'contact_phonenumber' => $postData['contact_phonenumber'] ?? '',
                'website'             => $postData['website'] ?? '',
                'title'               => $postData['title'] ?? '',
                'password'            => $postData['passwordr'],
                'company'             => $postData['company'] ?? '',
                'vat'                 => $postData['vat'] ?? '',
                'phonenumber'         => $postData['phonenumber'] ?? '',
                'country'             => $postData['country'] ?? '',
                'city'                => $postData['city'] ?? '',
                'address'             => $postData['address'] ?? '',
                'zip'                 => $postData['zip'] ?? '',
                'state'               => $postData['state'] ?? '',
                'custom_fields'       => isset($postData['custom_fields']) && is_array($postData['custom_fields']) ? $postData['custom_fields'] : [],
                'default_language'    => ('' != get_contact_language()) ? get_contact_language() : get_option('active_language'),
        ], true);

        if ($clientid) {
            hooks()->do_action('after_client_register', $clientid);

            if ('1' == get_option('customers_register_require_confirmation')) {
                send_customer_registered_email_to_administrators($clientid);

                $this->customers_model->require_confirmation($clientid);
                $this->response(['message' => _l('customer_register_account_confirmation_approval_notice')], 200);
            }

            $this->response(['message' => _l('clients_successfully_registered')], 200);
        }

        $this->response(['message' => _l('something_went_wrong')], 400);
    }
}

/* End of file Register.php */
