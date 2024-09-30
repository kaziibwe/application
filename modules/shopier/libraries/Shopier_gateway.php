<?php


use Shopier\Enums;
use Shopier\Exceptions\NotRendererClassException;
use Shopier\Exceptions\RendererClassNotFoundException;
use Shopier\Exceptions\RequiredParameterException;
use Shopier\Models\Address;
use Shopier\Models\Buyer;
use Shopier\Models\ShopierResponse;
use Shopier\Renderers\AutoSubmitFormRenderer;
use Shopier\Shopier;

/**
 * Class Shopier_gateway
 * Shopier payment gateway
 */
class Shopier_gateway extends App_gateway
{
    /**
     * @var Shopier
     */
    protected $shopier;

    /**
     * @var Tcmb
     */
    protected $tcmb;

    /**
     * Shopier_gateway constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('shopier');
        $this->setName('Shopier');


        $this->setSettings(array(
            array(
                'name' => 'api_key',
                'label' => 'shopier_api_key',
                'type' => 'input',
            ),
            array(
                'name' => 'api_secret_key',
                'label' => 'shopier_api_secret_key',
                'encrypted' => true,
                'type' => 'input'
            ),
            array(
                'name' => 'website_index',
                'label' => 'shopier_website_index',
                'type' => 'input',
                'input_type' => 'number',
                'default_value' => 1,
            ),
            array(
                'name' => 'return_url',
                'label' => 'shopier_return_url',
                'type' => 'input',
                'input_type' => 'text',
                'default_value' => site_url('shopier/gateways/shopier/complete_purchase'),
                'field_attributes' => [
                    'disabled' => 'disabled'
                ]
            ),
            array(
                'name' => 'currencies',
                'label' => 'settings_paymentmethod_currencies',
                'default_value' => 'TRY,USD,EUR'
            ),
            array(
                'name' => 'convert_to_try',
                'label' => 'shopier_convert_to_try',
                'type' => 'yes_no',
                'default_value' => 1,
            ),
        ));

        $this->shopier = new Shopier($this->getSetting('api_key'), $this->decryptSetting('api_secret_key'));
        $this->ci->load->library('shopier/tcmb');
        $this->tcmb = $this->ci->tcmb;

        hooks()->add_action('before_render_payment_gateway_settings', [$this, 'shopier_notice']);
    }

    /**
     * Notice under the Shopier tab on payment gateways page
     *
     * @param $gateway
     */
    public function shopier_notice($gateway)
    {
        if ($gateway['id'] == $this->getId()) {
            $shopierUrl = 'https://www.shopier.com/';
            $imageUrl = site_url('modules/shopier/assets/shopier_icon.png');
            echo <<<EOL
<a href="$shopierUrl" target="_blank"><img src="$imageUrl" width="32"></a>
EOL;

        }
    }

    /**
     * Start processing payment
     *
     * @param $data
     * @throws NotRendererClassException
     * @throws RendererClassNotFoundException
     * @throws RequiredParameterException
     */
    public function process_payment($data)
    {
        $this->ci->load->model('clients_model');
        $invoice = $data['invoice'];

        $buyer = $this->prepareBuyer($invoice);
        $billingAddress = $this->prepareBillingAddress($invoice);
        $shippingAddress = $this->prepareShippingAddress($invoice);

        $params = $this->shopier->getParams();
        $params->setWebsiteIndex($this->getSetting('website_index'));
        $params->setBuyer($buyer);
        $params->setBillingAddress($billingAddress);
        $params->setShippingAddress($shippingAddress);

        $params->setOrderData($data['hash'], $data['amount']);
        $params->setProductData(format_invoice_number($invoice->id));
        $params->setProductType(Enums\ProductType::DEFAULT_TYPE);

        switch ($invoice->currency_name) {
            case 'USD':
                $params->setCurrency(Enums\Currency::USD);
                break;
            case 'EUR':
                $params->setCurrency(Enums\Currency::EUR);
                break;
            default:
                $params->setCurrency(Enums\Currency::TL);
                break;
        }

        if ($this->getSetting('convert_to_try') && $invoice->currency_name != 'TRY') {
            $currencyConverter = $this->tcmb->createCurrencyConverter();
            $amountTRY = $currencyConverter->convertToTRY($data['amount'], $invoice->currency_name);
            $amountTRY = round($amountTRY, 2);
            $params->setCurrency(Enums\Currency::TL);
            $params->setTotalOrderValue($amountTRY);
        }

        $params->setCurrentLanguage($invoice->client->default_language == 'turkish' ? Enums\Language::TR : Enums\Language::EN);
        $params->setModulVersion('1.0.8');

//        print_r($params->toArray());die;

        $this->shopier->goWith($this->shopier->createRenderer(AutoSubmitFormRenderer::class));
    }

    /**
     * Check whether response is valid and control hash
     *
     * @param ShopierResponse $shopierResponse
     * @return bool
     */
    public function check_valid_response($shopierResponse)
    {
        return $shopierResponse && $shopierResponse->hasValidSignature($this->decryptSetting('api_secret_key'));
    }

    /**
     * If str is empty then replace it with dash character
     *
     * @param $str
     * @return string
     */
    private function maybeDash($str)
    {
        return $str ? $str : '-';
    }

    /**
     * Prepare buyer data
     *
     * @param $invoice
     * @return Buyer
     */
    protected function prepareBuyer($invoice)
    {
        $contact = $this->ci->clients_model->get_contact($invoice->client->userid);

        if (!$contact) {
            $contact = (object)[
                'firstname' => $invoice->client->company,
                'lastname' => $invoice->client->company,
                'email' => slug_it($invoice->client->company) . '@example.com',
                'phonenumber' => '-',
            ];
        }

        $buyer = new Buyer([
            'id' => $invoice->client->userid,
            'name' => $this->maybeDash($contact->firstname),
            'surname' => $this->maybeDash($contact->lastname),
            'email' => $this->maybeDash($contact->email),
            'phone' => $this->maybeDash($contact->phonenumber)
        ]);

        try {
            $buyer->account_age = (int)((time() - strtotime($invoice->client->datecreated)) / 86400);
        } catch (Exception $e) {
        }

        return $buyer;
    }

    /**
     * Prepare billing address
     *
     * @param $invoice
     * @return Address
     */
    protected function prepareBillingAddress($invoice)
    {
        return new Address([
            'address' => $this->maybeDash($invoice->billing_street) . ' ' . $this->maybeDash($invoice->billing_state),
            'city' => $this->maybeDash($invoice->billing_city),
            'country' => $invoice->billing_country ? get_country($invoice->billing_country)->short_name : '-',
            'postcode' => $this->maybeDash($invoice->billing_zip),
        ]);
    }

    /**
     * Prepare shipping address
     *
     * @param $invoice
     * @return Address
     */
    protected function prepareShippingAddress($invoice)
    {
        return new Address([
            'address' => $this->maybeDash($invoice->shipping_street) . ' ' . $this->maybeDash($invoice->shipping_state),
            'city' => $this->maybeDash($invoice->shipping_city),
            'country' => $invoice->shipping_country ? get_country($invoice->shipping_country)->short_name : '-',
            'postcode' => $this->maybeDash($invoice->shipping_zip),
        ]);
    }


}