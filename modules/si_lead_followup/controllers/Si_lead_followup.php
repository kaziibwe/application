<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_lead_followup extends AdminController 
{
	public function __construct()
	{
		parent::__construct(); 
		if (!is_admin() && !has_permission('settings', '', 'view') && 
			!has_permission('si_lead_followup', '', 'view') && 
			!has_permission('si_lead_followup', '', 'view_own')) {
			access_denied(_l('si_lead_followup'));
		}
	}
	function index()
	{
		if(!get_option(SI_LEAD_FOLLOWUP_MODULE_NAME.'_activated') || get_option(SI_LEAD_FOLLOWUP_MODULE_NAME.'_activation_code')=='')
			access_denied(_l('si_sms'));
		if (!has_permission('si_lead_followup', '', 'view') && !has_permission('si_lead_followup', '', 'view_own')) {
			access_denied(_l('si_lead_followup'));
		}
		if ($this->input->post()) {
			if (!has_permission('si_lead_followup', '', 'create')) {
				ajax_access_denied();
			}
			$custom_trigger_name = 'si_lead_followup_custom_sms';
			$filter_by = $this->input->post('filter_by');
			$name = $this->input->post('name');
			$status = $this->input->post('status');
			$source = ($this->input->post('source') ? $this->input->post('source') : 0);
			$message = $this->input->post('sms_content');
			$schedule_days = $this->input->post('schedule_days');
			$schedule_hour = $this->input->post('schedule_hour');
			
			try{
				$settings = $this->input->post('settings');
				$dlt_template_id_key = '';
				$dlt_template_id_value = '';
				if(is_array($settings)){
					foreach($settings as $key=>$value){
						$dlt_template_id_key = $key;
						$dlt_template_id_value = $value;
					}
				}
				$schedule_data = array('name' => $name,
										'status' => $status,
										'source' => $source,
										'filter_by' =>$filter_by,
										'content' => $message,
										'dlt_template_id_key' =>$dlt_template_id_key,
										'dlt_template_id_value' => $dlt_template_id_value,
										'staff_id' => get_staff_user_id(),
										'schedule_days' => $schedule_days,
										'schedule_hour' => $schedule_hour,
										'dateadded' => date('Y-m-d H:i:s'),
								);
				$result = $this->si_lead_followup_model->add_schedule($schedule_data);
				if($result){				
					echo json_encode(['success' => true,'message'=> _l('added_successfully',_l('si_lead_followup'))]);
				}
				else
					echo json_encode(['success' => false,'message'=> _l('si_lfs_schedule_error_message')]);	
				die();
			}
			catch(Exception $e){
				echo json_encode(['success' => false,'message'=>$e->getMessage()]);
			}
		}
		$data['merge_fields'] = si_lead_followup_get_merge_fields();
		$data['lead_statuses'] = $this->leads_model->get_status();
		$data['lead_sources'] = $this->leads_model->get_source();
		$data['title'] = _l('si_lfs_schedule_send_title');
		$this->load->view('schedule_sms_send', $data);
	}
	
	function table()
	{
		$data = $this->input->post();
		$this->app->get_table_data(module_views_path(SI_LEAD_FOLLOWUP_MODULE_NAME,'tables/schedule_sms'), $data);
	}
	
	function get_schedule_sms_by_id($schedule_id,$is_edit=false)
	{
		if ($this->input->is_ajax_request() && is_numeric($schedule_id)) {
			$data  = [];
			$where = [];
			if (!has_permission('si_lead_followup', '', 'view'))
				$where['staff_id'] = get_staff_user_id();
			$schedule = (array)$this->si_lead_followup_model->get_schedule($schedule_id,$where);
			if(!empty($schedule)){
				$data['schedule'] = $schedule;
				if(!$is_edit){
					$data['contacts'] = $this->si_lead_followup_model->get_schedule_rel_ids($schedule['id']);
					$html = $this->load->view('_includes/view_schedule_sms', $data,true);
				}
				else{
					$merge_fields = '{name}';
					$data['merge_fields'] = si_lead_followup_get_merge_fields($schedule['filter_by']);
					$data['lead_statuses'] = $this->leads_model->get_status();
					$data['lead_sources'] = $this->leads_model->get_source();		
					$html = $this->load->view('_includes/edit_schedule_sms', $data,true);
				}	
				echo json_encode([
					'success' => true,
					'html' => $html,
				]);
				die();
			}
		}
		die();
	}

	function save_edit_schedule($schedule_id='')
	{
		if (!has_permission('si_lead_followup', '', 'edit')) {
			ajax_access_denied();
		}
		if($this->input->is_ajax_request() && is_numeric($schedule_id) && $schedule_id > 0){
			$where = [];
			if (!has_permission('si_lead_followup', '', 'view'))
				$where['staff_id'] = get_staff_user_id();
			$schedule = $this->si_lead_followup_model->get_schedule($schedule_id,$where);
			if($schedule){
				$name = $this->input->post('name');
				$status = $this->input->post('status');
				$source = ($this->input->post('source') ? $this->input->post('source') : 0);
				$message = $this->input->post('sms_content');
				$schedule_days = $this->input->post('schedule_days');
				$schedule_hour = $this->input->post('schedule_hour');
				try{
					$settings = $this->input->post('settings');
					$dlt_template_id_key = '';
					$dlt_template_id_value = '';
					if(is_array($settings)){
						foreach($settings as $key=>$value){
							$dlt_template_id_key = $key;
							$dlt_template_id_value = $value;
						}
					}
					$schedule_data = array( 'name' => $name,
											'status' => $status,
											'source' => $source,
											'content' => $message,
											'dlt_template_id_key' =>$dlt_template_id_key,
											'dlt_template_id_value' => $dlt_template_id_value,
											'schedule_days' => $schedule_days,
											'schedule_hour' => $schedule_hour,
									);
					$result = $this->si_lead_followup_model->update_schedule($schedule_id,$schedule_data);
					if($result){				
						echo json_encode(['success' => true,'message'=> _l('updated_successfully',_l('si_lead_followup'))]);
					}
					else
						echo json_encode(['success' => false,'message'=> _l('si_lfs_schedule_error_message')]);	
					die();
				}
				catch(Exception $e){
					echo json_encode(['success' => false,'message'=>$e->getMessage()]);
				}
			}
		}	
	}
	
	function schedule_delete($schedule_id)
	{
		if (has_permission('si_lead_followup', '', 'delete')) {
			$success = $this->si_lead_followup_model->delete_schedule($schedule_id);
			if ($success) {
				$success = true;
				$message = _l('deleted', _l('si_lead_followup'));
				
			} else {
				$success = false;
				$message =  _l('problem_deleting', _l('si_lead_followup'));
				
			}
			echo json_encode([
				'success' => $success,
				'message' => $message,
			]);
		}
		die;
	}
	
	function validate()
	{
		if (!is_admin() && !has_permission('settings', '', 'view')) {
			ajax_access_denied();
		}
		try{
			$purchase_key   = trim($this->input->post('purchase_key', false));
			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_USERAGENT      => 'curl',
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_TIMEOUT        => 30,
				CURLOPT_URL            => SI_LEAD_FOLLOWUP_VALIDATION_URL,
				CURLOPT_POST           => 1,
				CURLOPT_POSTFIELDS     => [
					'url' => site_url(),
					'module'     => SI_LEAD_FOLLOWUP_KEY,
					'purchase_key'    => $purchase_key,
				],
			]);
			$result = curl_exec($curl);
			$error  = '';
			if (!$curl || !$result) {
				$error = 'Curl Error - Contact your hosting provider with the following error as reference: Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl);
			}
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if($code==404)
				$error = 'Server request unavailable, try after sometime.';
				
			curl_close($curl);
			if ($error != '') {
				echo json_encode([
					'success' => false,
					'message'=>$error,
				]);
				die();
			}
			echo ($result);
		}
		catch (Exception $e) {
			echo json_encode(array('success'=>false,'message'=>$e->getMessage()));
		}
	}
}