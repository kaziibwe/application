<?php

use Carbon\Carbon;

defined('BASEPATH') or exit('No direct script access allowed');

class Flexiblewa extends AdminController
{
    public function index(){
        $this->load->model('flexiblewa/flexibleworkflow_model');
        $data['title'] = _l('flexiblewa');
        $data['rules'] = $this->flexibleworkflow_model->all();
        $data['statuses'] = flexiblewa_get_task_statuses();

        foreach($data['rules'] as &$rule){
            $rule['display_value'] = flexiblewa_get_display_value($rule['rule_id'], $rule['rule_value']);
        }
        
        $this->app_css->add('flexiblewa-tree-css', module_dir_url('flexiblewa', 'assets/css/flexiblewa.css'), 'admin', ['app-css']);
        $this->app_scripts->add('flexiblewa-js', module_dir_url('flexiblewa', 'assets/js/flexiblewa.js'), 'admin', ['app-js']);
        $this->load->view('index', $data);
    }

    public function rule(){
        $post = $this->input->post();
        //print_r($post);die();
        if($post){
            $this->load->model('flexiblewa/flexibleworkflow_model');
            $post = array_merge($post, [
                'section_name' => flexiblewa_get_task_status_name($post['section_id']),
                'rule_name' => flexiblewa_get_rule_name($post['rule_id']),
                'user_id' => get_staff_user_id(),
                'date_created' => to_sql_date(Carbon::now()->toDateTimeString(), true)
            ]);
            //print_r($post['rule_id']);
            switch ($post['rule_id']) {
                case FLEXIBLEWA_SET_ASSIGNED_TO_ACTION:
                    $post = array_merge($post, [
                        'rule_value' => implode(',', $post['assignees'])
                    ]);
                    unset($post['assignees']);

                    break;
                case FLEXIBLEWA_SET_DUE_DATE_TO_ACTION:
                    $post = array_merge($post, [
                        'rule_value' => '+ ' . $post['time_count'] . ' ' . $post['period']
                    ]);
                    unset($post['time_count']);
                    unset($post['period']);
                    
                    break;
                case FLEXIBLEWA_SET_PRIORITY_TO_ACTION:
                    $post = array_merge($post, [
                        'rule_value' => $post['priority']
                    ]);
                    unset($post['priority']);
                    
                    break;
                case FLEXIBLEWA_ADD_NEW_CHECKLIST_ITEM_ACTION:
                    $post = array_merge($post, [
                        'rule_value' => $post['checklist']
                    ]);
                    unset($post['checklist']);
                    
                    break;
                case FLEXIBLEWA_ADD_NEW_REMINDER_ACTION:
                    $reminder = '+ ' . $post['time_count'] . ' ' . $post['period'];
                    $reminder_data = [
                        $reminder,
                        $post['reminder_user_id']
                    ];

                    $post = array_merge($post, [
                        'rule_value' => implode(',', $reminder_data)
                    ]);
                    unset($post['time_count']);
                    unset($post['period']);
                    unset($post['reminder_user_id']);
                    
                    break;
                case FLEXIBLEWA_ADD_NEW_COMMENT_ACTION:
                    $post = array_merge($post, [
                        'rule_value' => $post['comment']
                    ]);
                    unset($post['comment']);
                    
                    break;
                case FLEXIBLEWA_ADD_NEW_FOLLOWER_ACTION:
                    $post = array_merge($post, [
                        'rule_value' => implode(',', $post['followers'])
                    ]);
                    unset($post['followers']);

                    break;
                
                case FLEXIBLEWA_ADD_NEW_FILE_ACTION:
                    $uploaded_file_path = flexiblewa_upload_file('file');

                    if(!$uploaded_file_path){
                        set_alert('danger', _l('flexiblewa_adding_rule_failed'));
                        redirect(admin_url('flexiblewa')); 
                    }

                    $post = array_merge($post, [
                        'rule_value' => $uploaded_file_path
                    ]);
                    unset($post['file']);

                    break;

                case FLEXIBLEWA_MOVE_TO_ANOTHER_RELATION_ACTION:
                    
                    $post = array_merge($post, [
                        'rule_value' => implode(',', [
                            $post['rel_type'],
                            $post['relation_id']
                        ])
                    ]);
                    unset($post['rel_type']);
                    unset($post['relation_id']);
                    break;

                case FLEXIBLEWA_MOVE_TO_SECTION_ACTION:
                    if($post['section_id'] == $post['move_to_section']){
                        set_alert('danger', _l('flexiblewa_move_to_different_section'));
                        redirect(admin_url('flexiblewa')); 
                    }
                    
                    $post = array_merge($post, [
                        'rule_value' => $post['move_to_section']
                    ]);
                    unset($post['move_to_section']);

                    break;

                    case FLEXIBLEWA_MARK_AS_COMPLETE_ACTION:
                        $this->load->model('Tasks_model');
                        $post = array_merge($post, [
                            'rule_value' => $this->tasks_model::STATUS_COMPLETE
                        ]);
                        break;
                
                default:
                    # code...
                    break;
            }

            $conditions = [
                'section_id' => $post['section_id'],
                'rule_id' => $post['rule_id'],
                'user_id' => $post['user_id']
            ];

            // If the section-action combo exists for a user
            $workflow = $this->flexibleworkflow_model->get($conditions);
            if($workflow){
                // Update the record
                $saved = $this->flexibleworkflow_model->update($workflow['id'], $post);
            }else{
                $saved = $this->flexibleworkflow_model->add($post);
            }
            
            if($saved){
                set_alert('success', _l('flexiblewa_rule_added_successfully'));
                redirect(admin_url('flexiblewa'));
            }
        }
    }

