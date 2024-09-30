<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Get the reply type based on ID
 *
 * @param string $id
 * @return array
 */
if (!function_exists('wb_get_reply_type')) {
    function wb_get_reply_type($id = '')
    {
        $reply_types = [
            [
                'id'     => 1,
                'label'  => _l('on_exact_match'),
            ],
            [
                'id'     => 2,
                'label'  => _l('when_message_contains'),
            ],
            [
                'id'     => 3,
                'label'  => _l('when_client_send_the_first_message'),
            ],
            [
                'id'     => 4,
                'label'  => _l('default_message_on_no_match'),
            ],
        ];

        if (!empty($id)) {
            $key = array_search($id, array_column($reply_types, 'id'));

            return $reply_types[$key];
        }

        return $reply_types;
    }
}

/**
 * Get WhatsApp template based on ID
 *
 * @param string $id
 * @return array
 */
if (!function_exists('wb_get_whatsapp_template')) {
    function wb_get_whatsapp_template($id = '')
    {
        get_instance()->db->where_in('header_data_format', ['', 'TEXT', 'IMAGE']);
        if (is_numeric($id)) {
            return get_instance()->db->order_by('language', 'asc')->get_where(db_prefix().'wtc_templates', ['id' => $id, 'status' => 'APPROVED'])->row_array();
        }

        return get_instance()->db->order_by('language', 'asc')->get_where(db_prefix().'wtc_templates', ['status' => 'APPROVED'])->result_array();
    }
}

/**
 * Get campaign data based on campaign ID
 *
 * @param string $campaign_id
 * @return array
 */
if (!function_exists('wb_get_campaign_data')) {
    function wb_get_campaign_data($campaign_id = '')
    {
        return get_instance()->db->get_where(db_prefix().'wtc_campaign_data', ['campaign_id' => $campaign_id])->result_array();
    }
}

/**
 * Check if a string is a valid JSON
 *
 * @param string $string
 * @return bool
 */
