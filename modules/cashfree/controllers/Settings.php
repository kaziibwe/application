<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Settings extends AdminController 
{
	public function __construct()
	{
		parent::__construct(); 
		if (!is_admin() && !has_permission('settings', '', 'view')) {
			access_denied(_l('cashfree'));
		}
	}
	public function index()
	{
		redirect(admin_url());
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
				CURLOPT_URL            => SI_CASHFREE_VALIDATION_URL,
				CURLOPT_POST           => 1,
				CURLOPT_POSTFIELDS     => [
					'url' => site_url(),
					'module'     => SI_CASHFREE_KEY,
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
