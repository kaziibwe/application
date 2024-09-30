<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APP_MODULES_PATH. 'quickbooks/libraries/vendor/quickbooks/v3-php-sdk/src/config.php');
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
/**
 * @author Pavel Espinal
 */
class Quickbook_config {

 /**
    *
     * @var CI_Controller
     */
    private $CI;
    private $dataService;
    private $accessToken;



    public function __construct() {
        $this->CI =& get_instance();
        if ( ! $this->CI->session instanceof CI_Session)

        {
            throw new RuntimeException("Attempting to load Quickbooks library in absence of CI_Session.");

        }
        $this->setDataService();

    }

    private function setDataService() {
        $lbReturn = false;
        try {
            $this->dataService = DataService::Configure(array(

                'auth_mode'         => 'oauth2',

                'ClientID'          => get_option('quickbook_client_id'),

                'ClientSecret'      => get_option('quickbook_client_secret'),

                'accessTokenKey'    => $this->CI->session->userdata('quickbook_access_token'),

                'refreshTokenKey'   => $this->CI->session->userdata('quickbook_refresh_token'),

                'QBORealmID'        => $this->CI->session->userdata('realmId'),

                'scope'             => 'com.intuit.quickbooks.accounting openid profile email phone address','hidden',

                'RedirectURI'       => site_url('/admin/quickbooks/check_auth_quickbook'),

                'baseUrl'           => get_option('is_quickbooks_app_in_production_mode')

            ));
            $lbReturn = true;
        } catch (Exception $ex) {
            log_activity("Error : There was a problem while initializing DataService.\n".$ex->getMessage());
            log_message('error', "There was a problem while initializing DataService.\n" . $ex->getMessage());return;

        }
        return $lbReturn;

    }
    /**
     * Get Data Service
     * 
     * @return QuickBooksOnline\API\DataService\DataService

     * @throws Exception

     */
    public function getDataService() {
        if ( ! $this->dataService instanceof DataService) {
            throw new Exception("The DataService object of the Quickbooks SDK is not ready.");

        } else {

            return $this->dataService;

        }

    }

    // called by controller (function: check_auth_quickbook)
    public function generate_accessToken($data){

        if(!empty($data)){

            try {

                $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();

                $parseUrl = $this->parseAuthRedirectUrl($_SERVER['QUERY_STRING']);

                $this->accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);

                $this->dataService->updateOAuth2Token($this->accessToken);

                $quickbook_access_token= $this->accessToken->getAccessToken();
                $quickbook_refresh_token= $this->accessToken->getRefreshToken();
                $this->CI->session->set_userdata('quickbook_refresh_token',$quickbook_refresh_token);
                $this->CI->session->set_userdata('quickbook_access_token',$quickbook_access_token);
                $this->CI->session->set_userdata('realmId',$parseUrl['realmId']);

                
                $token_data=array(
                        array(
                            'name'  => 'quickbook_refresh_token',
                            'value' => $quickbook_refresh_token
                        ),

                        array(
                            'name'  => 'quickbook_access_token',
                            'value' => $quickbook_access_token
                        ),

                        array(
                            'name'  => 'quickbook_realmId',
                            'value' => $parseUrl['realmId']
                        )
                );
                $this->storeQbRefreshToken($token_data);

                return 1;


            } catch(Exception $ex){


                log_activity("Error : There was a problem in generation access token.\n".$ex->getMessage());
                redirect('/admin/quickbooks/quickbooks_process');


            }

        }

    }

    function storeQbRefreshToken($token_data){
        // set refresh token in db option table
        $this->CI->db->select('*');
        $this->CI->db->from('tbloptions');
        $this->CI->db->where('name','quickbook_refresh_token');    
        $check_access_token =  $this->CI->db->get()->result_array();

        if(empty($check_access_token)){
           $this->CI->db->insert_batch('tbloptions', $token_data); 
        }else{
           $this->CI->db->update_batch('tbloptions', $token_data,'name');
        }
    }



    function parseAuthRedirectUrl($url){

        parse_str($url,$qsArray);

        return array(

            'code' => $qsArray['code'],

            'realmId' => $qsArray['realmId']

        );

    }





    public function refresh_token(){

        try{
            //$this->tax();
            $oauth2LoginHelper = new OAuth2LoginHelper(get_option('quickbook_client_id'),get_option('quickbook_client_secret'));
            $accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($this->CI->session->userdata('quickbook_refresh_token'));
            $this->CI->session->set_userdata('quickbook_access_token',$accessTokenObj->getAccessToken());

            $this->CI->session->set_userdata('quickbook_refresh_token',$accessTokenObj->getRefreshToken());

            return 1;
        } catch(Exception $ex){

            log_activity("Error : There was a problem in refresh token.\n".$ex->getMessage());
            redirect('/admin/quickbooks/quickbooks_process');

        }

    }

    public function get_company_country()
    {
        $company = $this->dataService->Query("SELECT * FROM CompanyInfo");
        $error = $this->dataService->getLastError();
        if ($error) {
            log_activity("Error : There was a problem in fetching company.\n".$error->getResponseBody());
        } else {
            return $company[0]->Country;
        }
    }
}