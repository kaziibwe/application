<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Authorization_token
 * ----------------------------------------------------------
 * API Token Generate/Validation.
 */
require_once __DIR__.'/../vendor/autoload.php';

use Firebase\JWT\JWT as Customer_Rest_JWT;
use Firebase\JWT\Key as Customer_Rest_Key;

class Authorization_token
{
    /**
     * Token Key.
     */
    protected $token_key;

    /**
     * Token algorithm.
     */
    protected $token_algorithm;

    /**
     * Token Request Header Name.
     */
    protected $token_header;

    /**
     * Codeigniter instance
     */
    protected $ci;

    /**
     * Token Expire Time
     * ------------------
     * (1 day) : 60 * 60 * 24 = 86400
     * (1 hour) : 60 * 60 = 3600.
     */
    protected $token_expire_time = 315569260;

    public function __construct()
    {
        $this->ci = &get_instance();

        /*
         * jwt config file load
         */
        $this->ci->load->config('jwt');

        /*
         * Load Config Items Values
         */
        $this->token_key          = $this->ci->config->item('jwt_key');
        $this->token_algorithm    = $this->ci->config->item('jwt_algorithm');
        $this->token_header       = $this->ci->config->item('token_header');
        $this->token_expire_time  = $this->ci->config->item('token_expire_time');
        register_language_files('mmb', ["customers_api"]);
        load_client_language();
    }

    /**
     * Generate Token.
     *
     * @param: {array} data
     *
     * @param mixed|null $data
     */
    public function generateToken($data = null)
    {
        if ($data && is_array($data)) {
            try {
                return Customer_Rest_JWT::encode($data, base_url(), $this->token_algorithm);
            } catch (Exception $e) {
                return 'Message: '.$e->getMessage();
            }
        } else {
            return 'Token Data Undefined!';
        }
    }

    public function get_token()
    {
        /**
         * Request All Headers.
         */
        $headers = $this->ci->input->request_headers();

        /*
         * Authorization Header Exists
         */
        return $this->token($headers);
    }

    /**
     * Validate Token with Header.
     *
     * @return : user informations
     */
    public function validateToken()
    {
        /**
         * Request All Headers.
         */
        $headers = $this->ci->input->request_headers();

        /**
         * Authorization Header Exists.
         */
        $token_data = $this->tokenIsExist($headers);

        if (true === $token_data['status']) {
            try {
                /*
                 * Token Decode
                 */
                try {
                    $token_decode = Customer_Rest_JWT::decode($token_data['token'], new Customer_Rest_Key(base_url(), $this->token_algorithm));
                } catch (Exception $e) {
                    return ['status' => false, 'message' => $e->getMessage()];
                }

                if (!empty($token_decode) && is_object($token_decode)) {
                    // Check Token API Time [API_TIME]
                    if (empty($token_decode->API_TIME || !is_numeric($token_decode->API_TIME))) {
                        return ['status' => false, 'message' => _l('token_time_not_defined')];
                    }
                    /**
                     * Check Token Time Valid.
                     */
                    $time_difference = strtotime('now') - $token_decode->API_TIME;
                    if ($time_difference >= $this->token_expire_time) {
                        return ['status' => false, 'message' => _l('token_time_expire')];
                    }
                    /*
                     * All Validation False Return Data
                     */
                    return ['status' => true, 'data' => $token_decode];
                }

                    return ['status' => false, 'message' => _l('forbidden')];
            } catch (Exception $e) {
                return ['status' => false, 'message' => $e->getMessage()];
            }
        } else {
            // Authorization Header Not Found!
            return ['status' => false, 'message' => $token_data['message']];
        }
    }

    /**
     * Token Header Check.
     *
     * @param: request headers
     *
     * @param mixed $headers
     */
    private function tokenIsExist($headers)
    {
        if (!empty($headers) && is_array($headers)) {
            foreach ($headers as $header_name => $header_value) {
                if (strtolower(trim($header_name)) == strtolower(trim($this->token_header))) {
                    return ['status' => true, 'token' => $header_value];
                }
            }
        }

        return ['status' => false, 'message' => _l('token_is_not_defined')];
    }

    private function token($headers)
    {
        if (!empty($headers) && is_array($headers)) {
            foreach ($headers as $header_name => $header_value) {
                if (strtolower(trim($header_name)) == strtolower(trim($this->token_header))) {
                    return $header_value;
                }
            }
        }

        return _l('token_time_not_defined');
    }
}
