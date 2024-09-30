<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Projectroadmap extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
        $this->load->model('projectroadmap_model');

    }

    public function index(){
        $this->load->model('staff_model');
        $data['circle_progress_asset'] = true;
        $data['status'] = $this->projects_model->get_project_statuses();
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('projectroadmap', 'table'));
        }
        $this->app_scripts->add('circle-progress-js','assets/plugins/jquery-circle-progress/circle-progress.min.js');
        $data['title'] = _l('projectroadmap');
        $this->load->view('projectroadmap', $data);
    }

    public function projectroadmap_table(){
        $this->app->get_table_data('projectroadmap');
    }

    public function view_projectroadmap($id = ''){
	
		\modules\projectroadmap\core\Apiinit::ease_of_mind('projectroadmap');
		\modules\projectroadmap\core\Apiinit::the_da_vinci_code('projectroadmap');
        if($id != ''){
            $this->load->model('dashboard_model');
            $data['project'] = $this->projects_model->get($id);
            $data['currency'] = $this->projects_model->get_currency($id);
            $percent           = $this->projects_model->calc_progress($id);
            $percent_circle        = $percent / 100;
            $data['percent_circle'] = $percent_circle;
            $data['project_task'] = $this->projects_model->get_tasks($id,array(),false, false);
            $this->app_scripts->add('circle-progress-js','assets/plugins/jquery-circle-progress/circle-progress.min.js');
            $data['project_total_logged_time'] = $this->projects_model->total_logged_time($id);
            $data['milestones'] = $this->projects_model->get_milestones($id);
            $data['milestones_status_stats'] = json_encode($this->projectroadmap_model->milestones_status_stats($id));
            $data['tasks_status_stats'] = json_encode($this->projectroadmap_model->tasks_status_stats($id));
            $data['tasks_priority_stats'] = json_encode($this->projectroadmap_model->tasks_priority_stats($id));
            $data['project_hour_stats'] = json_encode($this->projectroadmap_model->project_hour_stats($id));
            
            $data['members'] = $this->projectroadmap_model->get_project_members($id);
            $data['project_status'] = get_project_status_by_id($data['project']->status);
            $data['title']          = $data['project']->name;
            $this->load->view('view_projectroadmap', $data);
        }else{
                $this->index();
        }
    }

    public function add_projectroadmap_filter_widget($id = ''){
        if($id != ''){
            $data['rel_id'] = $id;
            $data['rel_type'] = 'projectroadmap';
            $data['add_from'] = get_staff_user_id();
            $success = $this->projectroadmap_model->add_projectroadmap_filter_widget($data);
        echo json_encode([
            'success' => $success,
            'message' => _l('added_successfully', _l('widget'))
        ]);
        die(); 
        }
    }

     public function remove_projectroadmap_filter_widget($id = ''){
        if($id != ''){
            $success = $this->projectroadmap_model->remove_projectroadmap_filter_widget($id);
            echo json_encode([
            'success' => $success,
            'message' => _l('deleted', _l('widget'))
        ]);
        die(); 
        }
    } 
    

    public function projectroadmap_tasks($project_id)
    {
        if ($this->input->is_ajax_request()) {

            $select = [
                'name',
                '(SELECT tblmilestones.name from tblmilestones where tblmilestones.id = ' . db_prefix() . 'tasks.milestone) as milestone_name',
                get_sql_select_task_asignees_full_names() . ' as assignees',
                'startdate',
                'duedate',
                'status',
            ];
            $where = [];
            array_push($where, 'AND rel_type="project" AND rel_id = '.$project_id);
			\modules\projectroadmap\core\Apiinit::ease_of_mind('projectroadmap');
			\modules\projectroadmap\core\Apiinit::the_da_vinci_code('projectroadmap');
            if ($this->input->post('milestones')) {
                $milestones  = $this->input->post('milestones');
                if(is_array($milestones)){
                $_where = '';
                foreach ($milestones as $value) {
                    if($_where == ''){
                        $_where .= 'AND (';
                    }else{
                        $_where .= ' or ';
                    }
                    $_where .= 'milestone = '.$value;
                }
                 if($_where != ''){
                        $_where .= ')';
                    }
                array_push($where, $_where);
                }else{
                    array_push($where, 'AND milestone = '.$milestones);
                }
            }

           if ($this->input->post('task_status')) {
                $task_status  = $this->input->post('task_status');
                $_where = '';
                foreach ($task_status as $value) {
                    if($_where == ''){
                        $_where .= 'AND (';
                    }else{
                        $_where .= ' or ';
                    }
                    if($value == 1){
                        $_where .= '(status = 1 and duedate > "'.date('Y-m-d').'")';
                    }elseif ($value == 2) {
                        $_where .= '(status = 1 and duedate < "'.date('Y-m-d').'")';
                    }elseif ($value == 3) {
                        $_where .= '(status in (2,3,4) and duedate > "'.date('Y-m-d').'")';
                    }elseif ($value == 4) {
                        $_where .= '(status in (2,3,4) and duedate < "'.date('Y-m-d').'")';
                    }elseif ($value == 5) {
                        $_where .= '(status = 5 and duedate > datefinished)';
                    }elseif ($value == 6) {
                        $_where .= '(status = 5 and duedate < datefinished)';
                    }
                }
                 if($_where != ''){
                        $_where .= ')';
                    }
                array_push($where, $_where);
            }
            if ($this->input->post('members')) {
                $members  = $this->input->post('members');
                if(is_array($members)){
                $_where = '';
                foreach ($members as $value) {
                    if($_where == ''){
                        $_where .= 'AND (';
                    }else{
                        $_where .= ' or ';
                    }
                    $_where .= $value .' IN (SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id ORDER BY ' . db_prefix() . 'task_assigned.staffid)';
                }
                 if($_where != ''){
                        $_where .= ')';
                    }
                array_push($where, $_where);
                }else{
                    array_push($where, 'AND '.$members.' IN (SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id ORDER BY ' . db_prefix() . 'task_assigned.staffid)');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = 'tbltasks';
            $join         = [];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                '' . db_prefix() . 'tasks.id',
                'datefinished',
        'rel_type',
        'rel_id',
        'recurring',
        tasks_rel_name_select_query() . ' as rel_name',
        'billed',
        '(SELECT staffid FROM ' . db_prefix() . 'task_assigned  WHERE taskid=' . db_prefix() . 'tasks.id AND staffid=' . get_staff_user_id() . ') as is_assigned',
        get_sql_select_task_assignees_ids() . ' as assignees_ids',
        '(SELECT MAX(id) FROM tbltaskstimers WHERE task_id= ' . db_prefix() . 'tasks.id and staff_id=' . get_staff_user_id() . ' and end_time IS NULL) as not_finished_timer_by_current_staff',
        '(SELECT CASE WHEN addedfrom=' . get_staff_user_id() . ' AND is_added_from_contact=0 THEN 1 ELSE 0 END) as current_user_is_creator',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
            $row = [];
            $outputName = '';

            if ($aRow['not_finished_timer_by_current_staff']) {
                $outputName .= '<span class="pull-left text-danger"><i class="fa fa-clock-o fa-fw"></i></span>';
            }

            $outputName .= '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" class="display-block main-tasks-table-href-name' . (!empty($aRow['rel_id']) ? ' mbot5' : '') . '" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['name'] . '</a>';
            $outputName .= '<div class="row-options">';
            $outputName .= '</div>';

            $row[] = $outputName;

            $row[] = $aRow['milestone_name'];

            $row[] = format_members_by_ids_and_names($aRow['assignees_ids'], $aRow['assignees']);
            

            $row[] = _dt($aRow['startdate']);

            $row[] = _dt($aRow['duedate']);
            $sum = 0;
            $time = $this->tasks_model->get_timesheeets($aRow['id']);
            
            foreach($time as $tm){
                if($tm['time_spent'] == NULL){

                   $str = sec2qty(time() - $tm['start_time']);
                  } else {
                   
                   $str =  sec2qty($tm['time_spent']);
                  }
                $sum += $str;
            }

            $row[] = _format_number($sum);
        
            $status          = get_task_status_by_id($aRow['status']);
            $outputStatus    = '';

            $status_color = $status['color'];
            $status_name = $status['name'];
            if(($aRow['status'] != 5 && $aRow['duedate'] < date('Y-m-d')) || ($aRow['status'] === 5 && $aRow['duedate'] < $aRow['datefinished'])){
                $status_name .= '<span class="text-danger">('._l('late').')</span>';
            }
            $outputStatus .= '<span class="inline-block label" style="color:' . $status_color . ';border:1px solid ' . $status_color . '" task-status-table="' . $aRow['status'] . '">';

            $outputStatus .= $status_name;
            

        $outputStatus .= '</span>';

        $row[] = $outputStatus;

        $row = hooks()->apply_filters('tasks_table_row_data', $row, $aRow);

        $row['DT_RowClass'] = 'has-row-options';
        if ((!empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['status'] != 5) {
            $row['DT_RowClass'] .= ' text-danger';
        }

        $output['aaData'][] = $row;
    }
            echo json_encode($output);
            die();
        }
    }
}