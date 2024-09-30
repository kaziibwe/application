<?php


use Shopier\Models\ShopierResponse;

/**
 * @property Shopier_gateway shopier_gateway
 */
class Shopier extends App_Controller
{
    /**
     * Return url page controller method
     */
    public function complete_purchase()
    {
        $this->load->library('shopier/shopier_gateway');

        $shopierResponse = ShopierResponse::fromPostData();

        if ($this->shopier_gateway->check_valid_response($shopierResponse)) {
            $invoice = $this->db
                ->where('hash', $shopierResponse->getPlatformOrderId())
                ->get(db_prefix() . 'invoices')
                ->row();

            if ($shopierResponse->isSuccess()) {
                $success = $this->shopier_gateway->addPayment([
                    'amount' => $invoice->total,
                    'invoiceid' => $invoice->id,
                    'transactionid' => $shopierResponse->getPaymentId()
                ]);

                if ($success) {
                    set_alert('success', _l('online_payment_recorded_success'));
                } else {
                    set_alert('danger', _l('online_payment_recorded_success_fail_database'));
                }
            } else {
                set_alert('warning', 'Payment could not received');
            }

            redirect(site_url('invoice/' . $invoice->id . '/' . $invoice->hash));

        }

        die('.');
    }
}