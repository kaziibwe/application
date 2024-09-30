<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



//require_once(APP_MODULES_PATH. 'quickbooks/libraries/vendor/autoload.php');



require_once(APP_MODULES_PATH . 'quickbooks/libraries/vendor/quickbooks/v3-php-sdk/src/config.php');


use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Core\OAuth\OAuth1\OAuth1;
use QuickBooksOnline\API\Exception\SdkException;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2AccessToken;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\Payment;
use QuickBooksOnline\API\Facades\Purchase;
use QuickBooksOnline\API\Facades\Account;
use QuickBooksOnline\API\Facades\PaymentMethod;
use QuickBooksOnline\API\Facades\TaxAgency;
use QuickBooksOnline\API\Facades\TaxRate;
use QuickBooksOnline\API\Facades\TaxService;

/**



 * @author Pavel Espinal



 */







class Quickbooks
{



    /**



     *



     * @var CI_Controller



     */







    private $CI;







    private $dataService;







    private $accessToken;







    public function __construct()
    {

        $this->CI = &get_instance();

        if (!$this->CI->session instanceof CI_Session) {

            throw new RuntimeException("Attempting to load Quickbooks library in absence of CI_Session.");
        }



        $this->setDataService();
    }







    private function setDataService()
    {

        $this->refresh_token();

        $lbReturn = false;

        try {

            $this->CI->db->select('*');

            $this->CI->db->from('tbloptions');

            $this->CI->db->where('name', 'quickbook_refresh_token');

            $db_data =  $this->CI->db->get()->row();

            $db_refresh_token = $db_data->value;



            //-----------------------------------

            $this->CI->db->select('*');

            $this->CI->db->from('tbloptions');

            $this->CI->db->where('name', 'quickbook_access_token');

            $db_data =  $this->CI->db->get()->row();

            $db_access_token = $db_data->value;

            //-----------------------------------

            $this->CI->db->select('*');

            $this->CI->db->from('tbloptions');

            $this->CI->db->where('name', 'quickbook_realmId');

            $db_data =  $this->CI->db->get()->row();

            $db_quickbook_realmId = $db_data->value;







            $this->dataService = DataService::Configure(array(

                'auth_mode'         => 'oauth2',

                'ClientID'          => get_option('quickbook_client_id'),

                'ClientSecret'      => get_option('quickbook_client_secret'),

                'accessTokenKey'    => $db_access_token,

                'refreshTokenKey'   => $db_refresh_token,

                'QBORealmID'        => $db_quickbook_realmId,

                'scope'             => 'com.intuit.quickbooks.accounting openid profile email phone address', 'hidden',

                'RedirectURI'       => site_url('/admin/quickbooks/check_auth_quickbook'),

                'baseUrl'           => get_option('is_quickbooks_app_in_production_mode')

            ));



            $lbReturn = true;
        } catch (Exception $ex) {

            log_activity("Error : There was a problem while initializing DataService.\n" . $ex->getMessage());
            log_message('error', "There was a problem while initializing DataService.\n" . $ex->getMessage());
            return;
        }







        return $lbReturn;
    }





    /**

     * Get Data Service

     * 

     * @return QuickBooksOnline\API\DataService\DataService

     * @throws Exception

     */

    public function getDataService()
    {

        if (!$this->dataService instanceof DataService) {

            throw new Exception("The DataService object of the Quickbooks SDK is not ready.");
        } else {

            return $this->dataService;
        }
    }







    // used within same class

