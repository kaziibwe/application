<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();

        //$this->load->model('auth_model');
        
    }

//-------------------------------------------------

 public function getRowById($table, $id)
    {
        $q = $this->db->get_where($table, array('id'=>$id), 1);
        if($q->num_rows()> 0)
        {
            return $q->row();
        }
        return false;
    }
//---------------------------------------------------

 public function getproject($table, $id)
    {
        $query = $this->db->select('name, description, status, progress, start_date','clientid')
            ->where('clientid', $id)
            ->get('tblprojects'); 
        if($query->num_rows() > 0)
        {
          return $query->result();
        }
        return false;

    }
//-----------------------------------------------------

     public function getReply($table, $id)
    {
        $query = $this->db->select('ticketid,userid,contactid, id, message','attachment')
            ->where('ticketid', $id)
            ->get('tblticket_replies'); 
        if($query->num_rows() > 0)
        {
          return $query->result();
        }
        return false;

    }
//-----------------------------------------------------------------
 public function getloginComapny($table, $id)
  {
        $query = $this->db->select('company, userid')
            ->where('userid', $id)
            ->get('tblclients'); 
        if($query->num_rows() > 0)
        {
          return $query->result();
        }
        return false;

  }
//------------------------------------------------------------------

  public function getLogoDetail($table, $id)
    {
        $query = $this->db->select('id, name,value')
            ->where('id', $id)
            ->get('tbloptions'); 
        if($query->num_rows() > 0)
        {
          return $query->result();
        }
        return false;

    }
//-----------------------------------------------------------------------

  public function getTicketById($tblticket_replies=null)
    {
        // $this->db->select('tblticket_replies.ticketid,tblticket_replies.id,tblticket_replies.message,tblticket_replies.admin,tblcontacts.firstname,tblcontacts.lastname,tblticket_attachments.file_name'); //,tblstaff.staffid, tblstaff.firstname,tblstaff.lastname
        // $this->db->from('tblticket_replies');
        // $this->db->join('tblcontacts', 'tblticket_replies.contactid =  tblcontacts.id');
  
        // $this->db->where('tblticket_replies.ticketid', $tblticket_replies);
        //$this->db->where('tblticket_replies.ticketid', $tblticket_replies);
		
      $this->db->select('tblticket_replies.id as id,tblticket_replies.ticketid,tblticket_attachments.file_name, tblticket_replies.admin,tblticket_replies.message,tblcontacts.firstname,tblcontacts.lastname'); //,tblticket_attachments.file_name
      $this->db->from('tblticket_replies');
      $this->db->join('tblcontacts', 'tblcontacts.userid =  tblticket_replies.userid','left');
     $this->db->join('tblticket_attachments', 'tblticket_attachments.replyid =  tblticket_replies.id','left');
      $this->db->where('tblticket_replies.ticketid', $tblticket_replies);
	  $query = $this->db->get();  
	  
	  
      return  $query->result(); 
        
    }

 //-----------------------------------------------------

 public function getStaffById($staff=null)
    {
        $this->db->select('staff.firstname,staff.staffid,proposal_comments.proposalid, proposal_comments.content');
        $this->db->from('staff');
        $this->db->join('proposal_comments', 'staff.staffid =  proposal_comments.staffid');
        $this->db->where('staff.staffid', $staff);
        $query = $this->db->get();        
        return $query->result(); 
    }
//-----------------------------------------------------------
 public function getTicket($table, $id)
    {
        $this->db->select('tbltickets.ticketid,tbltickets.name,tbltickets.subject, tbltickets.status,tbltickets.department,tbltickets.message,tbltickets.date,tbltickets.userid');//,tblticket_attachments.file_name
        $this->db->from('tbltickets');
        //$this->db->join('tblticket_attachments', 'tbltickets.ticketid =  tblticket_attachments.ticketid');
        $this->db->where('tbltickets.userid', $id);
        $this->db->order_by('tbltickets.date','desc');
		 
        $query = $this->db->get();        
		  
        return $query->result(); 
    }

  //    public function getTicketById($id)
  //   {
  //       $this->db->select('tblticket_replies.id,tblticket_replies.admin,tblticket_replies.message,tblticket_attachments.file_name,tblticket_attachments.replyid');
  //       $this->db->from('tblticket_replies');
  //       $this->db->join('tblticket_attachments', 'tblticket_attachments.replyid = tblticket_replies.id');
  //       $this->db->where('tblticket_replies.ticketid', $id);
  //         // $this->db->order_by('tblticket_replies.date','desc');
  //       $query = $this->db->get();      
		
		// return $this->db->last_query(); 
  //       // return $query->result(); 
  //   }

 // public function getTicket($table, $id)
 //    {
 //        $query = $this->db->select('name, subject, status, department, date,userid,ticketid')
 //            ->where('userid', $id)
 //            ->order_by('date','desc')
 //            ->get('tbltickets'); 
 //         if($query->num_rows() > 0)
 //          {
 //            return $query->result();
 //          }
 //        return false;

 //    }
