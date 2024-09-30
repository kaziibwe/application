<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends AdminController

{
    function __construct()
    {
        parent::__construct();           

        $this->api_key              =   'XYZXYZXYZXYZXYZXYZ';

       // $this->clientTeam           =   array();

       $this->load->model('api_model');
		
        //$this->load->library('send_sms');
         // $this->salt_length = $this->config->item('salt_length', 'ion_auth');
          // $this->hash_method = $this->config->item('hash_method', 'ion_auth');
            // if ($this->hash_method == 'bcrypt') {
            // if ($this->random_rounds) {
                // $rand = rand($this->min_rounds, $this->max_rounds);
                // $rounds = array('rounds' => $rand);
            // } else {
                // $rounds = array('rounds' => $this->default_rounds);
            // }

            // $this->load->library('bcrypt', $rounds);
        // }

    }



  // public function getResponse($func, $parms = NULL)
    // {

        // if (isset($_SERVER['HTTP_ORIGIN'])) 
          // {
             // header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");

             // header('Access-Control-Allow-Credentials: true');

             // header('Access-Control-Max-Age: 86400');   
          // }

        // if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
          // {

            // if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))

                // header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            // if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))

                // header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

              // exit(0);

          // }

            // if($parms)
            // {

               // echo $this->$func($parms);            
            // }
             // else
             // {
              // echo $this->$func();
              // }

    // } 


	public function index()
    {
        if (!has_permission('api', '', 'view')) {
            access_denied('api');
        }
		
		$data['apiData']   =   $this->api_model->get();
		
		$data['title']                 = _l('Manage Api');
		
        $this->load->view('manage', $data);
    }

	public function updateApi($id = '')
    {
        if (!has_permission('api', '', 'view')) {
            access_denied('api');
        }
		
		$dta['status'] = $this->input->get('status');
		
		$success = $this->api_model->updateApi($dta, $id);
		
        if ($success) {
         echo "updated_successfully";
        }
		
    }
	


public function get_support()
   {
	 if($_GET['api_key'] != $this->api_key){
		echo json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            
		}
		else
		{	
		$invoiceList     =   $this->api_model->getTableByCondition('tbltickets');
        if(count($invoiceList) > 0){
			$result  =   array('data'=>$invoiceList, 'message'=>"Successfully", 'status'=>200);
         }else{
			$result = array('message' => "Sorry, wrong api key does not match !", 'status' =>400);
		}
		echo json_encode($result);
		}	
    }
////////////////////////////////////////////////////////////////////////

/////////////////////////CUSTOMER DETAIL////////////////////////////////////

 public function customerDetail($userid)
      {
        
          if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

      }

        if($userid){

            $uDetail      =   $this->api_model->getTableByCondition('tblclients', array('userid'=>$userid));
  
            $data           =   $uDetail ? $uDetail : array();

            $result         =   array('data'=>$data, 'message'=>'Successfully', 'status'=>1);

        }else{

            $result = array('message' => "some error occured", 'status' => 0);

        }

        echo json_encode($result);
        exit();

    }
/////////////////////////////////////////////////////////////////////////