    public function delete_rule($rule_id = ''){
        if(!$rule_id){
            set_alert('danger', _l('flexiblewa_rule_not_found'));
        }else{
            $this->load->model('flexiblewa/flexibleworkflow_model');
            $deleted = $this->flexibleworkflow_model->delete([
                'id' => $rule_id
            ]);

            if($deleted){
                set_alert('success', _l('flexiblewa_rule_deleted_successfully'));
            }
        }

        redirect(admin_url('flexiblewa'));
    }

    public function ajax()
    {
        $action = $this->input->get('action') ? $this->input->get('action') : $this->input->post('action');

        $result = [
            'success' => false,
            'data' => []
        ];
        switch ($action) {
            case 'get_list_of_actions_for_section':
                $section_id = $this->input->get('id');
                $conditions = [
                    'section_id' => $section_id
                ];
                $this->load->model('flexiblewa/flexibleworkflow_model');
                $actions = $this->flexibleworkflow_model->all($conditions);
                $result['html'] = $this->load->view('partials/list-of-actions',['actions'=>$actions],true);
                $result['success'] = true;
                break;

            case 'relations':
                $rel_type = $this->input->get('rel_type');
                $result['data']['relations'] = flexiblewa_get_relations($rel_type);
                $result['success'] = true;
                break;
            case 'assign_to_staff':
            case 'add_new_follower':
                $result['data']['members'] = flexiblewa_get_staff_members();
                $result['success'] = true;
                break;

            case 'move_to_section':
                    $status_id = $this->input->get('status_id');
                    $result['data']['statuses'] = flexiblewa_get_task_statuses($status_id);
                    $result['success'] = true;
                    break;

            case 'update_actions_order':
                $this->load->model('flexiblewa/flexibleworkflow_model');
                $actions = $this->input->post("actions");
                $result['success'] = $this->flexibleworkflow_model->update_actions_order($actions);
                break;

            case 'get_mentions':
                $members = flexiblewa_get_staff_members();
                $members = array_map(function ($member) {
                    $_member['id'] = $member['staffid'];
                    $_member['name'] = $member['firstname'] . ' ' . $member['lastname'];
                    return $_member;
                }, $members);
                $result = $members;
                break;

        }
        header('Content-Type: application/json');
        echo json_encode( $result );
    }

}