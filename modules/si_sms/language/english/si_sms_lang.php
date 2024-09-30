<?php
# Version 1.0.0
$lang['si_sms']		= 'Add-on SMS Manager';
$lang['si_sms_custom_send'] = 'Add-on Custom SMS';#permissions
$lang['si_sms_schedule_send'] = 'Add-on Schedule SMS';#permissions
#menu
$lang['si_sms_menu'] = 'Add-on SMS';
$lang['si_sms_custom_send_menu'] = 'Send Custom SMS';
$lang['si_sms_templates_menu'] = 'SMS Templates';
$lang['si_sms_schedule_send_menu'] = 'Schedule SMS';
#texts for pages
$lang['si_sms_custom_send_title'] = 'Send Custom SMS';
$lang['si_sms_schedule_send_title'] = 'List of Scheduled SMS';
$lang['si_sms_send_to'] = 'Select Options to Send to';
$lang['si_sms_templates'] = 'SMS Templates';
$lang['si_sms_template_name'] = 'Template name';
$lang['si_sms_is_public'] = 'Show Template to All Staff';
$lang['si_sms_text'] = 'SMS Text';
$lang['si_sms_alphanumeric_validation'] = 'Letters, numbers, and underscores only please';
$lang['si_sms_add'] = 'Add';
$lang['si_sms_sent_message'] = 'SMS Sent';
$lang['si_sms_sent_error_message'] = 'No contacts found having valid phone number to send SMS';
$lang['si_sms_schedule_add'] = 'Add Scheduled SMS';
$lang['si_sms_schedule_date'] = 'Schedule Date';
$lang['si_sms_schedule_datetime'] = 'Schedule Date and Time';
$lang['si_sms_schedule_executed'] = 'Executed';
$lang['si_sms_schedule_pnone_info'] = 'Note: SMS will be sent if the phonenumber is added';
$lang['si_sms_schedule_error_message'] = 'There is some issue in saving the Schedule SMS';
$lang['si_sms_schedule_success_activity_log_text'] 	= '%s Scheduled SMS Executed';
#settings
$lang['si_sms_settings'] = 'Add-on SMS Settings';#settings
$lang['si_sms_settings_tab1'] = "Add-on SMS Settings";
$lang['si_sms_settings_primary'] = "Primary Contact";
$lang['si_sms_settings_all'] = "All Contacts";
$lang['si_sms_settings_client'] = "Client Only";
$lang['si_sms_settings_validate'] = "Validate";
$lang['si_sms_settings_purchase_code_help'] = "You have received purchase code in your email, while you purchased this module. Kindly add that code here and validate to activate this module.";
$lang['si_sms_settings_exclude_status_change'] = 'Select statuses for which you do not want to trigger SMS, when status is changed.';
#settings db fields
$lang['si_sms_settings_send_to_customer'] = 'Send Custom SMS to Customer\'s contacts';
$lang['si_sms_settings_send_to_alt_client'] = 'Send Add-on Trigger SMS to Client\'s number, if primary contact\'s number is not available';
$lang['si_sms_settings_activation_code'] = 'Enter Module Licence Purchase Code';
$lang['si_sms_settings_project_status_exclude'] = 'Exclude Project Status';
$lang['si_sms_settings_task_status_exclude'] = 'Exclude Task Status';
$lang['si_sms_settings_invoice_status_exclude'] = 'Exclude Invoice Status';
$lang['si_sms_settings_lead_status_exclude'] = 'Exclude Lead Status';
$lang['si_sms_settings_ticket_status_exclude'] = 'Exclude Ticket Status';
$lang['si_sms_settings_clear_schedule_sms_log_after_days'] = 'Delete Scheduled SMS executed Log after days';
$lang['si_sms_settings_clear_schedule_sms_log_after_days_info'] = 'Set 0 if you do not want to delete executed SMS Log';
#trigger names
$lang['si_sms_tag']		= ' <a href="'.admin_url('settings?group=si_sms_settings').'"><span class="badge bg-warning">Add-on SMS</span></a>';
$lang['si_sms_label_project_created'] = 'New Project Created (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_project_created'] = 'Trigger when New Project is Created for Client, SMS will be sent customer Primary contact (or to Client).';
$lang['si_sms_label_invoice_created'] = 'New Invoice Created (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_invoice_created'] = 'Trigger when New Invoice is Created for Client, SMS will be sent customer Primary contact (or to Client).';
$lang['si_sms_label_proposal_created'] = 'New Proposal Created (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_proposal_created'] = 'Trigger when New Proposal is Created for Client, SMS will be sent phone number mentioned in Proposal';
$lang['si_sms_label_estimate_created'] = 'New Estimate Created (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_estimate_created'] = 'Trigger when New Estimate is Created for Client, SMS will be sent customer Primary contact (or to Client).';
$lang['si_sms_label_contract_created'] = 'New Contract Created (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_contract_created'] = 'Trigger when New Contract is Created for Client, SMS will be sent customer Primary contact (or to Client).';
$lang['si_sms_label_ticket_created'] = 'New Support Ticket Created (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_ticket_created'] = 'Trigger when New Support Ticket is Created for Client, SMS will be sent customer contact selected in Ticket.';
$lang['si_sms_label_credit_note_created'] = 'New Credit Note Created (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_credit_note_created'] = 'Trigger when New Credit Note is Created for Client, SMS will be sent customer Primary contact (or to Client).';
$lang['si_sms_label_lead_created'] = 'New Lead Created (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_lead_created'] = 'Trigger when New Lead is Created for Client, SMS will be sent to mobile number added in lead.';
$lang['si_sms_label_project_status_changed'] = 'Project Status Changed (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_project_status_changed'] = 'Trigger when Project status is changed for Client, SMS will be sent customer Primary contact (or to Client).';
$lang['si_sms_label_task_status_changed'] = 'Task Status Changed (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_task_status_changed'] = 'Trigger when Task status is changed for Client, SMS will be sent customer Primary contact (or to Client).';
$lang['si_sms_label_invoice_status_changed'] = 'Invoice Status Changed (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_invoice_status_changed'] = 'Trigger when Invoice status is changed for Client, SMS will be sent customer Primary contact (or to Client).';
$lang['si_sms_label_lead_status_changed'] = 'Lead Status Changed (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_lead_status_changed'] = 'Trigger when Lead status is changed for Client, SMS will be sent to mobile number added in lead.';
$lang['si_sms_label_ticket_status_changed'] = 'Support Ticket Status Changed (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_ticket_status_changed'] = 'Trigger when Support Ticket status is changed for Client, SMS will be sent customer Primary contact (or to Client).';
# Version 1.0.3
$lang['si_sms_label_proposal_accepted'] = 'Proposal Accepted (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_proposal_accepted'] = 'Trigger when Proposal status is changed to Accepted by Client, SMS will be sent to contact number saved in proposal.';
$lang['si_sms_label_proposal_declined'] = 'Proposal Declined (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_proposal_declined'] = 'Trigger when Proposal status is changed to Declined by Client, SMS will be sent to contact number saved in proposal.';
$lang['si_sms_label_proposal_accepted_to_staff'] = 'Proposal Accepted (to staff)'.$lang['si_sms_tag'];
$lang['si_sms_info_proposal_accepted_to_staff'] = 'Trigger when Proposal status is changed to Accepted by Client, SMS will be sent to staff.';
$lang['si_sms_label_proposal_declined_to_staff'] = 'Proposal Declined (to staff)'.$lang['si_sms_tag'];
$lang['si_sms_info_proposal_declined_to_staff'] = 'Trigger when Proposal status is changed to Declined by Client, SMS will be sent to staff.';
$lang['si_sms_label_estimate_accepted'] = 'Estimate Accepted (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_estimate_accepted'] = 'Trigger when Estimate status is changed to Accepted for Client, SMS will be sent customer Primary contact (or to Client).';
$lang['si_sms_label_estimate_declined'] = 'Estimate Declined (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_estimate_declined'] = 'Trigger when Estimate status is changed to Declined for Client, SMS will be sent customer Primary contact (or to Client).';
$lang['si_sms_label_estimate_accepted_to_staff'] = 'Estimate Accepted (to staff)'.$lang['si_sms_tag'];
$lang['si_sms_info_estimate_accepted_to_staff'] = 'Trigger when Estimate status is changed to Accepted by Client, SMS will be sent to staff.';
$lang['si_sms_label_estimate_declined_to_staff'] = 'Estimate Declined (to staff)'.$lang['si_sms_tag'];
$lang['si_sms_info_estimate_declined_to_staff'] = 'Trigger when Estimate status is changed to Declined by Client, SMS will be sent to staff.';
$lang['si_sms_label_contact_created'] = 'Client Contact Created (to customer)'.$lang['si_sms_tag'];
$lang['si_sms_info_contact_created'] = 'Trigger when Contact is created for Client, SMS will be sent to contact.';
