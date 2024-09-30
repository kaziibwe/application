<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Add-on SMS Manager
Description: Module provides new SMS Triggers and gives custom SMS send functionality and add Scheduled SMS. This module do not affect on any default SMS triggers.
Author: Sejal Infotech
Version: 1.0.6
Requires at least: 2.3.*
Author URI: http://www.sejalinfotech.com
*/

define('SI_SMS_MODULE_NAME', 'si_sms');
define('VALIDATION_URL','http://www.sejalinfotech.com/perfex_validation/index.php');
define('SI_SMS_KEY','c2lfc21z');

define('SI_SMS_TRIGGER_PROJECT_CREATED', 'si_sms_project_created');
define('SI_SMS_TRIGGER_INVOICE_CREATED', 'si_sms_invoice_created');
define('SI_SMS_TRIGGER_PROPOSAL_CREATED', 'si_sms_proposal_created');
define('SI_SMS_TRIGGER_ESTIMATE_CREATED', 'si_sms_estimate_created');
define('SI_SMS_TRIGGER_CONTRACT_CREATED', 'si_sms_contract_created');
define('SI_SMS_TRIGGER_TICKET_CREATED', 'si_sms_ticket_created');
define('SI_SMS_TRIGGER_CREDIT_NOTE_CREATED','si_sms_credit_note_created');
define('SI_SMS_TRIGGER_LEAD_CREATED','si_sms_lead_created');
define('SI_SMS_TRIGGER_PROJECT_STATUS_CHANGED','si_sms_project_status_changed');
define('SI_SMS_TRIGGER_TASK_STATUS_CHANGED','si_sms_task_status_changed');
define('SI_SMS_TRIGGER_INVOICE_STATUS_CHANGED','si_sms_invoice_status_changed');
define('SI_SMS_TRIGGER_LEAD_STATUS_CHANGED','si_sms_lead_status_changed');
define('SI_SMS_TRIGGER_TICKET_STATUS_CHANGED','si_sms_ticket_status_changed');
define('SI_SMS_TRIGGER_PROPOSAL_ACCEPTED','si_sms_proposal_accepted');
define('SI_SMS_TRIGGER_PROPOSAL_DECLINED','si_sms_proposal_declined');
define('SI_SMS_TRIGGER_PROPOSAL_ACCEPTED_TO_STAFF','si_sms_proposal_accepted_to_staff');
define('SI_SMS_TRIGGER_PROPOSAL_DECLINED_TO_STAFF','si_sms_proposal_declined_to_staff');
define('SI_SMS_TRIGGER_ESTIMATE_ACCEPTED','si_sms_estimate_accepted');
define('SI_SMS_TRIGGER_ESTIMATE_DECLINED','si_sms_estimate_declined');
define('SI_SMS_TRIGGER_ESTIMATE_ACCEPTED_TO_STAFF','si_sms_estimate_accepted_to_staff');
define('SI_SMS_TRIGGER_ESTIMATE_DECLINED_TO_STAFF','si_sms_estimate_declined_to_staff');
define('SI_SMS_TRIGGER_CONTACT_CREATED','si_sms_contact_created');

$CI = &get_instance();

hooks()->add_action('admin_init', 'si_sms_hook_admin_init');
hooks()->add_filter('module_'.SI_SMS_MODULE_NAME.'_action_links', 'module_si_sms_action_links');
hooks()->add_action('settings_tab_footer','si_sms_hook_settings_tab_footer');#for perfex low version V2.4 
hooks()->add_action('settings_group_end','si_sms_hook_settings_tab_footer');#for perfex high version V2.8.4
hooks()->add_filter('before_settings_updated','si_sms_hook_before_settings_updated');
if(get_option(SI_SMS_MODULE_NAME.'_activated') && get_option(SI_SMS_MODULE_NAME.'_activation_code')!=''){
hooks()->add_filter('sms_gateway_available_triggers','si_sms_hook_sms_gateway_available_triggers');
hooks()->add_action('after_add_project','si_sms_hook_after_add_project');
hooks()->add_action('after_invoice_added','si_sms_hook_after_invoice_added');
hooks()->add_action('proposal_created','si_sms_hook_proposal_created');
hooks()->add_action('after_estimate_added','si_sms_hook_after_estimate_added');
hooks()->add_action('after_contract_added','si_sms_hook_after_contract_added');
hooks()->add_action('ticket_created','si_sms_hook_ticket_created');
hooks()->add_action('after_create_credit_note','si_sms_hook_after_create_credit_note');
hooks()->add_action('lead_created','si_sms_hook_lead_created');
hooks()->add_action('project_status_changed','si_sms_hook_project_status_changed');
hooks()->add_action('task_status_changed','si_sms_hook_task_status_changed');
hooks()->add_action('invoice_status_changed','si_sms_hook_invoice_status_changed');
hooks()->add_action('lead_status_changed','si_sms_hook_lead_status_changed');
hooks()->add_action('after_ticket_status_changed','si_sms_hook_after_ticket_status_changed');
hooks()->add_action('proposal_accepted','si_sms_hook_proposal_accepted_declined');
hooks()->add_action('proposal_declined','si_sms_hook_proposal_accepted_declined');
hooks()->add_action('estimate_accepted','si_sms_hook_estimate_accepted_declined');
hooks()->add_action('estimate_declined','si_sms_hook_estimate_accepted_declined');
hooks()->add_action('contact_created','si_sms_hook_contact_created');
}

/**
* Add additional settings for this module in the module list area
* @param  array $actions current actions
* @return array
*/
function module_si_sms_action_links($actions)
{
	if(get_option(SI_SMS_MODULE_NAME.'_activated') && get_option(SI_SMS_MODULE_NAME.'_activation_code')!=''){
		$actions[] = '<a href="' . admin_url('settings?group=sms') . '">SMS</a>';
		$actions[] = '<a href="' . admin_url('settings?group=si_sms_settings') . '">' . _l('settings') . '</a>';
	}
	else
		$actions[] = '<a href="' . admin_url('settings?group=si_sms_settings') . '">' . _l('si_sms_settings_validate') . '</a>';
	return $actions;
}

