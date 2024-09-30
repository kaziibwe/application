<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once(LIBSPATH . 'pdf/App_pdf.php');

class Si_customer_profile_pdf extends App_pdf
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
		$this->SetTitle($client->company);
		// Customer groups
		$groups = $this->ci->clients_model->get_groups();
		$selected = array();
		$customer_groups = $this->ci->clients_model->get_customer_groups($client->userid);
		if(isset($customer_groups)){
			foreach($customer_groups as $group){
				array_push($selected,$group['groupid']);
			}
		}
		$customer_groups_name = array();
		if(!empty($groups))
		{
			foreach($groups as $group)
			{
				if(in_array($group['id'],$selected))
					$customer_groups_name[] = $group['name'];
			}
		}
		$data['customer_groups']= $customer_groups_name;
		$data['client']			= $client;
		$contact = $this->ci->clients_model->get_contact(get_primary_contact_user_id($this->client_id));
		if ($contact) {
			$data['contact'] = $contact;
		}
		
		$path = get_upload_path_by_type('customer').$client->userid.'/';
		$path = './uploads/clients/'.$client->userid.'/';

		$client_kyc_detail = get_client_kyc_details($client->userid);
		if(!empty($client_kyc_detail))
		{
			$profile_logo = $this->ci->si_export_customer_model->get_customer_files($client->userid,array($client_kyc_detail['profile_file_id']));
			$data['profile_logo']=(!empty($profile_logo)?$path.$profile_logo[0]['file_name']:'');
			$files = unserialize($client_kyc_detail['files_id']);
			if(!empty($files))
			{
				$data['files']=array();
				$files_list = $this->ci->si_export_customer_model->get_customer_files($client->userid,$files);
				foreach($files_list as $file)
				{
					$file_name = pathinfo($file['file_name'], PATHINFO_FILENAME);
					$file_name = str_replace("_"," ",$file_name);
					$file_name = str_replace("-"," ",$file_name);
					$data['files'][] = ucfirst($file_name);
					$data['files_with_path'][] = $path.$file['file_name'];
				}
			}
		}
		else
		{
			$data['profile_logo'] = '';
		}	
	
		$this->set_view_vars($data);
	
		return $this->build();
	}
	
	protected function type()
	{
		return 'si-customer-profile';
	}
	
	protected function file_path()
	{
		$customPath = APP_MODULES_PATH.SI_EXPORT_CUSTOMER_MODULE_NAME . '/views/my_customer_export_profile_pdf.php';
		$actualPath = APP_MODULES_PATH.SI_EXPORT_CUSTOMER_MODULE_NAME . '/views/customer_export_profile_pdf.php';
	
		if (file_exists($customPath)) {
			$actualPath = $customPath;
		}
	
		return $actualPath;
	}
	
	public function get_format_array()
	{
		return  [
			'orientation' => (get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_orientation') == 'L'?'L':'P'),
			'format'      => (get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_orientation') == 'L'?'Landscape':'Portrait'),
		];
	}
}
