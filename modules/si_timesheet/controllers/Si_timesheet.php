<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_timesheet extends AdminController 
{
	public function __construct()
	{
		parent::__construct(); 
		if (!is_admin() && !has_permission('si_timesheet', '', 'view') && !has_permission('si_timesheet', '', 'view_own')) {
			access_denied(_l('si_timesheet'));
		}
		$this->load->model('currencies_model');
	}
	
	/* get Calendar functions */
	public function index()
	{
		$saved_filter_name='';
		$filter_id = $this->input->get('filter_id');
		if($filter_id!='' && is_numeric($filter_id) && empty($this->input->post()))
		{
			$filter_obj = $this->si_timesheet_model->get($filter_id);
			if(!empty($filter_obj))
			{
				$_POST = unserialize($filter_obj->filter_parameters);
				$saved_filter_name = $filter_obj->filter_name;
			}	
		}
		
		$has_permission_view   = has_permission('si_timesheet', '', 'view');

		if (!$has_permission_view) {
			$staff_id = get_staff_user_id();
		} elseif ($this->input->post('member')) {
			$staff_id = $this->input->post('member');
		} else {
			$staff_id = '';
		}
		
		$status = $this->input->post('status');//fetch array of statuses
		if(empty($status))
			$status=array('');
			
		if ($this->input->post('rel_id')) {
			$rel_id = $this->input->post('rel_id');
		} else {
			$rel_id = '';
		}
		
		if ($this->input->post('rel_type')) {
			$rel_type = $this->input->post('rel_type');
		} else {
			$rel_type = '';
		}
		if ($this->input->post('group_id')) {
			$group_id = $this->input->post('group_id');
		} else {
			$group_id = '';
		}
		if ($this->input->post('date_by')) {
			$date_by = $this->input->post('date_by');
		} else {
			$date_by = '';
		}
		$tag = $this->input->post('tags');//fetch array of tags
		if(empty($tag))
			$tag=array('');//blank for All Tags	
		
		//save filter in db
		$save_filter = $this->input->post('save_filter');
		$filter_name='';
		$current_user_id = get_staff_user_id();
		if($save_filter==1)
		{
			$filter_name=$this->input->post('filter_name');
			$all_filter = $this->input->post();
			unset($all_filter['save_filter']);
			unset($all_filter['filter_name']);
			$saved_filter_name = $filter_name;
			$filter_parameters = serialize($all_filter);
			$filter_data = array('filter_name'=>$filter_name,
								 'filter_parameters'=>$filter_parameters,
								 'staff_id'=>$current_user_id);
			if($filter_id!='' && is_numeric($filter_id))
				$this->si_timesheet_model->update($filter_data,$filter_id);
			else
			{					 
				$filter_data['filter_type'] = SI_TS_FILTER_TYPE_CALENDAR;
				$filter_data['created_dt'] = date('Y-m-d H:i:s');
				$new_filter_id = $this->si_timesheet_model->add($filter_data);
			}	
		}	
			
		$data['title']                = _l('si_timesheet');
		add_calendar_assets();
		
		$data['members']  = $this->staff_model->get();
		$data['staff_id'] = $staff_id;
		$data['statuses']  =$status;
		$data['rel_id']   = $rel_id;
		$data['rel_type'] = $rel_type;
		$data['groups']   = $this->clients_model->get_groups();//customer_groups
		$data['group_id'] = $group_id;
		$data['report_months'] = $this->input->post('report_months');
		$data['report_from'] = $this->input->post('report_from');
		$data['report_to'] = $this->input->post('report_to');
		$data['date_by'] = $date_by;
		$data['tags']  =$tag;
		$data['filter_templates'] = $this->si_timesheet_model->get_templates($current_user_id,SI_TS_FILTER_TYPE_CALENDAR);
		$data['saved_filter_name'] = $saved_filter_name;

		$this->load->view('timesheet/calendar', $data);
	}

	public function get_calendar_data()
	{
		$_POST = $_REQUEST; //for Perfex Version > 2.8.2 (before that data was get as POST, now get as GET)
		if ($this->input->is_ajax_request()) {
	
			echo json_encode($this->si_timesheet_model->get_calendar_data(
				date('Y-m-d', strtotime($this->input->post('start'))),
				date('Y-m-d', strtotime($this->input->post('end'))),
				'',
				'',
				$this->input->post()
			));
			die();
		}
	}
	
	public function edit_timesheet()
	{
		if ($this->input->is_ajax_request()) {
			$start = $this->input->post('start');
			$end = $this->input->post('end');
			$id = $this->input->post('id');
			if($this->si_timesheet_model->edit_timesheet($start,$end,$id))
				echo json_encode(['success'=>true]);
			else
				echo json_encode(['error'=>true,'message'=>_l('si_ts_error_edit_timesheet')]);
			die();
		}
	}
	public function save_timesheet()
	{
		if ($this->input->post()) {
			$data    = $this->input->post();
			$success = $this->si_timesheet_model->add_update_timesheet($data);
			$message = '';
			if ($success) {
				if (isset($data['id'])) {
					$message = _l('si_ts_timesheet_updated_successfully');
				} else {
					$message = _l('si_ts_timesheet_added_successfully');
				}
			}
			echo json_encode([
				'success' => $success,
				'message' => $message,
			]);
			die();
		}
		
	}
	public function view_timesheet($id=1)
	{
		$data['timesheet'] = $this->si_timesheet_model->get_timesheet($id);
		$data['editable'] = true;
		if(get_option(SI_TIMESHEET_MODULE_NAME.'_completed_task_allow_edit')==0 && $data['timesheet']->status==5)
			$data['editable'] = false;
		if (!empty($data['timesheet']))
			$this->load->view('timesheet/timesheet', $data);
	}

	public function delete_timesheet($id)
	{
		if ($this->input->is_ajax_request()) {
			$timesheet = $this->si_timesheet_model->get_timesheet($id);
			if ($timesheet->staff_id != get_staff_user_id() && !is_admin()) {
				echo json_encode([
					'success' => false,
				]);
				die;
			}
			$success = $this->si_timesheet_model->delete_timesheet($id);
			$message = '';
			if ($success) {
				$message = _l('si_ts_timesheet_deleted_successfully');
			}
			echo json_encode([
				'success' => $success,
				'message' => $message,
			]);
			die();
		}
	}
	
	public function ajax_search_assign_task_to_timer()
	{
		if ($this->input->is_ajax_request()) {
			$q = $this->input->post('q');
			$q = trim($q);
			$rel_type = $this->input->post('rel_type');
			$rel_id = $this->input->post('rel_id');
			$this->db->select('name, id, CONCAT(IFNULL(' . tasks_rel_name_select_query() . ',\'\')," ('._l('task').' : '._l('task_single_start_date').' ",DATE_FORMAT(startdate,"%d-%m-%Y")," - '._l('task_single_due_date').' ",IFNULL(DATE_FORMAT(duedate,"%d-%m-%Y"),\'-\'),")") as subtext');
			$this->db->from(db_prefix() . 'tasks');
			$this->db->where('' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ')');
			#do not fetch completed tasks if set
			if(get_option(SI_TIMESHEET_MODULE_NAME.'_completed_task_allow_add')==0)
				$this->db->where('status != ', 5);
			#do not fetch excluded status Tasks, if set	
			$exclude_status = unserialize(get_option(SI_TIMESHEET_MODULE_NAME.'_task_status_exclude_add'));
			#if status is from excluded statuses then ignore and return
			if(!empty($exclude_status))
				$this->db->where_not_in('status', $exclude_status);
			#fetch the task which having rel_type and rel_id set
			if($rel_type !='')
				$this->db->where('rel_type', $rel_type);
			if($rel_id !='' && is_numeric($rel_id))
				$this->db->where('rel_id', $rel_id);			
				
			$this->db->where('(name LIKE "%' . $q . '%" OR ' . tasks_rel_name_select_query() . ' LIKE "%' . $q . '%")');
			echo json_encode($this->db->get()->result_array());
		}
	}
	
	function timesheet_templates()
	{
		$data=array();
		$data['title']    = _l('si_ts_filter_templates');
		$current_user_id = get_staff_user_id();
		$data['filter_templates'] = $this->si_timesheet_model->get_templates($current_user_id);
		$this->load->view('timesheet_list_filters', $data);
	}
	function del_timesheet_filter($id)
	{
		$current_user_id = get_staff_user_id();
		$this->si_timesheet_model->delete($id,$current_user_id);
		redirect('si_timesheet/timesheet_templates');
	}
	
	
	private function get_where_report_period($field = 'date')
	{
		$months_report      = $this->input->post('report_months');
		$custom_date_select = '';
		if ($months_report != '') {
			if (is_numeric($months_report)) {
				// Last month
				if ($months_report == '1') {
					$beginMonth = date('Y-m-01 00:00:00', strtotime('first day of last month'));
					$endMonth   = date('Y-m-t 23:59:59', strtotime('last day of last month'));
				} else {
					$months_report = (int) $months_report;
					$months_report--;
					$beginMonth = date('Y-m-01 00:00:00', strtotime("-$months_report MONTH"));
					$endMonth   = date('Y-m-t 23:59:59');
				}

				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . strtotime($beginMonth) . '" AND "' . strtotime($endMonth) . '")';
			} elseif ($months_report == 'today') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . strtotime(date('Y-m-d 00:00:00')) . '" AND "' . strtotime(date('Y-m-d 23:59:59')) . '")';
			} elseif ($months_report == 'this_week') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . strtotime(date('Y-m-d 00:00:00', strtotime('monday this week'))) . '" AND "' . strtotime(date('Y-m-d 23:59:59', strtotime('sunday this week'))) . '")';
			} elseif ($months_report == 'last_week') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . strtotime(date('Y-m-d 00:00:00', strtotime('monday last week'))) . '" AND "' . strtotime(date('Y-m-d 23:59:59', strtotime('sunday last week'))) . '")';			
			} elseif ($months_report == 'this_month') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . strtotime(date('Y-m-01 00:00:00')) . '" AND "' . strtotime(date('Y-m-t 23:59:59')) . '")';
			} elseif ($months_report == 'this_year') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' .
				strtotime(date('Y-01-01 00:00:00')) .
				'" AND "' .
				strtotime(date('Y-12-31 23:59:59')) . '")';
			} elseif ($months_report == 'last_year') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' .
				strtotime(date(date('Y', strtotime('last year')) . '-01-01 00:00:00')) .
				'" AND "' .
				strtotime(date(date('Y', strtotime('last year')) . '-12-31 23:59:59')) . '")';
			} elseif ($months_report == 'custom') {
				$from_date = to_sql_date($this->input->post('report_from'));
				$to_date   = to_sql_date($this->input->post('report_to'));
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . strtotime($from_date. ' 00:00:00') . '" AND "' . strtotime($to_date . ' 23:59:59') . '")';
			}
		}
		
		 return $custom_date_select;
	}
	
	public function timesheet_summary()
	{
		$this->db->query("SET SESSION sql_mode = ''");//to allow sql_mode=only_full_group_by
		$this->db->query("SET SESSION group_concat_max_len=100000;");//need to set to override default value of 1024, to fetch huge data in Group_Concat for time
		
		$overview = [];
		
		$saved_filter_name='';
		$filter_id = $this->input->get('filter_id');
		if($filter_id!='' && is_numeric($filter_id) && empty($this->input->post()))
		{
			$filter_obj = $this->si_timesheet_model->get($filter_id);
			if(!empty($filter_obj))
			{
				$_POST = unserialize($filter_obj->filter_parameters);
				$saved_filter_name = $filter_obj->filter_name;
			}	
		}	

		$has_permission_create = has_permission('tasks', '', 'create');
		$has_permission_view   = (has_permission('tasks', '', 'view') && has_permission('si_timesheet', '', 'view'));

		if (!$has_permission_view) {
			$staff_id = get_staff_user_id();
		} elseif ($this->input->post('member')) {
			$staff_id = $this->input->post('member');
		} else {
			$staff_id = '';
		}
		
		if ($this->input->post('rel_id')) {
			$rel_id = $this->input->post('rel_id');
		} else {
			$rel_id = '';
		}
		
		if ($this->input->post('rel_type')) {
			$rel_type = $this->input->post('rel_type');
		} else {
			$rel_type = '';
		}
		if ($this->input->post('group_id')) {
			$group_id = $this->input->post('group_id');
		} else {
			$group_id = '';
		}
		if ($this->input->post('group_by')) {
			$group_by = $this->input->post('group_by');
		} else {
			$group_by = '';
		}
		if ($this->input->post('billable')!='') {
			$billable = $this->input->post('billable');
		} else {
			$billable = '';
		}
		$tag = $this->input->post('tags');//fetch array of tags
		if(empty($tag))
			$tag=array('');//blank for All Tags

		if ($this->input->post('hourly_rate_by')!='') {
			$hourly_rate_by = $this->input->post('hourly_rate_by');# by staff or tasks table
		} else {
			$hourly_rate_by = 'staff';
		}

		$status = $this->input->post('status');//fetch array of statuses
		if(empty($status))
			$status=array('');
			
		$hide_columns = $this->input->post('hide_columns');//fetch array of statuses
		if(empty($hide_columns))
			$hide_columns=array();	
		

		$fetch_month_from = 'start_time';//timestamp

		$save_filter = $this->input->post('save_filter');
		$filter_name='';
		$current_user_id = get_staff_user_id();
		if($save_filter==1)
		{
			$filter_name=$this->input->post('filter_name');
			$all_filter = $this->input->post();
			unset($all_filter['save_filter']);
			unset($all_filter['filter_name']);
			$saved_filter_name = $filter_name;
			$filter_parameters = serialize($all_filter);
			$filter_data = array('filter_name'=>$filter_name,
								 'filter_parameters'=>$filter_parameters,
								 'staff_id'=>$current_user_id);
			if($filter_id!='' && is_numeric($filter_id))
				$this->si_timesheet_model->update($filter_data,$filter_id);
			else
			{
				$filter_data['filter_type'] = SI_TS_FILTER_TYPE_SUMMARY;
				$filter_data['created_dt'] = date('Y-m-d H:i:s');					 
				$new_filter_id = $this->si_timesheet_model->add($filter_data);
			}	
		}	
		// Task rel_name
		$sqlTasksSelect = db_prefix().'taskstimers.*,' . tasks_rel_name_select_query() . ' as rel_name,'.db_prefix().'tasks.status,'.db_prefix().'tasks.name,'.db_prefix().'tasks.rel_id,CONCAT('.db_prefix() . 'staff.firstname,\' \','.db_prefix() . 'staff.lastname) as staff_full_name,'.db_prefix() . $hourly_rate_by.'.hourly_rate,'.db_prefix().'tasks.rel_type, GROUP_CONCAT(\'{"\',start_time,\'":"\',(end_time-start_time),\'"}\') as json_time';

		$sqlTasksSelect .= ',SUM(CASE
							WHEN end_time is NULL THEN ' . time() . '-start_time
							ELSE end_time-start_time
							END) as total_logged_time';

		$this->db->select($sqlTasksSelect);
		
		if($this->input->post('report_months')!='')
		{
			$custom_date_select = $this->get_where_report_period($fetch_month_from);
			$this->db->where("1=1 ".$custom_date_select);
		}
		
		if($rel_type!='')
			$this->db->where('rel_type', $rel_type);
		if($billable!='')
			$this->db->where('billable', $billable);	
		if ($rel_id && $rel_id != '') {
			$this->db->where('rel_id', $rel_id);
		}
		if ($group_id !='' && $rel_type == 'customer') {
			$this->db->join(db_prefix() .'customer_groups',db_prefix() .'customer_groups.customer_id='.db_prefix() . 'tasks.rel_id','left');
			$this->db->where('groupid', $group_id);
		}

		if (!$has_permission_view) {
			$this->db->where(db_prefix() . 'taskstimers.staff_id',$staff_id);
			
		} elseif ($has_permission_view) {
			if (is_numeric($staff_id)) {
				$this->db->where(db_prefix() . 'taskstimers.staff_id',$staff_id);
			}
		}
		
		if ($tag && !in_array('',$tag)) {
			$this->db->where_in(db_prefix() . 'tasks.id','select distinct(rel_id) from '.db_prefix() . 'taggables where '.db_prefix() . 'taggables.rel_type=\'task\' and tag_id in('.implode(',',$tag).')',false);
		}

		if ($status && !in_array('',$status)) {
			$this->db->where_in('status', $status);
		}
		
		$this->db->where(db_prefix() . 'taskstimers.task_id > 0');
		$this->db->order_by($fetch_month_from, 'ASC');
		$this->db->group_by(db_prefix() . 'taskstimers.task_id, staff_id');
		$this->db->join(db_prefix() . 'tasks',db_prefix() . 'tasks.id='.db_prefix() . 'taskstimers.task_id','left');
		$this->db->join(db_prefix() . 'staff',db_prefix() . 'staff.staffid='.db_prefix() . 'taskstimers.staff_id','left');
		$overview_ = $this->db->get(db_prefix() . 'taskstimers');

		unset($overview[0]);
		$months_cols = array();
		$month_names=[_l('January'), _l('February'), _l('March'), _l('April'), _l('May'), _l('June'), _l('July'), _l('August'), _l('September'), _l('October'), _l('November'), _l('December')];
		if($overview_){
			foreach($overview_->result_array() as $row)
			{
				$by='';
				if($group_by=='rel_name' && $row['rel_name']!='')
					$by = ucfirst($row['rel_type'])." - ".$row['rel_name'];
				elseif($group_by=='rel_name_and_name' && $row['rel_name']!='')
					$by = ucfirst($row['rel_type'])." - ".$row['rel_name']." : ".$row['name'];
				elseif($group_by=='name_and_rel_name' && $row['rel_name']!='')
					$by = ucfirst($row['rel_type'])." - ".$row['name']." : ".$row['rel_name'];	
				if($group_by=='task_name')
					$by = $row['name'];		
				elseif($group_by=='status')
					$by = format_task_status($row['status']);
				elseif($group_by=='staff')
					$by = $row['staff_id'];	
					
				//set months columnes in table
				$json_month = json_decode("[".$row['json_time']."]");
			
				foreach($json_month as $months)
				{
					foreach((array)$months as $key=>$val)
					{
						$key= date('Y_m',$key);
						if(isset($row[$key]))
							$row[$key]+=$val;
						else
							$row[$key]=$val;	
						if(!in_array($key,$months_cols))
						{	//add column if not exit for month
							$get_key_data = explode('_',$key);
							$yy = (isset($get_key_data[0])?$get_key_data[0]:'Year');
							$mm = (isset($get_key_data[1])?($month_names[$get_key_data[1]-1]):'Month');
							$months_cols[$key] = $yy.' ('.$mm.')';
						}		
					}
				}
					
				$overview[$by][]=$row;
				ksort($overview);
				ksort($months_cols);
			}
		}	

		$overview = [
			'staff_id' => $staff_id,
			'detailed' => $overview,
			'rel_id'   => $rel_id,
			'rel_type' => $rel_type,
			'group_id' => $group_id,
		];

		$data['members']  = $this->staff_model->get();
		$data['overview'] = $overview['detailed'];
		$data['years']    = $this->tasks_model->get_distinct_tasks_years(($this->input->post('month_from') ? $this->input->post('month_from') : 'startdate'));
		$data['staff_id'] = $overview['staff_id'];
		$data['base_currency'] = $this->currencies_model->get_base_currency();
		$data['title']    = _l('si_ts_timesheet_summary_menu');
		$data['rel_id']   = $overview['rel_id'];
		$data['rel_type'] = $overview['rel_type'];
		$data['billable'] = $billable;
		$data['groups']   = $this->clients_model->get_groups();//customer_groups
		$data['group_id'] = $group_id;
		$data['report_months'] = $this->input->post('report_months');
		$data['report_from'] = $this->input->post('report_from');
		$data['report_to'] = $this->input->post('report_to');
		$data['group_by'] = $group_by;
		$data['statuses']  =$status;
		$data['tags']  =$tag;
		$data['hourly_rate_by'] = $hourly_rate_by;
		$data['filter_templates'] = $this->si_timesheet_model->get_templates($current_user_id,SI_TS_FILTER_TYPE_SUMMARY);
		$data['saved_filter_name'] = $saved_filter_name;
		$data['hide_columns'] = $hide_columns;
		$data['months_cols'] = $months_cols;
		$data['show_custom_fields'] = get_option(SI_TIMESHEET_MODULE_NAME.'_show_task_custom_fields');
		$this->load->view('timesheet_summary', $data);
	}
}
