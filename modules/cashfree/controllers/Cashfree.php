<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Cashfree extends App_Controller
{
	public function pay($id, $hash)
	{
		check_invoice_restrictions($id, $hash);
		$this->load->model('invoices_model');
		$invoice  = $this->invoices_model->get($id);
		$language = load_client_language($invoice->clientid);
		$tokenData = json_decode($invoice->token);
		$data['invoice'] = $invoice;
		if($this->session->has_userdata('cashfree_payment_session_id') && isset($tokenData->amount)){
			$data['amount'] = $tokenData->amount;
			$data['payment_session_id'] = $this->session->userdata('cashfree_payment_session_id');
			$data['mode']= $this->cashfree_gateway->getSetting('test_mode_enabled') == 1 ? 'test' :'prod';
			$this->load->view('pay', $data);
		}
		else
			redirect(site_url('invoice/' . $id . '/' . $hash));
	}
	public function success($id, $hash)
	{
		check_invoice_restrictions($id, $hash);
		$this->load->model('invoices_model');
		$invoice  = $this->invoices_model->get($id);
		$language = load_client_language($invoice->clientid);
		$order_id   = $this->input->get('order_id');
		$result = $this->cashfree_gateway->verifyPayment($order_id);
		if ($result['success'] === false) {
			set_alert('warning', $result['error']);
		} else {
			$result = $this->cashfree_gateway->recordPayment($order_id,$result['amount'], $invoice);
			if ($result['success'] === false) {
				set_alert('warning', $result['error']);
			} else {
				set_alert('success', $result['message']);
			}
		}
		redirect(site_url('invoice/' . $id . '/' . $hash));
	}
}