//-------------------------------------------------------------
     public function getTicketFile($table, $id, $replyid)
    {
      if($replyid)
      {
        $query = $this->db->select('file_name')
            ->where('ticketid', $id)
              ->where('replyid', $replyid)
            ->get('tblticket_attachments'); 
          }
          // else
          // {
             // $query = $this->db->select('file_name')
            // ->where('ticketid', $id)
          // ->get('tblticket_attachments'); 
          // }
         if($query->num_rows() > 0)
          {
            return $query->row();
          }
        return false;

    }
	
	
	public function getTicketMainFile($table, $id)
    {
      if($id)
      {
        $query = $this->db->select('file_name')
            ->where('ticketid', $id)
              ->where('replyid', Null)
            ->get('tblticket_attachments'); 
          }
          // else
          // {
             // $query = $this->db->select('file_name')
            // ->where('ticketid', $id)
          // ->get('tblticket_attachments'); 
          // }
         if($query->num_rows() > 0)
          {
            return $query->row();
          }
        return false;

    }
  
//----------------------------------------------------------------    
     public function getTicketName($table, $id)
    {
        $query = $this->db->select('firstname, lastname, staffid')
            ->where('staffid', $id)
            ->get('tblstaff'); 
         if($query->num_rows() > 0)
          {
            return $query->result();
          }
        return false;

    }
//----------------------------------------------------------

public function getDepartment($table)
    {
       $q = $this->db->select('departmentid, name')
       ->get('tbldepartments'); 
        if($q->num_rows() > 0)
          {
            return $q->result();
          }
         return false;

    }

//-----------------------------------------------------------

public function getStatus($table)
    {
       $q = $this->db->select('ticketstatusid, name')
       ->get('tbltickets_status'); 
        if($q->num_rows() > 0)
          {
            return $q->result();
          }
         return false;

    }
//------------------------------------------------------------

public function getProjectList($table,$id)
    {
       $q = $this->db->select('id, name')
       ->where('clientid', $id)
       ->get('tblprojects'); 
        if($q->num_rows() > 0)
          {
             return $q->result();
          }
     return false;
    }
//------------------------------------------------------------------

 public function getContract($table, $id)
    {
        $query = $this->db->select('datestart, dateend, contract_value, description')
         ->where('id', $id)
          ->get('tblcontracts'); 
          if($query->num_rows() > 0)
         {
            return $query->result();
         }
         return false;

    }
//------------------------------------------------------------------

 public function getTableBalanceRowsById($table, $id)
    {
       $query = $this->db->select('SUM(debit) AS debit_total, SUM(credit) AS credit_total')
        ->where('company_id', $id)
        ->get($table);       
        if($query->num_rows() > 0)
        {
            return $query->result();
        }
         return false;

    }
//------------------------------------------------------------

 public function getInvoice($table, $id)
    {
        $query = $this->db->select('id,duedate, date, total, currency,status,clientid,number,prefix')
            ->where('clientid', $id)
            ->where('status !=', 2)
            ->get('tblinvoices'); 
        if($query->num_rows() > 0)
         {
            return $query->result();
         }
         return false;
    }
//------------------------------------------------------------
 public function getPayment($table, $id)
    {
        $query = $this->db->select('sum(amount) as amount')
        ->where('invoiceid', $id)
        ->get('tblinvoicepaymentrecords'); 
         if($query->num_rows() > 0)
         {
            return $query->result();
         }
        return false;

    }