function si_sms_hook_settings_tab_footer($tab)
{
	if($tab['slug']=='si_sms_settings' && !get_option(SI_SMS_MODULE_NAME.'_activated')){
		echo '<script src="'.module_dir_url('si_sms','assets/js/si_sms_settings_footer.js').'"></script>';
	}
}
/**
* Load the module model
*/
$CI->load->model(SI_SMS_MODULE_NAME . '/si_sms_model');
$CI->load->model('invoices_model');
/**
* Load the module model
*/
$CI->load->helper(SI_SMS_MODULE_NAME . '/si_sms');

/**
* Register activation module hook
*/
register_activation_hook(SI_SMS_MODULE_NAME, 'si_sms_activation_hook');

function si_sms_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SI_SMS_MODULE_NAME, [SI_SMS_MODULE_NAME]);
/**
* Register cron run
*/
register_cron_task('si_sms_hook_after_cron_run');

/**
*	Admin Init Hook for module
*/
function si_sms_hook_admin_init()
{
	/*Add customer permissions */
	$capabilities = [];
	$capabilities['capabilities'] = [
		'create'   => _l('permission_create'),
	];
	register_staff_capabilities('si_sms_custom_send', $capabilities, _l('si_sms_custom_send'));
	$capabilities['capabilities'] = [
		'view_own' => _l('permission_view_own'),
        'view'     => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create'   => _l('permission_create'),
        'edit'     => _l('permission_edit'),
        'delete'   => _l('permission_delete'),
	];
	register_staff_capabilities('si_sms_schedule_send', $capabilities, _l('si_sms_schedule_send'));
	
	$CI = &get_instance();
	/**  Add Tab In Settings Tab of Setup **/
	if (is_admin() || has_permission('settings', '', 'view')) {
		$CI->app_tabs->add_settings_tab('si_sms_settings', [
			'name'     => _l('si_sms_settings'),
			'view'     => 'si_sms/si_sms_settings',
			'position' => 60,
			'icon'     => 'fa fa-message',//supported from Perfex V 3.0
		]);
	}
	if(get_option(SI_SMS_MODULE_NAME.'_activated') && get_option(SI_SMS_MODULE_NAME.'_activation_code')!=''){
		/** Add Menu for Custom SMS**/
		if (is_admin() || has_permission('si_sms_custom_send', '', 'create') || has_permission('si_sms_schedule_send', '', 'view') || has_permission('si_sms_schedule_send', '', 'view_own')) {
			$CI->app_menu->add_sidebar_menu_item('si_sms_menu', [
				'collapse' => true,
				'icon'     => 'fa fa-comment-o fa-message',
				'name'     => _l('si_sms_menu'),
				'position' => 35,
			]);
			if(has_permission('si_sms_custom_send', '', 'create')){
				$CI->app_menu->add_sidebar_children_item('si_sms_menu', [
					'slug'     => 'si-sms-custom-send-menu',
					'name'     => _l('si_sms_custom_send_menu'),
					'href'     => admin_url('si_sms/custom_sms'),
					'position' => 1,
				]);
			}
			$CI->app_menu->add_sidebar_children_item('si_sms_menu', [
				'slug'     => 'si-sms-templates-menu',
				'name'     => _l('si_sms_templates_menu'),
				'href'     => admin_url('si_sms/list_templates'),
				'position' => 2,
			]);
			if(has_permission('si_sms_schedule_send', '', 'view') || has_permission('si_sms_schedule_send', '', 'view_own')){
				$CI->app_menu->add_sidebar_children_item('si_sms_menu', [
					'slug'     => 'si-sms-schedule-menu',
					'name'     => _l('si_sms_schedule_send_menu'),
					'href'     => admin_url('si_sms/schedule_sms'),
					'position' => 3,
				]);
			}
		}
	}
}

