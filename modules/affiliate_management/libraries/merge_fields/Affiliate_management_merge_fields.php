<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Affiliate_management_merge_fields extends App_merge_fields
{
    /**
     * This function builds an array of custom email templates keys.
     * The provided keys will be available in perfex email template editor for the supported templates.
     * @return array
     */
    public function build()
    {
        $this->ci->load->helper('affiliate_management/affiliate_management');

        // List of email templates used by the plugin
        $available = [];
        $templates = AffiliateManagementHelper::get_email_templates();

        $ref_templates = [
            AffiliateManagementHelper::EMAIL_TEMPLATE_SIGNUP_THROUGH_AFFILIATE,
            AffiliateManagementHelper::EMAIL_TEMPLATE_SUCCESSFUL_REFERRAL_COMMISSION,
            AffiliateManagementHelper::EMAIL_TEMPLATE_REFERRAL_COMMISSION_REVERSAL
        ];

        $commission_templates = [
            AffiliateManagementHelper::EMAIL_TEMPLATE_SUCCESSFUL_REFERRAL_COMMISSION,
            AffiliateManagementHelper::EMAIL_TEMPLATE_REFERRAL_COMMISSION_REVERSAL
        ];

        $payout_templates = [
            AffiliateManagementHelper::EMAIL_TEMPLATE_PAYOUT_UPDATED,
            AffiliateManagementHelper::EMAIL_TEMPLATE_NEW_PAYOUT_REQUEST_FOR_ADMIN,
            AffiliateManagementHelper::EMAIL_TEMPLATE_PAYOUT_UPDATED_FOR_ADMIN,
        ];

        $common_tags = [
            '{affiliate_name}',
            '{affiliate_balance}',
            '{affiliate_slug}',
            '{affiliate_status}',
            '{affiliate_created_at}',
        ];

        $other_tags = [
            '{payout_id}' => $payout_templates,
            '{payout_amount}'  => $payout_templates,
            '{payout_created_at}'  => $payout_templates,
            '{payout_status}'  => $payout_templates,
            '{payout_note_for_affiliate}'  => $payout_templates,
            '{payout_note_for_admin}'  => $payout_templates,
            '{referral_created_at}' => $ref_templates,
            '{referral_name}' => $ref_templates,
            '{referral_created_at}' => $ref_templates,
            '{commission_amount}' => $commission_templates,
            '{commission_created_at}' => $commission_templates,
            '{payment_amount}' => $commission_templates,
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

        foreach ($other_tags as $tag => $templates) {
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
            foreach ($data as $key => $value) {
                if (str_ends_with($key, '_amount')) $value = app_format_money($value, get_base_currency());
                $fields['{' . $res . '_' . $key . '}'] = $value;

                if (str_starts_with($key, $res . '_')) {
                    $cleaned_key = str_ireplace($res . '_', '', $key);
                    if (!empty($cleaned_key)) {
                        $fields['{' . $res . '_' . $cleaned_key . '}'] = $value;
                    }
                }
            }
        }
        return $fields;
    }
}
