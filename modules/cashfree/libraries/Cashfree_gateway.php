<?php
defined('BASEPATH') or exit('No direct script access allowed');
use GuzzleHttp\Client;
class Cashfree_gateway extends App_gateway
{
	protected $sandbox_url = 'https://sandbox.cashfree.com/pg/';
	protected $production_url = 'https://api.cashfree.com/pg/';
	
	public function __construct()
	{
		parent::__construct();
	
		$this->setId('cashfree');
		$this->setName('Cashfree Payments');
		$this->setSettings([
			[
				'name'      => 'apikey',
				'encrypted' => true,
				'label'     => 'settings_paymentmethod_cashfree_api',
			],
			[
				'name'      => 'apisecret',
				'encrypted' => true,
				'label'     => 'settings_paymentmethod_cashfree_api_secret',
			],
			[
				'name'      => 'version',
				'label'     => 'settings_paymentmethod_cashfree_version',
				'default_value' => '2022-09-01',
				'field_attributes'=>['disabled'=>true],
			],
			[
				'name'          => 'description_dashboard',
				'label'         => 'settings_paymentmethod_description',
				'type'          => 'textarea',
				'default_value' => 'Payment for Invoice {invoice_number}',
			],
			[
				'name'          => 'currencies',
				'label'         => 'settings_paymentmethod_currencies',
				'default_value' => 'INR',
			],
			[
				'name'          => 'test_mode_enabled',
				'type'          => 'yes_no',
				'default_value' => 1,
				'label'         => 'settings_paymentmethod_testing_mode',
			]
		]);
	}
	public function get_action_url()
	{
		return $this->getSetting('test_mode_enabled') == '1' ? $this->sandbox_url : $this->production_url;
	}
	public function process_payment($data)
	{
		try {
			$invoice = $data['invoice'];
			$amount = number_format($data['amount'], 2, '.', '');
			$contact = is_client_logged_in() ? $this->ci->clients_model->get_contact(get_contact_user_id()) : null;
			
			$json_data = array(
				"order_id" => 'order_'.time(),
				'order_currency'   => $invoice->currency_name,
				'order_amount' => $amount,
				"customer_details" => [
					"customer_id" 		=> $invoice->clientid,
					"customer_name" 	=> $contact ? $contact->firstname . ' ' . $contact->lastname : '',
					"customer_email" 	=> $contact ? $contact->email : '',
					"customer_phone" 	=> $contact ? $contact->phonenumber : '1234512345',
				],
				"order_meta" => [
					"return_url"=> site_url("cashfree/success/".$invoice->id . "/" . $invoice->hash."?order_id={order_id}"),
				],
				"order_note" => str_replace(
					'{invoice_number}',
					format_invoice_number($invoice->id),
					$this->getSetting('description_dashboard')
				),
			);
			$order = $this->_createOrder($json_data);
			if (isset($order["payment_session_id"]) && $order["payment_session_id"] !== '') {
				$this->_updateInvoiceTokenData(
					json_encode(['amount' => $amount, 'cashfree_order_id' => $order["order_id"]]),
					$data['invoice']->id
				);
				$this->ci->session->set_userdata(['cashfree_payment_session_id' => $order["payment_session_id"]]);
				redirect(site_url('cashfree/pay/' . $data['invoice']->id . '/' . $data['invoice']->hash));
			}
			set_alert('warning',$result['message']);
			redirect(site_url('invoice/' . $data['invoice']->id . '/' . $data['invoice']->hash));
		} catch (\Exception $e) {
			$response = json_decode($e->getResponse()->getBody()->getContents(), true);
			set_alert('warning',$response['message']);
			redirect(site_url('invoice/' . $data['invoice']->id . '/' . $data['invoice']->hash));
		}
		return false;
	}
	public function verifyPayment($order_id)
	{
		try {
			$order = $this->_getOrderById($order_id);
			if(isset($order['order_status'])){
				if($order['order_status']=='PAID')
					return ['success' => true,'amount'=>$order['order_amount']];
				else
					return ['success' => false,'error'=>'Transaction is Fail'];	
			}
			else
				return ['success' => false,'error'=>$order['message']];
			
		} catch (Exception $e) {
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}
	public function recordPayment($cashfree_order_id, $amount, $invoice)
	{
		$tokenData = json_decode($invoice->token);
		if ($tokenData->cashfree_order_id != $cashfree_order_id) {
			return ['success' => false, 'error' => 'Returned order ID does not matched stored order ID.'];
		}
		$payment = $this->_getPaymentByOrderId($cashfree_order_id);
		try {
			if ($invoice->status !== Invoices_model::STATUS_PAID && 
				$invoice->status !== Invoices_model::STATUS_CANCELLED && 
				$payment['payment_status']=='SUCCESS' && $payment['is_captured']) {
				
				$this->_updateInvoiceTokenData(null, $invoice->id);
				$success = $this->addPayment([
						'amount'        => $amount,
						'invoiceid'     => $invoice->id,
						'transactionid' => $cashfree_order_id,
						'paymentmethod' => $payment['payment_group'],
				]);
				if($this->ci->session->has_userdata('cashfree_payment_session_id'))
					$this->ci->session->unset_userdata('cashfree_payment_session_id');
				$message = _l($success ? 'online_payment_recorded_success' : 'online_payment_recorded_success_fail_database');
				return [
					'success' => $success,
					'message' => $message,
				];
			}
			return ['success' => false, 'error' => _l('invalid_transaction')];
		} catch (Exception $e) {
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}
	public function _updateInvoiceTokenData($data, $invoice_id)
	{
		$this->ci->db->where('id', $invoice_id);
		$this->ci->db->update(db_prefix() . 'invoices', [
			'token' => $data,
		]);
	}
	private function _createOrder($data)
	{
		$url = $this->get_action_url().'orders';
		$client = new Client();
		$response = $client->request('POST', $url, [
			'body' => json_encode($data),
			'headers' => [
				'accept' => 'application/json',
				'content-type' => 'application/json',
				'x-api-version' => $this->getSetting('version'),
				'x-client-id' => $this->decryptSetting('apikey'),
				'x-client-secret' => $this->decryptSetting('apisecret'),
			],
		]);
		$result = json_decode($response->getBody(), true);
		return $result;
	}
	private function _getOrderById($order_id)
	{
		$url = $this->get_action_url()."orders/".$order_id;
		$client = new Client();
		$response = $client->request('GET', $url, [
			'headers' => [
				'accept' => 'application/json',
				'content-type' => 'application/json',
				'x-api-version' => $this->getSetting('version'),
				'x-client-id' => $this->decryptSetting('apikey'),
				'x-client-secret' => $this->decryptSetting('apisecret'),
			],
		]);
		$result = json_decode($response->getBody(), true);
		return $result;
	}
	private function _getPaymentByOrderId($order_id)
	{
		$url = $this->get_action_url()."orders/".$order_id."/payments";
		$client = new Client();
		$response = $client->request('GET', $url, [
			'headers' => [
				'accept' => 'application/json',
				'x-api-version' => $this->getSetting('version'),
				'x-client-id' => $this->decryptSetting('apikey'),
				'x-client-secret' => $this->decryptSetting('apisecret'),
		  	],
		]);
		$result = json_decode($response->getBody(),true);
		return $result[0];
	}
}