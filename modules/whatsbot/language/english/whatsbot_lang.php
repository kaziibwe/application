<?php

defined('BASEPATH') || exit('No direct script access allowed');

// connect account
$lang['whatsbot'] = 'WhatsBot';
$lang['connect_account'] = 'Connect Account';
$lang['connect_whatsapp_business'] = 'Connect Whatsapp Business';
$lang['campaigning'] = 'Campaigning';
$lang['business_account_id_description'] = 'Your WhatsApp Business Account (WABA) ID';
$lang['access_token_description'] = 'Your User Access Token after signing up at for an account at Facebook Developers Portal';
$lang['whatsapp_business_account_id'] = 'Whatsapp Business Account ID';
$lang['whatsapp_access_token'] = 'Whatsapp Access Token';
$lang['webhook_callback_url'] = 'Webhook Callback URL';
$lang['verify_token'] = 'Verify Token';
$lang['connect'] = 'Connect';
$lang['whatsapp'] = 'Whatsapp';
$lang['one_click_account_connection'] = 'One Click Account Connection';
$lang['connect_your_whatsapp_account'] = 'Connect Your Whatsapp Account';
$lang['copy'] = 'Copy';
$lang['copied'] = 'Copied!!';
$lang['disconnect'] = 'Disconnect';
$lang['number'] = 'Number';
$lang['number_id'] = 'Number ID';
$lang['quality'] = 'Quality';
$lang['status'] = 'Status';
$lang['business_account_id'] = 'Business Account ID';
$lang['permissions'] = 'Permissions';
$lang['phone_number_id_description'] = 'ID of the phone number connected to the WhatsApp Business API. If you are unsure about it, you can use a GET Phone Number ID request to retrieve it from WhatsApp API ( https://developers.facebook.com/docs/whatsapp/business-management-api/manage-phone-numbers )';
$lang['phone_number_id'] = 'Number ID of the WhatsApp Registered Phone';
$lang['update_details'] = 'Update Details';

$lang['bots'] = 'Bots';
$lang['bots_management'] = 'Bots Management';
$lang['create_template_base_bot'] = 'Create template base bot';
$lang['create_message_bot'] = 'Create message bot';
$lang['type'] = 'Type';
$lang['message_bot'] = 'Message Bot';
$lang['new_template_bot'] = 'New Template Bot';
$lang['new_message_bot'] = 'New Message Bot';
$lang['bot_name'] = 'Bot Name';
$lang['reply_text'] = 'Reply text <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Text that will be sent to the lead or contact. You can also use {companyname}, {crm_url} or any other custom merge fields of lead or contact, or use the \'@\' sign to find available merge fields" data-placement="bottom"></i> <span class="text-muted">(Maximum allowed characters should be 1024)</span>';
$lang['reply_type'] = 'Reply type';
$lang['trigger'] = 'Trigger';
$lang['header'] = 'Header';
$lang['footer_bot'] = 'Footer <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 60" data-placement="bottom"></i>';
$lang['bot_with_reply_buttons'] = 'Option 1: Bot with reply buttons';
$lang['bot_with_button_link'] = 'Option 2: Bot with button link - CTA URL';
$lang['button1'] = 'Button1 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 20" data-placement="bottom"></i>';
$lang['button1_id'] = 'Button1 ID <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 256" data-placement="bottom"></i>';
$lang['button2'] = 'Button2 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 20" data-placement="bottom"></i>';
$lang['button2_id'] = 'Button2 ID <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 256" data-placement="bottom"></i>';
$lang['button3'] = 'Button3 <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 20" data-placement="bottom"></i>';
$lang['button3_id'] = 'Button3 ID <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 256" data-placement="bottom"></i>';
$lang['button_name'] = 'Button Name <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Maximum allowed characters should be 20" data-placement="bottom"></i>';
$lang['button_link'] = 'Button Link';
$lang['enter_name'] = 'Enter Name';
$lang['select_reply_type'] = 'Select reply type';
$lang['enter_bot_reply_trigger'] = 'Enter bot reply trigger';
$lang['enter_header'] = 'Enter header';
$lang['enter_footer'] = 'Enter footer';
$lang['enter_button1'] = 'Enter button1';
$lang['enter_button1_id'] = 'Enter button1 ID';
$lang['enter_button2'] = 'Enter button2';
$lang['enter_button2_id'] = 'Enter button2 ID';
$lang['enter_button3'] = 'Enter button3';
$lang['enter_button3_id'] = 'Enter button3 ID';
$lang['enter_button_name'] = 'Enter button name';
$lang['enter_button_url'] = 'Enter button url';
$lang['on_exact_match'] = 'Reply bot: On exact match';
$lang['when_message_contains'] = 'Reply bot: When message contains';
$lang['when_client_send_the_first_message'] = 'Welcome reply - when lead or client send the first message';
$lang['bot_create_successfully'] = 'Bot create successfully';
$lang['bot_update_successfully'] = 'Bot update successfully';
$lang['bot_deleted_successfully'] = 'Bot deleted successfully';
$lang['templates'] = 'Templates';
$lang['template_data_loaded'] = 'Templates loaded successfully';
$lang['load_templates'] = 'Load Templates';
$lang['template_management'] = 'Template Management';

