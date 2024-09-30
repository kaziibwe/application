<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_export_customer extends AdminController
{
	public function __construct()
	{
		parent::__construct(); 
		$this->load->model('si_export_customer_model');
		if (!is_admin() && !has_permission('si_export_customer', '', 'view') && !has_permission('si_export_customer_matrix', '', 'view') && !has_permission('si_export_customer_services', '', 'view')) {
			access_denied(_l('si_export_customer'));
		}
	}
	
	public function export_customer_profile($id)
	{
		if (has_permission('si_export_customer', '', 'view')) {
			$pdf = app_pdf('si-customer-profile', APP_MODULES_PATH.SI_EXPORT_CUSTOMER_MODULE_NAME.'/libraries/pdf/Si_customer_profile_pdf', $id);
			$client = $this->clients_model->get($id);
			if (!$client) {
				show_404();
			}
			$type = 'I';
			if ($this->input->get('download')) {
				$type = 'D';
			}
			$pdf->output('#' . $client->userid . '_' . $client->company . '_' . _d(date('Y-m-d')) . '.pdf', $type);
		}
	}
	
	public function export_customer_matrix($id)
	{
		if (has_permission('si_export_customer_matrix', '', 'view')) {
			$pdf = app_pdf('si-customer-matrix', APP_MODULES_PATH.SI_EXPORT_CUSTOMER_MODULE_NAME.'/libraries/pdf/Si_customer_matrix_pdf', $id);
			$client = $this->clients_model->get($id);
			if (!$client) {
				show_404();
			}
			$type = 'I';
			if ($this->input->get('download')) {
				$type = 'D';
			}
			$pdf->output('#' . $client->userid . '_' . $client->company . '_matrix_' . _d(date('Y-m-d')) . '.pdf', $type);
		}
	}
	
	public function save_customer_files($id)
	{
		$files_id = $this->input->post('files');
		$profile_file = $this->input->post('profile_file');
		$data['files_id'] = serialize($files_id);
		$data['profile_file_id'] = $profile_file;
		$this->si_export_customer_model->update_client_kyc($data,$id);
		redirect("admin/clients/client/$id?group=export");
	}
	
	public function get_customer_matrix($customer_id)
	{
		if (!has_permission('si_export_customer_matrix', '', 'view')) {
			header('HTTP/1.0 400 Bad error');
			echo _l('access_denied');
			die;
		}

		$from		= $this->input->get('from');
		$to			= $this->input->get('to');

		$data['client'] = $this->clients_model->get($customer_id);
		//get project statuses
		$this->load->model('projects_model');
		$data['project_statuses'] =$statuses = $this->projects_model->get_project_statuses();
		foreach($statuses as $key=>$status)
		{
			$where = "clientid = $customer_id and ( start_date between '".to_sql_date($from)."' and '".to_sql_date($to)."') and  status = ".$status['id'];
			$count = total_rows(db_prefix().'projects',$where);
			$data['project_statuses'][$key]['total']=$count;
		}
		//get statements or Account summery	
		$data['statement'] = $this->clients_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));
		
		//get invoices total
		$inputs = array('customer_id'=>$customer_id,
								'from'=> to_sql_date($from),
								'to'=> to_sql_date($to),
								);
		$data['invoices'] = $this->si_export_customer_model->get_invoices_total($inputs);
		$data['_currency']    = $data['invoices']['currencyid'];
		$data['estimates'] = $this->si_export_customer_model->get_estimates_total($inputs);
		unset($data['estimates']['currencyid']);
		$data['expenses'] = $this->si_export_customer_model->get_expenses_total($inputs);
		$data['tasks'] = $this->si_export_customer_model->get_tasks_total($inputs);
		$data['tickets'] = $this->si_export_customer_model->get_tickets_total($inputs);
		$data['from'] = $from;
		$data['to']   = $to;
		$viewData['html'] = $this->load->view('si_export_customer/includes/_get_client_matrix_data', $data, true);

		echo json_encode($viewData);
	}
	
	public function client_services_report()
	{
		if (!has_permission('si_export_customer_services', '', 'view')) {
			header('HTTP/1.0 400 Bad error');
			echo _l('access_denied');
			die;
		}
	
		$this->load->model('invoice_items_model');
		$data = array();
		$search_list = array();
		$table_data = array();
		$group_list = array();
		if ($this->input->is_ajax_request()) {
			$filter_by = $this->input->post('filter_by')!=''?$this->input->post('filter_by'):'customer';
			if($filter_by=='customer')
			{
				$clients = $this->clients_model->get('',[db_prefix().'clients.active'=>1]);
				$group_list = $this->clients_model->get_groups();
				if(!empty($clients)){
					foreach($clients as $row)
						$search_list[] = array('id'=>$row['userid'],'name'=>$row['company']);
				}
			}
			if($filter_by=='service')
			{
				$services = $this->invoice_items_model->get();
				$group_list = $this->invoice_items_model->get_groups();
				if(!empty($services)){
					foreach($services as $row)
						$search_list[] = array('id'=>$row['itemid'],'name'=>$row['description']);
				}
				
			}
			echo json_encode(array('search_list'=>$search_list,'group_list'=>$group_list));
			exit;
		}
		$filter_by = $this->input->post('filter_by')!=''?$this->input->post('filter_by'):'customer';
		$filter_id = $this->input->post('search_list')!=''?$this->input->post('search_list'):0;
		$group_id = $this->input->post('group_list')!=''?$this->input->post('group_list'):0;
		$group_by  = $this->input->post('group_by')!=''?$this->input->post('group_by'):'';
		$has_permission_view   = has_permission('staff', '', 'view');

		if(!$has_permission_view)	{
			$staff_id = get_staff_user_id();
		}elseif ($this->input->post('member'))	{
			$staff_id = $this->input->post('member');
		}else{
			$staff_id = '';
		}
		if($filter_by=='customer')
		{
			$clients = $this->clients_model->get('',[db_prefix().'clients.active'=>1]);
			$group_list = $this->clients_model->get_groups();
			if(!empty($clients)){
				foreach($clients as $row)
					$search_list[] = array('id'=>$row['userid'],'name'=>$row['company']);
			}
			
			//get list of services of that customer
			if($this->input->server('REQUEST_METHOD')=='POST' || !$this->input->is_ajax_request())
			{
				$result = $this->si_export_customer_model->get_customer_services_list($filter_id,$group_id,$staff_id);
				foreach($result as $row)
				{
					$by='';
					if($group_by =='customer')
						$by = $row['company'];
					elseif($group_by =='item')
						$by = $row['description'];	
					elseif($group_by =='item_group')
						$by = $row['item_group_name'];	
					$table_data[$by][]=$row;
				}
			}
		}
		if($filter_by=='service')
		{
			$services = $this->invoice_items_model->get();
			$group_list = $this->invoice_items_model->get_groups();
			if(!empty($services)){
				foreach($services as $row)
					$search_list[] = array('id'=>$row['itemid'],'name'=>$row['description']);
			}
			
			//get list of customers of that service
			if($this->input->server('REQUEST_METHOD')=='POST')
			{
				$result = $this->si_export_customer_model->get_service_customers_list($filter_id,$group_id,$staff_id);
				foreach($result as $row)
				{
					$by='';
					if($group_by =='customer')
						$by = $row['company'];
					elseif($group_by =='item')
						$by = $row['description'];	
					elseif($group_by =='item_group')
						$by = $row['item_group_name'];	
					$table_data[$by][]=$row;
				}
			}
		}
		
		$data['filter_by'] = $filter_by;
		$data['filter_id'] = $filter_id;
		$data['group_id'] = $group_id;
		$data['group_list'] = $group_list;
		$data['group_by'] = $group_by;
		$data['search_list'] = $search_list;
		$data['table_data'] = $table_data;
		$data['members']  = $this->staff_model->get();
		$data['staff_id'] = $staff_id;
		$data['title']    = _l('si_customer_services');
		$this->load->view('customer_services_report', $data);
	}
}
