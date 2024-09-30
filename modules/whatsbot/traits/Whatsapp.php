<?php

namespace modules\whatsbot\traits;

use Netflie\WhatsAppCloudApi\Message\ButtonReply\Button;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\ButtonAction;
use Netflie\WhatsAppCloudApi\Message\CtaUrl\TitleHeader;
use Netflie\WhatsAppCloudApi\Message\Template\Component;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Netflie\WhatsAppCloudApi\Message\Media\LinkID;
use Netflie\WhatsAppCloudApi\Message\Media\MediaObjectID;
use WpOrg\Requests\Requests as WhatsappMarketingRequests;

trait Whatsapp
{
    // Facebook API endpoint for WhatsApp Cloud API
    public static $facebookAPI = 'https://graph.facebook.com/v20.0/';

    public static $extensionMap = [
        'image/jpeg'                                                                => 'jpg',
        'image/png'                                                                 => 'png',
        'audio/mp3'                                                                 => 'mp3',
        'video/mp4'                                                                 => 'mp4',
        'audio/aac'                                                                 => 'aac',
        'audio/amr'                                                                 => 'amr',
        'audio/ogg'                                                                 => 'ogg',
        'audio/mp4'                                                                 => 'mp4',
        'text/plain'                                                                => 'txt',
        'application/pdf'                                                           => 'pdf',
        'application/vnd.ms-powerpoint'                                             => 'ppt',
        'application/msword'                                                        => 'doc',
        'application/vnd.ms-excel'                                                  => 'xls',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
        'video/3gp'                                                                 => '3gp',
        'image/webp'                                                                => 'webp',
    ];

    /**
     * Retrieve phone numbers associated with the WhatsApp Business Account
     *
     * @return array Response containing status and data or error message
     */
    public function getPhoneNumbers()
    {
        $accessToken = $this->getToken();
        $accountId   = $this->getAccountID();

        $request = WhatsappMarketingRequests::get(
            self::$facebookAPI . $accountId . '/phone_numbers?access_token=' . $accessToken
        );
        $response = json_decode($request->body);
        if (property_exists($response, 'error')) {
            return ['status' => false, 'message' => $response->error->message];
        }

        return ['status' => true, 'data' => $response->data];
    }

    /**
     * Load message templates from WhatsApp
     *
     * @return array Response containing status and data or error message
     */
    public function loadTemplatesFromWhatsApp()
    {
        $accessToken = $this->getToken();
        $accountId   = $this->getAccountID();

        $url = self::$facebookAPI . $accountId . '/?fields=id,name,message_templates,phone_numbers&access_token=' . $accessToken;

        $request  = WhatsappMarketingRequests::get($url);
        $response = json_decode($request->body);
        if (property_exists($response, 'error')) {
            return ['status' => false, 'message' => $response->error->message];
        }

        return ['status' => true, 'data' => $response->message_templates->data];
    }

    /**
     * Load WhatsApp Cloud API configuration
     *
     * @param string|null $fromNumber Optional phone number to use as the sender
     * @return WhatsAppCloudApi Instance of the WhatsAppCloudApi class
     */
    public function loadConfig($fromNumber = null)
    {
        return new WhatsAppCloudApi([
            'from_phone_number_id' => (!empty($fromNumber)) ? $fromNumber : $this->getPhoneID(),
            'access_token'         => $this->getToken(),
        ]);
    }

