<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_timesheet_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	//get timesheet calender data
	public function get_calendar_data($start, $end, $client_id = '', $contact_id = '', $filters = false)
	{
		$data = array();
		$client_data = false;
		$has_permission_create = has_permission('si_timesheet', '', 'create');
		$has_permission_view   = has_permission('si_timesheet', '', 'view');
		$has_permission_edit   = has_permission('si_timesheet', '', 'edit');
		
		if (!$has_permission_view || isset($filters['dashboard_calendar_filters'])) {
			$staff_id = get_staff_user_id();
		} elseif ($this->input->post('member')) {
			$staff_id = $this->input->post('member');
		} else {
			$staff_id = '';
		}
		if(isset($filters['status']))
			$status = $filters['status'];//fetch array of statuses
		else
			$status=array('');//blank for All Tags
		if(isset($filters['tags']))
			$tag = $filters['tags'];//fetch array of tags
		else
			$tag=array('');//blank for All Tags
			
		$this->db->select(db_prefix() . 'taskstimers.*,'.db_prefix() . 'tasks.name as title,' . tasks_rel_name_select_query() . ' as rel_name,rel_id,status,CASE WHEN duedate IS NULL THEN startdate ELSE duedate END as date', false);
		$this->db->where('(start_time BETWEEN "' . strtotime($start) . '" AND "' . strtotime($end) . '" OR end_time BETWEEN "' . strtotime($start) . '" AND "' . strtotime($end) . '")');
		$this->db->join(db_prefix() . 'tasks', db_prefix() . 'tasks.id=' . db_prefix() . 'taskstimers.task_id', 'left');
		if(isset($filters['rel_type']) && $filters['rel_type']!='')
			$this->db->where('rel_type', $filters['rel_type']);
		if (isset($filters['rel_id']) && $filters['rel_id'] != '') {
			$this->db->where('rel_id', $filters['rel_id']);
		}
		if (isset($filters['group_id']) && $filters['group_id'] !='' && $filters['rel_type'] == 'customer') {
			$this->db->join(db_prefix() .'customer_groups',db_prefix() .'customer_groups.customer_id='.db_prefix() . 'tasks.rel_id','left');
			$this->db->where('groupid', $filters['group_id']);
		}
		
		if (!$has_permission_view) {
			$sqlWhereStaff = '('.db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $staff_id . ')';

			// User dont have permission for view but have for create
			// Only show tasks createad by this user.
			if ($has_permission_create) {
				$sqlWhereStaff .= ' OR addedfrom=' . get_staff_user_id();
			}

			$sqlWhereStaff .= ')';
			$this->db->where($sqlWhereStaff);
		} elseif ($has_permission_view) {
			if (is_numeric($staff_id)) {
				$this->db->where('('.db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $staff_id . '))');
			}
		}
		if (is_numeric($staff_id)) {
			$this->db->where(db_prefix() . 'taskstimers.staff_id',$staff_id);
		}
		if ($tag && !in_array('',$tag)) {
			$this->db->where_in(db_prefix() . 'tasks.id','select distinct(rel_id) from '.db_prefix() . 'taggables where '.db_prefix() . 'taggables.rel_type=\'task\' and tag_id in('.implode(',',$tag).')',false);
		}
		if ($status && !in_array('',$status)) {
			$this->db->where_in('status', $status);
		}
		$this->db->where('task_id > 0');
		$this->db->from(db_prefix() . 'taskstimers');	
		$tasks = $this->db->get()->result_array();

		foreach ($tasks as $task) {
			$rel_showcase = '';
			if (!is_null($task['rel_id'])) {
				$rel_showcase = ' (' . $task['rel_name'] . ')';
			}
			//set start and end for time base event
			$task['start'] = date('Y-m-d H:i',$task['start_time']);
			$task['end'] = date('Y-m-d H:i',$task['end_time']);
			
			$staffOutput = '';
			if((is_admin() || $has_permission_view) && get_option(SI_TIMESHEET_MODULE_NAME.'_show_staff_icon_in_calendar')==1)//display staff only if multi staff task is showing
			{
				$staff_full_name = get_staff_full_name($task['staff_id']);
	
				$staffOutput	 = "<a data-toggle='tooltip' data-placement='bottom' data-title='" . $staff_full_name . "' href='" . admin_url('profile/' . $task['staff_id']) . "'>" . staff_profile_image($task['staff_id'], [
				"staff-profile-image-small",
			   ]) . "</a>";
	
				$staffOutput .= "<span class='hide'>" . $staff_full_name . "</span>";
			}
			
			
			$name             = mb_substr($task['title'], 0, 60) . "...";
			$task['_tooltip'] = $staffOutput._l('calendar_task') . " - " . $name . $rel_showcase;
			$task['title']    = _l('calendar_task') . " - " . $name . $rel_showcase." ("._dt(date('d-m-Y h:i A',$task['start_time']))." - ".(!is_null($task['end_time'])?_dt(date('d-m-Y h:i A',$task['end_time'])):'').")";
			$status           = get_task_status_by_id($task['status']);
			$task['color']    = $status['color'];
			$task['editable'] = (is_admin() || ($task['staff_id']==get_staff_user_id() && $has_permission_edit && !(get_option(SI_TIMESHEET_MODULE_NAME.'_completed_task_allow_edit')==0 && $task['status']==5))) ? true : false;
	
			if (!$client_data) {
				$task['onclick'] = 'view_timesheet(' . $task['id'] . '); return false';
				$task['url']     = '#';
			} else {
				$task['url'] = site_url('clients/project/' . $task['rel_id'] . '?group=project_tasks&taskid=' . $task['id']);
			}
			array_push($data, $task);
		}
		return $data;
	}
	/**
	* @param  integer (optional)
	* @return object
	* Get single timesheet filter
	*/
	public function get($id = '')
	{
		$this->db->where('staff_id',get_staff_user_id());
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'si_timesheet_filter')->row();
		}
		return $this->db->get(db_prefix() . 'si_timesheet_filter')->result_array();
	}
	/**
	* get all filter templates of that staff
	*/
	function get_templates($staff_id,$filter_type='')
	{
		if (is_numeric($staff_id)) {
			$this->db->where('staff_id', $staff_id);
			if(is_numeric($filter_type))
				$this->db->where('filter_type', $filter_type);
			return $this->db->get(db_prefix() . 'si_timesheet_filter')->result_array();
		}
		return array();
	}
	/**
	* Add new timesheet filter
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function add($data)
	{
		$this->db->insert(db_prefix() . 'si_timesheet_filter', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			log_activity('New Timesheet Filter Added [Name:' . $data['filter_name'] . ']');
			return $insert_id;
		}
		return false;
	}
	/**
	* Update timesheet filter
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function update($data,$filter_id)
	{
		$this->db->where('id',$filter_id);
		$update = $this->db->update(db_prefix() . 'si_timesheet_filter', $data);
		if ($update) {
			log_activity('Timesheet Filter Updated [Name:' . $data['filter_name'] . ']');
			return true;
		}
		return false;
	}
	/**
	* Delete timesheet filter
	* @param  mixed $id filter id
	* @return boolean
	*/
	public function delete($id,$staff_id)
	{
		$this->db->where('id', $id);
		$this->db->where('staff_id', $staff_id);
		$this->db->delete(db_prefix() . 'si_timesheet_filter');
		if ($this->db->affected_rows() > 0) {
			log_activity('Timesheet Filter Deleted [ID:' . $id . ']');
			return true;
		}
		return false;
	}
	
	//edit by drag and drop events from calendar
	public function edit_timesheet($start,$end,$id,$note=null)
	{
		if(is_numeric($id) && $start!='' && $end != '')
		{
			$this->db->where('id',$id);
			if(!is_admin())
				$this->db->where('staff_id',get_staff_user_id());
			$result = $this->db->get(db_prefix() . 'taskstimers');
			if($result->num_rows() >0)
			{
				$this->db->set('start_time',strtotime($start));
				$this->db->set('end_time',strtotime($end));
				if(!is_null($note))
					$this->db->set('note',$note);
				$this->db->where('id',$id);
				$this->db->update(db_prefix() . 'taskstimers');
				return $this->db->affected_rows();
			}	
		}
		return false;
	}
	public function get_timesheet($id)
    {
        $is_admin                     = is_admin();
        $has_permission_timesheets_view_own    = has_permission('si_timesheet', '', 'view_own');
		if (is_numeric($id)) {
			$this->db->select(db_prefix() . 'taskstimers.*,name,CONCAT(IFNULL(' . tasks_rel_name_select_query() . ',\'\')," ('._l('task').' : '._l('task_single_start_date').' ",DATE_FORMAT(startdate,"%d-%m-%Y")," - '._l('task_single_due_date').' ",IFNULL(DATE_FORMAT(duedate,"%d-%m-%Y"),\'-\'),")") as subtext,status');
            $this->db->where(db_prefix() . 'taskstimers.id',$id);
			if(!$is_admin && $has_permission_timesheets_view_own)
				$this->db->where('staff_id',get_staff_user_id());
			$this->db->join(db_prefix() . 'tasks',db_prefix() . 'tasks.id='.db_prefix() . 'taskstimers.task_id','left');	
			$result = $this->db->get(db_prefix() . 'taskstimers');
			
			if($result->num_rows() >0)
			{
				return $result->row();
			}
			return array();
		}
		return [];
	}
	
	/**
	* Add new or update Timesheet by popup from calendar
	* @param array $data event $_POST data
	*/
	public function add_update_timesheet($data)
	{
		$data['staff_id'] = get_staff_user_id();
		$data['start_time']  = strtotime(to_sql_date($data['start'],true), true);
		if ($data['end'] !== '') {
			$data['end_time'] = strtotime(to_sql_date($data['end'],true), true);
		}
		unset($data['start']);
		unset($data['end']);
		
		$data['note'] = nl2br($data['note']);
		if (isset($data['id'])) {
			unset($data['staff_id']);
			$this->db->where('id', $data['id']);
			$timer = $this->db->get(db_prefix() . 'taskstimers')->row();
			if (!$timer) {
				return false;
			}
			$this->db->where('id', $data['id']);
			$this->db->update(db_prefix() . 'taskstimers', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		$this->db->insert(db_prefix() . 'taskstimers', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			return true;
		}
		return false;
	}
	
	public function delete_timesheet($id)
	{
		$this->db->where('id', $id);
		$timesheet = $this->db->get(db_prefix() . 'taskstimers')->row();
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'taskstimers');
		if ($this->db->affected_rows() > 0) {
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'timesheet');
			$this->db->delete(db_prefix() . 'taggables');

			$this->db->select('rel_type,rel_id,name,visible_to_client');
			$this->db->where('id', $timesheet->task_id);
			$task = $this->db->get(db_prefix() . 'tasks')->row();

			if ($task->rel_type == 'project') {
				$additional_data = $task->name;
				$total           = $timesheet->end_time - $timesheet->start_time;
				$additional_data .= '<br /><seconds>' . $total . '</seconds>';
				$this->projects_model->log_activity($task->rel_id, 'project_activity_task_timesheet_deleted', $additional_data, $task->visible_to_client);
			}
			log_activity('Timesheet Deleted [' . $id . ']');
			return true;
		}
		return false;
	}
}
