<?php defined('BASEPATH') OR exit('No direct script access allowed');



class Mapi extends CI_controller



{

    function __construct()

    {

        parent::__construct();           

        $this->load->model('api_model');

		

		   $key =  $this->api_model->getTableByCondition('tblapi', array('status'=>'1'));

		

        $this->api_key              =   $key[0]->key;



    }



	public function index()

    {

        if (!has_permission('api', '', 'view')) 

        {

            access_denied('api');

        }

		

	   	 $data['apiData']   =   $this->api_model->get();

		   $data['title']                 = _l('Manage Api');

       $this->load->view('manage', $data);

    }



 	public function updateApi($id = '')

    {

        if (!has_permission('api', '', 'view'))

         {

            access_denied('api');

          }



	    	$dta['status'] = $this->input->get('status');

	    	$success = $this->api_model->updateApi($dta, $id);

		

        if ($success) 

        {

          echo "updated_successfully";

        }

		

    }

	

//-------------------CUSTOMER DETAIL-------------------//



  public function companyDetail($userid)

    {

       if($_GET['api_key'] != $this->api_key)

        {

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }



        if($userid)

        {

          $uDetail      =   $this->api_model->getTableByCondition('tblclients', array('userid'=>$userid));

          $data           =   $uDetail ? $uDetail : array();

          $result         =   array('data'=>$data, 'message'=>'Successfully', 'status'=>1);



        }

        else

           {

             $result = array('message' => "some error occured", 'status' => 0);

           }

        echo json_encode($result);

        exit();



    }



//----------------------TICKET DETAIL----------------------------



  public function ticketDetail($userid)

    {

		

		

       if($_GET['api_key'] != $this->api_key)

         {

           return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

         }



         if($userid)

           {

             $uDetail      =   $this->api_model->getTicket('tbltickets',$userid);
            if(!empty($uDetail)){
              foreach ($uDetail as $key => $value) {
               $department_name = $this->api_model->getDepartmentById('tbldepartments',$value->department);
               $status_name = $this->api_model->getStatusById('tbltickets_status',$value->status);
               $uDetail[$key]->department =  $department_name->name;
               $uDetail[$key]->status =  $status_name->name;
              } 
            }

             $data         =   $uDetail ? $uDetail : Null;

             $result       =   array('data'=>$data, 'message'=>'Successfully', 'status'=>1);

           }

           else

              {

                 $result = array('message' => "some error occured", 'status' => 0);

              }

         echo json_encode($result);

         exit();

    }

 //--------------------------Ticket Image-----------------------

    public function ticketImage($ticketid,$replyid)

    {

       if($_GET['api_key'] != $this->api_key)

         {

           return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

         }

          $ticketid = $_GET['ticketid'];

          $replyid = $_GET['replyid'];



         if($ticketid)

           {

            $tDetail      =   $this->api_model->getTicketFile('tblticket_attachments',$ticketid,$replyid);



			if($tDetail === false)

			{

				$data = array('file_name'=>Null);

			}	

			else

			{

				$data         =   $tDetail ? $tDetail : Null;	

				

			}		

             

             $result       =   array('data'=>$data, 'message'=>'Successfully', 'status'=>1);

           }

           else

              {

                 $result = array('message' => "Please enter ticket id.", 'status' => 0);

              }

         echo json_encode($result);

         exit();



    }

	

	public function ticketMainImage()

    {

		

		

       if($_GET['api_key'] != $this->api_key)

         {

           return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

         }

          $ticketid = $_GET['ticketid'];

          



         if($ticketid)

           {

            $tDetail      =   $this->api_model->getTicketMainFile('tblticket_attachments',$ticketid,$replyid);



			if($tDetail === false)

			{

				$data = array('file_name'=>Null);

			}	

			else

			{

				$data         =   $tDetail ? $tDetail : Null;	

				

			}		

             

             $result       =   array('data'=>$data, 'message'=>'Successfully', 'status'=>1);

           }

           else

              {

                 $result = array('message' => "Please enter ticket id.", 'status' => 0);

              }

         echo json_encode($result);

         exit();



    }

//---------------------------Department-------------------------



  public function department()

    {





        if($_GET['api_key'] != $this->api_key)

         {

           return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

         }

     

             $department   =   $this->api_model->getDepartment('tbldepartments');

             $data         =   $department ? $department: Null;              

             $result         =   array('data'=>$data,'message'=>'Successfully', 'status'=>1);

              

         

             echo json_encode($result);

     }

//------------------------Ticket Status---------------------------



 public function ticketStatus()