/** hook for before settings saved**/
function si_sms_hook_before_settings_updated($data)
{

	if(isset($data['settings']) && array_key_exists('si_sms_send_to_customer',$data['settings'])){
		$status_excludes = array('si_sms_project_status_exclude', 
							'si_sms_task_status_exclude',
							'si_sms_invoice_status_exclude',
							'si_sms_lead_status_exclude',
							'si_sms_ticket_status_exclude',
							);
		foreach($status_excludes as $key){					
			if(array_key_exists($key,$data['settings']))
				$data['settings'][$key] = serialize($data['settings'][$key]);
			else
				$data['settings'][$key] = serialize([]);	
		}
	}
	return $data;	
}
/**Hook to add sms triggers**/
function si_sms_hook_sms_gateway_available_triggers($triggers)
{
	$customer_merge_fields = [
		'{contact_firstname}',
		'{contact_lastname}',
		'{client_company}',
		'{client_id}',
	];
	$project_merge_fields = [
		'{project_name}',
		'{project_id}',
		'{project_status}',
	];
	$invoice_merge_fields = [
		'{invoice_number}',
		'{invoice_link}',
		'{invoice_date}',
		'{invoice_subtotal}',
		'{invoice_total}',
		'{invoice_short_url}',
		'{invoice_status}',
	];
	$proposal_merge_fields = [
		'{proposal_number}',
		'{proposal_id}',
		'{proposal_subject}',
		'{proposal_total}',
		'{proposal_open_till}',
		'{proposal_subtotal}',
		'{proposal_proposal_to}',
		'{proposal_link}',
		'{proposal_short_url}',
	];
	$estimate_merge_fields = [
		'{estimate_number}',
		'{estimate_date}',
		'{estimate_subtotal}',
		'{estimate_total}',
		'{estimate_link}',
		'{estimate_status}',
		'{estimate_short_url}',
	];
	$contract_merge_fields = [
		'{contract_id}',
		'{contract_subject}',
		'{contract_datestart}',
		'{contract_dateend}',
		'{contract_contract_value}',
		'{contract_link}',
		'{contract_short_url}',
	];
	$ticket_merge_fields = [
		'{contact_firstname}',
		'{ticket_date}',
		'{ticket_subject}',
		'{ticket_status}',
		'{ticket_priority}',
		'{ticket_service}',
		'{ticket_department}',
		'{ticket_department_email}',
		'{ticket_url}',
		'{ticket_public_url}',
	];
	$credit_note_merge_fields = [
		'{credit_note_number}',
		'{credit_note_subtotal}',
		'{credit_note_total}',
		'{credit_note_date}',
		'{credit_note_credits_remaining}',
		'{credit_note_credits_used}',
		'{credit_note_status}',
	];
	$task_merge_fields = [];
	$lead_merge_fields = [
		'{lead_name}',
		'{lead_email}',
		'{lead_description}',
		'{lead_position}',
		'{lead_phonenumber}',
		'{lead_company}',
		'{lead_zip}',
		'{lead_city}',
		'{lead_state}',
		'{lead_country}',
		'{lead_address}',
		'{lead_website}',
		'{lead_assigned}',
		'{lead_status}',
		'{lead_source}',
		'{lead_public_form_url}',
	];
	$contact_merge_fields = [
		'{contact_firstname}',
		'{contact_lastname}',
		'{contact_email}',
		'{contact_phonenumber}',
		'{contact_title}',
		'{client_company}',
		'{client_phonenumber}',
		'{client_country}',
		'{client_city}',
		'{client_zip}',
		'{client_state}',
		'{client_address}',
		'{client_vat_number}',
		'{client_id}',
	];
	$triggers[SI_SMS_TRIGGER_PROJECT_CREATED] = [
								'merge_fields' => array_merge($customer_merge_fields,$project_merge_fields),
								'label' => _l('si_sms_label_project_created'),
								'info'  => _l('si_sms_info_project_created'),
	];
	$triggers[SI_SMS_TRIGGER_INVOICE_CREATED] = [
								'merge_fields' => array_merge($customer_merge_fields,$invoice_merge_fields),
								'label' => _l('si_sms_label_invoice_created'),
								'info'  => _l('si_sms_info_invoice_created'),
	];
	$triggers[SI_SMS_TRIGGER_PROPOSAL_CREATED] = [
								'merge_fields' => $proposal_merge_fields,
								'label' => _l('si_sms_label_proposal_created'),
								'info'  => _l('si_sms_info_proposal_created'),
	];
	$triggers[SI_SMS_TRIGGER_ESTIMATE_CREATED] = [
								'merge_fields' => array_merge($customer_merge_fields,$estimate_merge_fields),
								'label' => _l('si_sms_label_estimate_created'),
								'info'  => _l('si_sms_info_estimate_created'),
	];
	$triggers[SI_SMS_TRIGGER_CONTRACT_CREATED] = [
								'merge_fields' => array_merge($customer_merge_fields,$contract_merge_fields),
								'label' => _l('si_sms_label_contract_created'),
								'info'  => _l('si_sms_info_contract_created'),
	];
	$triggers[SI_SMS_TRIGGER_TICKET_CREATED] = [
								'merge_fields' => $ticket_merge_fields,
								'label' => _l('si_sms_label_ticket_created'),
								'info'  => _l('si_sms_info_ticket_created'),
	];
	$triggers[SI_SMS_TRIGGER_CREDIT_NOTE_CREATED] = [
								'merge_fields' => array_merge($customer_merge_fields,$credit_note_merge_fields),
								'label' => _l('si_sms_label_credit_note_created'),
								'info'  => _l('si_sms_info_credit_note_created'),
	];
	$triggers[SI_SMS_TRIGGER_LEAD_CREATED] = [
								'merge_fields' => $lead_merge_fields,
								'label' => _l('si_sms_label_lead_created'),
								'info'  => _l('si_sms_info_lead_created'),
	];
	$triggers[SI_SMS_TRIGGER_PROJECT_STATUS_CHANGED] = [
								'merge_fields' => array_merge($customer_merge_fields,$project_merge_fields),
								'label' => _l('si_sms_label_project_status_changed'),
								'info'  => _l('si_sms_info_project_status_changed'),
	];
	#$triggers[SI_SMS_TRIGGER_TASK_STATUS_CHANGED] = [
	#							'merge_fields' => array_merge($customer_merge_fields,$task_merge_fields),
	#							'label' => _l('si_sms_label_task_status_changed'),
	#							'info'  => _l('si_sms_info_task_status_changed'),
	#];
	$triggers[SI_SMS_TRIGGER_INVOICE_STATUS_CHANGED] = [
								'merge_fields' => array_merge($customer_merge_fields,$invoice_merge_fields),
								'label' => _l('si_sms_label_invoice_status_changed'),
								'info'  => _l('si_sms_info_invoice_status_changed'),
	];
	$triggers[SI_SMS_TRIGGER_LEAD_STATUS_CHANGED] = [
								'merge_fields' => $lead_merge_fields,
								'label' => _l('si_sms_label_lead_status_changed'),
								'info'  => _l('si_sms_info_lead_status_changed'),
	];
	$triggers[SI_SMS_TRIGGER_TICKET_STATUS_CHANGED] = [
								'merge_fields' => $ticket_merge_fields,
								'label' => _l('si_sms_label_ticket_status_changed'),
								'info'  => _l('si_sms_info_ticket_status_changed'),
	];
	$triggers[SI_SMS_TRIGGER_PROPOSAL_ACCEPTED] = [
								'merge_fields' => $proposal_merge_fields,
								'label' => _l('si_sms_label_proposal_accepted'),
								'info'  => _l('si_sms_info_proposal_accepted'),
	];
	$triggers[SI_SMS_TRIGGER_PROPOSAL_DECLINED] = [
								'merge_fields' => $proposal_merge_fields,
								'label' => _l('si_sms_label_proposal_declined'),
								'info'  => _l('si_sms_info_proposal_declined'),
	];
	$triggers[SI_SMS_TRIGGER_PROPOSAL_ACCEPTED_TO_STAFF] = [
								'merge_fields' => $proposal_merge_fields,
								'label' => _l('si_sms_label_proposal_accepted_to_staff'),
								'info'  => _l('si_sms_info_proposal_accepted_to_staff'),
	];
	$triggers[SI_SMS_TRIGGER_PROPOSAL_DECLINED_TO_STAFF] = [
								'merge_fields' => $proposal_merge_fields,
								'label' => _l('si_sms_label_proposal_declined_to_staff'),
								'info'  => _l('si_sms_info_proposal_declined_to_staff'),
	];
	$triggers[SI_SMS_TRIGGER_ESTIMATE_ACCEPTED] = [
								'merge_fields' => array_merge($customer_merge_fields,$estimate_merge_fields),
								'label' => _l('si_sms_label_estimate_accepted'),
								'info'  => _l('si_sms_info_estimate_accepted'),
	];
	$triggers[SI_SMS_TRIGGER_ESTIMATE_DECLINED] = [
								'merge_fields' => array_merge($customer_merge_fields,$estimate_merge_fields),
								'label' => _l('si_sms_label_estimate_declined'),
								'info'  => _l('si_sms_info_estimate_declined'),
	];
	$triggers[SI_SMS_TRIGGER_ESTIMATE_ACCEPTED_TO_STAFF] = [
								'merge_fields' => array_merge($customer_merge_fields,$estimate_merge_fields),
								'label' => _l('si_sms_label_estimate_accepted_to_staff'),
								'info'  => _l('si_sms_info_estimate_accepted_to_staff'),
	];
	$triggers[SI_SMS_TRIGGER_ESTIMATE_DECLINED_TO_STAFF] = [
								'merge_fields' => array_merge($customer_merge_fields,$estimate_merge_fields),
								'label' => _l('si_sms_label_estimate_declined_to_staff'),
								'info'  => _l('si_sms_info_estimate_declined_to_staff'),
	];
	$triggers[SI_SMS_TRIGGER_CONTACT_CREATED] = [
								'merge_fields' => $contact_merge_fields,
								'label' => _l('si_sms_label_contact_created'),
								'info'  => _l('si_sms_info_contact_created'),
	];
	return $triggers;
}
function si_sms_hook_after_add_project($project_id)
{
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($project_id)){
		$CI = &get_instance();
		$CI->db->select('clientid,name,status');
		$CI->db->where('id', $project_id);
		$project = $CI->db->get(db_prefix() . 'projects')->row();
		$status = get_project_status_by_id($project->status);
		$merge_fields = si_sms_get_customer_merge_fields($project->clientid);
		$merge_fields['{project_name}'] = $project->name;
		$merge_fields['{project_id}'] = $project_id;
		$merge_fields['{project_status}'] = $status['name'];
		if(isset($merge_fields['phone_number']))
			$phonenumber = $merge_fields['phone_number'];
		
		if(!is_null($phonenumber) && $phonenumber!==''){
			$CI->app_sms->trigger(SI_SMS_TRIGGER_PROJECT_CREATED, $phonenumber, $merge_fields);
		}	
	}
	return;
}