/////////////////////////////////TICKET DETAIL//////////////////////////

    public function ticketDetail($userid)
      {
        
          if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

      }

        if($userid){

            $uDetail      =   $this->api_model->getTableByCondition('tbltickets', array('userid'=>$userid));

           

            $data           =   $uDetail ? $uDetail : array();

            $result         =   array('data'=>$data, 'message'=>'Successfully', 'status'=>1);

        }else{

            $result = array('message' => "some error occured", 'status' => 0);

        }

        echo json_encode($result);
        exit();

    }

 //////////////////////////////////////////////////////////////////////////////

  ////////////////////////////////UPDATE PROFILE////////////////////////////////

      public function updateProfile($userid)
   {
         
         if($_POST['api_key'] != $this->api_key){

        return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            
        }

     $userid = $_POST['userid'];

    
    if(isset($userid))
    {
         $company = isset($_POST['company']) ? $_POST['company']: '0';
          $gstin_number = isset($_POST['gstin_number']) ? $_POST['gstin_number']: '0';
          $phonenumber = isset($_POST['phonenumber']) ? $_POST['phonenumber']: '0';
          $website = isset($_POST['website']) ? $_POST['website']: '0';
          $country = isset($_POST['country']) ? $_POST['country']: '0';
          $state = isset($_POST['state']) ? $_POST['state']: '0';
          $city = isset($_POST['city']) ? $_POST['city']: '0';
          $zip = isset($_POST['zip']) ? $_POST['zip']: '0';

    
                        $arr = array(
                              'company'  => $company,
                              'gstin_number'  =>  $gstin_number, 
                              'phonenumber'  => $phonenumber,
                              'website' => $website,
                               'country' => $country,
                              'state' => $state,
                              'city' => $city,
                              'zip' => $zip

                        );         

         $this->api_model->updateRecordByCompany('tblclients',$arr,$userid);


                $data['message'] = ' Updated Successfully.';
                $data['success'] = 200;
        
        }else{
                  $data['message'] = 'Please enter all reqired parameters.';
                  $data['success'] = 400;
            } 
            echo json_encode($data);
        
      }
/////////////////////////////////////////////////////////////////////////////

///////////////////////////////////LOGIN////////////////////////////////////

    public function applogin() 
    {
      if($_POST['api_key'] != $this->api_key)
        {
           return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            
        }
           $email = $_POST['email'];
           $password = $_POST['password'];
		   
            $result =   $this->api_model->clientLogin($email, $password);
           
			var_dump($result);die;
			
           // $data['message'] = ' Login Successfully.';
           // $data['success'] = 200;
          
          // return json_encode($result);

    }
//////////////////////////////////////////////////////////////////
 public function addInvoice()
   {

        if($_POST['api_key'] != $this->api_key)
         {

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));        
         }
     
          $company = isset($_POST['company']) ? $_POST['company']: '0';
          $vat = isset($_POST['vat']) ? $_POST['vat']: '0';
          $phonenumber = isset($_POST['phonenumber']) ? $_POST['phonenumber']: '0';
          $city = isset($_POST['city']) ? $_POST['city']: '0';
          $country = isset($_POST['country']) ? $_POST['country']: '0';
          $zip = isset($_POST['zip']) ? $_POST['zip']: '0';
          $state = isset($_POST['state']) ? $_POST['state']: '0';
          $address = isset($_POST['address']) ? $_POST['address']: '0';
          $website = isset($_POST['website']) ? $_POST['website']: '0';
      

            if(!!$company && !!$vat && !!$phonenumber && !!$city  && !!$country && !!$zip && !!$state && !!$address && !!$website)
              {
                  $arr = array(
                              'company'  => $company,
                              'vat'  =>  $vat, 
                              'phonenumber'  => $phonenumber,
                              'city' => $city,
                              'country' => $country,
                              'zip' => $zip,
                              'state' => $state,
                              'address' => $address,
                              'website' => $website

                            );
                        $this->api_model->insertRecord('tblclients',$arr);
                        $data['message'] = ' Created Successfully.';
                        $data['success'] = 200;
              }
            else
                 {
                   $data['message'] = 'Please enter all reqired parameters.';
                   $data['success'] = 400;
                 } 
                echo json_encode($data);
    }
///////////////////////////////////////////////////////////////////////////