   {

        if($_GET['api_key'] != $this->api_key)

         {

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

         }

             $status   =   $this->api_model->getStatus('tbltickets_status');

             $data         =   $status ? $status: Null;              

             $result         =   array('data'=>$data,'message'=>'Successfully', 'status'=>1);

              

         

             echo json_encode($result);

     }

//--------------------------PROJECT --------------------------



  public function projectList($clientid)

    {



        if($_GET['api_key'] != $this->api_key)

         {

           return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

         }

     

             $projectList   =   $this->api_model->getProjectList('tblprojects',$clientid);

             $data         =   $projectList ? $projectList: array();                

             $result         =   array('data'=>$data,'message'=>'Successfully', 'status'=>1);

         

          echo json_encode($result);

    }

//-------------------------LOGO DETAIL-----------------------------



  public function LogoDetail($id)

    {



        if($_GET['api_key'] != $this->api_key)

         {

           return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

         }

     

             $logoList   =   $this->api_model->getLogoDetail('tbloptions',$id);

             $data         =   $logoList ? $logoList: array();                

             $result         =   array('data'=>$data,'message'=>'Successfully', 'status'=>1);

         

          echo json_encode($result);

   }

  

  //-----------------------UPDATE PROFILE---------------------------



  public function updateProfile($userid)

    {

         if($_POST['api_key'] != $this->api_key)

         {

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

        

          }

          else

            {

              $data['message'] = 'Please enter all reqired parameters.';

              $data['success'] = 400;

            } 

           echo json_encode($data);  

    }

 

//---------------------ADD TICKET-----------------------



  public function addTicket()

   {

      if($_POST['api_key'] != $this->api_key)

        {

          return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));        

        }

 

           $department = isset($_POST['department']) ? $_POST['department']: '0';

           $status = isset($_POST['status']) ? $_POST['status']: '0';

           $name = isset($_POST['name']) ? $_POST['name']: '0';

           $date = isset($_POST['date']) ? $_POST['date']: '0';

           $subject = isset($_POST['subject']) ? $_POST['subject']: '0';

           $message = isset($_POST['message']) ? $_POST['message']: '0';

           $userid = isset($_POST['userid']) ? $_POST['userid']: '0';

           $projectid = isset($_POST['projectid']) ? $_POST['projectid']: '0';

           $file_name = isset($_FILES['file_name']) ? $_FILES['file_name']: '0';  



          

        if(!!$department && !!$status && !!$name && !!$date  && !!$subject && !!$userid && !!$message && !!$projectid ) 

         {

                 $arr = array(

                              'department'  => $department,

                              'status' => $status,

                              'name' => $name,

                              'date' => $date,

                              'subject' => $subject,

                              'userid' => $userid,

                              'message' => $message,

                              'project_id' => $projectid

                            );





                   $ticketid = $this->api_model->insertRecord('tbltickets',$arr);

                   $this->upload_path          =   'uploads/ticket_attachments/'.$ticketid;

              //$path = FCPATH . 'uploads/ticket_attachments' . '/' . $ticket_id . '/';



            if (!file_exists($this->upload_path)) 

              {

                        mkdir($this->upload_path, 0775);

                        $fp = fopen($path . 'index.html', 'w');

                        fclose($fp);

               }

               $this->image_types          =   'gif|jpg|jpeg|png|tif|doc|docx|pdf|xls|xlsx|txt';        

               $this->allowed_file_size    =   '10242';

              $this->load->library('upload');

             if ($_FILES['file_name']['size'] > 0)

             {

                $config['upload_path']      = $this->upload_path;

                $config['allowed_types']    = $this->image_types;

                $config['max_size']         = $this->allowed_file_size;

                $config['overwrite']        = FALSE;

                $config['max_filename']     = 225;

                $config['encrypt_name']     = TRUE;

                $this->upload->initialize($config);

               if (!$this->upload->do_upload('file_name'))

                {

                   $error = $this->upload->display_errors();

                }

				      $file = $this->upload->file_name;

             }

                    if(!empty($ticketid))

                       {

                         $arrr = array(    

                                         'ticketid' => $ticketid,

                                          'file_name' => $file,

                                          'filetype' => $file_name['type'], 

                                          'dateadded' =>  date('Y-m-d H:i:s')

                                         );

                          $this->api_model->insertRecord('tblticket_attachments', $arrr);

                        }

                      

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



//----------------------------ADD REPLY----------------------------



  public function addTicketReply()

    {

      if($_POST['api_key'] != $this->api_key)

        {

         return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));        

        }

            $ticketid = isset($_POST['ticketid']) ? $_POST['ticketid']: '0';

            $message = isset($_POST['message']) ? $_POST['message']: '0';

            $date = isset($_POST['date']) ? $_POST['date']: '0';

            $contactid = isset($_POST['contactid']) ? $_POST['contactid']: '0';

            $status = isset($_POST['status']) ? $_POST['status']: '0';

            $file_name = $_FILES['file_name'];

            $this->upload_path          =   'uploads/ticket_attachments/'.$ticketid;

              //$path = FCPATH . 'uploads/ticket_attachments' . '/' . $ticket_id . '/';


          if (!file_exists($this->upload_path)) 

            {

                   mkdir($this->upload_path, 0755);

                    $fp = fopen($path . 'index.html', 'w');

                     fclose($fp);

             }



            $this->image_types          =   'jpg|jpeg|png|doc|docx|pdf|xls|xlsx|txt';        

            $this->allowed_file_size    =   '1024';

            $this->load->library('upload');

             if ($_FILES['file_name']['size'] > 0)

              {

                $config['upload_path']      = $this->upload_path;

                $config['allowed_types']    = $this->image_types;

                $config['max_size']         = $this->allowed_file_size;

                $config['overwrite']        = FALSE;

                $config['max_filename']     = 255;

				$config['encrypt_name']     = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('file_name'))

                 {

                    $error = $this->upload->display_errors();

                 }

				 

				 $photo  = $this->upload->file_name;

              }

 

       if(!empty($ticketid) && !empty($message) && !empty($date) && !empty($contactid)) 

         {
         // echo "<pre>";
         // print_r($_FILES); die;
              $arr = array(

                             'ticketid' => $ticketid,

                              'message' => $message,

                              'date' => $date,

                              'contactid' => $contactid

                            );



                  $replyid = $this->api_model->insertRecord('tblticket_replies', $arr);



                   if(!empty($_FILES['file_name']))

                    {

                         $arrr = array(

                                        'replyid' =>$replyid,

                                        'ticketid' => $ticketid,

                                        'file_name' => $photo,

                                        'filetype' => $file_name['type'], 

                                        'dateadded' =>  date('Y-m-d H:i:s')

                                       );



                            $this->api_model->insertRecord('tblticket_attachments', $arrr);

                      }

                      // else

                       // {

                          // $arrr = array(

                                        // 'replyid' =>$replyid,

                                        // 'ticketid' => $ticketid,

                                        // 'file_name' => Null,

                                        // 'filetype' => Null, 

                                        // 'dateadded' =>  date('Y-m-d H:i:s')

                                       // );



                            // $this->api_model->insertRecord('tblticket_attachments', $arrr);

                       // } 

                      

                  $data['message'] = 'Created Successfully.';

                  $data['success'] = 200;

          }

           else

               {

                 $data['message'] = 'Please enter message.';

                  $data['success'] = 400;

               } 

            echo json_encode($data);

    }