    /**
     * Send a template message using the WhatsApp Cloud API
     *
     * @param string $to Recipient phone number
     * @param array $template_data Data for the template message
     * @param string $type Type of the message, default is 'campaign'
     * @param string|null $fromNumber Optional sender phone number
     * @return array Response containing status, log data, and any response data or error message
     */
    public function sendTemplate($to, $template_data, $type = 'campaign', $fromNumber = null)
    {
        $this->load->model('whatsbot_model');
        $rel_type     = $template_data['rel_type'];
        $header_data = [];
        if ($template_data['header_data_format'] == 'TEXT') {
            $header_data  = wbParseText($rel_type, 'header', $template_data, 'array');
        }
        $body_data    = wbParseText($rel_type, 'body', $template_data, 'array');
        $buttons_data = wbParseText($rel_type, 'footer', $template_data, 'array');

        $component_header = $component_body = $component_buttons = [];
        $file_link = base_url(get_upload_path_by_type($type == "template_bot" ? 'template' : 'campaign') . $template_data['filename']);

        switch ($template_data['header_data_format']) {
            case 'IMAGE':
                $component_header[] = ['type' => 'image', 'image' => ["link" => $file_link]];
                break;

            case 'DOCUMENT':
                $component_header[] = ['type' => 'document', 'document' => ["link" => $file_link]];
                break;

            default:
                foreach ($header_data as $header) {
                    $component_header[] = ['type' => 'text', 'text' => $header];
                }
                break;
        }
        foreach ($body_data as $body) {
            $component_body[] = ['type' => 'text', 'text' => $body];
        }
        foreach ($buttons_data as $buttons) {
            $component_buttons[] = ['type' => 'text', 'text' => $buttons];
        }

        $whatsapp_cloud_api = $this->loadConfig($fromNumber);

        try {
            $components   = new Component($component_header, $component_body, $component_buttons);
            $result       = $whatsapp_cloud_api->sendTemplate($to, $template_data['template_name'], $template_data['language'], $components);
            $status       = true;
            $data         = json_decode($result->body());
            $responseCode = $result->httpStatusCode();
            $responseData = json_encode($result->decodedBody());
            $rawData      = json_encode($result->request()->body());
        } catch (\Netflie\WhatsAppCloudApi\Response\ResponseException $th) {
            $status       = false;
            $message      = $th->responseData()['error']['message'] ?? $th->rawResponse() ?? json_decode($th->getMessage());
            $responseCode = $th->httpStatusCode();
            $responseData = json_encode($message);
            $rawData      = json_encode([]);
        }

        $log_data['response_code']   = $responseCode;
        $log_data['category']        = $type;
        $log_data['category_id']     = $template_data['campaign_id'] ?? $template_data['campaign_table_id'];
        $log_data['rel_type']        = $rel_type;
        $log_data['rel_id']          = $template_data['rel_id'];
        $log_data['category_params'] = json_encode(['templateId' => $template_data['template_id'], 'message' => $message ?? '']);
        $log_data['response_data']   = $responseData;
        $log_data['raw_data']        = $rawData;

        return ['status' => $status, 'log_data' => $log_data, 'data' => $data ?? [], 'message' => $message->error->message ?? ''];
    }

    /**
     * Retrieve a URL for a media file using its media ID
     *
     * @param string $media_id Media ID to retrieve the URL for
     * @param string $accessToken Access token for authentication
     * @return string|null Filename of the saved media file or null on failure
     */
    public function retrieveUrl($media_id, $accessToken)
    {
        $uploadFolder = WHATSBOT_MODULE_UPLOAD_FOLDER;

        $client   = new \GuzzleHttp\Client();
        $url      = self::$facebookAPI . $media_id;
        $response = $client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        if (200 === $response->getStatusCode()) {
            $responseData = json_decode($response->getBody(), true);

            if (isset($responseData['url'])) {
                $media     = $responseData['url'];
                $mediaData = $client->get($media, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);
                if (200 === $mediaData->getStatusCode()) {
                    $imageContent = $mediaData->getBody();
                    $contentType  = $mediaData->getHeader('Content-Type')[0];

                    $extensionMap = self::$extensionMap;
                    $extension   = $extensionMap[$contentType] ?? 'unknown';
                    $filename    = 'media_' . uniqid() . '.' . $extension;
                    $storagePath = $uploadFolder . '/' . $filename;

                    $CI = &get_instance();
                    $CI->load->helper('file');
                    write_file($storagePath, $imageContent);

                    return $filename;
                }
            }
        }

        return null;
    }

    /**
     * Handle attachment upload and save the file
     *
     * @param array $attachment Attachment file information
     * @return string|bool Filename of the saved attachment or false on failure
     */
    public function handle_attachment_upload($attachment)
    {
        $uploadFolder = WHATSBOT_MODULE_UPLOAD_FOLDER;

        $contentType  = $attachment['type'];
        $extensionMap = self::$extensionMap;
        $extension = $extensionMap[$contentType] ?? 'unknown';

        $filename = uniqid('attachment_') . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $extension;

        $destination = $uploadFolder . '/' . $filename;
        if (move_uploaded_file($attachment['tmp_name'], $destination)) {
            return $filename;
        }
        return false;
    }

