<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_custom_status extends AdminController
{
	public function __construct()
	{
		parent::__construct(); 
		if (!is_admin() && !has_permission('si_custom_status', '', 'view')) {
			access_denied(_l('si_custom_status'));
		}
	}
	
	public function index()
	{
		redirect(admin_url('/'));
	}
	

	public function statuses($relto='')
	{
		if (!is_admin() && !has_permission('si_custom_status', '', 'view')) {
			access_denied(_l('si_custom_status'));
		}
		$edit_default_status = get_option(SI_CUSTOM_STATUS_MODULE_NAME.'_edit_default_status_'.$relto);
		$data['default_statuses'] = array();
		if($relto=='')
			redirect(admin_url('si_custom_status/statuses/projects'));
			
		if($relto=='projects')
			$data['default_statuses'] = $edit_default_status ? $this->si_custom_status_model->get_default_status('',["relto" => $relto]) : $this->projects_model->get_project_statuses();
		if($relto=='tasks')
			$data['default_statuses'] = $edit_default_status ? $this->si_custom_status_model->get_default_status('',["relto" => $relto]) : $this->tasks_model->get_statuses();
		$data['statuses']	= $this->si_custom_status_model->get_status('',["relto" => $relto]);
		$data['title']		= _l('si_custom_status_setup_menu');
		$data['relto']		= $relto;
		$data['edit_default_status']= $edit_default_status;
		$this->load->view('statuses/manage_statuses', $data);
	}

	public function status()
	{
		if (!is_admin() && !(has_permission('si_custom_status', '', 'create') || !has_permission('si_custom_status', '', 'edit')) ) {
			access_denied(_l('si_custom_status'));
		}
		if ($this->input->post()) {
			$data = $this->input->post();
			
			if(!isset($data['filter_default']))
				$data['filter_default'] = 0;		
				
			if (!$this->input->post('id')) {
				$inline = isset($data['inline']);
				if (isset($data['inline'])) {
					unset($data['inline']);
				}
				if(isset($data['is_default']))
					unset($data['is_default']);
				$id = $this->si_custom_status_model->add_status($data);
				if (!$inline) {
					if ($id) {
						set_alert('success', _l('added_successfully', _l('si_custom_statuses')));
					}
				} else {
					echo json_encode(['success' => $id ? true : fales, 'id' => $id]);
				}
			} else {
				$id = $data['id'];
				unset($data['id']);
				//check if updating the default statuses
				if($data['is_default']=='true'){
					unset($data['is_default']);
					$success = $this->si_custom_status_model->update_default_status($data, $id);
					
				}else{
					unset($data['is_default']);	
					$success = $this->si_custom_status_model->update_status($data, $id);
				}	
				if ($success) {
					set_alert('success', _l('updated_successfully', _l('si_custom_statuses')));
				}
			}
		}
	}

	public function delete_status($id)
	{
		if (!is_admin() && !has_permission('si_custom_status', '', 'delete')) {
			access_denied(_l('si_custom_status'));
		}
		if (!$id) {
			redirect($_SERVER['HTTP_REFERER']);
		}
		$response = $this->si_custom_status_model->delete_status($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('si_custom_statuses')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('si_custom_statuses')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('si_custom_statuses')));
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function save_edit_default_status()
	{
		if (!is_admin() && !has_permission('si_custom_status', '', 'edit')) {
			ajax_access_denied();
		}
		if($this->input->is_ajax_request()){
			$data = $this->input->post();
			if(isset($data['edit_default_status']) && isset($data['relto']))
			{
				$edit_status = $data['edit_default_status'];
				$relto = $data['relto'];
				if(is_numeric($edit_status)){
					update_option(SI_CUSTOM_STATUS_MODULE_NAME.'_edit_default_status_'.$relto,$edit_status);
					echo json_encode(['success'=>true,'message'=>'']);
				}
				else
					echo json_encode(['success'=>false,'message'=>_l('si_custom_status_error_in_update')]);	
			}
		}
	}
	
	public function change_default_status($id='',$status='',$relto='')
	{
		if (!is_admin() && !has_permission('si_custom_status', '', 'edit')) {
			access_denied(_l('si_custom_status'));
		}
		if(is_numeric($id) && is_numeric($status) && ($status == 0 || $status == 1) && $relto !='')
		{
			$default_status = $this->si_custom_status_model->get_default_status($id);
			if($default_status){
				$total_rows = total_rows(db_prefix().$relto,array('status'=>$default_status->status_id));
				if($total_rows == 0){
					$success = $this->si_custom_status_model->update_default_status(['active'=>$status], $id);
					if ($success) 
						set_alert('success', _l('updated_successfully', _l('si_custom_statuses')));
				}
				else
					set_alert('warning', _l('si_custom_status_default_active_info',$relto));
			}	
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
}