<?php

defined('BASEPATH') || exit('No direct script access allowed');

function parse_invoice_message($invoice_id, $message, $type = 'body')
{
    $CI = &get_instance();
    $CI->load->model(['invoices_model', 'clients_model']);

    $invoice = $CI->invoices_model->get($invoice_id);
    $client  = $CI->clients_model->get($invoice->clientid);
    $contact = $CI->clients_model->get_contacts($client->id, ['is_primary' => 1]);

    if ('header' == $type) {
        $header                        = [];
        $header['{{1}}']               = $contact->firstname;
        $header['{{2}}']               = $contact->lastname;

        $final_message = $message;

        foreach ($header as $key => $value) {
            $final_message = false !== stripos($final_message, $key)
                ? str_replace($key, $value, $final_message)
                : str_replace($key, '', $final_message);
        }

        return $final_message;
    }

    $body                        = [];
    $body['{{1}}']               = $contact->firstname;
    $body['{{2}}']               = $contact->lastname;

    $final_message = $message;

    foreach ($body as $key => $value) {
        $final_message = false !== stripos($final_message, $key)
            ? str_replace($key, $value, $final_message)
            : str_replace($key, '', $final_message);
    }

    return $final_message;
}

function set_header($header = [])
{
    $header_json   = [];
    $header['type'] = 'header';
    $params        = [];
    $i             = 1;
    foreach ($header as $value) {
        if (is_array($value)) {
            if (1 == $i) {
                $params[] = json_encode($value);
            }
            break;
        }
    }
    //header('Content-Type: application/json');
    $header_json['type']       = 'header';
    $header_json['parameters'] = '[' . implode(',', $params) . ']';

    return $header_json;
}

function set_body($body)
{
    $body_json   = [];
    $body['type'] = 'body';
    $params      = [];
    foreach ($body as $value) {
        if (is_array($value)) {
            $params[] = json_encode($value);
        }
    }
    //header('Content-Type: application/json');
    $body_json['type']       = 'body';
    $body_json['parameters'] = '[' . implode(',', $params) . ']';

    return $body_json;
}

function get_template_list()
{
    $CI = &get_instance();
    $CI->db->select('CONCAT(template_name," | ",language) as template,id');
    $CI->db->order_by('language');
    $result = $CI->db->get(db_prefix() . 'whatsapp_templates')->result_array();

    return $result;
}

function get_category_list()
{
    return [
        [
            'value'   => 'leads',
            'label'   => _l('lead'),
            'subtext' => _l('triggers_when_new_lead_created'),
        ],
        [
            'value'   => 'client',
            'label'   => _l('contact'),
            'subtext' => _l('triggers_when_new_contact_created'),
        ],
        [
            'value'   => 'invoice',
            'label'   => _l('invoice'),
            'subtext' => _l('triggers_when_new_invoice_created'),
        ],
        [
            'value'   => 'tasks',
            'label'   => _l('task'),
            'subtext' => _l('triggers_when_new_task_created'),
        ],
        [
            'value'   => 'projects',
            'label'   => _l('project'),
            'subtext' => _l('triggers_when_new_project_created'),
        ],
        [
            'value'   => 'proposals',
            'label'   => _l('proposal'),
            'subtext' => _l('triggers_when_new_proposal_created'),
        ],
        [
            'value'   => 'payments',
            'label'   => _l('payments'),
            'subtext' => _l('triggers_when_new_payment_created'),
        ],
        [
            'value'   => 'ticket',
            'label'   => _l('ticket'),
            'subtext' => _l('triggers_when_new_ticket_created'),
        ],
        [
            'value'   => 'secure_login',
            'label'   => _l('secure_login'),
            'subtext' => _l('triggers_when_staff_logs_in'),
        ]
    ];
}

function get_category_wise_merge_fields($types)
{
    $CI               = &get_instance();
    $merge_fields     = $CI->app_merge_fields->get();
    $all_merge_fields = $CI->app_merge_fields->all();

    $category_merge_fields = [];

    foreach ($all_merge_fields as $fields) {
        foreach ($fields as $key => $value) {
            foreach ($types as $type) {
                if ($key == $type) {
                    $category_merge_fields[$type] = $value;
                }
            }
        }
    }

    return $category_merge_fields;
}

/**
 * [remove_blank_value remove blank values and return array].
 *
 * @param  [array] $var
 * @param  [string] $key_to_check
 *
 * @return [array]
 */
if (!function_exists('remove_blank_value')) {
    function remove_blank_value($var, $key_to_check): array
    {
        $data = [];
        foreach ($var as $key => $value) {
            if ('' === $value[$key_to_check]) {
                unset($var[$key]);
                continue;
            }
            $data[] = $value;
        }

        return $data;
    }
}


if (!function_exists('isJson')) {
    function isJson($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
}


if (!function_exists('isXml')) {
    function isXml($string)
    {
        $prev = libxml_use_internal_errors(true);

        $doc    = simplexml_load_string($string);
        $errors = libxml_get_errors();

        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        return (empty($errors)) ? true : false;
    }
}

function send_to_list()
{
    return [

        [
            'value'   => 'staff',
            'label'   => _l('staff'),
            'subtext' => _l('it_will_send_message_to_staff'),
        ],

        [
            'value'   => 'contact',
            'label'   => _l('contact'),
            'subtext' => _l('it_will_send_message_to_primary_contact'),
        ],

        [
            'value'   => 'lead',
            'label'   => _l('leads'),
            'subtext' => _l('it_will_send_message_to_leads'),
        ],

    ];
}

function handle_image_upload()
{
    $CI = &get_instance();
    if (isset($_FILES['image']['name']) && '' != $_FILES['image']['name']) {
        $path        = get_upload_path_by_type('broadcast_images');
        $tmpFilePath = $_FILES['image']['tmp_name'];
        if (!empty($tmpFilePath) && '' != $tmpFilePath) {
            $path_parts  = pathinfo($_FILES['image']['name']);
            $extension   = $path_parts['extension'];
            $extension   = strtolower($extension);
            $filename    = time() . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                return $filename;
            }
        }
        return false;
    }
    return false;
}

function generateOTP() {
    $otp = "";
    $characters = "0123456789"; // Characters from which OTP will be generated
    $length = 6; // Length of the OTP
    
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $otp;
}

/*End of file "whatsapp_api_helper.".php */