//------------------------------------------------



 public function replyDetail($admin)

  {

    if($_GET['api_key'] != $this->api_key)

      {  

        return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

      }



      if($admin)

        {

            $uDetail      =   $this->api_model->getTicketById($admin); 

			

            $data           =   $uDetail ? $uDetail : array();

            $result         =   array('data'=>$data, 'message'=>'Successfully', 'status'=>1);

        }

        else

         {

            $result = array('message' => "Please enter ticket id ", 'status' => 0);

         }



        echo json_encode($result);

        exit();



   }



//-----------------------USER DETAIL-------------------------



  public function UserDetail($userid)

    {

       if($_GET['api_key'] != $this->api_key)

       {

        return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0)); }



       if($userid)

       {

          $uDetail      =   $this->api_model->getloginComapny('tblclients',$userid);

          $tDetail      =   $this->api_model->getUser('tblcontacts', $userid);        

          $data           =   $uDetail ? $uDetail : array();

          $data1           =   $tDetail ? $tDetail : array();

          $result         =   array('detail'=>$data1 ,'company_name'=>$data,'message'=>'Successfully', 'status'=>1);



        }

        else

          {

             $result = array('message' => "some error occured", 'status' => 0);

          }



        echo json_encode($result);

        exit();



    }

//-----------------------LOGIN--------------------------------



  public function applogin() 

    {

      if($_POST['api_key'] != $this->api_key)

        {

           return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

        }

          $email = $_POST['email'];

          $password = $_POST['password'];

          $result =   $this->api_model->clientLogin($email, $password);

           

		  if ($result['status'] == '200') 

        {

			    $data['message'] = ' Login Successfully.';

			    $data['success'] = 200;

			    $data['user_detail'] = $result['data'];

			  }

			   else

			      {

			       	$data['message'] = $result['message'];

			      	$data['success'] = $result['status']; 

			      }	



           echo json_encode($data);



    }



