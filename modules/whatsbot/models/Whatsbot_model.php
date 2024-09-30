<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Whatsbot_model extends App_Model
{
    use modules\whatsbot\traits\Whatsapp;

    public function __construct()
    {
        parent::__construct();
    }

    public function load_templates($accessToken = '', $accountId = '')
    {
        $templates = $this->loadTemplatesFromWhatsApp($accessToken, $accountId);

        // if there is any error from api then display appropriate message
        if (!$templates['status']) {
            return [
                'success' => false,
                'type'    => 'danger',
                'message' => $templates['message'],
            ];
        }
        $data       = $templates['data'];
        $insertData = [];

        foreach ($data as $key => $templateData) {
            // Adding all as we can change the status from webhook
            $insertData[$key]['template_id']   = $templateData->id;
            $insertData[$key]['template_name'] = $templateData->name;
            $insertData[$key]['language']      = $templateData->language;

            $insertData[$key]['status']   = $templateData->status;
            $insertData[$key]['category'] = $templateData->category;

            $components = array_column($templateData->components, null, 'type');

            $insertData[$key]['header_data_format']  = $components['HEADER']->format ?? '';
            $insertData[$key]['header_data_text']    = $components['HEADER']->text ?? null;
            $insertData[$key]['header_params_count'] = preg_match_all('/{{(.*?)}}/i', $components['HEADER']->text ?? '', $matches);

            $insertData[$key]['body_data']         = $components['BODY']->text ?? null;
            $insertData[$key]['body_params_count'] = preg_match_all('/{{(.*?)}}/i', $components['BODY']->text, $matches);

            $insertData[$key]['footer_data']         = $components['FOOTER']->text ?? null;
            $insertData[$key]['footer_params_count'] = preg_match_all('/{{(.*?)}}/i', $components['FOOTER']->text ?? '', $matches);

            $insertData[$key]['buttons_data'] = json_encode($components['BUTTONS'] ?? []);
        }
        $insertDataId     = array_column($insertData, 'template_id');
        $existingTemplate = $this->db->where_in(array_column($insertData, 'template_id'))->get(db_prefix() . 'wtc_templates')->result();

        $existingDataId = array_column($existingTemplate, 'template_id');

        $newTemplateId = array_diff($insertDataId, $existingDataId);
        $newTemplate   = array_filter($insertData, function ($val) use ($newTemplateId) {
            return in_array($val['template_id'], $newTemplateId);
        });

        // No need to update template data in db because you can't edit template in meta dashboard
        if (!empty($newTemplate)) {
            $this->db->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");
            $this->db->insert_batch(db_prefix() . 'wtc_templates', $newTemplate);
        }

        return ['success' => true];
    }

    public function getContactData($contactNumber, $name)
    {
        $contact = $this->db->get_where(db_prefix() . 'contacts', ['phonenumber' => $contactNumber])->row();
        if (!empty($contact)) {
            $contact->rel_type = 'contacts';
            $contact->name = $contact->firstname . ' ' . $contact->lastname;
            return $contact;
        }

        $lead = $this->db->get_where(db_prefix() . 'leads', ['phonenumber' => $contactNumber])->row();
        if (!empty($lead)) {
            $lead->rel_type = 'leads';
            return $lead;
        }

        $leadId = hooks()->apply_filters('ctl_auto_lead_creation', $contactNumber, $name);

        if (!empty($leadId)) {
            $lead           = $this->db->get_where(db_prefix() . 'leads', ['id' => $leadId])->row();
            $lead->rel_type = 'leads';
            return $lead;
        }

        return false;
    }

    public function updateStatus($status_data)
    {
        foreach ($status_data as $status) {
            $stat = is_array($status) ? $status['status'] : $status->status;
            $id = is_array($status) ? $status['id'] : $status->id;
            $this->db->update(db_prefix() . 'wtc_campaign_data', ['message_status' => $stat], ['whatsapp_id' => $id]);
        }
    }

    public function send_campaign($scheduled_data)
    {
        $logBatch = $chatMessage = [];

        foreach ($scheduled_data as $data) {
            switch ($data['rel_type']) {
                case 'leads':
                    $this->db->where('id', $data['rel_id']);
                    $rel_data      = $this->db->get(db_prefix() . 'leads')->row();
                    $interactionId = wbGetInteractionId($data, 'leads', $rel_data->id, $rel_data->name, $rel_data->phonenumber, $this->getDefaultPhoneNumber());
                    break;

                case 'contacts':
                    $this->db->where('id', $data['rel_id']);
                    $rel_data       = $this->db->get(db_prefix() . 'contacts')->row();
                    $data['userid'] = $rel_data->userid;
                    $interactionId  = wbGetInteractionId($data, 'contacts', $data['userid'], $rel_data->firstname . ' ' . $rel_data->lastname, $rel_data->phonenumber, $this->getDefaultPhoneNumber());
                    break;
            }
            $response = $this->sendTemplate($rel_data->phonenumber, $data);

            $logBatch[] = $response['log_data'];

            if (!empty($response['status'])) {
                $header = wbParseText($data['rel_type'], 'header', $data);
                $body   = wbParseText($data['rel_type'], 'body', $data);
                $footer = wbParseText($data['rel_type'], 'footer', $data);

                $header_data = '';
                if ($data['header_data_format'] == 'IMAGE') {
                    $header_data = '<a href="' . base_url(get_upload_path_by_type('campaign') . '/' . $data['filename']) . '" data-lightbox="image-group"><img src="' . base_url(get_upload_path_by_type('campaign') . '/' . $data['filename']) . '" class="img-responsive img-rounded" style="width: 300px"></img></a>';
                } elseif ($data['header_data_format'] == 'TEXT' || $data['header_data_format'] == '') {
                    $header_data = "<span class='tw-mb-3 bold'>" . nl2br(wbDecodeWhatsAppSigns($header ?? '')) . "</span>";
                }

                $buttonHtml = '';
                if (!empty(json_decode($data['buttons_data']))) {
                    $buttons = json_decode($data['buttons_data']);
                    $buttonHtml = "<div class='tw-flex tw-gap-2 tw-w-full padding-5 tw-flex-col mtop5'>";
                    foreach ($buttons->buttons as $key => $value) {
                        $buttonHtml .= '<button class="btn btn-default tw-w-full">' . $value->text . '</button>';
                    }
                    $buttonHtml .= '</div>';
                }

                // Prepare the data for chat message
                $chatMessage[] = [
                    'interaction_id' => $interactionId,
                    'sender_id'      => $this->getDefaultPhoneNumber(),
                    'url'            => null,
                    'message'        => "
                            $header_data
                            <p>" . nl2br(wbDecodeWhatsAppSigns($body)) . "</p>
                            <span class='text-muted tw-text-xs'>" . nl2br(wbDecodeWhatsAppSigns($footer ?? '')) . "</span>
                            $buttonHtml
                        ",
                    'status'     => 'sent',
                    'time_sent'  => date('Y-m-d H:i:s'),
                    'message_id' => $response['data']->messages[0]->id,
                    'staff_id'   => 0,
                    'type'       => 'text',
                ];
            }

            $update_data['status']           = (1 == $response['status']) ? 2 : $response['status'];
            $update_data['whatsapp_id']      = ($response['status']) ? reset($response['data']->messages)->id : null;
            $update_data['response_message'] = $response['message'] ?? '';
            $this->db->update(db_prefix() . 'wtc_campaign_data', $update_data, ['id' => $data['id']]);
        }

        // Add activity log
        $this->addWhatsbotLog($logBatch);

        // Add chat message
        $this->addChatMessage($chatMessage);

        return $this->db->update(db_prefix() . 'wtc_campaigns', ['is_sent' => 1, 'sending_count' => $data['sending_count'] + 1, 'scheduled_send_time' =>  date('Y-m-d H:i:s')], ['id' => $data['campaign_id']]);
    }

    public function addWhatsbotLog($logData)
    {
        if (!empty($logData)) {
            // Prepare the data for activity log
            $logsData = [
                'phone_number_id'     => get_option('wac_phone_number_id'),
                'access_token'        => get_option('wac_access_token'),
                'business_account_id' => get_option('wac_business_account_id'),
            ];
            $logData = array_map(function ($item) use ($logsData) {
                return array_merge($item, $logsData);
            }, $logData);
            return $this->db->insert_batch(db_prefix() . 'wtc_activity_log', $logData);
        }
        return false;
    }

    public function addChatMessage($chatMessage)
    {
        if (!empty($chatMessage)) {
            return $this->db->insert_batch(db_prefix() . 'wtc_interaction_messages', $chatMessage);
        }
    }

    public function getWhatsappLogDetails($id)
    {
        return $this->db->get_where(db_prefix() . 'wtc_activity_log', ['id' => $id])->row();
    }

    public function delete_log($id)
    {
        return $this->db->delete(db_prefix() . 'wtc_activity_log', ['id' => $id]);
    }

    public function delete_chat($id)
    {
        return $this->db->delete(db_prefix() . 'wtc_interactions', ['id' => $id]);
    }
}
