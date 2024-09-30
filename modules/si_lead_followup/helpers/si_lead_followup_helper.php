<?php
defined('BASEPATH') or exit('No direct script access allowed');

function send_lead_followup_schedule_sms_cron_run()
{
	$CI = &get_instance();
	$now = time();
	$today_date = date('Y-m-d 00:00:00',$now);
	$hour_now = date('G');
	$result = $CI->si_lead_followup_model->get_schedules($hour_now,$today_date);
	$count=0;
	if(!empty($result)){
		foreach($result as $row){
			$custom_trigger_name = 'si_lead_followup_custom_sms';
			$filter_by = $row['filter_by'];
			$message = $row['content'];
			$contacts = array();
			if($filter_by=='lead')
				$contacts = $CI->si_lead_followup_model->get_leads($row);
			elseif($filter_by=='staff')
				$contacts = $CI->si_lead_followup_model->get_lead_staffs($row);
			
			try{
				if(!empty($contacts)){
					$dlt_template_id_key = $row['dlt_template_id_key'];
					$dlt_template_id_value = $row['dlt_template_id_value'];
					if($dlt_template_id_key !='' && $dlt_template_id_value != ''){
							add_option($dlt_template_id_key,$dlt_template_id_value);
							$CI->app_object_cache->add($dlt_template_id_key, $dlt_template_id_value);
							$CI->app_object_cache->set($dlt_template_id_key, $dlt_template_id_value);
							update_option($dlt_template_id_key, $dlt_template_id_value);
					}
					$oc_name = 'sms-trigger-' . $custom_trigger_name . '-value';
					$CI->app_object_cache->add($oc_name, $message);
					$CI->app_object_cache->set($oc_name, $message);
					update_option('sms_trigger_' . $custom_trigger_name,$message);
					foreach($contacts as $contact)
					{
						$merge_fields = ['{name}'=>$contact['name']];
						if($filter_by=='lead'){
							$merge_fields = $CI->app_merge_fields->format_feature('leads_merge_fields',$contact['id']);
							$comment = false;
						}
						elseif($filter_by=='staff'){
							$lead_merge_fields = $CI->app_merge_fields->format_feature('leads_merge_fields',$contact['lead_id']);
							$merge_fields = $CI->app_merge_fields->format_feature('staff_merge_fields',$contact['id']);
							$merge_fields = array_merge($merge_fields,$lead_merge_fields);
							$comment = _l('si_lfs_schedule_staff_leads_comment',[$contact['lead_id'],$contact['lead_name']]);
						}
						$response = $CI->app_sms->trigger($custom_trigger_name, $contact['phonenumber'], $merge_fields);
						$CI->si_lead_followup_model->add_schedule_rel_ids($row['id'],$contact['id'],$comment);
					}
					update_option('sms_trigger_'.$custom_trigger_name,'');
					if($dlt_template_id_key !='')
						update_option($dlt_template_id_key,'');
				}
				$CI->si_lead_followup_model->update_schedule($row['id'],array('last_executed'=>date('Y-m-d H:i:s'),'cron'=>true));
			}
			catch(Exception $e){
				log_activity("Error in sending Lead Followup schedule SMS :".$e->getMessage());
			}
			$count++;
		}
		log_activity(_l('si_lfs_schedule_success_activity_log_text',$count));	
	}
	update_option(SI_LEAD_FOLLOWUP_MODULE_NAME.'_trigger_schedule_sms_last_run',date('Y-m-d H:i:s',$now));
}
 
function si_lead_followup_get_merge_fields($filter_by='')
{
	$merge_fields = array();
		
	$merge_fields['lead'] 		= '{lead_name}, {lead_email}, {lead_position}, {lead_company}, {lead_country},'.
								' {lead_zip}, {lead_city}, {lead_state}, {lead_address}, {lead_assigned},'.
								' {lead_status}, {lead_source}, {lead_phonenumber}, {lead_website}, {lead_link},'.
								' {lead_description}, {lead_public_form_url}, {lead_public_consent_url}';
		
	$merge_fields['staff'] 		= $merge_fields['lead'].','.'{staff_firstname}, {staff_lastname}, {staff_email}';//staff will have all his features with leads
	
	if($filter_by!='' && isset($merge_fields[$filter_by]))
		return $merge_fields[$filter_by];
	else
		return $merge_fields;
}		