if (!function_exists('wbIsJson')) {
    function wbIsJson($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
}

/**
 * Get the relation types
 *
 * @return array
 */
if (!function_exists('wb_get_rel_type')) {
    function wb_get_rel_type()
    {
        return [
            [
                'key'  => 'leads',
                'name' => _l('leads'),
            ],
            [
                'key'  => 'contacts',
                'name' => _l('contacts'),
            ],
        ];
    }
}

/**
 * Parse text with merge fields
 *
 * @param string $rel_type
 * @param string $type
 * @param array $data
 * @param string $return_type
 * @return string|array
 */
if (!function_exists('wbParseText')) {
    function wbParseText($rel_type, $type, $data, $return_type = 'text')
    {
        $rel_type = ('contacts' == $rel_type) ? 'client' : $rel_type;
        $CI       = get_instance();
        $CI->load->library('merge_fields/app_merge_fields');
        $merge_fields = $CI->app_merge_fields->format_feature(
            $rel_type . '_merge_fields',
            $data['userid'] ?? $data['rel_id'],
            $data['rel_id']
        );
        $other_merge_fields = $CI->app_merge_fields->format_feature('other_merge_fields');
        $merge_fields       = array_merge($other_merge_fields, $merge_fields);
        $parse_data         = [];

        for ($i = 1; $i <= $data["{$type}_params_count"]; ++$i) {
            if (wbIsJson($data["{$type}_params"] ?? '[]')) {
                $parsed_text = json_decode($data["{$type}_params"] ?? '[]', true);
                $parsed_text = array_map(static function ($body) use ($merge_fields) {
                    $body['value'] = preg_replace('/@{(.*?)}/', '{$1}', $body['value']);
                    foreach ($merge_fields as $key => $val) {
                        $body['value'] =
                            false !== stripos($body['value'], $key)
                            ? str_replace($key, !empty($val) ? $val : ' ', $body['value'])
                            : str_replace($key, '', $body['value']);
                    }

                    return preg_replace('/\s+/', ' ', trim($body['value']));
                }, $parsed_text);
            } else {
                $parsed_text[1] = preg_replace('/\s+/', ' ', trim($data["{$type}_params"]));
            }

            if ('text' == $return_type && !empty($data["{$type}_message"])) {
                $data["{$type}_message"] = str_replace("{{{$i}}}", !empty($parsed_text[$i]) ? $parsed_text[$i] : ' ', $data["{$type}_message"]);
            }
            $parse_data[] = !empty($parsed_text[$i]) ? $parsed_text[$i] : '.';
        }
        return ('text' == $return_type) ? $data["{$type}_message"] : $parse_data;
    }
}

/**
 * Parse message text with merge fields
 *
 * @param array $data
 * @return array
 */
if (!function_exists('wbParseMessageText')) {
    function wbParseMessageText($data)
    {
        $rel_type = $data['rel_type'];
        $rel_type = ('contacts' == $rel_type) ? 'client' : $rel_type;
        get_instance()->load->library('merge_fields/app_merge_fields');
        $merge_fields = get_instance()->app_merge_fields->format_feature(
            $rel_type . '_merge_fields',
            $data['userid'] ?? $data['rel_id'],
            $data['rel_id']
        );
        $other_merge_fields = get_instance()->app_merge_fields->format_feature('other_merge_fields');
        $merge_fields       = array_merge($other_merge_fields, $merge_fields);

        $data['reply_text'] = preg_replace('/@{(.*?)}/', '{$1}', $data['reply_text']);
        foreach ($merge_fields as $key => $val) {
            $data['reply_text'] =
                false !== stripos($data['reply_text'], $key)
                ? str_replace($key, !empty($val) ? $val : ' ', $data['reply_text'])
                : str_replace($key, '', $data['reply_text']);
        }

        return $data;
    }
}

/**
 * Get the campaign status based on status ID
 *
 * @param string $status_id
 * @return array
 */
if (!function_exists('wb_campaign_status')) {
    function wb_campaign_status($status_id = '')
    {
        $statusid              = ['0', '1', '2'];
        $status['label']       = ['Failed', 'Pending', 'Success'];
        $status['label_class'] = ['label-danger', 'label-warning', 'label-success'];
        if (in_array($status_id, $statusid)) {
            $index = array_search($status_id, $statusid);
            if (false !== $index && isset($status['label'][$index])) {
                $status['label'] = $status['label'][$index];
            }
            if (false !== $index && isset($status['label_class'][$index])) {
                $status['label_class'] = $status['label_class'][$index];
            }
        } else {
            $status['label']       = _l('draft');
            $status['label_class'] = 'label-default';
        }

        return $status;
    }
}

/**
 * Get all staff members
 *
 * @return array
 */
if (!function_exists('wb_get_all_staff')) {
    function wb_get_all_staff()
    {
        return get_instance()->db->get(db_prefix().'staff')->result_array();
    }
}

/**
 * Get staff members allowed to view message templates
 *
 * @return array
 */
if (!function_exists('wbGetStaffMembersAllowedToViewMessageTemplates')) {
    function wbGetStaffMembersAllowedToViewMessageTemplates()
    {
        get_instance()->db->join(db_prefix().'staff_permissions', db_prefix().'staff_permissions.staff_id = '.db_prefix().'staff.staffid', 'LEFT');
        get_instance()->db->where([db_prefix().'staff_permissions.capability' => 'view', db_prefix().'staff_permissions.feature' => 'wtc_template']);
        get_instance()->db->or_where([db_prefix().'staff.admin' => '1']);

        return get_instance()->db->get(db_prefix().'staff')->result_array();
    }
}

/**
 * Get the interaction ID based on data, relation type, ID, name, and phone number
 *
 * @param array $data
 * @param string $relType
 * @param string $id
 * @param string $name
 * @param string $phonenumber
 * @return int
 */
if (!function_exists('wbGetInteractionId')) {
    function wbGetInteractionId($data, $relType, $id, $name, $phonenumber, $fromNumber)
    {
        $interaction = get_instance()->db->get_where(db_prefix().'wtc_interactions', ['type' => $relType, 'type_id' => $id, 'wa_no' => $fromNumber])->row();

        if (!empty($interaction)) {
            return $interaction->id;
        }

        // If data has reply type then it is message bot else it is template bot
        $message = '';
        if (!empty($data['reply_type'])) {
            $message_data = wbParseMessageText($data);
            $message      = $message_data['reply_text'];
        }
        if (!empty($data['bot_type'])) {
            $message = wbParseText($data['rel_type'], 'header', $data).' '.wbParseText($data['rel_type'], 'body', $data).' '.wbParseText($data['rel_type'], 'footer', $data);
        }

        $interactionData = [
            'name'          => $name,
            'receiver_id'   => $phonenumber,
            'last_message'  => $message,
            'last_msg_time' => date('Y-m-d H:i:s'),
            'wa_no'         => get_option('wac_default_phone_number'),
            'wa_no_id'      => get_option('wac_phone_number_id'),
            'time_sent'     => date('Y-m-d H:i:s'),
            'type'          => $relType,
            'type_id'       => $id,
        ];

        get_instance()->db->insert(db_prefix().'wtc_interactions', $interactionData);

        return get_instance()->db->insert_id();
    }
}

/**
 * Decode WhatsApp signs to HTML tags
 *
 * @param string $text
 * @return string
 */
if (!function_exists('wbDecodeWhatsAppSigns')) {
    function wbDecodeWhatsAppSigns($text)
    {
        $patterns = [
            '/\*(.*?)\*/',       // Bold
            '/_(.*?)_/',         // Italic
            '/~(.*?)~/',         // Strikethrough
            '/```(.*?)```/',      // Monospace
        ];
        $replacements = [
            '<strong>$1</strong>',
            '<em>$1</em>',
            '<del>$1</del>',
            '<code>$1</code>',
        ];

        return preg_replace($patterns, $replacements, $text);
    }
}

if (!function_exists('wb_handle_whatsbot_upload')) {
    function wb_handle_whatsbot_upload($bot_id)
    {
        if (isset($_FILES['bot_file']['name'])) {
            $path        = get_upload_path_by_type('bot_files');
            $tmpFilePath = $_FILES['bot_file']['tmp_name'];
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES['bot_file']['name']);
                if (_upload_extension_allowed($filename)) {
                    $newFilePath = $path . $filename;
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        get_instance()->db->update(db_prefix().'wtc_bot', ['filename' => $filename], ['id' => $bot_id]);
                        return $filename;
                    }
                }
            }
        }
        return false;
    }
}

if (!function_exists('wb_handle_campaign_upload')) {
    function wb_handle_campaign_upload($id, $type)
    {
        if (isset($_FILES['image']['name'])) {
            $path        = get_upload_path_by_type($type);
            $tmpFilePath = $_FILES['image']['tmp_name'];
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES['image']['name']);
                if (_upload_extension_allowed($filename)) {
                    $newFilePath = $path . $filename;
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        get_instance()->db->update(db_prefix().'wtc_campaigns', ['filename' => $filename], ['id' => $id]);
                        return $filename;
                    }
                }
            }
        }
        return false;
    }
}

if (!function_exists('wb_get_allowed_extension')) {
    function wb_get_allowed_extension()
    {
        return [
            'image' => [
                'extension' => '.jpeg, .png',
                'size'      => 5
            ],
            'video' => [
                'extension' => '.mp4, .3gp',
                'size'      => 16,
            ],
            'audio' => [
                'extension' => '.aac, .amr, .mp3, .m4a, .ogg',
                'size'      => 16,
            ],
            'document' => [
                'extension' => '.pdf, .doc, .docx, .txt, .xls, .xlsx, .ppt, .pptx',
                'size'      => 100,
            ],
        ];
    }
}