/////////////////////////////////ADD TICKET/////////////////////////////////
       

  public function addTicket()
   {

        if($_POST['api_key'] != $this->api_key)
         {

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));        
         }
     
          $email = isset($_POST['email']) ? $_POST['email']: '0';
          $name = isset($_POST['name']) ? $_POST['name']: '0';
          $department = isset($_POST['department']) ? $_POST['department']: '0';
          $priority = isset($_POST['priority']) ? $_POST['priority']: '0';
          $status = isset($_POST['status']) ? $_POST['status']: '0';
          $service = isset($_POST['service']) ? $_POST['service']: '0';
          $subject = isset($_POST['subject']) ? $_POST['subject']: '0';
          $message = isset($_POST['message']) ? $_POST['message']: '0';
           $date = isset($_POST['date']) ? $_POST['date']: '0';
      

            if(!!$email && !!$name && !!$department && !!$priority  && !!$status && !!$service && !!$subject && !!$message && !!$date)
              {
                  $arr = array(
                              'email'  => $email,
                              'name'  =>  $name, 
                              'department'  => $department,
                              'priority' => $priority,
                              'status' => $status,
                              'service' => $service,
                              'subject' => $subject,
                              'message' => $message,
                              'date' => $date


                            );
                        $this->api_model->insertRecord('tbltickets',$arr);
                        $data['message'] = ' Created Successfully.';
                        $data['success'] = 200;
              }
            else
                 {
                   $data['message'] = 'Please enter all reqired parameters.';
                   $data['success'] = 400;
                 } 
                echo json_encode($data);
    }


///////////UPDATE PROFILE////////////////////////////////////////////

  


///////////////////////PRODUCT LIST //////////////////////////////////

   public function product_list()
    {

        if($_GET['api_key']!= $this->api_key)
          {
             return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            
          }
         
           $category_id = $_GET['category_id'];
      
        if($category_id)
           {
              
              $product_list     =   $this->api_model->getTableByCondition('products', array('category_id'=>$category_id));
        
              $data           =   $product_list ? $product_list : array();

              $result         =   array('data'=>$data, 'message'=>Successfully, 'status'=>1);
            }
            else
              {

                $result = array('message' => "some error occured", 'status' => 0);

              }
              return json_encode($result);
    }

/////////////////////////CATEGORY LIST///////////////////////////////

  public function category_list()

    {

        if($_GET['api_key'] != $this->api_key)
          {

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            
          }

         $category_id = $_GET['category_id'];

        if($category_id)
          {
                $category_list     =   $this->api_model->getTableRowById('categories', $category_id);

            $data           =   $category_list ? $category_list : array();
            
            $result         =   array('data'=>$data, 'message'=>Successfully, 'status'=>1);
                           
          }
          else
             {
                $result = array('message' => "some error occured", 'status' => 0);

             }
              return json_encode($result);
    }


///////////////////////////LOGIN////////////////////////////



////////////////////FORGOT PASSWORD/////////////////////

  public function forgot_password()
 {

    if($_POST['api_key'] != $this->api_key)
    {
     return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0)); 
    }
      $mobile_number = $_POST['mobile_number'];
  
       $randum_OTP = rand(100000,999999);
       
       $check_number = $this->api_model->getTableByCondition('users', array('phone' => $mobile_number) );

		//var_dump($check_number);die();

      if(!empty($check_number))
        {
            if($this->api_model->updateRecordByCondition('users', array('forget_otp' => $randum_OTP), array('phone' => $mobile_number)))
            {
                $message = 'Dear '.$check_number[0]->username.' OTP for Reset password '.$randum_OTP.'. %0a%0aTeam Robirtway';
                 $this->send_sms->send_sms_message($mobile_number, $message);
            }

			
			$result         =   array('message'=>"OTP send on mobile number.", 'status'=>1);	
        }
		else
		{
			$result         =   array('message'=>"No record found.", 'status'=>0);
    
		}
		return json_encode($result);
    }
   

///////////////////////////login_by_otp////////////////////////////
 
  public function login_by_otp()
   {
  
       if($_POST['api_key'] != $this->api_key)
        {
        	 return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0)); 
        }
          $mobile_number = $_POST['mobile_number'];

           $recieve_otp = $_POST['recieve_otp'];

       $check_number_otp = $this->api_model->getTableByCondition('users', array('phone' => $mobile_number,'forget_otp' => $recieve_otp));

      if(!empty($check_number_otp))
        {         
              $result         =   array('message'=>"Login Successfully.", 'status'=>1);  
          }
          else
          {
          	 $result         =   array('message'=>"No record found.", 'status'=>0);
  
          }
            return json_encode($result);
    
         }
