<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once(LIBSPATH . 'pdf/App_pdf.php');

class Si_customer_matrix_pdf extends App_pdf
{
	protected $client_id;
	
	public function __construct($client_id)
	{
		parent::__construct();
	
		$this->client_id = $client_id;
	}
	
	public function prepare()
	{
		$client = $this->ci->clients_model->get($this->client_id);
		if (!$client) {
			show_404();
		}
		$from = $this->ci->input->get('from');
		$to = $this->ci->input->get('to');
		
		$this->SetTitle($client->company);
		
		$data['client']			= $client;
		//get project statuses
		$this->ci->load->model('projects_model');
		$data['project_statuses'] =$statuses = $this->ci->projects_model->get_project_statuses();
		foreach($statuses as $key=>$status)
		{
			$where = "clientid = ".$this->client_id." and ( start_date between '".to_sql_date($from)."' and '".to_sql_date($to)."') and  status = ".$status['id'];
			$count = total_rows(db_prefix().'projects',$where);
			$data['project_statuses'][$key]['total']=$count;
		}
		//get statements or Account summery	
		$data['statement'] = $this->ci->clients_model->get_statement($this->client_id, to_sql_date($from), to_sql_date($to));
		
		//get invoices total
		$inputs = array('customer_id'=>$this->client_id,
								'from'=> to_sql_date($from),
								'to'=> to_sql_date($to),
								);
		$data['invoices'] = $this->ci->si_export_customer_model->get_invoices_total($inputs);
		$data['_currency']    = $data['invoices']['currencyid'];
		
		$data['estimates'] = $this->ci->si_export_customer_model->get_estimates_total($inputs);
		unset($data['estimates']['currencyid']);
		
		$data['expenses'] = $this->ci->si_export_customer_model->get_expenses_total($inputs);
		
		$data['tasks'] = $this->ci->si_export_customer_model->get_tasks_total($inputs);
		$data['tickets'] = $this->ci->si_export_customer_model->get_tickets_total($inputs);
		
		$data['from'] = $from;
		$data['to']   = $to;	
	
		$this->set_view_vars($data);
	
		return $this->build();
	}
	
	protected function type()
	{
		return 'si-customer-matrix';
	}
	
	protected function file_path()
	{
		$customPath = APP_MODULES_PATH.SI_EXPORT_CUSTOMER_MODULE_NAME . '/views/my_customer_export_matrix_pdf.php';
		$actualPath = APP_MODULES_PATH.SI_EXPORT_CUSTOMER_MODULE_NAME . '/views/customer_export_matrix_pdf.php';
	
		if (file_exists($customPath)) {
			$actualPath = $customPath;
		}
	
		return $actualPath;
	}
	
	public function get_format_array()
	{
		return  [
			'orientation' => (get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_orientation') == 'L'?'L':'P'),
			'format'      => (get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_orientation') == 'L'?'Landscape':'Portrait'),
		];
	}
}
