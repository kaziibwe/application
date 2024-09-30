<?php

defined('BASEPATH') || exit('No direct script access allowed');

use WpOrg\Requests\Requests as Whatsapp_api_Request_Lib;

class Whatsapp_api_lib
{
    public $CI;
    public $to;
    public $staff_to;
    public $client_to;
    public $lead_to;
    public $messaging_template;
    public $merge_fields;
    public $tableData;
    public $attachmentData;
    public $category = null;
    public $blocked_message = false;
    public $send_data = [
        'messaging_product' => 'whatsapp',
        'recipient_type'    => 'individual',
        'type'              => 'template',
        'template'          => [],
        'text'          => '',
    ];

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('mmb/whatsapp_api_model');
        $this->initData();
    }

    public function initData()
    {
        $this->merge_fields = [];
    }

    public function send_mapped_template($category, $return=false, ...$params)
    {
        $this->initData();

        $this->merge_fields = $this->CI->app_merge_fields->format_feature(
            'other_merge_fields'
        );
        
        $this->category = $category;
        $all_templates         = $this->CI->whatsapp_api_model->get_mapping_data(['whatsapp_templates_mapping.category' => $category, 'active' => 1]);
        $response              = ($this->{$category}(...$params));
        if (!$response['status']) {
            $log_data['response_code']    = '501';
            $log_data['response_data']    = json_encode(["message" => $response['message']]);
            $log_data['send_json']        = json_encode([]);
            $log_data['message_category'] = $category;
            $log_data['category_params']  = json_encode($params);
            $log_data['merge_field_data'] = json_encode([]);
            $this->CI->whatsapp_api_model->add_request_log($log_data);

            return;
        }
        foreach ($all_templates as $template) {
            $this->send_data['template']['name']       = $template->template_name;
            $this->send_data['template']['language']   = ['code' => $template->language];
            switch ($template->send_to) {
                case 'contact':
                    $this->to = $this->client_to;
                    break;

                case 'lead':
                    $this->to = $this->lead_to;
                    break;

                case 'staff':
                    $this->to = $this->staff_to;
                    break;

                default:
                    $this->to = $this->staff_to;
                    break;
            }
            // change ticket url as per message send to
            if ($category == "ticket") {
                $this->merge_fields['{ticket_url}'] = admin_url('tickets/ticket/' . $params[array_key_first($params)]);
                if ($template->send_to == 'contact') {
                    $this->merge_fields['{ticket_url}'] = site_url('clients/ticket/' . $params[array_key_first($params)]);
                }
            }
            // Change Link as per send to
            if ($category == "tasks") {
                $this->CI->db->where('id', $params[array_key_first($params)]);
                $task = $this->CI->db->get(db_prefix() . 'tasks')->row();
                $this->merge_fields['{task_link}'] = admin_url('tasks/view/' . $params[array_key_first($params)]);
                if ($template->send_to == 'contact') {
                    $this->merge_fields['{task_link}'] = site_url('clients/project/' . $task->rel_id . '?group=project_tasks&taskid=' . $params[array_key_first($params)]);
                }
            }
            if ($category == "projects") {
                $this->merge_fields['{project_link}'] = admin_url('projects/view/' . $params[array_key_first($params)]);
                if ($template->send_to == 'contact') {
                    $this->merge_fields['{project_link}'] = site_url('clients/project/' . $params[array_key_first($params)]);
                }
            }
            if ($category == "secure_login") {
                $staff = get_staff($params[array_key_first($params)]);
                $this->merge_fields['{whatsapp_auth_code}'] = $staff->whatsapp_auth_code;
            }

            $this->send_data['template']['components'] = [];
            $this->parseComponents('header', $template, 0);
            $this->parseComponents('body', $template, 1);
            $this->parseComponents('footer', $template, 2);

            $data = $this->send();

            if ($template->debug_mode) {
                $log_data                     = $data;
                $log_data['send_json']        = json_encode($this->send_data);
                $log_data['message_category'] = $category;
                $log_data['category_params']  = json_encode($params);
                $log_data['merge_field_data'] = json_encode($this->merge_fields);
                $this->CI->whatsapp_api_model->add_request_log($log_data);
            }

            if ($return && $data['response_code'] == '200') { return true; }
        }
    }

    public function send_custom_message($receiverData, $templateId, $message, $imageUrl, $debugMode, $rel_type = '')
    {
        $template = $this->CI->whatsapp_api_model->get_template_data($templateId);

        $this->send_data['template']['name']       = $template->template_name;
        $this->send_data['template']['language']   = ['code' => $template->language];
        $this->send_data['template']['components'] = [];

        $this->attachmentData = [
            "url" => $imageUrl
        ];

        $id = (!empty($rel_type)) ? ($rel_type == 'staff' ? 'staffid' : ($rel_type == 'client' ? 'userid' : 'id')) : 'id';

        foreach ($receiverData as $receiver) {
            $this->merge_fields = $this->CI->app_merge_fields->format_feature(
                $rel_type . '_merge_fields', $receiver[$id]
            );

            $modifyMergeFieldsArray = [];
            foreach ($this->merge_fields as $key => $value) {
                $modifiedKey = '@' . $key;
                $modifyMergeFieldsArray[$modifiedKey] = $value;
            }
            $template->body_params = str_replace(array_keys($modifyMergeFieldsArray), $modifyMergeFieldsArray, $message);

            $this->parseComponents('header', $template, 0);
            $this->parseComponents('body', $template, 1);
            $this->parseComponents('footer', $template, 2);

            $this->to = $receiver['phonenumber'];
            $data = $this->send();
            if ($debugMode) {
                $log_data                     = $data;
                $log_data['send_json']        = json_encode($this->send_data);
                $log_data['message_category'] = "Broadcast Message";
                $log_data['category_params']  = json_encode(["templateId" => $templateId, "message" => $message, "imageUrl" => $imageUrl]);
                $log_data['merge_field_data'] = json_encode($receiver);
                $this->CI->whatsapp_api_model->add_request_log($log_data);
            }
        }
    }

    public function parseComponents($type, $template, $index)
    {
        $merge_fields = $this->merge_fields;
        if (!empty($template->{$type . '_params_count'})) {
            $this->send_data['template']['components'][$index] = ['type' => $type];
            for ($i = 1; $i <= $template->{$type . '_params_count'}; $i++) {
                if (isJson($template->{$type . '_params'} ?? "[]")) {
                    $parsed_text = json_decode($template->{$type . '_params'} ?? "[]", true);
                    $parsed_text = array_map(static function ($body) use ($merge_fields) {
                        $body['value'] = preg_replace('/@{(.*?)}/', '{$1}', $body['value']);
                        foreach ($merge_fields as $key => $val) {
                            $body['value'] =
                                false !== stripos($body['value'], $key)
                                ? str_replace($key, $val, $body['value'])
                                : str_replace($key, '', $body['value']);
                        }
                        return preg_replace('/\s+/', ' ', trim($body['value']));
                    }, $parsed_text);
                } else {
                    $parsed_text[1] = preg_replace('/\s+/', ' ', trim($template->{$type . '_params'}));
                }

                $this->send_data['template']['components'][$index]['parameters'][] = ['type' => 'text', 'text' => !empty($parsed_text[$i]) ? $parsed_text[$i] : '.'];
            }
        }
        if ($type == "header" && empty($template->{$type . '_params_count'}) && ($template->header_data_format == "DOCUMENT" || $template->header_data_format == "IMAGE")) {
            $this->send_data['template']['components'][$index] = ['type' => $type];
            $this->send_data['template']['components'][$index]['parameters'] = [];
            if ($template->header_data_format == "DOCUMENT") {
                $this->send_data['template']['components'][$index]['parameters'][] = [
                    'type' => 'document',
                    'document' => [
                        "link" => $this->attachmentData['url'],
                        "filename" => $this->attachmentData['file_name']
                    ]
                ];
            }
            if ($template->header_data_format == "IMAGE") {
                $this->send_data['template']['components'][$index]['parameters'][] = [
                    'type' => 'image',
                    'image' => [
                        "link" => $this->attachmentData['url']
                    ]
                ];
            }
        }
    }

    public function prepareData($staffID = null, $clientID = null, $merge_field_name = '', ...$merge_field_data)
    {
        if (!empty($staffID)) {
            $staff = get_staff($staffID);
            if (!empty($staff->phonenumber) && null !== $staff->phonenumber) {
                $this->staff_to = $staff->phonenumber;
            }
            $staff_fields       = $this->CI->app_merge_fields->format_feature('staff_merge_fields', $staffID);
            $this->merge_fields = array_merge($staff_fields, $this->merge_fields);
        }
        if (!empty($clientID)) {
            $primaryContact = get_primary_contact_user_id($clientID);
            $client         = $this->CI->clients_model->get_contact($primaryContact);
            $this->blocked_message = !(int)$client->{$this->category.'_message'};
            if($client->{$this->category.'_message'}){
                if (!empty($client->phonenumber) && null !== $client->phonenumber) {
                    $this->client_to = $client->phonenumber;
                }
                if (!empty($client->{$this->category.'_forward_phone'}) && null !== $client->{$this->category.'_forward_phone'}) {
                    $this->client_to = $client->{$this->category.'_forward_phone'};
                }
            }
            $client_fields      = $this->CI->app_merge_fields->format_feature('client_merge_fields', $clientID);
            $this->merge_fields = array_merge($client_fields, $this->merge_fields);
        }
        $category_fields    = $this->CI->app_merge_fields->format_feature($merge_field_name, ...$merge_field_data);
        $this->merge_fields = array_merge($category_fields, $this->merge_fields);
    }

    public function send()
    {
        $this->send_data['to']       = $this->to;
        if (empty($this->to)) {
            if($this->blocked_message){
                return ['response_code' => 501, 'response_data' => json_encode(['message' => 'User unsubscribed for this message category'])];
            }
            return ['response_code' => 501, 'response_data' => json_encode(['message' => 'To Number not found'])];
        }
        $endpoint                    = 'https://graph.facebook.com/v14.0/' . get_option('phone_number_id') . '/messages';
        $data                        = [];
        $data['api_endpoint']        = $endpoint;
        $data['phone_number_id']     = get_option('phone_number_id');
        $data['access_token']        = get_option('whatsapp_access_token');
        $data['business_account_id'] = get_option('whatsapp_business_account_id');
        try {
            $request = Whatsapp_api_Request_Lib::post(
                $endpoint,
                ['Authorization' => 'Bearer ' . get_option('whatsapp_access_token')],
                $this->send_data,
            );
            $data['response_code'] = $request->status_code;
            $data['response_data'] = htmlentities($request->body);
        } catch (Exception $e) {
            $data['response_code'] = 'EXCEPTION';
            $data['response_data'] = json_encode(["message" => $e->getMessage()]);
        }
        return $data;
    }

    public function leads($leadsID)
    {
        $this->CI->load->model('leads_model');
        $tableData       = $this->CI->leads_model->get($leadsID);
        $this->prepareData($tableData->assigned, null, 'leads_merge_fields', $leadsID);
        $this->lead_to = $this->merge_fields['{lead_phonenumber}'];

        return ['status' => true];
    }

    public function client($contactID)
    {
        $this->CI->load->model('clients_model');
        $contactData = $this->CI->clients_model->get_contact($contactID);
        $admins      = $this->CI->clients_model->get_admins($contactData->userid);
        $admins      = array_column($admins, 'staff_id');

        $firstValue = (!empty($admins)) ? $admins[array_key_first($admins)] : [];

        $this->prepareData($firstValue, $contactData->userid, 'client_merge_fields', $contactID, $contactData->userid);

        return ['status' => true];
    }

    public function invoice($invoiceID)
    {
        $this->CI->load->model('invoices_model');
        $tableData       = $this->CI->invoices_model->get($invoiceID);
        $this->prepareData($tableData->sale_agent, $tableData->clientid, 'invoice_merge_fields', $invoiceID);
        $pdf = invoice_pdf($tableData);
        $pdf->Output(module_dir_path("mmb", 'uploads/whatsapp/invoices/wa_invoice_' . $invoiceID . '.pdf'), 'F');

        $this->attachmentData = [
            "file_name" => format_invoice_number($invoiceID) . ".pdf",
            "url" => base_url("modules/mmb/uploads/whatsapp/invoices/wa_invoice_" . $invoiceID . ".pdf"),
        ];

        return ['status' => true];
    }

    public function tasks($taskID)
    {
        $this->CI->load->model('tasks_model');
        $tableData       = $this->CI->tasks_model->get($taskID);
        if ('project' == $tableData->rel_type && !empty($tableData->project_data)) {
            $assignee = array_column($tableData->assignees, 'assigneeid');
            $this->prepareData($assignee[array_key_first($assignee)], $tableData->project_data->clientid, 'tasks_merge_fields', $taskID);

            return ['status' => true];
        }

        return ['status' => false, 'message' => 'Only project type is allowed in task'];
    }

    public function projects($projectID)
    {
        $this->CI->load->model('projects_model');
        $tableData       = $this->CI->projects_model->get($projectID);
        $members         = $this->CI->projects_model->get_project_members($projectID);
        $member_arr      = array_column($members, 'staff_id');
        $this->prepareData($member_arr[array_key_first($member_arr)], $tableData->clientid, 'projects_merge_fields', $projectID);

        return ['status' => true];
    }

    public function proposals($proposalID)
    {
        $this->CI->load->model('proposals_model');
        $tableData       = $this->CI->proposals_model->get($proposalID);
        if ('customer' == $tableData->rel_type) {
            $this->prepareData($tableData->assigned, $tableData->rel_id, 'proposals_merge_fields', $proposalID);

            return ['status' => true];
        }

        return ['status' => false, 'message' => 'Lead type is not supported for proposals'];
    }

    public function payments($paymentsID)
    {
        $this->CI->load->model('invoices_model');
        $invoiceid = $this->CI->db->select('invoiceid')->get_where(db_prefix() . 'invoicepaymentrecords', ['id' => $paymentsID])->row()->invoiceid;
        if (empty($invoiceid)) {
            return ['status' => false];
        }
        $tableData       = $this->CI->invoices_model->get($invoiceid);
        $this->prepareData($tableData->sale_agent, $tableData->clientid, 'invoice_merge_fields', $invoiceid, $paymentsID);

        return ['status' => true];
    }

    public function ticket($ticketID)
    {
        $this->CI->load->model('tickets_model');
        $tableData       = $this->CI->tickets_model->get($ticketID);
        $this->prepareData($tableData->assigned, $tableData->userid, 'ticket_merge_fields', "new-ticket-opened-admin", $ticketID);

        return ['status' => true];
    }

    public function secure_login($staffID)
    {
        $this->prepareData($staffID, '', 'staff_merge_fields', $staffID);

        return ['status' => true];
    }
}