//--------------------------------------------------------------

 public function getproposal($table, $id)
    {
        $query = $this->db->select('date,  currency,status,open_till, rel_id,datecreated')
         ->where('rel_id', $id)
         ->get('tblproposals'); 
         if($query->num_rows() > 0)
         {
           return $query->result();
        }
     return false;

    }
//----------------------------------------------------------------

public function getTotal($table, $id)
    {
        $query = $this->db->select(' sum(total) as total')
         ->where('rel_id', $id)
         ->get('tblproposals'); 
     if($query->num_rows() > 0)
        {
            return $query->row();
        }
        return false;

    }
//-----------------------------------------------------------------

public function insertRecord($table, $data)
    {
      if($this->db->insert($table, $data))
        {
            return $this->db->insert_id();
        }
        return false;

    }
    public function register($table, $data)
    {
      if($this->db->insert($table, $data))
        {
            return $this->db->insert_id();
        }
        return false;

    }

//---------------------------------------------------------------------

public function clientLogin($email, $password)
    {
        $query = $this->db->select('*')
        ->where('email', $email)
        ->limit(1)
         ->get('tblcontacts'); 
            			
        if ($query->num_rows() === 1)
         {
            $user       =   $query->row();
			$check_password   =   app_hasher()->CheckPassword($password, $user->password);
			
            if ($check_password === TRUE) {

                if ($user->active != 1) {          
                    return array('message'=>'Login unsuccessfull user not active', 'status'=>0);
                }
                return array('message'=>'Login successfull', 'status'=>200, 'data'=>$user);
            }
            else
             {
                return array('message'=>'Please check your password.', 'status'=>400);
             }   
        }
        else
         {
            return array('message'=>'email have not found in records.', 'status'=>404);
         }

    }
//----------------------------------------------------------------------

public function updateRowById($table, $data, $id)
    {
        if($this->db->update($table, $data, array('id'=>$id)))
        {
            return true;
        }
        return false;
    }
//---------------------------------------------------------------------

public function updateRecordByCompany($table, $data, $id)
    {
        if($this->db->update($table, $data, array('userid' => $id)))
        {
            return true;
        }
      return false;

    }
//-----------------------------------------------------------------------

public function updateRecordById($table, $data, $id)
    {
        if($this->db->update($table, $data, array('company_id' => $id)))
        {
            return true;
        }
        return false;
    }
//-----------------------------------------------------------------------

 public function getuser($table, $id)
    {
        $query = $this->db->select('firstname,lastname,profile_image, userid')
        ->where('userid', $id)
         ->get('tblcontacts'); 
         if($query->num_rows() > 0)
          {
            return $query->row();
          }
        return false;
    }
//----------------------------------------------------------------------

public function getTableByCondition($table, $condition = NULL, $orderby = NULL, $limit = NULL)
    {
        if($condition)
        {
            $this->db->where($condition);
        }

        if($orderby)
        {
            $this->db->order_by($orderby[0],$orderby[1]);
        }
        if($limit)
        {
            $this->db->limit($limit);
        }

        $q = $this->db->get($table);

        if ($q->num_rows() > 0) {

            $data = array();            

            foreach ($q->result() as $row) {

                $data[] =   $row;

            }

            return $data;
        }
        return false;
    }
//--------------------------------------------------------------------

public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'api')->row();
        }
       
        return $this->db->get(db_prefix() . 'api')->result_array();
    }
//------------------------------------------------------------------------

public function updateApi($dta, $id)
    {
		$id = "1";
		
		if($dta['status'] == '1')
		{
			$data['status'] = '1';
		}
		elseif($dta['status'] == '0')
		{
			$data['status'] = '0';
		}	
			
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'api', $data);
        if ($this->db->affected_rows() > 0) {
        
            return true;
        }

        return false;
    }


    public function getDepartmentById($table,$id){
    $this->db->select('name');
      $this->db->where('departmentid',$id);
      $q= $this->db->get($table);
      return $q->row();

    }

    public function getStatusById($table,$id){
      $this->db->select('name');
      $this->db->where('ticketstatusid',$id);
      $q= $this->db->get($table);
      return $q->row();

    }

//--------------------------------------------------------------------
}