function si_sms_hook_after_invoice_added($invoice_id)
{
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($invoice_id)){
		$CI = &get_instance();
		$CI->db->where('id', $invoice_id);
		$invoice = $CI->db->get(db_prefix() . 'invoices')->row();
		$currency = get_currency($invoice->currency);
		$merge_fields = si_sms_get_customer_merge_fields($invoice->clientid);
		$merge_fields['{invoice_number}'] 	= format_invoice_number($invoice_id);
		$merge_fields['{invoice_link}'] 	= site_url('invoice/' . $invoice_id . '/' . $invoice->hash);
		$merge_fields['{invoice_date}'] 	= _d($invoice->date);
		$merge_fields['{invoice_subtotal}'] = app_format_money($invoice->subtotal, $currency);
		$merge_fields['{invoice_total}'] 	= app_format_money($invoice->total, $currency);
		$merge_fields['{invoice_status}']  = format_invoice_status($invoice->status, '', false);
		$merge_fields['{invoice_short_url}']= (function_exists('get_invoice_shortlink')?get_invoice_shortlink($invoice) : site_url('invoice/' . $invoice_id . '/' . $invoice->hash));##invoice_short_url available from Perfex Version 2.7.3
		
		if(isset($merge_fields['phone_number']))
			$phonenumber = $merge_fields['phone_number'];
		
		if(!is_null($phonenumber) && $phonenumber!==''){
			$CI->app_sms->trigger(SI_SMS_TRIGGER_INVOICE_CREATED, $phonenumber, $merge_fields);
		}	
	}
	return;
}

function si_sms_hook_proposal_created($proposal_id)
{
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($proposal_id)){
		$CI = &get_instance();
		$proposal = $CI->proposals_model->get($proposal_id);
		if (!$proposal) {
			return;
		}
		if ($proposal->currency != 0) {
			$currency = get_currency($proposal->currency);
		} else {
			$currency = get_base_currency();
		}
		if(!is_null($proposal->phone) && $proposal->phone!==''){
			$phonenumber = $proposal->phone;
			$merge_fields['{proposal_number}'] 		= format_proposal_number($proposal_id);
			$merge_fields['{proposal_id}'] 			= $proposal_id;
			$merge_fields['{proposal_subject}'] 	= $proposal->subject;
			$merge_fields['{proposal_open_till}'] 	= _d($proposal->open_till);
			$merge_fields['{proposal_subtotal}'] 	= app_format_money($proposal->subtotal, $currency);
			$merge_fields['{proposal_total}'] 		= app_format_money($proposal->total, $currency);;
			$merge_fields['{proposal_proposal_to}'] = $proposal->proposal_to;
			$merge_fields['{proposal_link}']        = site_url('proposal/' . $proposal_id . '/' . $proposal->hash);
			$merge_fields['{proposal_short_url}']= (function_exists('get_proposal_shortlink')?get_proposal_shortlink($proposal) : site_url('proposal/' . $proposal_id . '/' . $proposal->hash));##proposal_short_url available from Perfex Version 2.7.3
		
			$CI->app_sms->trigger(SI_SMS_TRIGGER_PROPOSAL_CREATED, $phonenumber, $merge_fields);
		}	
	}
	return;
}

function si_sms_hook_after_estimate_added($estimate_id)
{
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($estimate_id)){
		$CI = &get_instance();
		$estimate = $CI->estimates_model->get($estimate_id);
		if (!$estimate) {
			return;
		}
		$currency = get_currency($estimate->currency);
		$merge_fields = si_sms_get_customer_merge_fields($estimate->clientid);
		if(isset($merge_fields['phone_number']))
			$phonenumber = $merge_fields['phone_number'];
		
		if(!is_null($phonenumber) && $phonenumber!==''){
			$merge_fields['{estimate_total}']        = app_format_money($estimate->total, $currency);
			$merge_fields['{estimate_subtotal}']     = app_format_money($estimate->subtotal, $currency);
			$merge_fields['{estimate_link}']         = site_url('estimate/' . $estimate_id . '/' . $estimate->hash);
			$merge_fields['{estimate_number}']       = format_estimate_number($estimate_id);
			$merge_fields['{estimate_date}']         = _d($estimate->date);
			$merge_fields['{estimate_status}']       = format_estimate_status($estimate->status, '', false);
			$merge_fields['{estimate_short_url}']	 = (function_exists('get_estimate_shortlink')?get_estimate_shortlink($estimate) : site_url('estimate/' . $estimate_id . '/' . $estimate->hash));##estimate_short_url available from Perfex Version 2.7.3
			
			$CI->app_sms->trigger(SI_SMS_TRIGGER_ESTIMATE_CREATED, $phonenumber, $merge_fields);
		}
	}
	return;
}

