<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Projectroadmap_model extends App_Model
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
    }

    public function get_list_children_by_staffid($staffid = 0, $list, $staff, $onlyid = false)
    {
        $new_list = [];
        foreach ($staff as $key => $item)
        {
            if ($item['team_manage'] == $staffid)
            {
                $new_list[] = $item;

                unset($staff[$key]);

                $list_n = $this->get_list_children_by_staffid($item['staffid'], $new_list, $staff);
               
                foreach($list_n as $li)
                {
                    if(!in_array($li, $list) && is_array($li)){
                        if($onlyid === false){
                            $list[] =  $li;
                        }else{
                            $list[] =  $li['staffid'];
                        }
                    }
                }
            }
        }
        return $list;
    }
    public function remove_projectroadmap_filter_widget($id){
        $this->db->where('id', $id);
        $this->db->delete('tbllist_widget');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function milestones_status_stats($id)
    {
        
        $milestones = $this->projects_model->get_milestones($id);

        $chart = [];
        $status_1 = ['name' => _l('not_started'), 'color' => '#777', 'y' => 0, 'z' => 92.9];
        $status_2 = ['name' => _l('not_started(late)'), 'color' => '#fc2d42', 'y' => 0, 'z' => 124.6];
        $status_3 = ['name' => _l('in_process'), 'color' => '#03a9f4', 'y' => 0, 'z' => 137.5];
        $status_4 = ['name' => _l('in_process(late)'), 'color' => '#ff6f00', 'y' => 0, 'z' => 201.8];
        $status_5 = ['name' => _l('complete'), 'color' => '#84c529', 'y' => 0, 'z' => 214.5];
        $status_6 = ['name' => _l('complete(late)'), 'color' => '#FDEC6D', 'y' => 0, 'z' => 235.6];
        foreach ($milestones as $milestone) {
          $where = 'milestone = '.$milestone['id'];
          $task = $this->projects_model->get_tasks($id,$where,false, false);
          $task_finish = $this->projects_model->get_tasks($id,'status = 5 and '.$where,false, true);
          $milestone_status = 1;
          $late = 0;
          foreach ($task as $key => $value) {
            if($value['status'] != 5 && $value['status'] != 1)
            {
              $milestone_status = 3;
            }
            if($value['duedate'] < date('Y-m-d') && $value['status'] !== 5){
              $late = 1;
            }
          }
          if(count($task) == $task_finish)
          {
            $milestone_status = 5;
          }
          if($milestone_status == 1){
            if($late == 0){
                $status_1['y'] += 1;
            }else{
                $status_2['y'] += 1;
            }
          }elseif ($milestone_status == 3) {
              if($late == 0){
                $status_3['y'] += 1;
            }else{
                $status_4['y'] += 1;
            }
          }elseif ($milestone_status == 5) {
            if($late == 0){
                $status_5['y'] += 1;
            }else{
                $status_6['y'] += 1;
            }
          }
        }
        if($status_1['y'] > 0){
            array_push($chart, $status_1);
        }
        if($status_2['y'] > 0){
            array_push($chart, $status_2);
        }
        if($status_3['y'] > 0){
            array_push($chart, $status_3);
        }
        if($status_4['y'] > 0){
            array_push($chart, $status_4);
        }
        if($status_5['y'] > 0){
            array_push($chart, $status_5);
        }
        if($status_6['y'] > 0){
            array_push($chart, $status_6);
        }
       
        return $chart;
    }

    public function tasks_status_stats($id)
    {
        $this->load->model('projects_model');
        $tasks = $this->projects_model->get_tasks($id,array(),false, false);

        $chart = [];

        $status_1 = ['name' => _l('not_started'), 'color' => '#777', 'y' => 0];
        $status_2 = ['name' => _l('not_started(late)'), 'color' => '#fc2d42', 'y' => 0];
        $status_3 = ['name' => _l('in_process'), 'color' => '#03a9f4', 'y' => 0];
        $status_4 = ['name' => _l('in_process(late)'), 'color' => '#ff6f00', 'y' => 0];
        $status_5 = ['name' => _l('complete'), 'color' => '#84c529', 'y' => 0];
        $status_6 = ['name' => _l('complete(late)'), 'color' => '#FDEC6D', 'y' => 0];
        foreach ($tasks as $task) {
            if($task['status'] == 1){
                if($task['duedate'] > date('Y-m-d')){
                    $status_1['y'] += 1;
                }else{
                    $status_2['y'] += 1;
                }
            }elseif ($task['status'] != 3 && $task['status'] != 5) {
                if($task['duedate'] > date('Y-m-d')){
                    $status_3['y'] += 1;
                }else{
                    $status_4['y'] += 1;
                }
            }elseif ($task['status'] == 5) {
                if($task['duedate'] >= $task['datefinished']){
                    $status_5['y'] += 1;
                }else{
                    $status_6['y'] += 1;
                }
            }
        }
        if($status_1['y'] > 0){
            array_push($chart, $status_1);
        }
        if($status_2['y'] > 0){
            array_push($chart, $status_2);
        }
        if($status_3['y'] > 0){
            array_push($chart, $status_3);
        }
        if($status_4['y'] > 0){
            array_push($chart, $status_4);
        }
        if($status_5['y'] > 0){
            array_push($chart, $status_5);
        }
        if($status_6['y'] > 0){
            array_push($chart, $status_6);
        }

        return $chart;
    }

    public function tasks_priority_stats($id)
    {
        $this->load->model('projects_model');
        $tasks = $this->projects_model->get_tasks($id,array(),false, false);

        $chart = [];

        $status_1 = ['name' => _l('task_priority_low'), 'color' => '#777', 'data' => 0];
        $status_2 = ['name' => _l('task_priority_medium'), 'color' => '#fc2d42', 'data' => 0];
        $status_3 = ['name' => _l('task_priority_high'), 'color' => '#03a9f4', 'data' => 0];
        $status_4 = ['name' => _l('task_priority_urgent'), 'color' => '#ff6f00', 'data' => 0];
        foreach ($tasks as $task) {
            if($task['priority'] == 1){
                $status_1['data'] += 1;
            }elseif ($task['priority'] == 2) {
                $status_2['data'] += 1;
            }elseif ($task['priority'] == 3) {
                $status_3['data'] += 1;
            }elseif ($task['priority'] == 4) {
                $status_4['data'] += 1;
            }
            
        }
        if($status_1['data'] > 0){
            $status_1['data'] = [$status_1['data']];
            array_push($chart, $status_1);
        }
        if($status_2['data'] > 0){
            $status_2['data'] = [$status_2['data']];
            array_push($chart, $status_2);
        }
        if($status_3['data'] > 0){
            $status_3['data'] = [$status_3['data']];
            array_push($chart, $status_3);
        }
        if($status_4['data'] > 0){
            $status_4['data'] = [$status_4['data']];
            array_push($chart, $status_4);
        }
        
        return $chart;
    }

    public function project_hour_stats($id)
    {
        $this->load->model('projects_model');
        $tasks = $this->projects_model->get_tasks($id,array(),false, false);
        $chart = [];

        $status_1 = ['name' => _l('estimate_hour'), 'data' => 0];
        $status_2 = ['name' => _l('task_total_logged_time'), 'data' => 0];
        $estimate_hour = 0;
        $sum = 0;
        $estimate_hour = 0;
        $this->db->select('estimated_hours');
        $this->db->where('id',$id);
        $est =  $this->db->get(db_prefix() . 'projects')->row();
        if($est){
            $estimate_hour = $est->estimated_hours;
        }
        foreach ($tasks as $task) {
            $time = $this->tasks_model->get_timesheeets($task['id']);
            
            foreach($time as $tm){
                if($tm['time_spent'] == NULL){

                   $str = sec2qty(time() - $tm['start_time']);
                  } else {
                   
                   $str =  sec2qty($tm['time_spent']);
                  }
                $sum += $str;
            }
        }
            $status_1['data'] = [(int)$estimate_hour];
            array_push($chart, $status_1);

            $status_2['data'] = [$sum];
            array_push($chart, $status_2);
        
        return $chart;
    }

    public function get_project_members($id)
    {
        $this->db->select('email,project_id,staff_id, CONCAT(firstname, \' \', lastname) as full_name');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'project_members.staff_id');
        $this->db->where('project_id', $id);

        return $this->db->get(db_prefix() . 'project_members')->result_array();
    }

    public function get_filter_widget($staff, $type = ''){
        return $this->db->query('select * from tbllist_widget where add_from = '.$staff.' and rel_type = "'.$type.'"')->result_array();
    }

    public function add_projectroadmap_filter_widget($data){
        $this->db->insert('tbllist_widget', $data);
        $filter_id = $this->db->insert_id();
        return $filter_id;
    }

    public function view_projectroadmap_helper($id){
    $this->load->model('projects_model');
    $this->load->model('reports_model');
    $data['project'] = $this->projects_model->get($id);
    $data['currency'] = $this->projects_model->get_currency($id);
    $percent           = $this->projects_model->calc_progress($id);
    $percent_circle        = $percent / 100;
    $data['percent_circle'] = $percent_circle;
    $data['project_task'] = $this->projects_model->get_tasks($id,array(),false, false);
    $data['circle_progress_asset'] = true;
    $data['project_total_logged_time'] = $this->projects_model->total_logged_time($id);
    $data['milestones'] = $this->projects_model->get_milestones($id);
    $data['milestones_status_stats'] = json_encode($this->milestones_status_stats($id));
    $data['tasks_status_stats'] = json_encode($this->tasks_status_stats($id));
    $data['tasks_priority_stats'] = json_encode($this->tasks_priority_stats($id));
    $data['project_hour_stats'] = json_encode($this->project_hour_stats($id));
    
    $data['members'] = $this->get_project_members($id);
    $data['project_status'] = get_project_status_by_id($data['project']->status);
    
    return $data;
}
}