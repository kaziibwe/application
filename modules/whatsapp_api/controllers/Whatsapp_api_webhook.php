<?php

defined('BASEPATH') || exit('No direct script access allowed');

    class Whatsapp_api_webhook extends App_Controller
    {
        public function __construct()
        {
            parent::__construct();
        }

        //A notification is sent to you when a WhatsApp Business Account (WABA) has been reviewed.
        public function account_review_update()
        {
        }

        //A notification is sent to you when a change to your WABA has occured. This change can include phone number update, a policy violation, a WABA has been banned and more.
        public function account_update()
        {
        }

        //A notification is sent to you when a capability has been updated. This can include a change for the maximum number of phone numbers a WABA can have or conversation per phone number.
        public function business_capability_update()
        {
        }

        //A notification is sent to you when the message template has been approved or rejected, or if it has been disabled.
        public function message_template_status_update()
        {
        }

        //A notification is sent to you when your business has received a message.
        public function messages()
        {
        }

        //A notification is sent to you when the name associated with a phone number has been approved or rejected.
        public function phone_number_name_update()
        {
        }

        //A notification is sent to you when the quality-related status for a phone number has an update.
        public function phone_number_quality_update()
        {
        }
    }

    /* End of file Whatsapp_api.php */
