<?php

defined('BASEPATH') || exit('No direct script access allowed');

if (!function_exists('convertSerializeDataToObject')) {
    function convertSerializeDataToObject($data)
    {
        return json_decode(json_encode(unserialize($data)));
    }
}

if (!function_exists('isAuthorized')) {
    function isAuthorized()
    {
        if (checkModuleStatus()) {
            return [
                'response' => [
                    'message' => checkModuleStatus()['response']['message'],
                ],
                'response_code' => 404,
            ];
        }

        $loggedInClient = get_instance()->authorization_token->validateToken();
        if (!$loggedInClient['status']) {
            return [
                'response' => [
                    'message' => $loggedInClient['message'],
                ],
                'response_code' => 401,
            ];
        }

        get_instance()->db->where('id', $loggedInClient['data']->contact_id);
        $contact = get_instance()->db->get(db_prefix() . 'contacts');

        $token = $contact->row()->customer_api_key;
        if (empty($token)) {
            return [
                'response' => [
                    'message' => _l('login_to_continue'),
                ],
                'response_code' => 401,
            ];
        }

        $authToken = get_instance()->input->request_headers()['Authorization'];

        if (trim($token) !== trim($authToken)) {
            return [
                'response' => [
                    'message' => _l('login_to_continue'),
                ],
                'response_code' => 401,
            ];
        }

        $isClientActive = get_client($contact->row()->userid)->active;

        if ($isClientActive == 0) {
            return [
                'response' => [
                    'message' => _l('admin_auth_inactive_account'),
                ],
                'response_code' => 401,
            ];
        }

        return $loggedInClient;
    }
}

if (!function_exists('checkModuleStatus')) {
    function checkModuleStatus()
    {
        get_instance()->load->library('app_modules');
        if (get_instance()->app_modules->is_inactive('mmb')) {
            return [
                'response' => [
                    'message' => 'Customers REST API module is deactivated. Please reactivate or contact support',
                ],
                'response_code' => 404,
            ];
        }
    }
}

if (!function_exists('get_invoice_status_by_id')) {
    function get_invoice_status_by_id($status='')
    {
        if (!class_exists('Invoices_model', false)) {
            get_instance()->load->model('invoices_model');
        }

        if (Invoices_model::STATUS_UNPAID == $status) {
            $status = _l('invoice_status_unpaid');
        } elseif (Invoices_model::STATUS_PAID == $status) {
            $status = _l('invoice_status_paid');
        } elseif (Invoices_model::STATUS_PARTIALLY == $status) {
            $status = _l('invoice_status_not_paid_completely');
        } elseif (Invoices_model::STATUS_OVERDUE == $status) {
            $status = _l('invoice_status_overdue');
        } elseif (Invoices_model::STATUS_CANCELLED == $status) {
            $status = _l('invoice_status_cancelled');
        } else {
            // status 6
            $status = _l('invoice_status_draft');
        }

        return $status;
    }
}

if (!function_exists('get_proposals_status_by_id')) {
    function get_proposals_status_by_id($status)
    {
        if (1 == $status) {
            $status      = _l('proposal_status_open');
        } elseif (2 == $status) {
            $status      = _l('proposal_status_declined');
        } elseif (3 == $status) {
            $status      = _l('proposal_status_accepted');
        } elseif (4 == $status) {
            $status      = _l('proposal_status_sent');
        } elseif (5 == $status) {
            $status      = _l('proposal_status_revised');
        } elseif (6 == $status) {
            $status      = _l('proposal_status_draft');
        }

        return $status;
    }
}

/* End of file "customers_api.".php */