function si_sms_hook_after_contract_added($contract_id)
{
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($contract_id)){
		$CI = &get_instance();
		$contract = $CI->contracts_model->get($contract_id);
		if (!$contract) {
			return;
		}
		$currency = get_base_currency();
		$merge_fields = si_sms_get_customer_merge_fields($contract->client);
		if(isset($merge_fields['phone_number']))
			$phonenumber = $merge_fields['phone_number'];
		
		if(!is_null($phonenumber) && $phonenumber!==''){
			$merge_fields['{contract_id}']        		= $contract->id;
			$merge_fields['{contract_subject}']        	= $contract->subject;
			$merge_fields['{contract_datestart}']      	= _d($contract->datestart);
			$merge_fields['{contract_dateend}']        	= _d($contract->dateend);
			$merge_fields['{contract_contract_value}'] 	= app_format_money($contract->contract_value, $currency);
			$merge_fields['{contract_link}'] = site_url('contract/' . $contract->id . '/' . $contract->hash);
			$merge_fields['{contract_short_url}']	 	= (function_exists('get_contract_shortlink')?get_contract_shortlink($contract) : site_url('contract/' . $contract->id . '/' . $contract->hash));##contract_short_url available from Perfex Version 2.7.3
			
			$CI->app_sms->trigger(SI_SMS_TRIGGER_CONTRACT_CREATED, $phonenumber, $merge_fields);
		}
	}
	return;
}
function si_sms_hook_ticket_created($ticket_id)
{
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($ticket_id)){
		$CI = &get_instance();
		$ticket = $CI->tickets_model->get($ticket_id,[db_prefix() . 'tickets.contactid > '=>0]);
		if (!$ticket) {
			return;
		}
		$contact = $CI->clients_model->get_contact($ticket->contactid);
		if ($contact) {
			if(!is_null($contact->phonenumber) && $contact->phonenumber!=='')
				$phonenumber = $contact->phonenumber;
			$merge_fields['{contact_firstname}'] = $contact->firstname;
		}
		
		if(!is_null($phonenumber) && $phonenumber!==''){
			$merge_fields['{ticket_date}']    = _dt($ticket->date);
			$merge_fields['{ticket_subject}'] = $ticket->subject;
			$merge_fields['{ticket_status}'] = ticket_status_translate($ticket->status);
			$merge_fields['{ticket_priority}'] = ticket_priority_translate($ticket->priority);
			
			$CI->db->where('departmentid', $ticket->department);
			$department = $CI->db->get(db_prefix().'departments')->row();
			if ($department) {
				$merge_fields['{ticket_department}']       = $department->name;
				$merge_fields['{ticket_department_email}'] = $department->email;
			}
			else {
				$merge_fields['{ticket_department}']       = '';
				$merge_fields['{ticket_department_email}'] = '';
			}
			$CI->db->where('serviceid', $ticket->service);
			$service = $CI->db->get(db_prefix().'services')->row();
			if ($service) {
				$merge_fields['{ticket_service}'] = $service->name;
			}
			else {
				$merge_fields['{ticket_service}'] = '';
			}
			$merge_fields['{ticket_url}'] = site_url('clients/ticket/' . $ticket_id);
			$merge_fields['{ticket_public_url}'] = (function_exists('get_ticket_public_url') ? get_ticket_public_url($ticket) : '');##ticket_public_url available from Perfex Version 2.4.2
			
			$CI->app_sms->trigger(SI_SMS_TRIGGER_TICKET_CREATED, $phonenumber, $merge_fields);
		}	
	}
	return;
}

