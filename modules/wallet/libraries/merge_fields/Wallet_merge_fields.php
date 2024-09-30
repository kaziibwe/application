<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wallet_merge_fields extends App_merge_fields
{
    /**
     * This function builds an array of custom email templates keys.
     * The provided keys will be available in perfex email template editor for the supported templates.
     * @return array
     */
    public function build()
    {
        // List of email templates used by the plugin
        $available = [];

        $withdraw_templates = [
            'wallet_withdraw_request',
            'wallet_withraw_request_for_admin'
        ];

        $templates = $withdraw_templates;

        $common_tags = [
            '{log_ref_id}',
            '{log_amount}',
            '{log_description}',
            '{log_mode}',
            '{log_created_at}',
            '{log_metadata_withdraw_method}',
            '{log_metadata_withdraw_note}',
            '{log_metadata_withdraw_admin_note}',
            '{log_metadata_withdraw_status}',
        ];

        $tagsMap = [];
        foreach ($common_tags as $tag) {
            $tagsMap[] = [
                'name'      => ucfirst(trim(str_replace(['{', '}', '_'], ' ', $tag))),
                'key'       => $tag, // Key for instance name
                'available' => $available,
                'templates' => $templates,
            ];
        }

        return $tagsMap;
    }

    /**
     * Format merge fields for company instance
     * @param  object $company
     * @return array
     */
    public function format($template_data)
    {
        return $this->set_template_data_format($template_data);
    }

    /**
     * Company Instance merge fields
     * @param  object $company
     * @return array
     */
    public function set_template_data_format($template_data)
    {
        $fields = [];
        foreach ($template_data as $res => $data) {
            if (in_array($res, ['client', 'contact']) || empty($data)) continue;
            $fields = array_merge($this->map_fields($res, $data), $fields);
        }
        return $fields;
    }

    private function map_fields($res, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {

            if ($key == 'metadata' && !empty($value)) {
                $value = is_string($value) ? json_decode($value, true) : $value;
            }

            if (!empty($value) && (is_array($value) || is_object($value))) {
                $value = (array)$value;
                $fields = array_merge($fields, $this->map_fields($res . '_' . $key, $value));
                continue;
            }

            if (str_ends_with($key, '_amount')) $value = app_format_money($value, get_base_currency());
            $fields['{' . $res . '_' . $key . '}'] = $value;

            if (str_starts_with($key, $res . '_')) {
                $cleaned_key = str_ireplace($res . '_', '', $key);
                if (!empty($cleaned_key)) {
                    $fields['{' . $res . '_' . $cleaned_key . '}'] = $value;
                }
            }
        }
        return $fields;
    }
}