    public function generate_accessToken($data)
    {

        if (!empty($data)) {

            try {

                $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();

                $parseUrl = $this->parseAuthRedirectUrl($_SERVER['QUERY_STRING']);

                $this->accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);

                $this->dataService->updateOAuth2Token($this->accessToken);

                $this->CI->session->set_userdata('quickbook_access_token', $this->accessToken->getAccessToken());

                $this->CI->session->set_userdata('quickbook_refresh_token', $this->accessToken->getRefreshToken());

                $this->CI->session->set_userdata('realmId', $parseUrl['realmId']);

                return 1;
            } catch (Exception $ex) {

                log_activity("Error : There was a problem generating access token.\n" . $ex->getMessage());
                redirect('/admin/quickbooks/quickbooks_process');
            }
        }
    }







    function parseAuthRedirectUrl($url)
    {

        parse_str($url, $qsArray);

        return array(

            'code' => $qsArray['code'],

            'realmId' => $qsArray['realmId']

        );
    }











    public function refresh_token()
    {

        try {

            // get db referesh token

            $this->CI->db->select('*');

            $this->CI->db->from('tbloptions');

            $this->CI->db->where('name', 'quickbook_refresh_token');

            $db_data =  $this->CI->db->get()->row();

            $db_refresh_token = $db_data->value;



            $oauth2LoginHelper = new OAuth2LoginHelper(get_option('quickbook_client_id'), get_option('quickbook_client_secret'));

            $accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($db_refresh_token);



            $quickbook_refresh_token = $accessTokenObj->getRefreshToken();

            $quickbook_access_token = $accessTokenObj->getAccessToken();



            $this->CI->session->set_userdata('quickbook_access_token', $quickbook_access_token);

            $this->CI->session->set_userdata('quickbook_refresh_token', $quickbook_refresh_token);





            $token_data = array(

                array(

                    'name'  => 'quickbook_refresh_token',

                    'value' => $quickbook_refresh_token

                ),

                array(

                    'name'  => 'quickbook_access_token',

                    'value' => $quickbook_access_token

                )

            );



            $this->storeQbRefreshToken($token_data);



            return 1;
        } catch (Exception $ex) {







            $error = $oauth2LoginHelper->getLastError();


            log_activity("Error : There was a problem  in refresh token.\n");





            redirect('/admin/quickbooks/quickbooks_process');
        }
    }



    function storeQbRefreshToken($token_data)
    {

        // set refresh token in db option table

        $this->CI->db->select('*');

        $this->CI->db->from('tbloptions');

        $this->CI->db->where('name', 'quickbook_refresh_token');

        $check_access_token =  $this->CI->db->get()->result_array();



        if (empty($check_access_token)) {

            $this->CI->db->insert_batch('tbloptions', $token_data);
        } else {

            $this->CI->db->update_batch('tbloptions', $token_data, 'name');
        }
    }
    // $data = posted data from crm

    public function create_invoice($data, $flag = 0)
    {


        $customer_data = array();

        $item_data = array();

        if (!empty($data)) {

            // customer creation and checking

            $customer_data = $this->check_customer_exist_or_not($data);

            if (empty($customer_data)) {

                $customer_data = $this->create_customer($data);
            }

            // item creation and checking 
            foreach ($data->items as $key => $value) {

                $items = $this->check_item_exist_or_not($value);

                if (!empty($items)) {

                    $item_data[] = $items;
                } else {


                    $item_data[] = $this->create_items($value);
                }
            }

            // create invoice 
            $invoice_id = $this->check_invoice_exist_or_not($data);

            if (!isset($invoice_id[0])) {

                $invoice_id = $this->create_invoice_data($data, $customer_data, $item_data);
            } else {
                $invoice_id = $invoice_id[0]->Id;
            }


            if (!$flag) {
                if ($invoice_id) {

                    $payment = $this->make_invoice_payment($data, $customer_data, $invoice_id);

                    return $payment;
                }
            } else {
                return;
            }
        } else {



            redirect(site_url('/admin/dashboard'));
        }
    }

    function create_customer($user)
    {

        $theResourceObj = Customer::create([

            "BillAddr" => [

                "Line1" => $user->client->billing_street,

                "City" => $user->client->billing_city,

                "Country" => $user->client->billing_country,

                "CountrySubDivisionCode" => '',

                "PostalCode" => $user->client->billing_zip

            ],





            "Notes" => $user->clientnote,

            "Title" => "",

            "GivenName" => $user->client->company,

            "MiddleName" => "",

            "FamilyName" => "",

            "Suffix" => "",

            "FullyQualifiedName" => $user->client->company,

            "CompanyName" => $user->client->company,

            "DisplayName" => $user->client->company,

            "PrimaryPhone" => [

                "FreeFormNumber" => $user->client->phonenumber

            ],

            "PrimaryEmailAddr" => [

                "Address" => (get_client_primary_email($user->client->userid)  != null) ? get_client_primary_email($user->client->userid) : ""

            ]

        ]);


        $resultingObj = $this->dataService->Add($theResourceObj);

        $error = $this->dataService->getLastError();



        if ($error) {
            add_notification(array('description' => "Error : There was a problem in creating " . $user->client->company . " customer " . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity("Error : There was a problem in creating customer.\n" . $error->getResponseBody());
        } else {

            return $resultingObj;
        }

        // Reference url: https://github.com/IntuitDeveloper/SampleApp-CRUD-PHP/blob/master/CRUD_Examples/Customer/CustomerCreate.php

    }

    function check_customer_exist_or_not($user)
    {

        try {
            $customer = $this->dataService->Query("SELECT * FROM Customer where GivenName='" . $user->client->company . "'");
        } catch (Exception $ex) {
            add_notification(array('description' => "Error: In fetching Customer " . $ex->getMessage(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In fetching Customer ' . $ex->getMessage());
        }

        $error = $this->dataService->getLastError();
        if ($error) {
            add_notification(array('description' => "Error: In fetching Customer " . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In fetching Customer ' . $error->getResponseBody());
        }
        if (isset($customer[0])) {

            return $customer[0];
        } else {

            return false;
        }
    }

    function check_item_exist_or_not($item)
    {

        try {
            $item = $this->dataService->Query("SELECT * FROM Item where Name='" . $item['description'] . "'");
        } catch (Exception $ex) {
            add_notification(array('description' => "Error: In fetching Item " . $ex->getMessage(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In fetching Item' . $ex->getMessage());
        }



        $error = $this->dataService->getLastError();
        if ($error) {
            add_notification(array('description' => "Error: In fetching Item " . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In fetching Item' . $error->getResponseBody());
        }


        if (isset($item[0]) || $item != null) {

            return $item[0];
        } else {

            return false;
        }
    }

    function create_items($item)
    {
        $CI = &get_instance();
        $CI->load->library(QUICKBOOKS_MODULE . '/quickbook_expenses');

        try {
            $payment_method = $CI->quickbook_expenses->check_payment_accounts_exist_or_not('Items');

            if (empty($payment_method)) {
                $payment_method = $CI->quickbook_expenses->create_payment_accounts('Items');
            }

            $Item = Item::create([



                "Name" => $item['description'],

                "Description" => $item['long_description'],

                "Active" => true,

                "Taxable" => true,

                "UnitPrice" => $item['rate'],

                "Type" => "Service",

                "IncomeAccountRef" => ["value" => $payment_method->Id, "name" => $payment_method->Name],

                "PurchaseCost" => $item['rate'],

                "ExpenseAccountRef" => ["value" => $payment_method->Id, "name" => $payment_method->Name],

                "AssetAccountRef" => ["value" => $payment_method->Id, "name" => $payment_method->Name],

                "InvStartDate" =>  date('Y-m-d H:i:s', strtotime("-1 days")),

            ]);
        } catch (Exception $ex) {
            add_notification(array('description' => 'Error: In creating ' . $item['description'] . ' Item' . $ex->getMessage(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In creating ' . $item['description'] . ' Item ' . $ex->getMessage());
        }




        $resultingObj = $this->dataService->Add($Item);

        $error = $this->dataService->getLastError();

        if ($error) {

            add_notification(array('description' => 'Error: In creating ' . $item['description'] . ' Item' . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In creating ' . $item['description'] . ' Item ' . $error->getResponseBody());
        } else {

            return $resultingObj;
        }
    }
    function is_exist_taxCode($sum)
    {
        if ($sum == "TaxRate0") {
            $agency = $this->dataService->Query("SELECT * FROM TaxCode where Name='SalesTaxRate0'");
            if (isset($agency[0])) {
                return $agency[0];
            } else {
                return 0;
            }
        } else {

            $agency = $this->dataService->Query("SELECT * FROM TaxCode where Name='" . $sum . "'");
          
            if (isset($agency[0])) {
                return $agency[0];
            } else {
                return 0;
            }
        }
    }
    function is_exist_taxCode_for_expenses($sum)
    {
        if ($sum == "TaxRate0") {

            $agency = $this->dataService->Query("SELECT * FROM TaxCode where Name='ExpensesTaxRate0'");
        } else {
            $agency = $this->dataService->Query("SELECT * FROM TaxCode where Name='" . $sum . "'");
        }
        if (isset($agency[0])) {
            return $agency[0];
        } else {
            return 0;
        }
    }
    function is_exist_taxAgency()
    {
        $agency = $this->dataService->Query("SELECT * FROM TaxAgency where Name='TaxAgency'");
        if (isset($agency[0])) {
            return $agency[0];
        } else {
            return 0;
        }
    }

    function create_taxAgency()
    {
        $agency =  [
            "DisplayName" => "TaxAgency"
        ];

        $theResourceObj = TaxAgency::create($agency);


        $resultingObj = $this->dataService->Add($theResourceObj);

        $error = $this->dataService->getLastError();





        if ($error) {

            add_notification(array('description' => 'Error: In creating TaxAgency' . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In creating TaxAgency' . $error->getResponseBody());
        } else {

            return $resultingObj;
        }
    }

    function create_taxCode($agency, $item, $taxCodeName)
    {
        try {

            foreach ($item['taxname'] as $tax) {
                $taxRate = explode("|", $tax['taxname'])[1];
                $taxName = explode("|", $tax['taxname'])[0];

                $TaxRateObject =   $this->is_taxRate_exists("Sales_" . $taxName);
                if ($TaxRateObject) {
                    $currentTaxServiceDetail = TaxRate::create([
                        "TaxRateId" =>  $TaxRateObject->Id,
                    ]);
                } else {
                    $currentTaxServiceDetail = TaxRate::create([
                        "TaxRateName" =>  "Sales_" . $taxName,
                        "RateValue" =>  $taxRate,
                        "TaxAgencyId" => $agency->Id
                    ]);
                }
                $TaxRateDetails[] = $currentTaxServiceDetail;
            }

            $theResourceObj = TaxService::create([
                "TaxCode" =>  $taxCodeName,
                "TaxRateDetails" => $TaxRateDetails
            ]);



            $resultingObj = $this->dataService->Add($theResourceObj);

            $error = $this->dataService->getLastError();





            if ($error) {
                add_notification(array('description' => 'Error: In creating TaxCode' . $taxName . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
                log_activity('Error: In creating TaxCode' . $taxName . $error->getResponseBody());
            } else {
                return $resultingObj->TaxService->TaxCodeId;
            }
        } catch (Exception $ex) {
            add_notification(array('description' => 'Error: In creating TaxCode' . $sum . $ex->getMessage(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Exception: In creating TaxCode' . $sum . $ex->getMessage());
        }
    }
    function is_taxRate_exists($name)
    {
        try {
            $taxRate = $this->dataService->Query("SELECT * FROM TaxRate where Name='" . $name . "'");
            $error = $this->dataService->getLastError();

            if ($error) {
                log_activity('Error: In TaxRate" ' . $error->getHttpStatusCode());
                log_message('error', "Checking TaxRate exist or not \n" . $error->getHttpStatusCode());
            } else {

                if (isset($taxRate[0])) {
                    return $taxRate[0];
                } else {
                    return 0;
                }
            }
        } catch (Exception $ex) {
            log_activity('Error: In TaxRate"' . $error->getHttpStatusCode());
            log_message('error', "Checking TaxRate exist or not \n" . $ex->getMessage());
        }
    }
    function create_taxCode_for_expenses($agency, $taxes)
    {
        try {
            $sum = 0;
            $codeName = '';
            foreach ($taxes as $tax) {
                if (isset($tax->taxrate)) {
                    $codeName = $codeName . $tax->name . $tax->taxrate . "% ,";
                    $sum += $tax->taxrate;
                    $TaxRateObject =   $this->is_taxRate_exists($tax->name);
                    if ($TaxRateObject) {
                        $currentTaxServiceDetail = TaxRate::create([
                            "TaxRateId" =>  $TaxRateObject->Id,
                            "TaxApplicableOn" => "Purchase"
                        ]);
                    } else {
                        $currentTaxServiceDetail = TaxRate::create([
                            "TaxRateName" =>  $tax->name,
                            "RateValue" =>  $tax->taxrate,
                            "TaxAgencyId" => $agency->Id,
                            "TaxApplicableOn" => "Purchase"
                        ]);
                    }
                    $TaxRateDetails[] = $currentTaxServiceDetail;
                }
            }

            $TaxService = TaxService::create([
                "TaxCode" =>  $codeName,
                "TaxRateDetails" => $TaxRateDetails
            ]);

            $resultingObj = $this->dataService->Add($TaxService);

            $error = $this->dataService->getLastError();





            if ($error) {
                add_notification(array('description' => 'Error: In creating TaxCode' . $sum . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
                log_activity('Error: In creating TaxCode' . $sum . $error->getResponseBody());
            } else {

                return $resultingObj->TaxService->TaxCodeId;
            }
        } catch (Exception $ex) {
            add_notification(array('description' => 'Error: In creating TaxCode' . $sum . $ex->getMessage(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In creating TaxCode' . $sum . $ex->getMessage());
        }
    }

    //Reference url: https://github.com/intuit/QuickBooks-V3-PHP-SDK/blob/master/src/_Samples/InvoiceCreate.php

    //$invoice_data= posted data from crm

    function create_invoice_data($invoice_data, $customer_data, $items_data)
    {

        try {

            $agency = $this->is_exist_taxAgency();
            if (!$agency) {
                $agency =  $this->create_taxAgency();
            }

            $items = array();
            foreach ($items_data as $key => $item_data) {


                foreach ($invoice_data->items as $innerKey => $item) {
                    $invoice_data->items[$innerKey]['taxname']          = get_invoice_item_taxes($invoice_data->items[$innerKey]['id']);
                }
                $sum = 0;
                $taxName = '';
                if (isset($invoice_data->items[$key]['taxname'])) {

                    foreach ($invoice_data->items[$key]['taxname'] as $tax) {
                        $sum = $sum + explode("|", $tax['taxname'])[1];
                        $taxName = $taxName . explode("|", $tax['taxname'])[0] . " " . explode("|", $tax['taxname'])[1] . "% ,,";
                    }
                }

                if (get_option('xero_company_country') != "US") {

                    if (!$sum) {
                        $taxCode = $this->is_exist_taxCode("TaxRate0");
                        $taxCodeId = $taxCode->Id;
                    } else {
                        $taxCode = $this->is_exist_taxCode("Sales_" . $taxName);
                        if (!$taxCode) {
                            $taxCode = $this->create_taxCode($agency, $invoice_data->items[$key], "Sales_" . $taxName);
                            
                        }
                        if (isset($taxCode)) {
                            if (isset($taxCode->Id)) {
                                $taxCodeId = $taxCode->Id;
                            } else {
                                $taxCodeId = $taxCode;
                            }
                        } else {
                            add_notification(array('description' => "Error! in  Invoice creating in QuickBooks against Perfex Invoice# = " . format_invoice_number($invoice_data->id), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'link' => 'invoices/invoice/' . $invoice_data->id, 'isread' => 0));
                            log_activity("Error! in  Invoice creating in QuickBooks against Perfex Invoice# = " . format_invoice_number($invoice_data->id));
                        }
                    }
                } else {
                    if ($sum) {
                        $taxCodeId = 'Tax';
                    } else {
                        $taxCodeId = 'NON';
                    }
                }
                $items[] = array(

                    'DetailType' => 'SalesItemLineDetail',
                    'Description' => $invoice_data->items[$key]['long_description'],
                    'Amount' => $invoice_data->items[$key]['qty'] * $invoice_data->items[$key]['rate'],

                    'SalesItemLineDetail' =>

                    array(

                        "TaxCodeRef" =>

                        array(

                            "value" => $taxCodeId,

                        ),



                        "UnitPrice" => $invoice_data->items[$key]['rate'],

                        "Qty" => $invoice_data->items[$key]['qty'],

                        'ItemRef' =>

                        array(

                            'name' => $item_data->Name,

                            'value' => $item_data->Id,

                        ),



                    )



                );
            }




            $discounty_array = array(



                array(
                    "DetailType" => "DiscountLineDetail",

                    "DiscountLineDetail" =>

                    array(

                        "PercentBased" => true,

                        "DiscountPercent" => $invoice_data->discount_percent

                    )

                )



            );

            // prepare to create invoice

            $doc_number = $this->getFormattedInvNumber($invoice_data);

            $theResourceObj = Invoice::create([

                "DocNumber" => $doc_number,

                "Line" => array_merge($items, $discounty_array),

                "CustomerRef" => [

                    "value" => $customer_data->Id

                ],
                "TxnTaxDetail" => [

                    "TotalTax" => $invoice_data->total_tax

                ],
            ]);

            $resultingObj = $this->dataService->Add($theResourceObj);
            $error = $this->dataService->getLastError();





            if ($error) {

                add_notification(array('description' => "Error! in  Invoice creating in QuickBooks against Perfex Invoice# = " . format_invoice_number($invoice_data->id) . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'link' => 'invoices/invoice/' . $invoice_data->id, 'isread' => 0));
                log_activity("Error! in (create_customer())  Invoice creating in QuickBooks against Perfex Invoice# = " . format_invoice_number($invoice_data->id) . $error->getResponseBody());
            } else {
                add_notification(array('description' => "Invoice created in QuickBooks with Invoice# = " . format_invoice_number($invoice_data->id) . " against the Perfex Invoice Invoice#: = " . format_invoice_number($invoice_data->id), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'link' => 'invoices/invoice/' . $invoice_data->id, 'isread' => 0));
                log_activity("Invoice created in QuickBooks with Invoice# = " . format_invoice_number($invoice_data->id) . " against the Perfex Invoice Invoice#: = " . format_invoice_number($invoice_data->id));
                set_quickBooks_company_realmId($invoice_data->id, 'invoices');
            }
        } catch (Exception $ex) {

            add_notification(array('description' => "Error! in  Invoice creating in QuickBooks against Perfex Invoice# = " . format_invoice_number($invoice_data->id) . $ex->getMessage(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'link' => 'invoices/invoice/' . $invoice_data->id, 'isread' => 0));
            log_activity("Error! in  Invoice creating in QuickBooks against Perfex Invoice# = " . format_invoice_number($invoice_data->id) . $ex->getMessage());
        }
    }

    function create_paymentMethod($name)
    {
        $pamentMethod = PaymentMethod::create([
            "Name" => $name
        ]);
        $resultingPamentMethod = $this->dataService->Add($pamentMethod);

        $error = $this->dataService->getLastError();


        if ($error) {

            log_activity("Error : There was a problem in PaymentMethod.\n" . $error->getResponseBody());
            log_message('error', "There was a problem in PaymentMethod \n" . $error->getResponseBody());
        } else {
            return $resultingPamentMethod;
        }
    }
    function is_exist_PaymentMethod($name)
    {
        try {
            $PaymentMethod = $this->dataService->Query("SELECT * FROM PaymentMethod where Name='" . $name . "'");

            $error = $this->dataService->getLastError();

            if ($error) {
                log_activity('Error: In PaymentMethod" . $error->getHttpStatusCode()');
                log_message('error', "Checking PaymentMethod exist or not \n" . $error->getHttpStatusCode());
            } else {
                if (isset($PaymentMethod[0])) {
                    return $PaymentMethod[0];
                } else {
                    return 0;
                }
            }
        } catch (Exception $ex) {
            log_activity('Error: In PaymentMethod" . $error->getHttpStatusCode()');
            log_message('error', "Checking PaymentMethod exist or not \n" . $ex->getMessage());
        }
    }
    // payment= posted data from crm

    function make_invoice_payment($payment, $customer_data, $invoice_Id)
    {
        try {

            // prepare to do payment in invoice

            foreach ($payment->payments as $payment_data) {



                if (!empty($payment_data)) {

                    $PaymentMethod = $this->is_exist_PaymentMethod(get_paymentMethod_for_quickBooks($payment_data['paymentmode']));
                    if (!$PaymentMethod) {
                        $PaymentMethod =  $this->create_paymentMethod(get_paymentMethod_for_quickBooks($payment_data['paymentmode']));
                    }
                    $CI = &get_instance();
                    $CI->load->model('invoices_model');
                    $invoice_data = $CI->invoices_model->get($payment_data['invoiceid']);
                    $theResourceObj = Payment::create([

                        "CustomerRef" =>

                        [

                            "value" => $customer_data->Id

                        ],


                        "PaymentMethodRef" =>  [
                            "value" => $PaymentMethod->Id
                        ],
                        "PaymentRefNum" => $this->getFormattedInvNumber($invoice_data),
                        "TotalAmt" => $payment_data['amount'],

                        "TxnDate" => $payment_data['date'],

                        "Line" => [

                            [



                                "Amount" => $payment_data['amount'],

                                "LinkedTxn" => [

                                    [



                                        "TxnId" => $invoice_Id,

                                        "TxnType" => "Invoice"



                                    ]
                                ]



                            ]
                        ]



                    ]);

                    $resultingObj = $this->dataService->Add($theResourceObj);
                    $error = $this->dataService->getLastError();

                    if ($error) {
                        add_notification(array('description' => "Error! in  Payment creating in QuickBooks against Perfex Payment ID# = " . $payment_data['paymentid'] . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'link' => 'invoices/invoices/' . $payment_data['paymentid'], 'isread' => 0));
                        log_activity("Error! in  Payment creating in QuickBooks against Perfex Payment ID# = " . $payment_data['paymentid'] . $error->getResponseBody());
                        log_message('error', "Payment creating in QuickBooks against Perfex Payment ID# = " . $payment_data['paymentid'] . $error->getResponseBody());
                    } else {
                        add_notification(array('description' => "Payment created in QuickBooks against Perfex Payment ID# = " . $payment_data['paymentid'], 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'link' => 'invoices/list_invoices/' . $payment_data['paymentid'], 'isread' => 0));
                        log_activity("Payment created in QuickBooks against Perfex Payment ID# = " . $payment_data['paymentid']);
                        set_quickBooks_company_realmId($payment_data['paymentid'], 'invoicepaymentrecords');
                        return $resultingObj;
                    }
                }
            }
            return;
        } catch (Exception $ex) {

            add_notification(array('description' => "Error! in  Payment creating in QuickBooks against Perfex Payment ID# = " . $payment->id . $ex->getMessage(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'link' => 'invoices/list_invoices/' . $payment->id, 'isread' => 0));
            log_activity("Error! in  Payment creating in QuickBooks against Perfex Payment ID# = " . $payment->id . $ex->getMessage());
            log_message('error', "Payment creating in QuickBooks against Perfex Payment ID# = " . $payment->id . $ex->getMessage());
        }
    }







    public function getFormattedInvNumber($invoice_data)
    {

        $prefix = '';

        if ($invoice_data->number_format == 1) {



            $prefix = $invoice_data->prefix;

            if (strlen($invoice_data->number) == 1) {

                $doc_number = $prefix . '00000' . $invoice_data->number;
            } else if (strlen($invoice_data->number) == 2) {

                $doc_number = $prefix . '0000' . $invoice_data->number;
            } else if (strlen($invoice_data->number) == 3) {

                $doc_number = $prefix . '000' . $invoice_data->number;
            } else if (strlen($invoice_data->number) == 4) {

                $doc_number = $prefix . '00' . $invoice_data->number;
            } else if (strlen($invoice_data->number) == 5) {

                $doc_number = $prefix . '0' . $invoice_data->number;
            } else {

                $doc_number = $prefix . '' . $invoice_data->number;
            }
        } else if ($invoice_data->number_format == 2) {



            $prefix = $invoice_data->prefix . '' . date('Y') . '/';

            if (strlen($invoice_data->number) == 1) {

                $doc_number = $prefix . '00000' . $invoice_data->number;
            } else if (strlen($invoice_data->number) == 2) {

                $doc_number = $prefix . '0000' . $invoice_data->number;
            } else if (strlen($invoice_data->number) == 3) {

                $doc_number = $prefix . '000' . $invoice_data->number;
            } else if (strlen($invoice_data->number) == 4) {

                $doc_number = $prefix . '00' . $invoice_data->number;
            } else if (strlen($invoice_data->number) == 5) {

                $doc_number = $prefix . '0' . $invoice_data->number;
            } else {

                $doc_number = $prefix . '' . $invoice_data->number;
            }
        } else if ($invoice_data->number_format == 3) {



            $prefix = $invoice_data->prefix;

            $postfix = date('y');



            if (strlen($invoice_data->number) == 1) {

                $doc_number = $prefix . '00000' . $invoice_data->number . '-' . $postfix;
            } else if (strlen($invoice_data->number) == 2) {

                $doc_number = $prefix . '0000' . $invoice_data->number . '-' . $postfix;
            } else if (strlen($invoice_data->number) == 3) {

                $doc_number = $prefix . '000' . $invoice_data->number . '-' . $postfix;
            } else if (strlen($invoice_data->number) == 4) {

                $doc_number = $prefix . '00' . $invoice_data->number . '-' . $postfix;
            } else if (strlen($invoice_data->number) == 5) {

                $doc_number = $prefix . '0' . $invoice_data->number . '-' . $postfix;
            } else {

                $doc_number = $prefix . '' . $invoice_data->number . '-' . $postfix;
            }
        } else {

            $prefix = $invoice_data->prefix;

            $postfix = date('m/Y');

            if (strlen($invoice_data->number) == 1) {

                $doc_number = $prefix . '00000' . $invoice_data->number . '/' . $postfix;
            } else if (strlen($invoice_data->number) == 2) {

                $doc_number = $prefix . '0000' . $invoice_data->number . '/' . $postfix;
            } else if (strlen($invoice_data->number) == 3) {

                $doc_number = $prefix . '000' . $invoice_data->number . '/' . $postfix;
            } else if (strlen($invoice_data->number) == 4) {

                $doc_number = $prefix . '00' . $invoice_data->number . '/' . $postfix;
            } else if (strlen($invoice_data->number) == 5) {

                $doc_number = $prefix . '0' . $invoice_data->number . '/' . $postfix;
            } else {

                $doc_number = $prefix . '' . $invoice_data->number . '/' . $postfix;
            }
        }



        return $doc_number;
    }





    public function deleteAllPaymentInvoice($QBinvoice)
    {
        try {
            if (isset($QBinvoice[0])) {

                // if only one payment in QB single array

                if (isset($QBinvoice[0]->LinkedTxn->TxnId)) {

                    $payment_id = $QBinvoice[0]->LinkedTxn->TxnId;

                    $this->deletePayment($payment_id);
                } elseif (is_array($QBinvoice[0]->LinkedTxn)) {
                    foreach ($QBinvoice[0]->LinkedTxn as $pay_txn) {

                        if (isset($pay_txn->TxnId)) {

                            $payment_id = $pay_txn->TxnId;

                            $this->deletePayment($payment_id);
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            log_activity("Error! in  Payment Deleteing in QuickBooks "  . $ex->getMessage());
            log_message('error', "Payment Deleteing in QuickBooks " . $ex->getMessage());
        }
    }





    public function deletePayment($payment_id)
    {
        try {
            $payment = $this->dataService->Query("SELECT * from Payment WHERE Id='" . $payment_id . "'");
        } catch (Exception $ex) {
            log_activity("Error! in  Payment Deleteing in QuickBooks "  . $ex->getMessage());
            log_activity('Error: In Payments' . $ex->getMessage());
        }





        if (!empty($payment)) {

            $pay = Payment::create([

                "Id" => $payment[0]->Id,

                "SyncToken" => $payment[0]->SyncToken

            ]);

            $this->dataService->Delete($pay);
        }
    }





    function check_invoice_exist_or_not($invoice_data)
    {



        $doc_number = $this->getFormattedInvNumber($invoice_data);


        try {
            $invoice = $this->dataService->Query("SELECT * FROM Invoice where DocNumber='" . $doc_number . "'");
        } catch (Exception $ex) {
            add_notification(array('description' => "Error! in Fetching Invoice from QuickBooks " . $ex->getMessage(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(),  'isread' => 0));
            log_activity("Error! in Fetching Invoice from QuickBooks " . $ex->getMessage());
        }


        if ($invoice) {

            return $invoice;
        } else {

            return false;
        }
    }
}
