<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_sms_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	* @param  integer (optional)
	* @return object
	* Get single template filter
	*/
	public function get($id = '')
	{
		$staff_id = get_staff_user_id();
		$this->db->where('(staff_id = '.$staff_id.' OR is_public = 1)');
		
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'si_sms_templates')->row();
		}
		return $this->db->get(db_prefix() . 'si_sms_templates')->result_array();
	}
	/**
	* Add new sms template
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function add($data)
	{
		$this->db->insert(db_prefix() . 'si_sms_templates', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			log_activity('New Add-on SMS Template Added [Name:' . $data['template_name'] . ']');
			return $insert_id;
		}
		return false;
	}
	/**
	* Update sms templates
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function update($template_id,$data)
	{
		$this->db->where('id',$template_id);
		$this->db->where('staff_id',get_staff_user_id());
		$update = $this->db->update(db_prefix() . 'si_sms_templates', $data);
		if ($update) {
			log_activity('Add-on SMS Template Updated [Name:' . $data['template_name'] . ']');
			return true;
		}
		return false;
	}
	/**
	* Delete sms template
	* @param  mixed $id template id
	* @return boolean
	*/
	public function delete($id,$staff_id)
	{
		$this->db->where('id', $id);
		$this->db->where('staff_id', $staff_id);
		$this->db->delete(db_prefix() . 'si_sms_templates');
		if ($this->db->affected_rows() > 0) {
			log_activity('Add-on SMS Template Deleted [ID:' . $id . ']');
			return true;
		}
		return false;
	}
	public function get_clients()
	{
		$permission_view_own = (!has_permission('customers', '', 'view'));
		if($permission_view_own){
			$this->db->join(db_prefix() . 'customer_admins',db_prefix() . 'customer_admins.customer_id='.db_prefix() . 'clients.userid');
			$this->db->where(db_prefix() . 'customer_admins.staff_id',get_staff_user_id());
		}
		$this->db->select('userid as id,company as name',false);	
		$this->db->where('active',1);
		$this->db->from(db_prefix() . 'clients');
		return $this->db->get()->result_array();
	}
	public function get_leads()
	{
		$has_permission_view = has_permission('leads', '', 'view');
		if (!$has_permission_view) {
			$this->db->where('(assigned = ' . get_staff_user_id() . ' OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');
		}
		$this->db->select('id,name');	
		$this->db->from(db_prefix() . 'leads');
		return $this->db->get()->result_array();
	}
	public function get_client_contacts($client_ids,$allow_without_phonenumber = false, $where = ['active' => 1])
	{
		if ($client_ids != '' && (is_numeric($client_ids) || is_array($client_ids))){
			$send_type = get_option(SI_SMS_MODULE_NAME.'_send_to_customer');
			if($send_type=='all' || $send_type=='primary'){
				$this->db->select('id,CONCAT(firstname," ",lastname) as name,phonenumber,email,userid',false);
				$this->db->where($where);
				if (is_numeric($client_ids)) {
					$this->db->where('userid', $client_ids);
				}
				if (is_array($client_ids)) {
					$this->db->where_in('userid', $client_ids);
				}
				if($send_type=='primary')
					$this->db->where('is_primary', 1);
				if(!$allow_without_phonenumber){
					$this->db->where('phonenumber IS NOT NULL');
					$this->db->where('phonenumber<>', '');
				}
				return $this->db->get(db_prefix() . 'contacts')->result_array();
			}
			if($send_type=='client'){
				$this->db->select('userid as id,company as name,phonenumber,"" as email',false);
				$this->db->where($where);
				if (is_numeric($client_ids)) {
					$this->db->where('userid', $client_ids);
				}
				if (is_array($client_ids)) {
					$this->db->where_in('userid', $client_ids);
				}
				if(!$allow_without_phonenumber){
					$this->db->where('phonenumber IS NOT NULL');
					$this->db->where('phonenumber<>', '');
				}
				return $this->db->get(db_prefix() . 'clients')->result_array();
			}
		}
		return array();
	}
	public function get_leads_contacts($lead_ids,$allow_without_phonenumber = false, $where = [])
	{
		if ($lead_ids != '' && (is_numeric($lead_ids) || is_array($lead_ids))){
			$this->db->select('id,name,phonenumber,email');
			$this->db->where($where);
			if (is_numeric($lead_ids)) {
				$this->db->where('id', $lead_ids);
			}
			if (is_array($lead_ids)) {
				$this->db->where_in('id', $lead_ids);
			}
			if(!$allow_without_phonenumber){
				$this->db->where('phonenumber IS NOT NULL');
				$this->db->where('phonenumber<>', '');
			}
			return $this->db->get(db_prefix() . 'leads')->result_array();
		}
		return array();
	}
	public function get_staffs_contacts($staff_ids,$allow_without_phonenumber = false, $where = ['active' => 1])
	{
		if ($staff_ids != '' && (is_numeric($staff_ids) || is_array($staff_ids))){
			$this->db->select('staffid as id,CONCAT(firstname," ",lastname) as name,phonenumber,email',false);
			$this->db->where($where);
			if (is_numeric($staff_ids)) {
				$this->db->where('staffid', $staff_ids);
			}
			if (is_array($staff_ids)) {
				$this->db->where_in('staffid', $staff_ids);
			}
			if(!$allow_without_phonenumber){
				$this->db->where('phonenumber IS NOT NULL');
				$this->db->where('phonenumber<>', '');
			}
			return $this->db->get(db_prefix() . 'staff')->result_array();
		}
		return array();
	}
	/**
	* @param  integer (optional)
	* @return object
	* Get single scheduler
	*/
	public function get_schedule($id,$where = [])
	{
		$this->db->where('id', $id);
		if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }
		return $this->db->get(db_prefix() . 'si_sms_schedule')->row();
	}
	/**
	* @param  integer (optional)
	* @return object
	* Get range of scheduler
	*/
	public function get_schedules($start_date,$end_date)
	{
		$this->db->where('schedule_date between "'.$start_date.'" and "'.$end_date.'"');
		$this->db->where('executed', 0);
		$this->db->order_by('schedule_date','ASC');
		return $this->db->get(db_prefix() . 'si_sms_schedule')->result_array();
	}
	/**
	* Add new sms scheduler
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function add_schedule($data)
	{
		$rel_ids = array();
		if(isset($data['rel_ids'])){
			$rel_ids = $data['rel_ids'];
			unset($data['rel_ids']);
		}	
		$this->db->insert(db_prefix() . 'si_sms_schedule', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			if(!empty($rel_ids))
				$this->add_schedule_rel_ids($insert_id,$rel_ids);
			log_activity('Add-on Schedule SMS Added [Id:' . $insert_id . ']');
			return $insert_id;
		}
		return false;
	}
	/**
	* Update sms schedule
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function update_schedule($schedule_id,$data)
	{
		$rel_ids = array();
		if(isset($data['rel_ids'])){
			$rel_ids = $data['rel_ids'];
			unset($data['rel_ids']);
		}
		$via_cron = false;
		if(isset($data['cron'])){
			$via_cron = true;
			unset($data['cron']);
		}
		
		if(is_staff_logged_in() && !$via_cron){
			$this->db->where('staff_id',get_staff_user_id());
		}
		$this->db->where('id',$schedule_id);
			
		$update = $this->db->update(db_prefix() . 'si_sms_schedule', $data);
		if ($update) {
			if(!empty($rel_ids))
				$this->add_schedule_rel_ids($schedule_id,$rel_ids);
			return true;
		}
		return false;
	}
	/**
	* Delete sms schedule
	* @param  mixed $id schedule id
	* @return boolean
	*/
	public function delete_schedule($id)
	{
		if (!has_permission('si_sms_schedule_send', '', 'view'))
			$this->db->where('staff_id', get_staff_user_id());
		
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'si_sms_schedule');
		
		if ($this->db->affected_rows() > 0) {
			//delete existing rel_ids
			$this->db->where('schedule_id',$id);
			$this->db->delete(db_prefix() . 'si_sms_schedule_rel');
			return true;
		}
		return false;
	}
	/**
	* Add sms schedule send to ids
	* @param  mixed $id schedule id
	* @return boolean
	*/
	private function add_schedule_rel_ids($schedule_id,$rel_ids)
	{
		if(is_numeric($schedule_id) && $schedule_id > 0) {
			//delete existing rel_ids
			$this->db->where('schedule_id',$schedule_id);
			$this->db->delete(db_prefix() . 'si_sms_schedule_rel');
			//add new rel_ids
			foreach($rel_ids as $rel_id){
				$_data = array('schedule_id' => $schedule_id,'rel_id'=>$rel_id);
				$this->db->insert(db_prefix() . 'si_sms_schedule_rel', $_data);
			}
		}
	}
	//get rel ids as array
	public function get_schedule_rel_ids($schedule_id)
	{
		$rel_ids = array();
		if(is_numeric($schedule_id)){
			$this->db->where('schedule_id',$schedule_id);
			$result = $this->db->get(db_prefix() . 'si_sms_schedule_rel')->result_array();
			foreach($result as $row){
				$rel_ids[] = $row['rel_id'];
			}
		}
		return $rel_ids;
	}
}
