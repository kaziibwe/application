<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

/**
 * @author Pavel Espinal
 */
class Quickbook_expenses
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
            log_activity("Error : There was a problem in DataService.\n" . $ex->getMessage());

            log_message('error', "There was a problem while initializing DataService.\n" . $ex->getMessage());
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
                log_activity("Error : There was a problem genetating Access Token.\n" . $ex->getMessage());
                redirect('/admin/quickbooks/quickbooks_process');
            }
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


    function parseAuthRedirectUrl($url)
    {

        parse_str($url, $qsArray);

        return array(

            'code' => $qsArray['code'],

            'realmId' => $qsArray['realmId']

        );
    }
    // Refresh the QuickBooks Token
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

            log_activity("Error : There was a problem refresh token.\n" . $ex->getMessage());
            redirect('/admin/quickbooks/quickbooks_process');
        }
    }
    // Create Expenses in QuickBooks
    public function create_expense_data($data)
    {
        $customer_data = array();
        $payment_method = array();
        $line = array();


        if (!empty($data)) {

            // get customer information or create customer on quickbooks

            if ($data['company'] != '') {

                $customer = $this->check_customer_exist_or_not($data['company']);

                if (empty($customer)) {

                    $customer = $this->create_customer($data);
                }
                $customer_data = array(

                    'CustomerRef' => array(

                        "value" => $customer->Id,

                        "name" => $customer->GivenName

                    )

                );
            }
            // get expense category information or create category on quickbooks

            $category = $this->check_category_exist_or_not($data['category_name']);

            if (empty($category)) {

                $category = $this->create_category($data['category_name']);
            }
            // get expense accounts information or create accounts on quickbooks

            if ($data['payment_mode_name'] != '') {

                if ($data['payment_mode_name'] == 'Bank') {
                    $payment_mode_name = "Savings";
                    $payment_method = $this->check_payment_accounts_exist_or_not('Savings');
                } else if ($data['payment_mode_name'] == 'Credit Card') {
                    $payment_mode_name = "Visa";
                    $payment_method = $this->check_payment_accounts_exist_or_not('Visa');
                }

                if (empty($payment_method)) {
                    $payment_method = $this->create_payment_accounts($payment_mode_name);
                }
            } else {
                // $data['payment_mode_name'] = "Savings";
                $payment_method = $this->check_payment_accounts_exist_or_not('Savings');
                if (empty($payment_method)) {
                    $payment_method = $this->create_payment_accounts('Savings');
                }
            }

            $this->create_expense($category, $data, $payment_method, $customer_data);
        }
    }

    function check_customer_exist_or_not($user)
    {
        try {
            $customer = $this->dataService->Query("SELECT * FROM Customer where GivenName='" . $user . "'");


            $error = $this->dataService->getLastError();

            if ($customer[0]) {

                return $customer[0];
            } else {

                return false;
            }
        } catch (Exception $ex) {
            log_activity('Error: In Customer' . $ex->getMessage());
        }
    }
    function create_customer($user)
    {

        $theResourceObj = Customer::create([

            "BillAddr" => [

                "Line1" => $user['billing_street'],

                "City" => $user['billing_city'],

                "Country" => $user['billing_country'],

                "CountrySubDivisionCode" => '',

                "PostalCode" => $user['billing_zip']

            ],
            "Taxable" => true,
            "Title" => "",

            "GivenName" => $user['company'],

            "MiddleName" => "",

            "FamilyName" => "",

            "Suffix" => "",

            "FullyQualifiedName" => $user['company'],

            "CompanyName" => $user['company'],

            "DisplayName" => $user['company'],

            "PrimaryPhone" => [

                "FreeFormNumber" => $user['phonenumber']

            ],
            "PrimaryEmailAddr" => [

                "Address" => (get_client_primary_email($user['userid'])  != null) ? get_client_primary_email($user['userid']) : ""

            ]

        ]);



        $resultingObj = $this->dataService->Add($theResourceObj);

        $error = $this->dataService->getLastError();



        if ($error) {

            log_message('error', "Checking Customer \n" . $error->getHttpStatusCode());
        } else {

            return $resultingObj;
        }
    }

    function create_category($category)
    {



        $theResourceObj = Account::create([

            "AccountType" => "Expense",

            "Name" => ucfirst($category),

            'Classification' => "Expense",

        ]);



        $resultingObj = $this->dataService->Add($theResourceObj);

        $error = $this->dataService->getLastError();

        if ($error) {

            log_message('error', "Category creation \n" . $error->getHttpStatusCode());
        } else {

            return $resultingObj;
        }
    }



    function check_category_exist_or_not($category)
    {
        try {
            $account = $this->dataService->Query("SELECT * FROM Account where Name='" . $category . "'");

            $error = $this->dataService->getLastError();

            if ($error) {
                log_activity('Error: In Expense" . $error->getHttpStatusCode()');
                log_message('error', "Checking Expense exist or not \n" . $error->getHttpStatusCode());
            } else {

                if (isset($account[0])) {
                    return $account[0];
                } else {
                    return 0;
                }
            }
        } catch (Exception $ex) {
            log_activity('Error: In Expense" . $error->getHttpStatusCode()');
            log_message('error', "Checking Expense exist or not \n" . $ex->getMessage());
        }
    }





    function create_payment_accounts($payments_method_name)
    {


        $theResourceObj = Account::create([

            "AccountType" => "Credit Card",

            "Name" => ucfirst($payments_method_name),

        ]);



        $resultingObj = $this->dataService->Add($theResourceObj);

        $error = $this->dataService->getLastError();

        if ($error) {


            log_message('error', "Payment Accounts creation \n" . $error->getResponseBody());
        } else {

            return $resultingObj;
        }
    }



    function check_payment_accounts_exist_or_not($payments_method_name)
    {

        try {
            $payment_accounts = $this->dataService->Query("SELECT * FROM Account where Name='" . $payments_method_name . "'");
        } catch (Exception $ex) {
            log_activity('Error: In Payment Account Checking Payment method exist or not' . $ex->getMessage());
        }


        $error = $this->dataService->getLastError();

        if ($error) {

            log_message('error', "Checking Payment method exist or not \n" . $error->getResponseBody());
            log_activity('Error: In Payment Account Checking Payment method exist or not' . $error->getHttpStatusCode());
        } else {
            
            if (isset($payment_accounts[0])) {
                return $payment_accounts[0];
            } else {
                return 0;
            }
        }
    }





    function check_expense_exist_or_not($doc_number)
    {

        try {
            $payment_accounts = $this->dataService->Query("SELECT * FROM Purchase where DocNumber='" . $doc_number . "'");
        } catch (Exception $ex) {
            log_activity('Error: In Payment Account' . $ex->getMessage());
        }


        $error = $this->dataService->getLastError();

        if ($error) {

            log_message('error', "Checking Payment method exist or not \n" . $error->getHttpStatusCode());
        } else {

             
            if (isset($payment_accounts[0])) {
                return $payment_accounts[0];
            } else {
                return 0;
            }
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
    function create_items($item)
    {
        $CI = &get_instance();

        try {
            $payment_method = $this->check_payment_accounts_exist_or_not('Items');

            if (empty($payment_method)) {
                $payment_method = $this->create_payment_accounts('Items');
            }

            $Item = Item::create([



                "Name" => $item['expense_name'],

                "Description" => $item['note'],

                "Active" => true,

                "Taxable" => true,

                "UnitPrice" => 0,

                "Type" => "Service",

                "IncomeAccountRef" => ["value" => $payment_method->Id, "name" => $payment_method->Name],

                "PurchaseCost" => 0,

                "ExpenseAccountRef" => ["value" => $payment_method->Id, "name" => $payment_method->Name],

                "AssetAccountRef" => ["value" => $payment_method->Id, "name" => $payment_method->Name],

                "InvStartDate" =>  date('Y-m-d H:i:s', strtotime("-1 days")),

            ]);
        } catch (Exception $ex) {
            add_notification(array('description' => 'Error: In creating ' . $item['expense_name'] . ' Item' . $ex->getMessage(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In creating ' . $item['expense_name'] . ' Item ' . $ex->getMessage());
        }




        $resultingObj = $this->dataService->Add($Item);

        $error = $this->dataService->getLastError();

        if ($error) {

            add_notification(array('description' => 'Error: In creating ' . $item['expense_name'] . ' Item' . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In creating ' . $item['expense_name'] . ' Item ' . $error->getResponseBody());
        } else {

            return $resultingObj;
        }
    }
    function check_item_exist_or_not($item)
    {

        try {
            $item = $this->dataService->Query("SELECT * FROM Item where Name='" . $item . "'");
        } catch (Exception $ex) {
            add_notification(array('description' => "Error: In fetching Item " . $ex->getMessage(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In fetching Item' . $ex->getMessage());
        }



        $error = $this->dataService->getLastError();
        if ($error) {
            add_notification(array('description' => "Error: In fetching Item " . $error->getResponseBody(), 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'isread' => 0));
            log_activity('Error: In fetching Item' . $error->getResponseBody());
        }


        if (isset($item[0])) {

            return $item[0];
        } else {

            return false;
        }
    }

    function create_expense($category, $data, $payment_method, $customer_data)
    {
        $CI = &get_instance();
        if (!$data['payment_mode_name']) {
            $data['payment_mode_name'] = 'Bank';
        }
        $item = $this->check_item_exist_or_not($data['expense_name']);
        if (!$item) {
            $item = $this->create_items($data);
        }
        $PaymentMethod = $this->is_exist_PaymentMethod($data['payment_mode_name']);
        if (!$PaymentMethod) {
            $PaymentMethod =  $this->create_paymentMethod($data['payment_mode_name']);
        }
        $CI->load->model('expenses_model');
        $CI->load->library(QUICKBOOKS_MODULE . '/quickbooks');

        $agency = $CI->quickbooks->is_exist_taxAgency();
       
        if (!$agency) {
            $agency =  $CI->quickbooks->create_taxAgency();
        }
        $totalTax =  (isset(get_tax_for_quickBooks($data['tax'])->taxrate) ? get_tax_for_quickBooks($data['tax'])->taxrate : 0) + (isset(get_tax_for_quickBooks($data['tax2'])->taxrate) ? get_tax_for_quickBooks($data['tax2'])->taxrate : 0);
        $taxes = array(get_tax_for_quickBooks($data['tax']), get_tax_for_quickBooks($data['tax2']));
      
        $taxAmount = 0;
        if (get_option('xero_company_country') != "US") {

            if (!$totalTax) {
                $taxCode = $CI->quickbooks->is_exist_taxCode_for_expenses("TaxRate0");
                if (isset($taxCode->Id)) {
                    $taxCodeId = $taxCode->Id;
                } else {
                    log_activity("Error : There was a problem in Expenses.\n");
                    return;
                }
            } else {
                $codeName = '';
                foreach ($taxes as $tax) {
                    if (isset($tax->taxrate)) {
                        $codeName = $codeName.$tax->name . $tax->taxrate . "% ,";
                    }
                }
                $taxCode = $CI->quickbooks->is_exist_taxCode_for_expenses($codeName);

                if (!$taxCode) {
                    $taxCode = $CI->quickbooks->create_taxCode_for_expenses($agency, $taxes);
                }
                if (isset($taxCode)) {
                    if(isset($taxCode->Id))
                    {
                        $taxCodeId = $taxCode->Id;
                    }else{
                        $taxCodeId = $taxCode;
                    }
                    
                } else {
                    log_activity("Error : There was a problem in Expenses.\n");
                    return;
                }
            }
        } else {
            if (!$totalTax) {
                $taxCodeId = 'NON';
            } else {
                $taxAmount = $data['amount'] / 100 * $totalTax;
                $taxCodeId = 'Tax';
            }
        }
        $line = array(

            "AccountRef" => array(

                "value" => $category->Id,

                "name" => $category->Name

            ),



            "BillableStatus" =>  "NotBillable",

        );

        $purchaseOrder = Purchase::create([

            'PaymentType' => 'CreditCard',

            'AccountRef' =>

            array(

                'name' =>  $payment_method->Name,

                'value' =>  $payment_method->Id,

            ),


            "TxnDate" => $data['date'],

            "DocNumber" =>  $data['id'],

            "Line" => [
                [

                    "Amount" => $data['amount'] + $taxAmount,
                    "Description" => $data['note'],
                    "DetailType" => "ItemBasedExpenseLineDetail",

                    "ItemBasedExpenseLineDetail" => [
                        "ItemRef" => [
                            "value" => $item->Id
                        ],
                        "TaxCodeRef" => array(

                            "value" => $taxCodeId,

                        ),
                    ]

                ],
                [

                    "Amount" => 0,

                    "DetailType" => "AccountBasedExpenseLineDetail",

                    "AccountBasedExpenseLineDetail" => ((!empty($customer_data)) ? array_merge($customer_data, $line) : $line)

                ],

            ],
            "PaymentMethodRef" => [
                "value" => $PaymentMethod->Id
            ]
        ]);



        $resultingpurchaseOrder = $this->dataService->Add($purchaseOrder);
        $error = $this->dataService->getLastError();


        if ($error) {

            log_activity("Error : There was a problem in Expenses.\n" . $error->getResponseBody());
            log_message('error', "Expense Creation \n" . $error->getResponseBody());
        } else {
            set_expense_created_in_quickBooks($data['id']);
            add_notification(array('description' => "Expense created in QuickBooks with id# = " . $resultingpurchaseOrder->Id . " against the Perfex Expense id#: = " . $data['id'], 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'link' => 'expenses', 'isread' => 0));
            log_activity("Expense created in QuickBooks with id# = " . $resultingpurchaseOrder->Id . " against the Perfex Expense id#: = " . $data['id']);
            return $resultingpurchaseOrder;
        }
    }
}