// campaigns
$lang['campaign'] = 'Campaign';
$lang['campaigns'] = 'Campaigns';
$lang['send_new_campaign'] = 'Send New Campaign';
$lang['campaign_name'] = 'Campaign Name';
$lang['template'] = 'Template';
$lang['scheduled_send_time'] = '<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="Per client, based on the contact timezone" data-placement="left"></i>Scheduled Send Time';
$lang['scheduled_time_description'] = 'Per client, based on the contact timezone';
$lang['ignore_scheduled_time_and_send_now'] = 'Ignore scheduled time and send now';
$lang['template'] = 'Template';
$lang['leads'] = 'Leads';
$lang['delivered_to'] = 'Delivered To';
$lang['read_by'] = 'Read by';
$lang['variables'] = 'Variables';
$lang['body'] = 'Body';
$lang['variable'] = 'Variable';
$lang['match_with_selected_field'] = 'Match with a selected field';
$lang['preview'] = 'Preview';
$lang['send_campaign'] = 'Send campaign';
$lang['send_to'] = 'Send to';
$lang['send_campaign'] = 'Send Campaign';
$lang['view_campaign'] = 'View Campaign';
$lang['campaign_daily_task'] = 'Campaign daily task';
$lang['back'] = 'Back';
$lang['phone'] = 'Phone';
$lang['message'] = 'Message';
$lang['currently_type_not_supported'] = 'Currently <strong> %s </strong> template type is not supported!';
$lang['of_your'] = 'of your ';
$lang['contacts'] = 'Contacts';
$lang['select_all_leads'] = 'Select all Leads';
$lang['select_all_note_leads'] = 'If you select this, all future leads are included in this campaign.';
$lang['select_all_note_contacts'] = 'If you select this, all future contacts are included in this campaign.';

$lang['verified_name'] = 'Verified Name';
$lang['mark_as_default'] = 'Mark as default';
$lang['default_number_updated'] = 'Default phone number id updated successfully';
$lang['currently_using_this_number'] = 'Currently using this number';
$lang['leads'] = 'Leads';
$lang['pause_campaign'] = 'Pause Campaign';
$lang['resume_campaign'] = 'Resume Campaign';
$lang['campaign_resumed'] = 'Campaign resumed';
$lang['campaign_paused'] = 'Campaign paused';

//Template
$lang['body_data'] = 'Body Data';
$lang['category'] = 'Category';

// Template bot
$lang['create_new_template_bot'] = 'Create new template bot';
$lang['template_bot'] = 'Template Bot';
$lang['variables'] = 'Variables';
$lang['preview'] = 'Preview';
$lang['template'] = 'Template';
$lang['bot_content_1'] = 'This message will be sent to the contact once the trigger rule is met in the message sent by the contact.';
$lang['save_bot'] = 'Save bot';
$lang['please_select_template'] = 'Please select a template';
$lang['use_manually_define_value'] = 'Use manually define value';
$lang['merge_fields'] = 'Merge Fields';
$lang['template_bot_create_successfully'] = 'Template bot created successfully';
$lang['template_bot_update_successfully'] = 'Template bot updated successfully';
$lang['text_bot'] = 'Text bot';
$lang['option_2_bot_with_link'] = 'Option 2: Bot with button link - Call to Action (CTA) URL';
$lang['option_3_file'] = 'Option 3: Bot with file';
// Bot settings
$lang['bot'] = 'Bot';
$lang['bot_delay_response'] = 'Message send when delay in response is expected';
$lang['bot_delay_response_placeholder'] = 'Give me a moment, I will have the answer shortly';

$lang['whatsbot'] = 'WhatsBot';

//campaigns
$lang['relation_type'] = 'Relation Type';
$lang['select_all'] = 'Select all';
$lang['total'] = 'Total';
$lang['merge_field_note'] = 'Use \'@\' Sign for add merge fields.';
$lang['send_to_all'] = 'Send to All ';
$lang['or'] = 'OR';

