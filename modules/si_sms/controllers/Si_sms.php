<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_sms extends AdminController 
{
	public function __construct()
	{
		parent::__construct(); 
		if (!is_admin() && !has_permission('settings', '', 'view') && !has_permission('si_sms_custom_send', '', 'create') && !has_permission('si_sms_schedule_send', '', 'view') && !has_permission('si_sms_schedule_send', '', 'view_own')) {
			access_denied(_l('si_sms'));
		}
	}
	function index()
	{
		redirect(admin_url('si_sms/custom_sms'));
	}
	function custom_sms()
	{
		if(!get_option(SI_SMS_MODULE_NAME.'_activated') || get_option(SI_SMS_MODULE_NAME.'_activation_code')=='')
			access_denied(_l('si_sms'));
		if (!has_permission('si_sms_custom_send', '', 'create')) {
			access_denied(_l('si_sms'));
		}
		if ($this->input->post()) {
			
			$custom_trigger_name = 'si_sms_custom_sms';
			$filter_by = $this->input->post('filter_by');
			$message = $this->input->post('sms_content');
			$contacts = array();
			if($filter_by=='customer'){
				$clients  = $this->input->post('si_clients');
				if(!empty($clients)){
					$contacts = $this->si_sms_model->get_client_contacts($clients);
				}
			}
			elseif($filter_by=='lead'){
				$leads  = $this->input->post('si_leads');
				if(!empty($leads)){
					$contacts = $this->si_sms_model->get_leads_contacts($leads);
				}
			}
			elseif($filter_by=='staff'){
				$staffs  = $this->input->post('si_staffs');
				if(!empty($staffs)){
					$contacts = $this->si_sms_model->get_staffs_contacts($staffs);
				}
			}
			try{
				if(!empty($contacts)){
					#check for DLT template Id if exist, add in options, if not added and add in data
					$settings = $this->input->post('settings');
					$dlt_template_id_key = '';
					$dlt_template_id_value = '';
					if(is_array($settings)){
						foreach($settings as $key=>$value){
							add_option($key,$value);#add key if not exist
							$dlt_template_id_key = $key;
							$dlt_template_id_value = $value;
							$this->app_object_cache->add($key, $value);//insert
							$this->app_object_cache->set($key, $value);//update
							update_option($key, $value);
						}
					}
					#check DLT Template ID end
					$oc_name = 'sms-trigger-' . $custom_trigger_name . '-value';
					$this->app_object_cache->add($oc_name, $message);//insert
					$this->app_object_cache->set($oc_name, $message);//update
					update_option('sms_trigger_' . $custom_trigger_name,$message);
					foreach($contacts as $contact)
					{
						$merge_fields = ['{name}'=>$contact['name']];
						if($filter_by=='customer'){
							$merge_fields = $this->app_merge_fields->format_feature('client_merge_fields', $contact['userid'],$contact['id']);
						}
						elseif($filter_by=='lead'){
							$merge_fields = $this->app_merge_fields->format_feature('leads_merge_fields',$contact['id']);
						}
						elseif($filter_by=='staff'){
							$merge_fields = $this->app_merge_fields->format_feature('staff_merge_fields',$contact['id']);
						}
						$response = $this->app_sms->trigger($custom_trigger_name, $contact['phonenumber'], $merge_fields);
					}
					update_option('sms_trigger_'.$custom_trigger_name,'');
					if($dlt_template_id_key !='')
						update_option($dlt_template_id_key,'');
					echo json_encode(['success' => true,'message'=> _l('si_sms_sent_message')]);
					die();
				}
				echo json_encode(['success' => false,'message'=> _l('si_sms_sent_error_message')]);
				die();
				
			}
			catch(Exception $e){
				echo json_encode(['success' => false,'message'=>$e->getMessage()]);
			}
		}
		
		$data['merge_fields'] = si_sms_get_merge_fields();
		$data['staff_list']  = $this->staff_model->get('',['is_not_staff' => 0, 'active' => 1]);
		$data['templates'] = $this->si_sms_model->get();
		$data['title'] = _l('si_sms_custom_send_title');
		$this->load->view('custom_sms_send', $data);
	}
	
	function get_clients_leads()
	{
		if (!has_permission('si_sms_custom_send', '', 'create') && !has_permission('si_sms_schedule_send', '', 'view') && !has_permission('si_sms_schedule_send', '', 'view_own')) {
			 ajax_access_denied();
		}
		if ($this->input->is_ajax_request()) {
			$clients = $this->si_sms_model->get_clients();
			$leads = $this->si_sms_model->get_leads();
			echo json_encode(array('clients'=>$clients,'leads'=>$leads));
			die();
		}
	}
	
	function list_templates()
	{
		$data=array();
		$data['title']    = _l('si_sms_templates_menu');
		$data['filter_templates'] = $this->si_sms_model->get();
		$this->load->view('sms_templates_list', $data);
	}
	# get template
	public function get_template($id)
	{
		if(is_numeric($id)){
			$template              = $this->si_sms_model->get($id);
			$template->content = clear_textarea_breaks($template->content);
			echo json_encode($template);
		}
	}
	# Add or Update sms template 
	public function save_template()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$data['is_public'] = isset($data['is_public']) ? 1 : 0;
			#check for DLT template Id if exist, add in options, if not added and add in data
			if(isset($data['settings'])){
				foreach($data['settings'] as $key=>$value){
					add_option($key,'');
					$data['dlt_template_id'] = $value;
				}
				unset($data['settings']);
			}
			#check DLT Template ID end
			if ($data['id'] == '') {
				unset($data['id']);
				$data['staff_id'] = get_staff_user_id();
				#Add template
				$id = $this->si_sms_model->add($data);
				if ($id) {
					echo json_encode([
						'success' => $id ? true : false,
						'message' => $id ? _l('added_successfully', _l('si_sms_templates')) : '',
						'id'      => $id,
						'name'    => $this->input->post('category_name'),
					]);
				}
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->si_sms_model->update($id, $data);
				if ($success) {
					set_alert('success', _l('updated_successfully', _l('si_sms_templates')));
				}
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	function del_sms_template($id)
	{
		$current_user_id = get_staff_user_id();
		$this->si_sms_model->delete($id,$current_user_id);
		redirect('si_sms/list_templates');
	}
	function schedule_sms()
	{
		if(!get_option(SI_SMS_MODULE_NAME.'_activated') || get_option(SI_SMS_MODULE_NAME.'_activation_code')=='')
			access_denied(_l('si_sms'));
		if (!has_permission('si_sms_schedule_send', '', 'view') && !has_permission('si_sms_schedule_send', '', 'view_own')) {
			access_denied(_l('si_sms'));
		}
		if ($this->input->post()) {
			if (!has_permission('si_sms_schedule_send', '', 'create')) {
				ajax_access_denied();
			}
			$custom_trigger_name = 'si_sms_custom_sms';
			$filter_by = $this->input->post('filter_by');
			$message = $this->input->post('sms_content');
			$schedule_date = $this->input->post('schedule_date');
			$rel_ids = array();
			if($filter_by=='customer'){
				$clients  = $this->input->post('si_clients');
				if(!empty($clients)){
					$rel_ids = $clients;
				}
			}
			elseif($filter_by=='lead'){
				$leads  = $this->input->post('si_leads');
				if(!empty($leads)){
					$rel_ids = $leads;
				}
			}
			elseif($filter_by=='staff'){
				$staffs  = $this->input->post('si_staffs');
				if(!empty($staffs)){
					$rel_ids = $staffs;
				}
			}
			try{
				if(!empty($rel_ids)){
					#check for DLT template Id if exist, add in database
					$settings = $this->input->post('settings');
					$dlt_template_id_key = '';
					$dlt_template_id_value = '';
					if(is_array($settings)){
						foreach($settings as $key=>$value){
							$dlt_template_id_key = $key;
							$dlt_template_id_value = $value;
						}
					}
					$schedule_data = array('filter_by' =>$filter_by,
											'rel_ids' => $rel_ids,
											'content' => $message,
											'dlt_template_id_key' =>$dlt_template_id_key,
											'dlt_template_id_value' => $dlt_template_id_value,
											'staff_id' => get_staff_user_id(),
											'schedule_date' => date('Y-m-d H:i:s',strtotime($schedule_date)),
											'dateadded' => date('Y-m-d H:i:s'),
									);
					$result = $this->si_sms_model->add_schedule($schedule_data);
					if($result){				
						echo json_encode(['success' => true,'message'=> _l('added_successfully',_l('si_sms_schedule_send_menu'))]);
					}
					else
						echo json_encode(['success' => false,'message'=> _l('si_sms_schedule_error_message')]);	
					die();
				}
				echo json_encode(['success' => false,'message'=> _l('si_sms_sent_error_message')]);
				die();
				
			}
			catch(Exception $e){
				echo json_encode(['success' => false,'message'=>$e->getMessage()]);
			}
		}
		
		$data['merge_fields'] = si_sms_get_merge_fields();
		
		$data['staff_list']  = $this->staff_model->get('',['is_not_staff' => 0, 'active' => 1]);
		$data['templates'] = $this->si_sms_model->get();
		$data['title'] = _l('si_sms_schedule_send_title');
		$this->load->view('schedule_sms_send', $data);
	}
	
	function schedule_table()
	{
		$data = $this->input->post();
		$this->app->get_table_data(module_views_path(SI_SMS_MODULE_NAME,'tables/schedule_sms'), $data);
	}
	
	function get_schedule_sms_by_id($schedule_id,$is_edit=false)
	{
		if ($this->input->is_ajax_request() && is_numeric($schedule_id)) {
			
			$data  = [];
			$where = [];
			if (!has_permission('si_sms_schedule_send', '', 'view'))
				$where['staff_id'] = get_staff_user_id();
			$schedule = (array)$this->si_sms_model->get_schedule($schedule_id,$where);
				
			if(!empty($schedule)){
				
				$data['schedule'] = $schedule;
				$rel_ids = $this->si_sms_model->get_schedule_rel_ids($schedule['id']);
					
				if(!$is_edit){//view
					$contacts = array();
					if($schedule['filter_by'] == 'customer'){
						$contacts = $this->si_sms_model->get_client_contacts($rel_ids,true);
					}
					elseif($schedule['filter_by'] == 'lead'){
						$contacts = $this->si_sms_model->get_leads_contacts($rel_ids,true);
					}
					elseif($schedule['filter_by'] == 'staff'){
						$contacts = $this->si_sms_model->get_staffs_contacts($rel_ids,true);
					}
					$data['contacts'] = $contacts;
					$html = $this->load->view('_includes/view_schedule_sms', $data,true);
				}
				else{
					$merge_fields = '{name}';
					if($schedule['filter_by']=='staff')
						$all_list = $this->staff_model->get('',['is_not_staff' => 0, 'active' => 1]);
					elseif($schedule['filter_by']=='customer')
						$all_list = $this->si_sms_model->get_clients();
					elseif($schedule['filter_by']=='lead')
						$all_list = $this->si_sms_model->get_leads();
	
					$data['all_rel_ids'] = $all_list;
					$data['merge_fields'] = si_sms_get_merge_fields($schedule['filter_by']);
					$data['selected_rel_ids'] = $rel_ids;		
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
		if(!get_option(SI_SMS_MODULE_NAME.'_activated') || get_option(SI_SMS_MODULE_NAME.'_activation_code')=='')
			ajax_access_denied();
		if (!has_permission('si_sms_schedule_send', '', 'edit')) {
			ajax_access_denied();
		}
		if($this->input->is_ajax_request() && is_numeric($schedule_id) && $schedule_id > 0){
			$where = [];
			if (!has_permission('si_sms_schedule_send', '', 'view'))
				$where['staff_id'] = get_staff_user_id();
			$schedule = $this->si_sms_model->get_schedule($schedule_id,$where);
			if($schedule){
				$rel_ids = $this->input->post('si_rel_id');
				$message = $this->input->post('sms_content');
				$schedule_date = $this->input->post('schedule_date');
			
				try{
					if(!empty($rel_ids)){
						#check for DLT template Id if exist, add in database
						$settings = $this->input->post('settings');
						$dlt_template_id_key = '';
						$dlt_template_id_value = '';
						if(is_array($settings)){
							foreach($settings as $key=>$value){
								$dlt_template_id_key = $key;
								$dlt_template_id_value = $value;
							}
						}
						$schedule_data = array( 'rel_ids' => $rel_ids,
												'content' => $message,
												'dlt_template_id_key' =>$dlt_template_id_key,
												'dlt_template_id_value' => $dlt_template_id_value,
												'schedule_date' => date('Y-m-d H:i:s',strtotime($schedule_date)),
										);
						$result = $this->si_sms_model->update_schedule($schedule_id,$schedule_data);
						if($result){				
							echo json_encode(['success' => true,'message'=> _l('updated_successfully',_l('si_sms_schedule_send_menu'))]);
						}
						else
							echo json_encode(['success' => false,'message'=> _l('si_sms_schedule_error_message')]);	
						die();
					}
					echo json_encode(['success' => false,'message'=> _l('si_sms_sent_error_message')]);
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
		if (has_permission('si_sms_schedule_send', '', 'delete')) {
			$success = $this->si_sms_model->delete_schedule($schedule_id);
			if ($success) {
				$success = true;
				$message = _l('deleted', _l('si_sms_schedule_send_menu'));
				
			} else {
				$success = false;
				$message =  _l('problem_deleting', _l('si_sms_schedule_send_menu'));
				
			}
			echo json_encode([
				'success' => $success,
				'message' => $message,
			]);
		}
		die;
	}
	
	public function validate()
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
				CURLOPT_URL            => VALIDATION_URL,
				CURLOPT_POST           => 1,
				CURLOPT_POSTFIELDS     => [
					'url' => site_url(),
					'module'     => SI_SMS_KEY,
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