function si_sms_hook_after_create_credit_note($credit_note_id)
{
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($credit_note_id)){
		$CI = &get_instance();
		$credit_note = $CI->credit_notes_model->get($credit_note_id);
		if (!$credit_note) {
			return;
		}
		$merge_fields = si_sms_get_customer_merge_fields($credit_note->clientid);
		if(isset($merge_fields['phone_number']))
			$phonenumber = $merge_fields['phone_number'];
		
		if(!is_null($phonenumber) && $phonenumber!==''){
			$merge_fields['{credit_note_number}']            = format_credit_note_number($credit_note_id);
			$merge_fields['{credit_note_total}']             = app_format_money($credit_note->total, $credit_note->currency_name);
			$merge_fields['{credit_note_subtotal}']          = app_format_money($credit_note->subtotal, $credit_note->currency_name);
			$merge_fields['{credit_note_credits_remaining}'] = app_format_money($credit_note->remaining_credits, $credit_note->currency_name);
			$merge_fields['{credit_note_credits_used}']      = app_format_money($credit_note->credits_used, $credit_note->currency_name);
			$merge_fields['{credit_note_date}']              = _d($credit_note->date);
			$merge_fields['{credit_note_status}']            = format_credit_note_status($credit_note->status, true);
			
			$CI->app_sms->trigger(SI_SMS_TRIGGER_CREDIT_NOTE_CREATED, $phonenumber, $merge_fields);
		}	
	}
	return;
}
/** hook for lead status changed**/
function si_sms_hook_lead_created($lead_id)
{
	#lead can come two ways. 
	#1. by creating lead from admin (integer lead_id will come)
	#2. by web to form lead generation ( array will come having $lead_id) 
	$lead_id = is_numeric($lead_id) ? $lead_id : (is_array($lead_id) ? $lead_id['lead_id'] : '');
	$merge_fields = array();
	$phonenumber = '';
	if (is_numeric($lead_id)) {
		$CI = &get_instance();
		$CI->db->where('id', $lead_id);
		$lead = $CI->db->get(db_prefix() . 'leads')->row();
		if(!is_null($lead->phonenumber) && $lead->phonenumber!=='')
		{
			$merge_fields['{lead_public_form_url}']    	= leads_public_url($lead->id);
			$merge_fields['{lead_name}']               	= $lead->name;
			$merge_fields['{lead_email}']              	= $lead->email;
			$merge_fields['{lead_position}']           	= $lead->title;
			$merge_fields['{lead_phonenumber}']        	= $lead->phonenumber;
			$merge_fields['{lead_company}']            	= $lead->company;
			$merge_fields['{lead_zip}']                	= $lead->zip;
			$merge_fields['{lead_city}']               	= $lead->city;
			$merge_fields['{lead_state}']              	= $lead->state;
			$merge_fields['{lead_address}']            	= $lead->address;
			$merge_fields['{lead_website}']            	= $lead->website;
			$merge_fields['{lead_description}']        	= $lead->description;
			$merge_fields['{lead_assigned}']			= '';
			$merge_fields['{lead_country}']				= '';
			$merge_fields['{lead_status}']				= '';
			$merge_fields['{lead_source}'] 				= '';
			
			if ($lead->assigned != 0) {
				$merge_fields['{lead_assigned}'] = get_staff_full_name($lead->assigned);
			}
			if ($lead->country != 0) {
				$country                  = get_country($lead->country);
				$merge_fields['{lead_country}'] = $country->short_name;
			}
			if ($lead->junk == 1) {
				$merge_fields['{lead_status}'] = _l('lead_junk');
			} elseif ($lead->lost == 1) {
				$merge_fields['{lead_status}'] = _l('lead_lost');
			} else {
				$CI->db->select('name');
				$CI->db->from(db_prefix().'leads_status');
				$CI->db->where('id', $lead->status);
				$status = $CI->db->get()->row();
				if ($status) {
					$merge_fields['{lead_status}'] = $status->name;
				}
			}
			$CI->db->select('name');
			$CI->db->from(db_prefix().'leads_sources');
			$CI->db->where('id', $lead->source);
			$source = $CI->db->get()->row();
			if ($source) {
				$merge_fields['{lead_source}'] = $source->name;
			}
			
			$phonenumber = $lead->phonenumber;
			$CI->app_sms->trigger(SI_SMS_TRIGGER_LEAD_CREATED, $phonenumber, $merge_fields);
		}
	}
	return;	
}
/** hook for project status changed**/
function si_sms_hook_project_status_changed($data)
{
	$_status = isset($data['status']) ? $data['status'] : '';
	$project_id = isset($data['project_id']) ? $data['project_id'] : '';
	$exclude_status = unserialize(get_option(SI_SMS_MODULE_NAME.'_project_status_exclude'));
	#if status is from excluded statuses then ignore and return
	if(!empty($exclude_status) && in_array($_status,$exclude_status))
		return;
	
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($project_id)){
		$CI = &get_instance();
		$CI->db->select('clientid,name');
		$CI->db->where('id', $project_id);
		$project = $CI->db->get(db_prefix() . 'projects')->row();
		$status = get_project_status_by_id($_status);
		$merge_fields = si_sms_get_customer_merge_fields($project->clientid);
		$merge_fields['{project_name}'] = $project->name;
		$merge_fields['{project_id}'] = $project_id;
		$merge_fields['{project_status}'] = $status['name'];
		if(isset($merge_fields['phone_number']))
			$phonenumber = $merge_fields['phone_number'];
		
		if(!is_null($phonenumber) && $phonenumber!==''){
			$CI->app_sms->trigger(SI_SMS_TRIGGER_PROJECT_STATUS_CHANGED, $phonenumber, $merge_fields);
		}
	}
	return;
}
/** hook for task status changed**/
function si_sms_hook_task_status_changed($data)
{
	$_status = isset($data['status']) ? $data['status'] : '';
	$task_id = isset($data['task_id']) ? $data['task_id'] : '';
	$exclude_status = unserialize(get_option(SI_SMS_MODULE_NAME.'_task_status_exclude'));
	#if status is from excluded statuses then ignore and return
	if(!empty($exclude_status) && in_array($_status,$exclude_status))
		return;
}
/** hook for invoice status changed**/
function si_sms_hook_invoice_status_changed($data)
{
	$_status = isset($data['status']) ? $data['status'] : '';
	$invoice_id = isset($data['invoice_id']) ? $data['invoice_id'] : '';
	
	$exclude_status = unserialize(get_option(SI_SMS_MODULE_NAME.'_invoice_status_exclude'));
	#if status is from excluded statuses then ignore and return
	if(!empty($exclude_status) && in_array($_status,$exclude_status))
		return;
	
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($invoice_id)){
		$CI = &get_instance();
		$CI->db->where('id', $invoice_id);
		$invoice = $CI->db->get(db_prefix() . 'invoices')->row();
		$currency = get_currency($invoice->currency);
		$merge_fields = si_sms_get_customer_merge_fields($invoice->clientid);
		$merge_fields['{invoice_number}'] 	= format_invoice_number($invoice_id);
		$merge_fields['{invoice_link}'] 	= site_url('invoice/' . $invoice_id . '/' . $invoice->hash);
		$merge_fields['{invoice_date}'] 	= _d($invoice->date);
		$merge_fields['{invoice_subtotal}'] = app_format_money($invoice->subtotal, $currency);
		$merge_fields['{invoice_total}'] 	= app_format_money($invoice->total, $currency);
		$merge_fields['{invoice_status}']  = format_invoice_status($invoice->status, '', false);
		$merge_fields['{invoice_short_url}']= (function_exists('get_invoice_shortlink')?get_invoice_shortlink($invoice) : site_url('invoice/' . $invoice_id . '/' . $invoice->hash));##invoice_short_url available from Perfex Version 2.7.3
		
		if(isset($merge_fields['phone_number']))
			$phonenumber = $merge_fields['phone_number'];
		
		if(!is_null($phonenumber) && $phonenumber!==''){
			$CI->app_sms->trigger(SI_SMS_TRIGGER_INVOICE_STATUS_CHANGED, $phonenumber, $merge_fields);
		}	
	}
	return;	
}
/** hook for lead status changed**/
function si_sms_hook_lead_status_changed($data)
{
	$lead_id = isset($data['lead_id']) ? $data['lead_id'] : '';
	$merge_fields = array();
	$phonenumber = '';
	if (is_numeric($lead_id)) {
		$CI = &get_instance();
		$CI->db->where('id', $lead_id);
		$lead = $CI->db->get(db_prefix() . 'leads')->row();
		$exclude_status = unserialize(get_option(SI_SMS_MODULE_NAME.'_lead_status_exclude'));
		#if status is from excluded statuses then ignore and return
		if(!empty($exclude_status) && in_array($lead->status,$exclude_status))
			return;
		if(!is_null($lead->phonenumber) && $lead->phonenumber!=='')
		{
			$merge_fields['{lead_public_form_url}']    	= leads_public_url($lead->id);
			$merge_fields['{lead_name}']               	= $lead->name;
			$merge_fields['{lead_email}']              	= $lead->email;
			$merge_fields['{lead_position}']           	= $lead->title;
			$merge_fields['{lead_phonenumber}']        	= $lead->phonenumber;
			$merge_fields['{lead_company}']            	= $lead->company;
			$merge_fields['{lead_zip}']                	= $lead->zip;
			$merge_fields['{lead_city}']               	= $lead->city;
			$merge_fields['{lead_state}']              	= $lead->state;
			$merge_fields['{lead_address}']            	= $lead->address;
			$merge_fields['{lead_website}']            	= $lead->website;
			$merge_fields['{lead_description}']        	= $lead->description;
			$merge_fields['{lead_assigned}']			= '';
			$merge_fields['{lead_country}']				= '';
			$merge_fields['{lead_status}']				= '';
			$merge_fields['{lead_source}'] 				= '';
			
			if ($lead->assigned != 0) {
				$merge_fields['{lead_assigned}'] = get_staff_full_name($lead->assigned);
			}
			if ($lead->country != 0) {
				$country                  = get_country($lead->country);
				$merge_fields['{lead_country}'] = $country->short_name;
			}
			if ($lead->junk == 1) {
				$merge_fields['{lead_status}'] = _l('lead_junk');
			} elseif ($lead->lost == 1) {
				$merge_fields['{lead_status}'] = _l('lead_lost');
			} else {
				$CI->db->select('name');
				$CI->db->from(db_prefix().'leads_status');
				$CI->db->where('id', $lead->status);
				$status = $CI->db->get()->row();
				if ($status) {
					$merge_fields['{lead_status}'] = $status->name;
				}
			}
			$CI->db->select('name');
			$CI->db->from(db_prefix().'leads_sources');
			$CI->db->where('id', $lead->source);
			$source = $CI->db->get()->row();
			if ($source) {
				$merge_fields['{lead_source}'] = $source->name;
			}
			
			$phonenumber = $lead->phonenumber;
			$CI->app_sms->trigger(SI_SMS_TRIGGER_LEAD_STATUS_CHANGED, $phonenumber, $merge_fields);
		}
	}
	return;	
}
/** hook for ticket status changed**/
function si_sms_hook_after_ticket_status_changed($data)
{
	$_status = isset($data['status']) ? $data['status'] : '';
	$ticket_id = isset($data['id']) ? $data['id'] : '';
	$exclude_status = unserialize(get_option(SI_SMS_MODULE_NAME.'_ticket_status_exclude'));
	#if status is from excluded statuses then ignore and return
	if(!empty($exclude_status) && in_array($_status,$exclude_status))
		return;
	
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($ticket_id)){
		$CI = &get_instance();
		$ticket = $CI->tickets_model->get($ticket_id,[db_prefix() . 'tickets.contactid > '=>0]);
		if (!$ticket) {
			return;
		}
		$contact = $CI->clients_model->get_contact($ticket->contactid);
		if ($contact) {
			if(!is_null($contact->phonenumber) && $contact->phonenumber!=='')
				$phonenumber = $contact->phonenumber;
				
			$merge_fields['{contact_firstname}'] = $contact->firstname;
		}
		
		if(!is_null($phonenumber) && $phonenumber!==''){
			$merge_fields['{ticket_date}']    = _dt($ticket->date);
			$merge_fields['{ticket_subject}'] = $ticket->subject;
			$merge_fields['{ticket_status}'] = ticket_status_translate($ticket->status);
			$merge_fields['{ticket_priority}'] = ticket_priority_translate($ticket->priority);
			
			$CI->db->where('departmentid', $ticket->department);
			$department = $CI->db->get(db_prefix().'departments')->row();
			if ($department) {
				$merge_fields['{ticket_department}']       = $department->name;
				$merge_fields['{ticket_department_email}'] = $department->email;
			}
			else {
				$merge_fields['{ticket_department}']       = '';
				$merge_fields['{ticket_department_email}'] = '';
			}
			$CI->db->where('serviceid', $ticket->service);
			$service = $CI->db->get(db_prefix().'services')->row();
			if ($service) {
				$merge_fields['{ticket_service}'] = $service->name;
			}
			else {
				$merge_fields['{ticket_service}'] = '';
			}
			$merge_fields['{ticket_url}'] = site_url('clients/ticket/' . $ticket_id);
			$merge_fields['{ticket_public_url}'] = (function_exists('get_ticket_public_url') ? get_ticket_public_url($ticket) : '');##ticket_public_url available from Perfex Version 2.4.2
			
			$CI->app_sms->trigger(SI_SMS_TRIGGER_TICKET_STATUS_CHANGED, $phonenumber, $merge_fields);
		}	
	}
	return;	
}
/** hook for proposal is accepted or declined**/
function si_sms_hook_proposal_accepted_declined($proposal_id)
{
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($proposal_id)){
		$CI = &get_instance();
		$proposal = $CI->proposals_model->get($proposal_id);
		if (!$proposal) {
			return;
		}
		if ($proposal->currency != 0) {
			$currency = get_currency($proposal->currency);
		} else {
			$currency = get_base_currency();
		}
		$merge_fields['{proposal_number}'] 		= format_proposal_number($proposal_id);
		$merge_fields['{proposal_id}'] 			= $proposal_id;
		$merge_fields['{proposal_subject}'] 	= $proposal->subject;
		$merge_fields['{proposal_open_till}'] 	= _d($proposal->open_till);
		$merge_fields['{proposal_subtotal}'] 	= app_format_money($proposal->subtotal, $currency);
		$merge_fields['{proposal_total}'] 		= app_format_money($proposal->total, $currency);;
		$merge_fields['{proposal_proposal_to}'] = $proposal->proposal_to;
		$merge_fields['{proposal_link}']        = site_url('proposal/' . $proposal_id . '/' . $proposal->hash);
		$merge_fields['{proposal_short_url}']= (function_exists('get_proposal_shortlink')?get_proposal_shortlink($proposal) : site_url('proposal/' . $proposal_id . '/' . $proposal->hash));##proposal_short_url available from Perfex Version 2.7.3
		
		//send to customer
		if(!is_null($proposal->phone) && $proposal->phone!==''){
			$phonenumber = $proposal->phone;
			
			if($proposal->status ==3)
				$CI->app_sms->trigger(SI_SMS_TRIGGER_PROPOSAL_ACCEPTED, $phonenumber, $merge_fields);
			elseif($proposal->status ==2)
				$CI->app_sms->trigger(SI_SMS_TRIGGER_PROPOSAL_DECLINED, $phonenumber, $merge_fields);	
		}
		//send to staff
		// Get creator and assigned;
		$CI->db->where('staffid', $proposal->addedfrom);
		$CI->db->or_where('staffid', $proposal->assigned);
		$staff_proposal = $CI->db->get(db_prefix() . 'staff')->result_array();
		foreach ($staff_proposal as $member) {
			$phonenumber = $member['phonenumber'];
			if(!is_null($phonenumber) && $phonenumber!==''){
				if($proposal->status ==3)
					$CI->app_sms->trigger(SI_SMS_TRIGGER_PROPOSAL_ACCEPTED_TO_STAFF, $phonenumber, $merge_fields);
				elseif($proposal->status ==2)
					$CI->app_sms->trigger(SI_SMS_TRIGGER_PROPOSAL_DECLINED_TO_STAFF, $phonenumber, $merge_fields);
			}	
		}			
	}
	return;
}
/**hook after estimate status changed to accepted or declined**/
function si_sms_hook_estimate_accepted_declined($estimate_id)
{
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($estimate_id)){
		$CI = &get_instance();
		$estimate = $CI->estimates_model->get($estimate_id);
		if (!$estimate) {
			return;
		}
		$currency = get_currency($estimate->currency);
		$merge_fields = si_sms_get_customer_merge_fields($estimate->clientid);
		if(isset($merge_fields['phone_number']))
			$phonenumber = $merge_fields['phone_number'];
		$merge_fields['{estimate_total}']        = app_format_money($estimate->total, $currency);
		$merge_fields['{estimate_subtotal}']     = app_format_money($estimate->subtotal, $currency);
		$merge_fields['{estimate_link}']         = site_url('estimate/' . $estimate_id . '/' . $estimate->hash);
		$merge_fields['{estimate_number}']       = format_estimate_number($estimate_id);
		$merge_fields['{estimate_date}']         = _d($estimate->date);
		$merge_fields['{estimate_status}']       = format_estimate_status($estimate->status, '', false);
		$merge_fields['{estimate_short_url}']	 = (function_exists('get_estimate_shortlink')?get_estimate_shortlink($estimate) : site_url('estimate/' . $estimate_id . '/' . $estimate->hash));##estimate_short_url available from Perfex Version 2.7.3	
		
		//send to client
		if(!is_null($phonenumber) && $phonenumber!==''){
			if($estimate->status == 4)
				$CI->app_sms->trigger(SI_SMS_TRIGGER_ESTIMATE_ACCEPTED, $phonenumber, $merge_fields);
			elseif($estimate->status == 3)
				$CI->app_sms->trigger(SI_SMS_TRIGGER_ESTIMATE_DECLINED, $phonenumber, $merge_fields);	
		}
		//send to staff
		$CI->db->where('staffid', $estimate->addedfrom);
		$CI->db->or_where('staffid', $estimate->sale_agent);
		$staff_estimate = $CI->db->get(db_prefix() . 'staff')->result_array();
		foreach ($staff_estimate as $member) {
			$phonenumber = $member['phonenumber'];
			if(!is_null($phonenumber) && $phonenumber!==''){
				if($proposal->status == 4)
					$CI->app_sms->trigger(SI_SMS_TRIGGER_ESTIMATE_ACCEPTED_TO_STAFF, $phonenumber, $merge_fields);
				elseif($proposal->status == 3)
					$CI->app_sms->trigger(SI_SMS_TRIGGER_ESTIMATE_DECLINED_TO_STAFF, $phonenumber, $merge_fields);
			}	
		}
	}
	return;
}
/**hook for when adding new contact**/
function si_sms_hook_contact_created($contact_id)
{
	$merge_fields = array();
	$phonenumber = '';
	if(is_numeric($contact_id)){
		$CI = &get_instance();
		$contact = $CI->clients_model->get_contact($contact_id);
		if (!$contact) {
			return;
		}
		$client = $CI->clients_model->get($contact->userid);
		$phonenumber = $contact->phonenumber;
		
		if(!is_null($phonenumber) && $phonenumber!==''){	
			$merge_fields['{contact_firstname}']    = $contact->firstname;
            $merge_fields['{contact_lastname}']     = $contact->lastname;
            $merge_fields['{contact_email}']        = $contact->email;
            $merge_fields['{contact_phonenumber}']  = $contact->phonenumber;
            $merge_fields['{contact_title}']        = $contact->title;
			$merge_fields['{client_company}']       = $client->company;
			$merge_fields['{client_phonenumber}']   = $client->phonenumber;
			$merge_fields['{client_country}']       = get_country_short_name($client->country);
			$merge_fields['{client_city}']          = $client->city;
			$merge_fields['{client_zip}']           = $client->zip;
			$merge_fields['{client_state}']         = $client->state;
			$merge_fields['{client_address}']       = $client->address;
			$merge_fields['{client_id}']            = $client->userid;
			$merge_fields['{client_vat_number}']	= (!empty($client->vat) ? $client->vat : '');
			
			$CI->app_sms->trigger(SI_SMS_TRIGGER_CONTACT_CREATED, $phonenumber, $merge_fields);
		}
	}
	return;		
}
/*hook to run a cron*/
function si_sms_hook_after_cron_run($manually)
{
	$last_run = strtotime(get_option(SI_SMS_MODULE_NAME.'_trigger_schedule_sms_last_run'));
	$time_now                = time();
	if (($time_now < ($last_run)) /*|| $manually === true*/) {
		return;
	}
	send_schedule_sms_cron_run();
	return;
}