//------------------DETAIL PROJECT--------------------------------



  public function projectDetail($clientid)

    {

         

      if($_GET['api_key'] != $this->api_key)

      {  

          return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

      }



        if($clientid)

        {

            $uDetail      =   $this->api_model->getproject('tblprojects',$clientid);

            $data           =   $uDetail ? $uDetail : array();

            $result         =   array('data'=>$data, 'message'=>'Successfully', 'status'=>1);



        }

        else

        {

          $result = array('message' => "some error occured", 'status' => 0);

        }



        echo json_encode($result);

        exit();



    }



//------------------PROPOSAL -------------------------------



  public function proposalDetail($rel_id)

    {

      if($_GET['api_key'] != $this->api_key)

      {

        return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

      }



       if($rel_id)

       {

          $uDetail      =   $this->api_model->getproposal('tblproposals',$rel_id);

          $tDetail      =   $this->api_model->getTotal('tblproposals', $rel_id);        

          $data           =   $uDetail ? $uDetail : array();

          $data1           =   $tDetail ? $tDetail : array();

          $result         =   array('value'=>$data1 ,'data'=>$data,'message'=>'Successfully', 'status'=>1);



        }

        else

        {

            $result = array('message' => "some error occured", 'status' => 0);

        }



        echo json_encode($result);

        exit();



    }



//------------------------STAFF DETAIL--------------------------



  public function staffDetail($staffid)

    {

      if($_GET['api_key'] != $this->api_key)

      {

        return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

      }



        if($staffid)

        {

            $uDetail      =   $this->api_model->getStaffById($staffid);         

            $data           =   $uDetail ? $uDetail : array();

            $result         =   array('data'=>$data, 'message'=>'Successfully', 'status'=>1);

        }

        else

           {

              $result = array('message' => "some error occured", 'status' => 0);

           }



        echo json_encode($result);

        exit();



    }



//-------------------------ADD STAFF-------------------------------



  public function addStaff()

    {

      if($_POST['api_key'] != $this->api_key)

        {

            return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));        

        }

           $content = isset($_POST['content']) ? $_POST['content']: '0';

           $staffid = isset($_POST['staffid']) ? $_POST['staffid']: '0';

    

            if(!!$content&& !!$staffid )

              {

                  $arr = array(

                                 'content'  => $content,

                                 'staffid' => $staffid

                              );



                   $this->api_model->insertRecord('tblproposal_comments',$arr);

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





//-------------------------CONTRACT-----------------------------



  public function contractDetail($id)

    { 

       if($_GET['api_key'] != $this->api_key)

       {

           return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

       }

         if($id)

         {

            $uDetail      =   $this->api_model->getContract('tblcontracts', $id);

            $data           =   $uDetail ? $uDetail : array();

            $result         =   array('data'=>$data, 'message'=>'Successfully', 'status'=>1);



         }

         else

         {

            $result = array('message' => "some error occured", 'status' => 0);

         }

         echo json_encode($result);

         exit();



    }



//---------------------INVOICES------------------------------



  public function invoiceDetail()

    {

      if($_GET['api_key'] != $this->api_key)

      {

        return json_encode(array('message' => "Sorry, wrong api key does not match !", 'status' => 0));            

      }

        $clientid =$_GET['clientid'];



        if(!empty($clientid))

        {

            $uDetail      =   $this->api_model->getInvoice('tblinvoices', $clientid);

            $vDetail      =   $this->api_model->getPayment('tblinvoicepaymentrecords', $uDetail[0]->id);

            $data           =   $uDetail ? $uDetail : array();

            $data1          =   $vDetail ? $vDetail : array();

            $pending = $data[0]->total - $data1[0]->amount;

            $result         =   array('total'=>$data[0]->total,'pending'=>$pending,'invoiceDetail' =>$data, 'message'=>'Successfully', 'status'=>200);



        }

        else

            {

               $result = array('message' => "Please enter client id", 'status' => 0);

            }



        echo json_encode($result);

        exit();



    }

//-------------------------------------------------------------------



  function getMemberChild($referId)

   {

       $getChilds          =   $this->api_model->getTableByCondition('companies', array('group_id'=>3, 'sponser_id'=>$referId));



        if($getChilds)

        {

            foreach ($getChilds as $getChild)

              {

                $this->clientTeam[] = $getChild->id;

                $this->getMemberChild($getChild->customer_sponser_id);

             }

        }



        return $this->clientTeam;

    }



//-----------------------------------------------------------------









}    

