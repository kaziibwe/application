<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_task_filters extends AdminController 
{
	public function __construct()
	{
		parent::__construct(); 
		$this->load->model('si_task_filter_model');
		if (!is_admin() && !has_permission('si_task_filters', '', 'view')) {
			access_denied(_l('custom_reports'));
		}
	}
	
	private function get_where_report_period($field = 'date')
	{
		$months_report      = $this->input->post('report_months');
		$custom_date_select = '';
		if ($months_report != '') {
			if (is_numeric($months_report)) {
				// Last month
				if ($months_report == '1') {
					$beginMonth = date('Y-m-01', strtotime('first day of last month'));
					$endMonth   = date('Y-m-t', strtotime('last day of last month'));
				} else {
					$months_report = (int) $months_report;
					$months_report--;
					$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
					$endMonth   = date('Y-m-t');
				}

				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
			} elseif ($months_report == 'today') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-d') . '" AND "' . date('Y-m-d') . '")';
			} elseif ($months_report == 'this_week') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-d', strtotime('monday this week')) . '" AND "' . date('Y-m-d', strtotime('sunday this week')) . '")';
			} elseif ($months_report == 'last_week') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-d', strtotime('monday last week')) . '" AND "' . date('Y-m-d', strtotime('sunday last week')) . '")';	
			} elseif ($months_report == 'this_month') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
			} elseif ($months_report == 'this_year') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' .
				date('Y-m-d', strtotime(date('Y-01-01'))) .
				'" AND "' .
				date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
			} elseif ($months_report == 'last_year') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' .
				date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
				'" AND "' .
				date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
			} elseif ($months_report == 'custom') {
				$from_date = to_sql_date($this->input->post('report_from'));
				$to_date   = to_sql_date($this->input->post('report_to'));
				if ($from_date == $to_date) {
					$custom_date_select = 'AND ' . $field . ' = "' . $from_date . '"';
				} else {
					$custom_date_select = 'AND (' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
				}
			}
		}
		
		 return $custom_date_select;
	}
	
	public function tasks_report($kanban='')
	{
		//for kanban
		if($this->input->post('kanban')==1 || $kanban=='kanban')
			$this->switch_kanban(0);
		else
			$this->switch_kanban(1);	
		$kanban = false;
        if ($this->session->userdata('si_tasks_kanban_view') == 'true') {
            $kanban = true;
        }
		$data['switch_kanban'] = $kanban;
		//end kanban
			
		$overview = [];
		
		$saved_filter_name='';
		$filter_id = $this->input->get('filter_id');
		if($filter_id!='' && is_numeric($filter_id) && empty($this->input->post()))
		{
			$filter_obj = $this->si_task_filter_model->get($filter_id);
			if(!empty($filter_obj))
			{
				$_POST = unserialize($filter_obj->filter_parameters);
				$saved_filter_name = $filter_obj->filter_name;
			}	
		}	

		$has_permission_create = has_permission('tasks', '', 'create');
		$has_permission_view   = has_permission('tasks', '', 'view');

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
		//tasks includes relation type(projects,invoices...) for that client
		$include_rel_type = [];
		if (!empty($this->input->post('include_rel_type')) && $rel_type=='customer') {
			$include_rel_type = ['customer'];//by deault task of only relted to client
			$include_rel_type = array_merge($include_rel_type,$this->input->post('include_rel_type'));
		}
		
		if ($this->input->post('group_by')) {
			$group_by = $this->input->post('group_by');
		} else {
			$group_by = '';
		}
		if ($this->input->post('date_by')) {
			$date_by = $this->input->post('date_by');
		} else {
			$date_by = '';
		}
		if ($this->input->post('billable')!='') {
			$billable = $this->input->post('billable');
		} else {
			$billable = '';
		}
		if ($this->input->post('priority')!='') {
			$priority = $this->input->post('priority');
		} else {
			$priority = '';
		}
		$tag = $this->input->post('tags');//fetch array of tags
		if(empty($tag))
			$tag=array('');//blank for All Tags

		$status = $this->input->post('status');//fetch array of statuses
		if(empty($status))
			$status=array(defined("tasks_model::STATUS_IN_PROGRESS")?tasks_model::STATUS_IN_PROGRESS:4);
			
		$hide_columns = $this->input->post('hide_columns');//fetch array of statuses
		if(empty($hide_columns))
			$hide_columns=array();	
		

		$fetch_month_from = $date_by;
		
		$save_filter = $this->input->post('save_filter');
		$filter_name='';
		$current_user_id = get_staff_user_id();
		if($save_filter==1)
		{
			$filter_name=$this->input->post('filter_name');
			$all_filter = $this->input->post();
			unset($all_filter['save_filter']);
			unset($all_filter['filter_name']);
			if(isset($all_filter['kanban']))
				unset($all_filter['kanban']);
			$saved_filter_name = $filter_name;
			$filter_parameters = serialize($all_filter);
			$filter_data = array('filter_name'=>$filter_name,
								 'filter_parameters'=>$filter_parameters,
								 'staff_id'=>$current_user_id);
			if($filter_id!='' && is_numeric($filter_id))
				$this->si_task_filter_model->update($filter_data,$filter_id);
			else					 
				$new_filter_id = $this->si_task_filter_model->add($filter_data);
		}	


		// Task rel_name
		$sqlTasksSelect = db_prefix().'tasks.*,' . tasks_rel_name_select_query() . ' as rel_name';

		// Task logged time
		$selectLoggedTime = get_sql_calc_task_logged_time('tmp-task-id');
		// Replace tmp-task-id to be the same like tasks.id
		$selectLoggedTime = str_replace('tmp-task-id', db_prefix() . 'tasks.id', $selectLoggedTime);

		if (is_numeric($staff_id)) {
			$selectLoggedTime .= ' AND staff_id=' . $staff_id;
			$sqlTasksSelect .= ',(' . $selectLoggedTime . ')';
		} else {
			$sqlTasksSelect .= ',(' . $selectLoggedTime . ')';
		}

		$sqlTasksSelect .= ' as total_logged_time';

		// Task checklist items
		$sqlTasksSelect .= ',' . get_sql_select_task_total_checklist_items();

		if (is_numeric($staff_id)) {
			$sqlTasksSelect .= ',(SELECT COUNT(id) FROM ' . db_prefix() . 'task_checklist_items WHERE taskid=' . db_prefix() . 'tasks.id AND finished=1 AND finished_from=' . $staff_id . ') as total_finished_checklist_items';
		} else {
			$sqlTasksSelect .= ',' . get_sql_select_task_total_finished_checklist_items();
		}

		// Task total comment and total files
		$selectTotalComments = ',(SELECT COUNT(id) FROM ' . db_prefix() . 'task_comments WHERE taskid=' . db_prefix() . 'tasks.id';
		$selectTotalFiles    = ',(SELECT COUNT(id) FROM ' . db_prefix() . 'files WHERE rel_id=' . db_prefix() . 'tasks.id AND rel_type="task"';

		if (is_numeric($staff_id)) {
			$sqlTasksSelect .= $selectTotalComments . ' AND staffid=' . $staff_id . ') as total_comments_staff';
			$sqlTasksSelect .= $selectTotalFiles . ' AND staffid=' . $staff_id . ') as total_files_staff';
		}

		$sqlTasksSelect .= $selectTotalComments . ') as total_comments';
		$sqlTasksSelect .= $selectTotalFiles . ') as total_files';

		// Task assignees
		$sqlTasksSelect .= ',' . get_sql_select_task_asignees_full_names() . ' as assignees' . ',' . get_sql_select_task_assignees_ids() . ' as assignees_ids';
		
		//for kanban view
		if($kanban){
		$sqlTasksSelect .= ',(SELECT staffid FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id AND staffid=' . get_staff_user_id() . ') as current_user_is_assigned';
        $sqlTasksSelect .= ',(SELECT CASE WHEN addedfrom=' . get_staff_user_id() . ' AND is_added_from_contact=0 THEN 1 ELSE 0 END) as current_user_is_creator';
		}

		$this->db->select($sqlTasksSelect);
		
		if($this->input->post('report_months')!='')
		{
			$custom_date_select = $this->get_where_report_period($fetch_month_from);
			$this->db->where("1=1 ".$custom_date_select);
		}
		
		if($rel_type!='' && empty($include_rel_type))
			$this->db->where('rel_type', $rel_type);
		if($billable!='')
			$this->db->where('billable', $billable);
		if($priority!='')
			$this->db->where('priority', $priority);		
		if ($rel_id && $rel_id != '' && empty($include_rel_type)) {
			$this->db->where('rel_id', $rel_id);
		}
		if ($group_id !='' && $rel_type == 'customer' && empty($include_rel_type)) {
			$this->db->join(db_prefix() .'customer_groups',db_prefix() .'customer_groups.customer_id='.db_prefix() . 'tasks.rel_id','left');
			$this->db->where('groupid', $group_id);
		}
		
		//if client is selected, then get tasks of that client;s relations like projects, invoices etc...
		if (($group_id !='' || $rel_id !='') && $rel_type == 'customer' && !empty($include_rel_type)) {
			
			if($rel_id !='')
				$_rel_id =  $rel_id;//if client is selected
			elseif($group_id !='')
				$_rel_id = "(select customer_id from ".db_prefix() ."customer_groups where groupid=".$group_id.")";	
			
			$client_tasks_related_to = $include_rel_type;
			$client_rel_to_query     = '(';
		
			$lastElement = end($client_tasks_related_to);
			foreach ($client_tasks_related_to as $rel_to) {
				if ($rel_to == 'invoice') {
					$client_rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE clientid in( ' . $_rel_id . '))';
				} elseif ($rel_to == 'estimate') {
					$client_rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'estimates WHERE clientid in (' . $_rel_id . '))';
				} elseif ($rel_to == 'contract') {
					$client_rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'contracts WHERE client in(' . $_rel_id . '))';
				} elseif ($rel_to == 'ticket') {
					$client_rel_to_query .= '(rel_id IN (SELECT ticketid FROM ' . db_prefix() . 'tickets WHERE userid in (' . $_rel_id . '))';
				} elseif ($rel_to == 'expense') {
					$client_rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'expenses WHERE clientid in(' . $_rel_id . '))';
				} elseif ($rel_to == 'proposal') {
					$client_rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'proposals WHERE rel_id in(' . $_rel_id . ') AND rel_type="customer")';
				} elseif ($rel_to == 'customer') {
					$client_rel_to_query .= '(rel_id IN (SELECT userid FROM ' . db_prefix() . 'clients WHERE userid in (' . $_rel_id . '))';
				} elseif ($rel_to == 'project') {
					$client_rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'projects WHERE clientid in (' . $_rel_id . '))';
				}
		
				$client_rel_to_query .= ' AND rel_type="' . $rel_to . '")';
				if ($rel_to != $lastElement) {
					$client_rel_to_query .= ' OR ';
				}
			}
			$client_rel_to_query .= ')';
			
			$this->db->where($client_rel_to_query);
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
		
		if ($tag && !in_array('',$tag)) {
			$this->db->where_in(db_prefix() . 'tasks.id','select distinct(rel_id) from '.db_prefix() . 'taggables where '.db_prefix() . 'taggables.rel_type=\'task\' and tag_id in('.implode(',',$tag).')',false);
		}

		if ($status && !in_array('',$status)) {
			$this->db->where_in('status', $status);
		}

		$this->db->order_by($fetch_month_from, 'ASC');
		$overview_ = $this->db->get(db_prefix() . 'tasks')->result_array();

		unset($overview[0]);
		foreach($overview_ as $row)
		{
			 //set format from id
			 if($row['rel_type']=='invoice' && is_numeric($row['rel_name']))
				 $row['rel_name'] = format_invoice_number($row['rel_name']);
			 if($row['rel_type']=='proposal' && is_numeric($row['rel_name']))
				 $row['rel_name'] = format_proposal_number($row['rel_name']);
			 if($row['rel_type']=='estimate' && is_numeric($row['rel_name']))
          		$row['rel_name'] = format_estimate_number($row['rel_name']);
				
			$by='';
			if($kanban)
				$by = $row['status'];//group by status id, for kanban
			elseif($group_by=='rel_name' && $row['rel_name']!='')
				$by = _l($row['rel_type'])." - ".$row['rel_name'];
			elseif($group_by=='rel_name_and_name' && $row['rel_name']!='')
				$by = _l($row['rel_type'])." - ".$row['rel_name']." : ".$row['name'];
			elseif($group_by=='name_and_rel_name' && $row['rel_name']!='')
				$by = _l($row['rel_type'])." - ".$row['name']." : ".$row['rel_name'];	
			elseif($group_by=='task_name')
				$by = $row['name'];		
			elseif($group_by=='status')
				$by = format_task_status($row['status']);
	
			$overview[$by][]=$row;
			ksort($overview);
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
		$data['title']    = _l('tasks_filter');
		$data['rel_id']   = $overview['rel_id'];
		$data['rel_type'] = $overview['rel_type'];
		$data['billable'] = $billable;
		$data['priority'] = $priority;
		$data['groups']   = $this->clients_model->get_groups();//customer_groups
		$data['group_id'] = $group_id;
		$data['include_rel_type'] = $include_rel_type;
		$data['report_months'] = $this->input->post('report_months');
		$data['report_from'] = $this->input->post('report_from');
		$data['report_to'] = $this->input->post('report_to');
		$data['group_by'] = $group_by;
		$data['date_by'] = $date_by;
		$data['statuses']  =$status;
		$data['tags']  =$tag;
		$data['filter_templates'] = $this->si_task_filter_model->get_templates($current_user_id);
		$data['saved_filter_name'] = $saved_filter_name;
		$data['hide_columns'] = $hide_columns;
		
		//for kanban view ajax
		if($this->input->is_ajax_request())
		{
			echo $this->load->view('kan_ban', $data,true);
			die();
		}
		
		$this->load->view('task_report', $data);
	}
	
	private function switch_kanban($set = 0)
    {
        if ($set == 1) {
            $set = 'false';
        } else {
            $set = 'true';
        }

        $this->session->set_userdata([
            'si_tasks_kanban_view' => $set,
        ]);
    }
	
	function list_filters()
	{
		$data=array();
		$data['title']    = _l('tasks_filter_templates');
		$current_user_id = get_staff_user_id();
		$data['filter_templates'] = $this->si_task_filter_model->get_templates($current_user_id);
		$this->load->view('task_list_filters', $data);
	}
	function del_task_filter($id)
	{
		$current_user_id = get_staff_user_id();
		$this->si_task_filter_model->delete($id,$current_user_id);
		redirect('si_task_filters/list_filters');
	}
	function get_task_status($id)
    {
        if ($this->tasks_model->is_task_assignee(get_staff_user_id(), $id)
            || $this->tasks_model->is_task_creator(get_staff_user_id(), $id)
            || has_permission('tasks', '', 'edit')) {

            // Generate task Status dropdown
			$task = (array)$this->tasks_model->get($id);
			$task_statuses = $this->tasks_model->get_statuses();
			
            $taskHtml = '';
			$success = false;
			if(!empty($task)){
				$status          = get_task_status_by_id($task['status']);
				$taskHtml    = '';
			
				$taskHtml .= '<span class="inline-block label" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" task-status-table="' . $task['status'] . '">';
			
				$taskHtml .= $status['name'];
			
				$taskHtml .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
				$taskHtml .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskStatus-' . $task['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
				$taskHtml .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
				$taskHtml .= '</a>';
		
				$taskHtml .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-' . $task['id'] . '">';
				foreach ($task_statuses as $taskChangeStatus) {
					if ($task['status'] != $taskChangeStatus['id'] && $taskChangeStatus['id']!=Tasks_model::STATUS_TESTING) {
						$taskHtml .= '<li>
						  <a href="#" onclick="si_tasks_status_update(' . $taskChangeStatus['id'] . ',' . $task['id'] . '); return false;">
							 ' . _l('task_mark_as', $taskChangeStatus['name']) . '
						  </a>
					   </li>';
					}
				}
				$taskHtml .= '</ul>';
				$taskHtml .= '</div>';
			
				$taskHtml .= '</span>';
				$success = true;

           }
			echo json_encode([
				'success'  => $success,
				'taskHtml' => $taskHtml,
			]);
        
        } else {
            echo json_encode([
                'success'  => false,
                'taskHtml' => '',
            ]);
        }
    }
}