    /**
     * Send a message using the WhatsApp Cloud API
     *
     * @param string $to Recipient phone number
     * @param array $message_data Data for the message
     * @param string|null $fromNumber Optional sender phone number
     * @return array Response containing status, log data, and any response data or error message
     */
    public function sendMessage($to, $message_data, $fromNumber = null)
    {
        $message_data       = wbParseMessageText($message_data);
        $whatsapp_cloud_api = $this->loadConfig($fromNumber);

        try {
            $rows = [];
            if (!empty($message_data['button1_id'])) {
                $rows[] = new Button($message_data['button1_id'], $message_data['button1']);
            }
            if (!empty($message_data['button2_id'])) {
                $rows[] = new Button($message_data['button2_id'], $message_data['button2']);
            }
            if (!empty($message_data['button3_id'])) {
                $rows[] = new Button($message_data['button3_id'], $message_data['button3']);
            }
            if (!empty($rows)) {
                $action = new ButtonAction($rows);
                $result = $whatsapp_cloud_api->sendButton(
                    $to,
                    $message_data['reply_text'],
                    $action,
                    $message_data['bot_header'],
                    $message_data['bot_footer']
                );
            } else if (!empty($message_data['button_name']) && !empty($message_data['button_url']) && filter_var($message_data['button_url'], \FILTER_VALIDATE_URL)) {
                $header = new TitleHeader($message_data['bot_header']);

                $result = $whatsapp_cloud_api->sendCtaUrl(
                    $to,
                    $message_data['button_name'],
                    $message_data['button_url'],
                    $header,
                    $message_data['reply_text'],
                    $message_data['bot_footer'],
                );
            } else {
                $message = $message_data['bot_header'] . "\n" . $message_data['reply_text'] . "\n" . $message_data['bot_footer'];
                if (!empty($message_data['filename'])) {
                    $link_id = new LinkID(base_url(get_upload_path_by_type('bot_files') . $message_data['filename']));
                    $bot_file_path = FCPATH . get_upload_path_by_type('bot_files') . $message_data['filename'];
                    if (is_image($bot_file_path)) {
                        $result = $whatsapp_cloud_api->sendImage($to, $link_id, $message);
                    } else if (is_html5_video($bot_file_path)) {
                        $result = $whatsapp_cloud_api->sendVideo($to, $link_id, $message);
                    } else if (!empty($message_data['filename'])) {
                        $result = $whatsapp_cloud_api->sendDocument($to, $link_id, $message_data['filename'], $message);
                    }
                } else {
                    $result = $whatsapp_cloud_api->sendTextMessage($to, $message, true);
                }
            }

            $status       = true;
            $data         = json_decode($result->body());
            $responseCode = $result->httpStatusCode();
            $responseData = $data;
            $rawData      = json_encode($result->request()->body());
        } catch (\Netflie\WhatsAppCloudApi\Response\ResponseException $th) {
            $status       = false;
            $message      = $th->responseData()['error']['message'] ?? $th->rawResponse() ?? $th->getMessage();
            $responseCode = $th->httpStatusCode();
            $responseData = $message;
            $rawData      = json_encode([]);
        }

        $log_data['response_code']   = $responseCode;
        $log_data['category']        = 'Message Bot';
        $log_data['category_id']     = $message_data['id'];
        $log_data['rel_type']        = $message_data['rel_type'];
        $log_data['rel_id']          = ' - ';
        $log_data['category_params'] = json_encode(['message' => $message ?? '']);
        $log_data['response_data']   = !empty($responseData) ? json_encode($responseData) : '';
        $log_data['raw_data']        = $rawData;

        $batchLogData[] = $log_data;
        $this->whatsbot_model->addWhatsbotLog($batchLogData);

        return ['status' => $status, 'log_data' => $log_data ?? [], 'data' => $data ?? [], 'message' => $message->error->message ?? ''];
    }

    /**
     * Get the access token for the WhatsApp Cloud API
     *
     * @return string Access token
     */
    private function getToken()
    {
        return get_option('wac_access_token');
    }

    /**
     * Get the access token for the WhatsApp Cloud API
     *
     * @return string Access token
     */
    private function getProfile()
    {
        $accessToken = $this->getToken();
        $phoneId   = $this->getPhoneID();

        $url = self::$facebookAPI . $phoneId . '/whatsapp_business_profile?fields=profile_picture_url&access_token=' . $accessToken;

        $request  = WhatsappMarketingRequests::get($url);
        $response = json_decode($request->body);

        if (property_exists($response, 'error')) {
            return ['status' => false, 'message' => $response->error->message];
        }

        return ['status' => true, 'data' => reset($response->data)];
    }

    /**
     * Get the phone number ID for the WhatsApp Cloud API
     *
     * @return string Phone number ID
     */
    private function getPhoneID()
    {
        return get_option('wac_phone_number_id');
    }

    /**
     * Get the business account ID for the WhatsApp Cloud API
     *
     * @return string Business account ID
     */
    private function getAccountID()
    {
        return get_option('wac_business_account_id');
    }

    /**
     * Get the default phone number for the WhatsApp Cloud API
     *
     * @return string Default phone number
     */
    private function getDefaultPhoneNumber()
    {
        return get_option('wac_default_phone_number');
    }
}