$lang['convert_whatsapp_message_to_lead'] = 'Acquire New Lead Automatically(convert new whatsapp messages to lead)';
$lang['leads_status'] = 'Lead status';
$lang['leads_assigned'] = 'Lead assigned';
$lang['whatsapp_auto_lead'] = 'Whatsapp Auto Lead';
$lang['webhooks_label'] = 'Whatsapp received data will be resend to';
$lang['webhooks'] = 'WebHooks';
$lang['enable_webhooks'] = 'Enable WebHooks Re-send';
$lang['chat'] = 'Chat';
$lang['black_listed_phone_numbers'] = 'Black listed phone numbers';
$lang['sent_status'] = 'Sent Status';

$lang['active'] = 'Active';
$lang['approved'] = 'Approved';
$lang['this_month'] = 'this month';
$lang['open_chats'] = 'Open Chats';
$lang['resolved_conversations'] = 'Resolved Conversations';
$lang['messages_sent'] = 'Messages sent';
$lang['account_connected'] = 'Account connected';
$lang['account_disconnected'] = 'Account disconnected';
$lang['webhook_verify_token'] = 'Webhook verify token';
// Chat integration
$lang['chat_message_note'] = 'Message will be send shortly. Please note that if new contact, it will not appear in this list untill the contact start interacting with you!';

$lang['activity_log'] = 'Activity Log';
$lang['whatsapp_logs'] = 'Whatsapp Logs';
$lang['response_code'] = 'Response Code';
$lang['recorded_on'] = 'Recorded On';

$lang['request_details'] = 'Request Details';
$lang['raw_content'] = 'Raw Content';
$lang['headers'] = 'Headers';
$lang['format_type'] = 'Format Type';

// Permission section
$lang['show_campaign'] = 'Show campaign';
$lang['clear_log'] = 'Clear Log';
$lang['log_activity'] = 'Log Activity';
$lang['load_template'] = 'Load Template';

$lang['action'] = 'Action';
$lang['total_parameters'] = 'Total Parameters';
$lang['template_name'] = 'Template Name';
$lang['log_cleared_successfully'] = 'Log cleared successfully';
$lang['whatsbot_stats'] = 'WhatsBot Stats';

$lang['not_found_or_deleted'] = 'Not found or deleted';
$lang['response'] = 'Response';

$lang['select_image'] = 'Select image';
$lang['image'] = 'Image';
$lang['image_deleted_successfully'] = 'Image deleted successfully';
$lang['whatsbot_settings'] = 'Whatsbot Settings';
$lang['maximum_file_size_should_be'] = 'Maximum file size should be ';
$lang['allowed_file_types'] = 'Allowed file types : ';

$lang['send_image'] = 'Send Image';
$lang['send_video'] = 'Send Video';
$lang['send_document'] = 'Send Document';
$lang['record_audio'] = 'Record Audio';
$lang['chat_media_info'] = 'More info for Supported Content-Types & Post-Processing Media Size';
$lang['help'] = 'Help';

// v1.1.0
$lang['clone'] = 'Clone';
$lang['bot_clone_successfully'] = 'Bot clone successfully';
$lang['all_chat'] = 'All Chats';
$lang['from'] = 'From:';
$lang['phone_no'] = 'Phone No:';
$lang['supportagent'] = 'Support Agent';
$lang['assign_chat_permission_to_support_agent'] = 'Assign chat permission to support agent only';
$lang['enable_whatsapp_notification_sound'] = 'Enable whatsapp chat notification sound';
$lang['notification_sound'] = 'Notification Sound';
$lang['trigger_keyword'] = 'Trigger Keyword';
$lang['modal_title'] = 'Select Support Agent';
$lang['close_btn'] = 'Close';
$lang['save_btn'] = 'Save';
$lang['support_agent'] = 'Support Agent';
$lang['change_support_agent'] = 'Change Support Agent';
$lang['replay_message'] = 'You can\'t send message 24 hours is over.';
$lang['support_agent_note'] = '<strong>Note: </strong>When you enable the support agent feature, the lead assignee will automatically be assigned to the chat. Admins can also assign a new agent from the chat page.';
$lang['permission_bot_clone'] = 'Clone Bot';
$lang['remove_chat'] = 'Remove Chat';
$lang['default_message_on_no_match'] = 'Default Reply - if any keyword does not match';
$lang['default_message_note'] = '<strong>Note: </strong>Enabling this option will increase your webhook load. For more info visit this <a href="https://docs.corbitaltech.dev/products/whatsbot/index.html" target="_blank">link</a>.';

$lang['whatsbot_connect_account'] = 'Whatsbot Connect Account';
$lang['whatsbot_message_bot'] = 'Whatsbot Message Bot';
$lang['whatsbot_template_bot'] = 'Whatsbot Template Bot';
$lang['whatsbot_template'] = 'Whatsbot Template';
$lang['whatsbot_campaigns'] = 'Whatsbot Campaigns';
$lang['whatsbot_chat'] = 'Whatsbot Chat';
$lang['whatsbot_log_activity'] = 'Whatsbot Log Activity';
