<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_lead_followup_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_leads($schedule=null)
	{
		$ids = array();
		if(!is_null($schedule) && is_array($schedule)){
			$this->db->where('status',$schedule['status']);
			if($schedule['source'] > 0)
				$this->db->where('source',$schedule['source']);
			$this->db->where('DATE(dateadded)',date('Y-m-d',strtotime('-'.$schedule['schedule_days'].' day')));
			$this->db->where('phonenumber IS NOT NULL');
			$this->db->where('phonenumber<>', '');	
		}
		$this->db->select('id,name,phonenumber,email');	
		$this->db->from(db_prefix() . 'leads');
		return $this->db->get()->result_array();
	}
	
	public function get_lead_staffs($schedule=null)
	{
		$ids = array();
		if(!is_null($schedule) && is_array($schedule)){
			$this->db->where('status',$schedule['status']);
			if($schedule['source'] > 0)
				$this->db->where('source',$schedule['source']);
			$this->db->where('DATE(dateadded)',date('Y-m-d',strtotime('-'.$schedule['schedule_days'].' day')));
			$this->db->where(db_prefix() . 'staff.phonenumber IS NOT NULL');
			$this->db->where(db_prefix() . 'staff.phonenumber<>', '');	
		}
		$this->db->where(db_prefix() . 'staff.active', 1);	
		$this->db->select('staffid as id,CONCAT(firstname," ",lastname) as name,'.db_prefix() . 'staff.phonenumber,'.db_prefix() . 'staff.email,'.db_prefix() . 'leads.id as lead_id,'.db_prefix() . 'leads.name as lead_name');
		$this->db->join(db_prefix() . 'staff',db_prefix() . 'staff.staffid = '.db_prefix() . 'leads.assigned');
		$this->db->from(db_prefix() . 'leads');
		return $this->db->get()->result_array();
	}
	
	public function get_schedule($id,$where = [])
	{
		$this->db->where('id', $id);
		if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
			$this->db->where($where);
		}
		return $this->db->get(db_prefix() . 'si_lead_followup_schedule')->row();
	}
	
	public function get_schedules($hour_now,$today_date)
	{
		$this->db->where('schedule_hour',$hour_now);
		$this->db->where('(last_executed < "'. $today_date.'" OR last_executed IS NULL)');
		$this->db->order_by('id','ASC');
		return $this->db->get(db_prefix() . 'si_lead_followup_schedule')->result_array();
	}
	
	public function add_schedule($data)
	{
		$this->db->insert(db_prefix() . 'si_lead_followup_schedule', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			log_activity('Lead Followup Schedule Added [Id:' . $insert_id . ']');
			return $insert_id;
		}
		return false;
	}
	
	public function update_schedule($schedule_id,$data)
	{
		$via_cron = false;
		if(isset($data['cron'])){
			$via_cron = true;
			unset($data['cron']);
		}
		
		if(is_staff_logged_in() && !$via_cron){
			$this->db->where('staff_id',get_staff_user_id());
		}
		$this->db->where('id',$schedule_id);
			
		$update = $this->db->update(db_prefix() . 'si_lead_followup_schedule', $data);
		if ($update) {
			return true;
		}
		return false;
	}
	
	public function delete_schedule($id)
	{
		if (!has_permission('si_lead_followup', '', 'view'))
			$this->db->where('staff_id', get_staff_user_id());
		
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'si_lead_followup_schedule');
		
		if ($this->db->affected_rows() > 0) {
			$this->db->where('schedule_id',$id);
			$this->db->delete(db_prefix() . 'si_lead_followup_schedule_rel');
			return true;
		}
		return false;
	}
	
	public function add_schedule_rel_ids($schedule_id,$rel_id,$comment=false)
	{
		if(is_numeric($schedule_id) && $schedule_id > 0) {
			$_data = array('schedule_id' => $schedule_id,'rel_id'=>$rel_id,'dateadded' => date('Y-m-d H:i:s'));
			if($comment)
				$_data['comment'] = $comment;
			$this->db->insert(db_prefix() . 'si_lead_followup_schedule_rel', $_data);
		}
	}

	public function get_schedule_rel_ids($schedule_id)
	{
		if(is_numeric($schedule_id)){
			$schedule = $this->get_schedule($schedule_id);
			if($schedule->filter_by == 'lead'){
				$this->db->select(db_prefix() . 'si_lead_followup_schedule_rel.*,' . db_prefix() . 'leads.name as name');
				$this->db->join(db_prefix() . 'leads',db_prefix().'leads.id = '.db_prefix() .'si_lead_followup_schedule_rel.rel_id','left');
			}elseif($schedule->filter_by == 'staff'){
				$this->db->select(db_prefix() . 'si_lead_followup_schedule_rel.*, CONCAT(firstname," ",lastname) as name');
				$this->db->join(db_prefix() . 'staff',db_prefix().'staff.staffid = '.db_prefix() .'si_lead_followup_schedule_rel.rel_id','left');
			}
			
			$this->db->where('schedule_id',$schedule_id);
			$this->db->order_by('dateadded','DESC');
			return $this->db->get(db_prefix() . 'si_lead_followup_schedule_rel')->result_array();
			
		}
		return array();
	}
}