//////////////////////////update Password////////////////////// 

  public function update_password()
   {
  
       if($_POST['api_key'] != $this->api_key)
        {
        	 return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0)); 
        }
         
           $user_id = $_POST['user_id'];
           $old_password = $_POST['old_password'];
           $new_password = $_POST['new_password'];

            $result =   $this->api_model->updatepassword($user_id, $old_password,$new_password);

           $data['message'] = ' Update Successfully.';
           $data['success'] = 200;
          
          return json_encode($result);
           
}
//////////////////////////////////////////////////////

    public function profile($company_id)

     {

        if($_GET['api_key'] != $this->api_key)
         {
           return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            
         }

          if($company_id)
           {

             $clientDetail   =   $this->api_model->getTableRowById('companies', $company_id); 

             if($clientDetail)
              {

                $clientDetail->member_sale = $clientDetail->member_sale ? json_decode($clientDetail->member_sale) : array();
              }

                $data           =   $clientDetail ? $clientDetail : array();

                $result         =   array('data'=>$data, 'message'=>null, 'status'=>1);
            }
            else
            {

             $result = array('message' => "some error occured", 'status' => 0);
            }

             return json_encode($result);

     }



    public function kycDetail($company_id)

     {

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }



        if($company_id){

            $kycDetail      =   $this->api_model->getTableByCondition('upload_kyc', array('client_id'=>$company_id));

            $data           =   $kycDetail ? $kycDetail : array();

            $result         =   array('data'=>$data, 'message'=>null, 'status'=>1);

        }else{

            $result = array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }



    function getMemberChild($referId)

    {

        $getChilds          =   $this->api_model->getTableByCondition('companies', array('group_id'=>3, 'sponser_id'=>$referId));

        if($getChilds)

        {

            foreach ($getChilds as $getChild) {

                $this->clientTeam[] = $getChild->id;

                $this->getMemberChild($getChild->customer_sponser_id);

            }

        }

        return $this->clientTeam;

    }



    function getMyTeam($company_id)

    {

        $newClient      =   $this->api_model->getTableRowById('companies', $company_id);

        $clientTeam     =   $this->getMemberChild($newClient->customer_sponser_id);       

        // if($id  != $this->session->userdata('company_id')){

        //     $clientTeam[]   =   $id;

        // }

        $team           =   $clientTeam ? implode(',', $clientTeam) : 0;

        $teamList       =   $this->api_model->getTableByCondition('companies', array("id IN ($team)" =>NULL, 'group_id'=>3));



        return $teamList ? $teamList : array();        

    }



    // client team member

    public function team($company_id)

    {

        // check api key

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }



        if($company_id){

            $teamList   =   $this->getMyTeam($company_id);

            $data       =   $teamList ? $teamList : array();

            $result     =   array('data'=>$data, 'message'=>null, 'status'=>1);

        }else{

            $result     =   array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }



    // client left team member

    public function leftTeam($company_id)

    {

        // check api key

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }



        if($company_id){

            $client     =   $this->api_model->getTableRowById('companies', $company_id);

            $leftChild  =   $client ? $this->api_model->getTableByCondition('companies', array('sponser_id'=>$client->customer_sponser_id, 'position'=>1)) : array();



            $teamList   =   $leftChild ? $this->getMyTeam($leftChild[0]->id) : array();

            $data       =   $teamList ? $teamList : array();

            $result     =   array('data'=>$data, 'message'=>null, 'status'=>1);

        }else{

            $result     =   array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }



    // client right team member

    public function rightTeam($company_id)

    {

        // check api key

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }



        if($company_id){

            $client     =   $this->api_model->getTableRowById('companies', $company_id);

            $rightChild =   $client ? $this->api_model->getTableByCondition('companies', array('sponser_id'=>$client->customer_sponser_id, 'position'=>2)) : array();



            $teamList   =   $rightChild ? $this->getMyTeam($rightChild[0]->id) : array();

            $data       =   $teamList ? $teamList : array();

            $result     =   array('data'=>$data, 'message'=>null, 'status'=>1);

        }else{

            $result     =   array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }



    // client direct team member

    public function directTeam($company_id)

    {

        // check api key

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }



        if($company_id){

            $client         =   $this->api_model->getTableRowById('companies', $company_id);

            $directChild    =   $client ? $this->api_model->getTableByCondition('companies', array('direct_sponser_id'=>$client->customer_sponser_id, 'group_id'=>3)) : array();

            $teamList   =   $directChild ? $this->getMyTeam($directChild[0]->id) : array();

            $data       =   $teamList ? $teamList : array();

            $result     =   array('data'=>$data, 'message'=>null, 'status'=>1);

        }else{

            $result     =   array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }



    // client tree view

    public function treeView($company_id)

    {

        // check api key

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }



        if($company_id){

            $client         =   $this->api_model->getTableRowById('companies', $company_id);            

            $level1         =   $this->api_model->getTableByCondition('companies', array('sponser_id'=>$client->customer_sponser_id), array('position','asc'));

            

            $data['client'] =   $client;

            if($level1){

                $data['client_tree']['level1']    =   $level1;

                $level2=   array();

				//print_r($level1);die;

                foreach ($level1 as $key => $value) {
                    
                    if($value->position == '1')
                    {
                      $position = 'Left';
                    }
                    else if($value->position == '2')
                     {
                      $position = 'Right'; 
                     } 


                    $level2[$position] = $this->api_model->getTableByCondition('companies', array('sponser_id'=>$value->customer_sponser_id), array('position','asc'));

                }

				//print_r($level2);die;

                $data['client_tree']['level2']    =   $level2;                

            }



            $result     =   array('data'=>$data, 'message'=>null, 'status'=>1);

        }else{

            $result     =   array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }



    // client wallet

    public function wallet($company_id)

    {

        // check api key

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }



        if($company_id){

            $client                    =  $this->api_model->getTableRowById('companies', $company_id);       

            $data['client']            =  $client;

            $data['topup_debit']       =  getWalletDebitAmount($client->id, 'wallet_topup');

            $data['topup_credit']      =  getWalletCreditAmount($client->id, 'wallet_topup');

            $data['topup_balance']     =  getWalletRemainingAmount($client->id, 'wallet_topup');



            $data['shopping_debit']    =  getWalletDebitAmount($client->id, 'wallet_shopping');

            $data['shopping_credit']   =  getWalletCreditAmount($client->id, 'wallet_shopping');

            $data['shopping_balance']  =  getWalletRemainingAmount($client->id, 'wallet_shopping');



            $data['payout_debit']      =  getWalletDebitAmount($client->id, 'wallet_payout');

            $data['payout_credit']     =  getWalletCreditAmount($client->id, 'wallet_payout');

            $data['payout_balance']    =  getWalletRemainingAmount($client->id, 'wallet_payout');



            $data['topup_list']        =  $this->api_model->getTableByCondition('wallet_topup', array('company_id'=>$client->id), array('date','asc'));

            $data['shopping_list']     =  $this->api_model->getTableByCondition('wallet_shopping', array('company_id'=>$client->id), array('date','asc'));

            $data['payout_list']       =  $this->api_model->getTableByCondition('wallet_payout', array('company_id'=>$client->id), array('date','asc'));



            $result     =   array('data'=>$data, 'message'=>null, 'status'=>1);

        }else{

            $result     =   array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }

 
    //  public function order_history($company_id)

    // {
        
    //     if($_GET['api_key'] != $this->api_key){

    //         return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

    //     }

    //     if($company_id){

    //      $orderhistory     =   $this->api_model->getTableByCondition('sales', array('customer_id'=>$company_id));
      
    //      $companyaddress     =   $this->api_model->getTableRowById('companies', $company_id);

    //         $data['orderhistory']  =   $orderhistory ? $orderhistory : array();
            
            
    //         if($companyaddress->address != '')
    //         {
    //           $data['address']   =   array('address'=>$companyaddress->address, 'city_text'=>$companyaddress->city_text, 'state_text'=>$companyaddress->state_text,  'country'=>$companyaddress->country );
    //            // $address   = array('address'=>$companyaddress->address, 'city_text'=>$companyaddress->city_text, 'state_text'=>$companyaddress->state_text,  'country'=>$companyaddress->country ); 
              
    //         }
    //         else
    //          {
    //             $data['address']          = NULL;
    //          }   
    //      // $data = array_merge($orderhistory, $data);



    //         $result         =   array('data'=>$data, 'message'=>Successfully, 'status'=>1);

    //     }else{

    //         $result = array('message' => "some error occured", 'status' => 0);

    //     }



    //     return json_encode($result);

    // }

 // public function update_kyc($company_id)

 //    {
 //        //print_r('Check');

 //        // check api key

 //        if($_['api_key'] != $this->api_key){

 //            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

 //        }



 //        if($company_id){

 //        $userimage     =   $this->api_model->getTableByCondition('upload_kyc', array('client_id'=>$company_id));

 //            $data           =   $userimage ? $userimage : array();

 //            $result         =   array( 'message'=> "Updated Successfully", 'status'=>1);
 
 //        }else{

 //            $result = array('message' => "some error occured", 'status' => 0);

 //        }



 //        return json_encode($result);

 //    }

    
 public function payout($company_id)

    {
        //print_r('Check');

        // check api key

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }


        if($company_id){

         $payout_list     =   $this->api_model->getTableByCondition('customer_payout', array('company_id'=>$company_id));

         $companyUsername     =   $this->api_model->getTableRowById('companies', $company_id);

            if($payout_list!=0)
            {
                $data ['payout_detail']  =   $payout_list ? $payout_list : array();
            }
              $data['username']           =   $companyUsername->username ? $companyUsername->username : array();

            $result        =   array('data'=>$data, 'message'=>"Successfully", 'status'=>1);
 
        }else{

            $result = array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }

     public function payoutList($payout_id)

    {
        //print_r('Check');

        // check api key

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }


        if($payout_id){

         $payoutList     =   $this->api_model->getTableByCondition('customer_payout_item', array('payout_id'=>$payout_id));

              $data          =   $payoutList ? $payoutList : array();

            $result        =   array('data'=>$data, 'message'=>"Successfully", 'status'=>1);
 
        }else{

            $result = array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }

 public function adminTdsCharge($company_id)

    {
        //print_r('Check');

        // check api key

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }



        if($company_id){

         $admincharge     =   $this->api_model->getTableByCondition('customer_payout', array('id'=>$company_id));

            $data           =   $admincharge ? $admincharge : array();

            $result         =   array('data'=>$data, 'message'=>Successfully, 'status'=>1);
 
        }else{

            $result = array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }


 // public function tdscharge($company_id)

 //    {
 //        //print_r('Check');

 //        // check api key

 //        if($_GET['api_key'] != $this->api_key){

 //            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

 //        }



 //        if($company_id){

 //         $tdsCharge     =   $this->api_model->getTableByCondition('customer_payout', array('id'=>$company_id));

 //            $data           =   $tdsCharge ? $tdsCharge : array();

 //            $result         =   array('data'=>$data, 'message'=>Successfully, 'status'=>1);
 
 //        }else{

 //            $result = array('message' => "some error occured", 'status' => 0);

 //        }



 //        return json_encode($result);

 //    }
     public function addclient(){

  
        if($_POST['api_key'] != $this->api_key){

          return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }
// print_r($_POST);
// die();
          // $data = array( 'message'   =>    "",   'success'   =>    200 );

          $name = isset($_POST['name']) ? $_POST['name']: '0';
          $email = isset($_POST['email']) ? $_POST['email']: '0';
          $phone = isset($_POST['phone']) ? $_POST['phone']: '0';
        $date_of_birth = isset($_POST['date_of_birth']) ? $_POST['date_of_birth']: '0';
          

            if(!!$name && !!$email && !!$phone && !!$date_of_birth){
                  $arr = array(
                              'name'  => $name,
                              'email'  =>  $email, 
                              'phone'  => $phone,
                              'date_of_birth' => $date_of_birth
                        );
                        $this->api_model->insertRecord('companies',$arr);
                        $data['message'] = ' Created Successfully.';
                        $data['success'] = 200;
            }else{
                  $data['message'] = 'Please enter all reqired parameters.';
                  $data['success'] = 400;
            } 
            echo json_encode($data);
      }


    public function update_kyc()
      {

          if($_POST['api_key'] != $this->api_key)
           {
             return json_encode(array('message' => "Sorry, please enter api key  !", 'status' => 0));            
           }

           $client_id = $_POST['client_id'];
        
      /*   if(isset($client_id))
          {  
               $upload_first_proof = isset($_FILES['upload_first_proof']['name']) ? $_FILES['upload_first_proof']['name']: '0';
               $upload_second_proof = isset($_FILES['upload_second_proof']['name']) ? $_FILES['upload_second_proof']['name']: '0';
               $upload_third_proof = isset($_FILES['upload_third_proof']['name']) ? $_FILES['upload_third_proof']['name']: '0';
                
               $arr = array(
                              'upload_first_proof'  => $upload_first_proof,
                              'upload_second_proof'  =>  $upload_second_proof, 
                              'upload_third_proof'  => $upload_third_proof
                              
                           );
                
            
                     $this->api_model->updateRecordByCondition('upload_kyc',$arr,array('client_id'=>$client_id));
                   $data['message'] = ' Updated Successfully.';
                   $data['success'] = 200;
                  
          }
          else
              {
                 // $error = array('error' => $this->upload->display_errors());
                    // $this->load->view('custom_view', $error);
                    $data['message'] = 'Please enter client id.';
                    $data['success'] = 400;
              }
			  
			  */
				 if(isset($client_id))
				  {   
					$this->load->library('upload');
					$this->upload_path          =   'assets/uploads/';
					$this->image_types          =   'gif|jpg|jpeg|png|tif';        
					$this->allowed_file_size    =   '10240';
		
					$upload_first_proof = 'no_image.png';
					if ($_FILES['upload_first_proof']['size'] > 0) {
						$config['upload_path']      = $this->upload_path.'upload_kyc/';
						$config['allowed_types']    = $this->image_types;
						$config['max_size']         = $this->allowed_file_size;
						$config['overwrite']        = FALSE;
						$config['max_filename']     = 25;
						$config['encrypt_name']     = TRUE;
						$this->upload->initialize($config);
						if (!$this->upload->do_upload('upload_first_proof')) {
							$error = $this->upload->display_errors();
							
							$data['message'] = 'Kyc proof first not upload, please try again.';
							$data['response_error'] = $error;
							$data['error_code'] = 400;
						   // $this->session->set_flashdata('error', $error);
							//redirect($_SERVER['HTTP_REFERER']);
						}
						$upload_first_proof         = $this->upload->file_name;
						$config = NULL;
					}

					$upload_second_proof = 'no_image.png';
					if ($_FILES['upload_second_proof']['size'] > 0) {
						$config['upload_path']      = $this->upload_path.'upload_kyc/';
						$config['allowed_types']    = $this->image_types;
						$config['max_size']         = $this->allowed_file_size;
						$config['overwrite']        = FALSE;
						$config['max_filename']     = 25;
						$config['encrypt_name']     = TRUE;
						$this->upload->initialize($config);
						if (!$this->upload->do_upload('upload_second_proof')) {
							$error = $this->upload->display_errors();
							
							$data['message'] = 'Kyc proof second not upload, please try again.';
							$data['response_error'] = $error;
							$data['error_code'] = 400;
							
							//$this->session->set_flashdata('error', $error);
							//redirect($_SERVER['HTTP_REFERER']);
						}
						$upload_second_proof        = $this->upload->file_name;
						$config = NULL;
					}

					$upload_third_proof = 'no_image.png';
					if ($_FILES['upload_third_proof']['size'] > 0) {
						$config['upload_path']      = $this->upload_path.'upload_kyc/';
						$config['allowed_types']    = $this->image_types;
						$config['max_size']         = $this->allowed_file_size;
						$config['overwrite']        = FALSE;
						$config['max_filename']     = 25;
						$config['encrypt_name']     = TRUE;
						$this->upload->initialize($config);
						if (!$this->upload->do_upload('upload_third_proof')) {
							$error = $this->upload->display_errors();
							$data['message'] = 'Kyc proof third not upload, please try again.';
							$data['response_error'] = $error;
							$data['error_code'] = 400;
							
							//$this->session->set_flashdata('error', $error);
							//redirect($_SERVER['HTTP_REFERER']);
						}
						$upload_third_proof = $this->upload->file_name;                
						$config = NULL;
					}
					
								$arr       =   array(
										'client_id'            => $client_id,
										'upload_first_proof'   => $upload_first_proof,
										'upload_second_proof'  => $upload_second_proof,
										'upload_third_proof'   => $upload_third_proof,
										'status'               => 0,                                
									);
									
									//print_r($arr);die;
									
				$this->api_model->updateRecordByCondition('upload_kyc',$arr,array('client_id'=>$client_id));
						   $data['message'] = ' Updated Successfully.';
						   $data['success'] = 200;
				 }
			else
				  {
						 $data['message'] = 'Please enter client id.';
						 $data['response_code'] = 400;
				  }				  
				
               echo json_encode($data);
        
     }

      public function kycList($client_id)

    {

        if($_GET['api_key'] != $this->api_key){

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }


        if($client_id){

         $kycList     =   $this->api_model->getTableByCondition('upload_kyc', array('client_id'=>$client_id));

              $data          =   $kycList ? $kycList : array();

            $result        =   array('data'=>$data, 'message'=>"Successfully", 'status'=>1);
 
        }else{

            $result = array('message' => "some error occured", 'status' => 0);

        }



        return json_encode($result);

    }
    //    public function updateProfile(){

  

    //       if($_POST['api_key'] != $this->api_key){

    //       return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

    //     }

    // $user_id = $_POST['user_id'];
    
    // if(isset($user_id))
    //  {
    //      $this->load->library('upload');
    //      $this->upload_path          =   'assets/uploads/';
    //      $this->image_types          =   'gif|jpg|jpeg|png|tif';        
    //      $this->allowed_file_size    =   '10240';
    
    //         $avatar = 'no_image.png';
    //        if ($_FILES['avatar']['size'] > 0)
    //         {
    //             $config['upload_path']      = $this->upload_path.'upload_kyc/';
    //             $config['allowed_types']    = $this->image_types;
    //             $config['max_size']         = $this->allowed_file_size;
    //             $config['overwrite']        = FALSE;
    //             $config['max_filename']     = 25;
    //             $config['encrypt_name']     = TRUE;
    //             $this->upload->initialize($config);

    //         if (!$this->upload->do_upload('avatar')) 
    //         {
    //             $error = $this->upload->display_errors();
              
    //             $data['message'] = 'Kyc proof first not upload, please try again.';
    //             $data['response_error'] = $error;
    //             $data['error_code'] = 400;
              
    //         }
           
    //           $first_name = isset($_POST['first_name']) ? $_POST['first_name']: '0';
    //           $email = isset($_POST['email']) ? $_POST['email']: '0';
    //           $phone = isset($_POST['phone']) ? $_POST['phone']: '0';
    //           $last_name = isset($_POST['last_name']) ? $_POST['last_name']: '0';
    //           $company = isset($_POST['company']) ? $_POST['company']: '0';
    //           $gender = isset($_POST['gender']) ? $_POST['gender']: '0';
    //           $avatar         = $this->upload->file_name;
    //           $config = NULL;
    //       }
                 
    //          $arr = array(
    //                          'user_id'        => $user_id,
    //                           'first_name'      => $first_name,
    //                           'email'           =>  $email, 
    //                           'phone'           => $phone,
    //                           'last_name' => $last_name,
    //                           'company' => $company,
    //                           'gender' => $gender,
    //                           'avatar' => $avatar,
    //                           'status'  => 0,     
    //                      );
                        
    //      $this->api_model->updateRecordById('users',$arr,$user_id);
    //               $data['message'] = ' Updated Successfully.';
    //            $data['success'] = 200;
        
    //     }else{
    //              $data['message'] = 'Please enter client id.';
    //              $data['response_code'] = 400;
    //         } 
    //         echo json_encode($data);
        
    //   }


}    
