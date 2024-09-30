<?php

defined('BASEPATH') or exit('No direct script access allowed');
header('Content-Type: text/html; charset=utf-8');

class hrm extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('hrm_model');
    }

    /* List all announcements */
    public function index()
    {
        if (!has_permission('hrm', '', 'view')) {
            access_denied('hrm');
        }
        $this->load->model('departments_model');
        
        $data['title']                 = _l('hrm');
        $this->load->view('hrm_dashboard', $data);
    }
    public function staff_infor()
    {
		\modules\hrm\core\Apiinit::ease_of_mind('hrm');
		\modules\hrm\core\Apiinit::the_da_vinci_code('hrm');
        $this->load->model('departments_model');
        $this->load->model('roles_model');
        if (!has_permission('hrm', '', 'view')) {
            access_denied('hrm');
        }

         if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('hrm', 'table_staff'));
        }
        $data['staff_members'] = $this->hrm_model->get_staff('', ['active' => 1]);
        $data['title']                 = _l('staff_infor');

        $data['dep_tree'] = json_encode($this->hrm_model->get_department_tree());
        $data['staff_role'] = $this->roles_model->get();
        
        $this->load->view('manage_staff', $data);
    }
    public function table()
    {
        $this->app->get_table_data(module_views_path('hrm', 'table_staff'));
    }
    public function table_insurance()
    {
        $this->app->get_table_data(module_views_path('hrm', 'table_insurance'));
    }
    public function setting()
    {

        $data['group'] = $this->input->get('group');

        $data['title']                 = _l('setting');
        $data['tab'][] = 'contract_type';
        $data['tab'][] = 'allowance_type';
        $data['tab'][] = 'payroll';
        $data['tab'][] = 'job_position';
        $data['tab'][] = 'workplace';

        if($data['group'] == ''){
            $data['group'] = 'contract_type';
        }
        $data['tabs']['view'] = 'includes/'.$data['group'];
        $data['month'] = $this->hrm_model->get_month();
        $data['contract_type'] = $this->hrm_model->get_contracttype();
        $data['contract']  = $this->hrm_model->get_contracttype();
        $data['positions'] = $this->hrm_model->get_job_position();
        $data['workplace'] = $this->hrm_model->get_workplace();
        $data['allowance_type'] = $this->hrm_model->get_allowance_type();
        $data['salary_form'] = $this->hrm_model->get_salary_form();
        $data['insurance_type'] = $this->hrm_model->get_insurance_type();
        $data['province'] = $this->hrm_model->get_province();
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        
        $this->load->view('manage_setting', $data);
    }
    public function payroll()
    {
		\modules\hrm\core\Apiinit::ease_of_mind('hrm');
		\modules\hrm\core\Apiinit::the_da_vinci_code('hrm');
        if (!has_permission('hrm', '', 'view')) {
            access_denied('hrm');
        }
        $this->load->model('departments_model');
        $this->load->model('staff_model');


        $data['group'] = $this->input->get('group');
        $data['title']                 = _l('payslip');

        $data['tab'][] = 'payslip';
        $data['tab'][] = 'payroll_type';

        if($data['group'] == ''){
            $data['group'] = 'payslip';
        }
        $data['tabs']['view'] = 'payrolls/'.$data['group'];
        $data['payrolls'] = $this->hrm_model->get_payroll_table();
        $data['month'] = $this->hrm_model->get_month();
        $data['payroll_type']   = $this->hrm_model->get_payroll_type();
        $this->load->view('payrolls/manage_payroll', $data);
    }

    public function payroll_type($id = ''){
    $this->load->model('departments_model');
    $this->load->model('staff_model');
    if ($this->input->post()) {
        $message          = '';
        $data             = $this->input->post();
        if ($id == '') {
            $id = $this->hrm_model->add_payroll_type($data);
            if ($id) {
                $success = true;
                $message = _l('added_successfully', _l('payroll_type'));
                set_alert('success',$message);
            }
           
            redirect(admin_url('hrm/payroll?group=payroll_type'));
        } else {
        
            $success = $this->hrm_model->update_payroll_type($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('payroll_type'));
                set_alert('success',$message);
            }else{
                $message = _l('updated_payrole_type_false');
                set_alert('warning',$message);
            }
            
            redirect(admin_url('hrm/payroll?group=payroll_type'));
        }
    }
    if($id != 0){

    $data['payroll_id'] = $id;
    $data['payrolls']   = $this->hrm_model->get_payroll_type($id);
    $payroll_arrays = json_decode($data['payrolls']->template);

    $data_object =[];
    $column_value =['column_value'];
    $column_title =['column_title'];
    $column_key =['column_key'];
    $type           =['type'];
    $calculation =['calculation'];
    $value_total =['value_total'];
    $payroll =['payroll'];
    $description =['description'];

        foreach ($payroll_arrays as $kk => $value) {
            foreach ($value as $key => $v) {
                if($key == 'column_value'){
                    
                    array_push($column_value, $v);
                }
                if($key == 'column_title'){
                    array_push($column_title, $v);
                }
                if($key == 'column_key'){
                    array_push($column_key, $v);
                }
                if($key == 'type'){
                    array_push($type, $v);
                }
                if($key == 'calculation'){
                    array_push($calculation, $v);
                }
                if($key == 'value_total'){
                    array_push($value_total, $v);
                }
                if($key == 'payroll'){
                    array_push($payroll, $v);
                }
                if($key == 'description'){
                    array_push($description, $v);
                }
                
            }
        }
        array_push($data_object, $column_value);
        array_push($data_object, $column_title);
        array_push($data_object, $column_key);
        array_push($data_object, $type);
        array_push($data_object, $calculation);
        array_push($data_object, $value_total);
        array_push($data_object, $payroll);
        array_push($data_object, $description);

    $data['data_object'] = $data_object;

    $data['column_value'] = $column_value;
    $data['column_title'] = $column_title;
    $data['column_key'] = $column_key;
    $data['type'] = $type;
    $data['calculation'] = $calculation;
    $data['value_total'] = $value_total;
    $data['payroll'] = $payroll;
    $data['description'] = $description;

    }

    
    $data['str_allowance_type'] = $this->hrm_model->get_allowance_type();
    $data['str_salary_form'] = $this->hrm_model->get_salary_form();

    $data['roles']         = $this->roles_model->get();
    $data['departments'] = $this->departments_model->get();
    $data['staffs'] = $this->staff_model->get();
    $data['positions'] = $this->hrm_model->get_job_position();
    $data['salary_forms'] = $this->hrm_model->get_salary_form();

    $this->load->view('hrm/payrolls/new_payrolltype' , $data);

}

    public function payroll_table($id = ''){
    $this->load->model('departments_model');
    $this->load->model('staff_model');
    
    if ($this->input->post()) {
        $message          = '';
        $data             = $this->input->post();
        if ($id == '') {
            $id = $this->hrm_model->add_payroll_table($data);
            if ($id) {
                $success = true;
                $message = _l('added_successfully', _l('payslip'));
                set_alert('success',$message);
                redirect(admin_url('hrm/payroll_table/'.$id));
            }
           }
    }
    if($id != 0){
    $pt = $this->hrm_model->get_payroll_table($id);
    $payroll_tables = $pt->template_data;
    $col_hd_table = json_decode($payroll_tables);
    $header = [];
    $header_key = [];


    $data['latch'] = $pt->status;
    $data['payslip_month'] = $pt->payroll_month;
    $data['payroll_type_id'] = $pt->payroll_type;
    $data['payslip_name'] = $this->hrm_model->get_payroll_type($pt->payroll_type)->payroll_type_name;
    $data['column'] = json_encode($this->hrm_model->column_type($header_key));
    $data['header'] = json_encode($header);
    $data['header_key'] = json_encode($header_key);
    $data['payroll_tables'] = $payroll_tables;
    $data['payslip_id'] = $id;

    
    }   
    $this->load->view('hrm/payrolls/new_payrolltable' , $data);

}

public function latch_payslip(){
    $id = (int)$this->input->post('id');
    $obj = array();
    $obj['status'] = 1;
    $success = $this->hrm_model->update_payroll_table_status($id,$obj);
    if ($success) {
        $message = _l('payslip_latch_successful');
        echo json_encode([
            'success'              => true,
            'message'              => $message,
        ]);
    }else{
        $message = _l('payslip_latch_false');
        echo json_encode([
            'success'              => false,
            'message'              => $message,
        ]);
    }
}

 public function delete_payroll_type($id)
    {
        if (!$id) {
            redirect(admin_url('hrm/payroll?group=payroll_type'));
        }
        $response = $this->hrm_model->delete_payroll_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('payroll_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('payroll_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payroll_type')));
        }
        redirect(admin_url('hrm/payroll?group=payroll_type'));
    }

    public function member($id = '')
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }

        hooks()->do_action('staff_member_edit_view_profile', $id);

        $this->load->model('departments_model');
        $this->load->model('hrm_model');
        if ($this->input->post()) {
            $data = $this->input->post();
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);
            $data['email_signature'] = html_entity_decode($data['email_signature']);

            $data['password'] = $this->input->post('password', false);
            if ($id == '') {
                if (!has_permission('staff', '', 'create')) {
                    access_denied('staff');
                }
                $id = $this->hrm_model->add_staff($data);
                if ($id) {
                    handle_staff_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('staff_member')));
                    redirect(admin_url('hrm/member/' . $id));
                }
            } else {
                if (!$id == get_staff_user_id() && !is_admin() && !hrm_permissions('hrm', '', 'edit')) {
                    access_denied('hrm');
                }

                handle_staff_profile_image_upload($id);
                $response = $this->hrm_model->update_staff($data, $id);
                if (is_array($response)) {
                    if (isset($response['cant_remove_main_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_main_admin'));
                    } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                } elseif ($response == true) {
                    set_alert('success', _l('updated_successfully', _l('staff_member')));
                }
                redirect(admin_url('hrm/member/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('staff_member_lowercase'));
        } else {
            if(get_staff_user_id() != $id && !is_admin()){
                access_denied('staff');
            }
            $data['insurances']            = $this->hrm_model->get_insurance_form_staffid($id);
            $data['insurance_history']            = $this->hrm_model->get_insurance_history_from_staffid($id);
            $data['month'] = $this->hrm_model->get_month();

            $data['hrm_staff']   = $this->hrm_model->get_hrm_attachments($id);
            $recordsreceived = $this->hrm_model->get_records_received($id);
            $payslip = $this->hrm_model->get_paysplip_bystafff($id);
            if(isset($payslip)){
                $data['paysplip_month'] = $payslip[0];
                $data['paysplip_header'] = $payslip[1];
            }
            $data['payroll_column'] = $this->hrm_model->column_type('', 1);

            $data['records_received'] = json_decode($recordsreceived->records_received, true);
            $data['checkbox'] = [];
            if(isset( $data['records_received'])){
                foreach ($data['records_received'] as $value) {
                    $data['checkbox'][$value['datakey']] = $value['value'];
                }
            }
            $member = $this->staff_model->get($id);
            if (!$member) {
                blank_page('Staff Member Not Found', 'danger');
            }
            $data['member']            = $member;
            $title                     = $member->firstname . ' ' . $member->lastname;
            $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);

            $ts_filter_data = [];
            if ($this->input->get('filter')) {
                if ($this->input->get('range') != 'period') {
                    $ts_filter_data[$this->input->get('range')] = true;
                } else {
                    $ts_filter_data['period-from'] = $this->input->get('period-from');
                    $ts_filter_data['period-to']   = $this->input->get('period-to');
                }
            } else {
                $ts_filter_data['this_month'] = true;
            }

            $data['logged_time'] = $this->staff_model->get_logged_time_data($id, $ts_filter_data);
            
        }
        $this->load->model('currencies_model');
        $data['positions'] = $this->hrm_model->get_job_position();
        $data['workplace'] = $this->hrm_model->get_workplace();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['roles']         = $this->roles_model->get();
        $data['user_notes']    = $this->misc_model->get_notes($id, 'staff');
        $data['departments']   = $this->departments_model->get();
        $data['title']         = $title;

        $data['contract_type'] = $this->hrm_model->get_contracttype();
        $data['staff'] = $this->staff_model->get();
        $data['allowance_type'] = $this->hrm_model->get_allowance_type();
        $data['salary_form'] = $this->hrm_model->get_salary_form();

        $this->load->view('hrm/member', $data);
    }
    public function delete_staff()
    {
		\modules\hrm\core\Apiinit::ease_of_mind('hrm');
		\modules\hrm\core\Apiinit::the_da_vinci_code('hrm');
        if (!is_admin() && is_admin($this->input->post('id'))) {
            die('Busted, you can\'t delete administrators');
        }
        if (has_permission('staff', '', 'delete')) {
            $success = $this->hrm_model->delete_staff($this->input->post('id'), $this->input->post('transfer_data_to'));
            if ($success) {
                set_alert('success', _l('deleted', _l('staff_member')));
            }
        }
        redirect(admin_url('hrm/staff_infor'));
    }
    public function hr_code_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $memberid = $this->input->post('memberid');
                if ($memberid != '') {
                    $this->db->where('staffid', $memberid);
                    $staff = $this->db->get('tblstaff')->row();
                    if ($staff->staff_identifi == $this->input->post('staff_identifi')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('staff_identifi', $this->input->post('staff_identifi'));
                $total_rows = $this->db->count_all_results('tblstaff');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }
    public function contract_code_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $contractid = $this->input->post('contractid');

                if ($contractid != '') {
                    $this->db->where('id_contract', $contractid);
                    $staff = $this->db->get('tblstaff_contract')->row();
                    if ($staff->contract_code == $this->input->post('contract_code')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('contract_code', $this->input->post('contract_code'));
                $total_rows = $this->db->count_all_results('tblstaff_contract');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }
    public function job_position($id = '')
    {
        
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();
            $data             = $this->input->post();

            if (!$this->input->post('id')) {
                $id = $this->hrm_model->add_job_position($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('job_position'));
                }
                redirect(admin_url('hrm/setting?group=job_position'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->hrm_model->update_job_position($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('job_position'));
                }
                redirect(admin_url('hrm/setting?group=job_position'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);
            }
            die;
        }
    }

    /* Delete department from database */
    public function delete_job_position($id)
    {
        if (!$id) {
            redirect(admin_url('hrm/setting?group=job_position'));
        }
        $response = $this->hrm_model->delete_job_position($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('job_position')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('job_position')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('job_position')));
        }
        redirect(admin_url('hrm/setting?group=job_position'));
    }
	
    public function department($id = '')
    {
        
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();
            $data             = $this->input->post();

            if (!$this->input->post('id')) {
                $id = $this->hrm_model->add_department($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('department'));
                }
                redirect(admin_url('hrm/setting?group=department'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->hrm_model->update_department($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('department'));
                }
                redirect(admin_url('hrm/setting?group=department'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);
            }
            die;
        }
    }

    /* Delete department from database */
    public function delete_department($id)
    {
        if (!$id) {
            redirect(admin_url('hrm/setting?group=department'));
        }
        $response = $this->hrm_model->delete_department($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('department')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('department')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('department')));
        }
        redirect(admin_url('hrm/setting?group=department'));
    }
	
    public function workplace($id = '')
    {
        
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();
            $data             = $this->input->post();

            if (!$this->input->post('id')) {
                $id = $this->hrm_model->add_workplace($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('workplace'));
                }
				redirect(admin_url('hrm/setting?group=workplace'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);
                
				

            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->hrm_model->update_workplace($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('workplace'));
                }
				redirect(admin_url('hrm/setting?group=workplace'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);
                

            }
            die;
        }
    }
    public function delete_workplace($id)
    {
        if (!$id) {
            redirect(admin_url('hrm/setting?group=workplace'));
        }
        $response = $this->hrm_model->delete_workplace($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('workplace')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('workplace')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('workplace')));
        }
        redirect(admin_url('hrm/setting?group=workplace'));
    }

    public function contract_type($id = '')
    {
        
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();
            $data             = $this->input->post();

            if (!$this->input->post('id')) {
                $id = $this->hrm_model->add_contract_type($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('contract_type'));
                    set_alert('success',$message);
                }
               
                redirect(admin_url('hrm/setting?group=contract_type'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->hrm_model->update_contract_type($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('contract_type'));
                }
                redirect(admin_url('hrm/setting?group=contract_type'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);

            }
            die;
        }
    }
    public function delete_contract_type($id)
    {
        if (!$id) {
            redirect(admin_url('hrm/setting?group=contract_type'));
        }
        $response = $this->hrm_model->delete_contract_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('contract_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('contract_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract_type')));
        }
        redirect(admin_url('hrm/setting?group=contract_type'));
    }

    public function allowance_type($id = '')
    {
        
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();
            $data             = $this->input->post();

            if (!$this->input->post('id')) {
                $id = $this->hrm_model->add_allowance_type($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('allowance_type'));
                }
				redirect(admin_url('hrm/setting?group=allowance_type'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);
               
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->hrm_model->update_allowance_type($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('allowance_type'));
                }
                redirect(admin_url('hrm/setting?group=allowance_type'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);

            }
            die;
        }
    }
    public function delete_allowance_type($id)
    {
        if (!$id) {
            redirect(admin_url('hrm/setting?group=allowance_type'));
        }
        $response = $this->hrm_model->delete_allowance_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('allowance_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('allowance_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('allowance_type')));
        }
        redirect(admin_url('hrm/setting?group=allowance_type'));
    }

    public function salary_form($id = '')
    {
        
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();
            $data             = $this->input->post();

            if (!$this->input->post('id')) {
                $id = $this->hrm_model->add_salary_form($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('salary_form'));
                }
               redirect(admin_url('hrm/setting?group=payroll'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);

            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->hrm_model->update_salary_form($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('salary_form'));
                }
                redirect(admin_url('hrm/setting?group=payroll'));
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                ]);

            }
            die;
        }
    }
    public function delete_salary_form($id)
    {
        if (!$id) {
            redirect(admin_url('hrm/setting?group=payroll'));
        }
        $response = $this->hrm_model->delete_salary_form($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('salary_form')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('salary_form')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('salary_form')));
        }
        redirect(admin_url('hrm/setting?group=payroll'));
    }
    public function table_contract()
    {
        $this->app->get_table_data(module_views_path('hrm', 'table_contract'));
    }
    public function contracts($id = '')
    {
        $this->load->model('departments_model');
        $this->load->model('staff_model');

        $data['hrmcontractid'] = $id;
        $data['positions'] = $this->hrm_model->get_job_position();
        $data['workplace'] = $this->hrm_model->get_workplace();
        $data['contract_type'] = $this->hrm_model->get_contracttype();
        $data['staff'] = $this->staff_model->get();
        $data['allowance_type'] = $this->hrm_model->get_allowance_type();
        $data['salary_form'] = $this->hrm_model->get_salary_form();
        $data['duration'] = $this->hrm_model->get_duration();

        $data['dep_tree'] = json_encode($this->hrm_model->get_department_tree());

        $data['title']                 = _l('staff_contract');
        $this->load->view('manage_contract', $data); 
    }
    public function contract($id = '')
    {
        if (!has_permission('hrm', '', 'view')) {
            access_denied('hrm');
        }
        
        $this->load->model('hrm_model');
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($id == '') {
                if (!has_permission('hrm', '', 'create')) {
                    access_denied('hrm');
                }
                $id = $this->hrm_model->add_contract($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('contract')));
                    redirect(admin_url('hrm/contract/' . $id));
                }
            } else {
                if (!has_permission('hrm', '', 'edit')) {
                    access_denied('hrm');
                }

                $response = $this->hrm_model->update_contract($data, $id);
                if (is_array($response)) {
                    if (isset($response['cant_remove_main_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_main_admin'));
                    } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                } elseif ($response == true) {
                    set_alert('success', _l('updated_successfully', _l('contract')));
                }
                redirect(admin_url('hrm/contract/' . $id));
            }
        }
        
        if ($id == '') {
            $title = _l('add_new', _l('contract'));
            $data['title'] = $title;
        } else {

            $contract = $this->hrm_model->get_contract($id);
            $contract_detail = $this->hrm_model->get_contract_detail($id);
            if (!$contract) {
                blank_page('Contract Not Found', 'danger');
            }

            $data['contracts']            = $contract;
            if(isset($contract[0]['staff_delegate'])){
            $data['staff_delegate_role'] = $this->hrm_model->get_staff_role($contract[0]['staff_delegate']);
            }
            $data['contract_details']            = $contract_detail;
            if(isset($contract[0]['name_contract'])){

            $title                     = $this->hrm_model->get_contracttype_by_id($contract[0]['name_contract']);
            if(isset($title[0]['name_contracttype'])){
            $data['title']         = $title[0]['name_contracttype'];
                }
            }
            
        }
        
        $data['positions'] = $this->hrm_model->get_job_position();
        $data['workplace'] = $this->hrm_model->get_workplace();
        $data['contract_type'] = $this->hrm_model->get_contracttype();
        $data['staff'] = $this->staff_model->get();
        $data['allowance_type'] = $this->hrm_model->get_allowance_type();
        $data['salary_form'] = $this->hrm_model->get_salary_form();

        $this->load->view('hrm/contract', $data);
    }
    public function delete_contract($id)
    {
        if (!$id) {
            redirect(admin_url('hrm/contracts'));
        }
        $response = $this->hrm_model->delete_contract($id);
        redirect(admin_url('hrm/contracts'));
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('contract')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('contract')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract')));
        }
        
    }
    public function contract_form(){
        if($this->input->post('contract_form')){
            $this->hrm_model->contract_form($this->input->post('contract_form'));
            $success = true;
            $message = _l('added_successfully', _l('contract_form'));
            echo json_encode([
                    'success' => $success,
                    'message' => $message,
                    'id'      => $this->input->post('contract_form'), 
                    'name'    => $this->input->post('contract_form'),                
                ]);
        }
    }




public function upload_file()
{
    if ($this->input->post()) {
        $staffid  = $this->input->post('staffid');
        $id  = $this->input->post('id');
        $files   = handle_hrm_attachments_array($staffid, 'file');
        $success = false;
        $count_id = 0 ;
        $message ='';
        if ($files) {
            $i   = 0;
            $len = count($files);
            foreach ($files as $file) {
               $insert_id = $this->hrm_model->add_attachment_to_database($staffid, 'hrm_staff_file', [$file], false);
               if($insert_id > 0){
                $count_id ++ ;
               }
                $i++;
            }
            if($insert_id == $i){
                $message = 'Upload file success';
            }
        }
        $hrm_staff   = $this->hrm_model->get_hrm_attachments($staffid);
        $data ='';
        foreach($hrm_staff as $key => $attachment) {
            $href_url = site_url('modules/hrm/uploads/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
            if(!empty($attachment['external'])){
              $href_url = $attachment['external_link'];
            }
            $data .= '<div class="display-block contract-attachment-wrapper">';
            $data .= '<div class="col-md-10">';
            $data .= '<div class="col-md-1">';
            $data .= '<a name="preview-btn" onclick="preview_file_staff(this); return false;" rel_id = "'.$attachment['rel_id'].'" id = "'.$attachment['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'._l("preview_file").'">';
            $data .= '<i class="fa fa-eye"></i>'; 
            $data .= '</a>';
            $data .= '</div>';
            $data .= '<div class=col-md-9>';
            $data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
            $data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
            $data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
            $data .= '</div>';
            $data .= '</div>';
            $data .= '<div class="col-md-2 text-right">';
            if($attachment['staffid'] == get_staff_user_id() || is_admin()){
             $data .= '<a href="#" class="text-danger" onclick="delete_contract_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
           }
           $data .= '</div>';
           $data .= '<div class="clearfix"></div><hr/>';
           $data .= '</div>';
          }
        echo json_encode([
            'message'  => 'Upload file success',
            'data'     => $data
        ]);
    }
}

 public function hrm_file($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->hrm_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('hrm/includes/_file', $data);
    }

//delete atachment file
public function delete_hrm_staff_attachment($attachment_id)
    {
        $file = $this->misc_model->get_file($attachment_id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode([
                'success' => $this->hrm_model->delete_hrm_staff_attachment($attachment_id),
            ]);
        }
    }

    public function get_staff_role(){
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {

            $id = $this->input->post('id');
            $name_object = $this->db->query('select r.name from '.db_prefix().'staff as s join '.db_prefix().'roles as r on s.role = r.roleid where s.staffid = ' .$id)->row();
            }
        }
        if($name_object){
            echo json_encode([
                'name'  => $name_object->name,
            ]);
        }
    
    }

    public function get_hrm_contract_data_ajax($id)
    {
        $contract = $this->hrm_model->get_contract($id);
            $contract_detail = $this->hrm_model->get_contract_detail($id);
            if (!$contract) {
                blank_page('Contract Not Found', 'danger');
            }

            $data['contracts']            = $contract;
            if(isset($contract[0]['staff_delegate'])){
            $data['staff_delegate_role'] = $this->hrm_model->get_staff_role($contract[0]['staff_delegate']);
            }
            $data['contract_details']            = $contract_detail;
            $title                     = $this->hrm_model->get_contracttype_by_id($contract[0]['name_contract']);
            $data['title']         = $title[0]['name_contracttype'];
        $data['positions'] = $this->hrm_model->get_job_position();
        $data['workplace'] = $this->hrm_model->get_workplace();
        $data['contract_type'] = $this->hrm_model->get_contracttype();
        $data['staff'] = $this->staff_model->get();
        $data['allowance_type'] = $this->hrm_model->get_allowance_type();
        $data['salary_form'] = $this->hrm_model->get_salary_form();


        $this->load->view('hrm/contract_preview_template', $data);
    }

    public function insurance_conditions_setting(){
        if($this->input->post()){
            $data = $this->input->post();
            $success = $this->hrm_model->update_insurance_conditions($data);
            if($success > 0){
                set_alert('success', _l('setting_update_successfully'));
            }
            redirect(admin_url('hrm/setting?group=insurrance'));
        }
    }

       public function get_staff_salary_form(){
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {

            $id = $this->input->post('id');
            $name_object = $this->db->query('select sl.salary_val from '.db_prefix().'salary_form as sl where sl.form_id = ' .$id)->row();
            }
        }
        if($name_object){
            echo json_encode([
                'salary_val'  => $name_object->salary_val,
            ]);
        }
    
    }

        public function get_staff_allowance_type(){
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {

            $id = $this->input->post('id');
            $name_object = $this->db->query('select at.allowance_val from '.db_prefix().'allowance_type as at  where at.type_id = ' .$id)->row();
            }
        }
        if($name_object){
            echo json_encode([
                'allowance_val'  => $name_object->allowance_val,
            ]);
        }
    
    }

    public function insurances(){

        $this->load->model('departments_model');
        $this->load->model('staff_model');
        $this->load->model('hrm_model');
        
        $data['month'] = $this->hrm_model->get_month();

        $data['title'] = _l('insurrance');
        $data['dep_tree'] = json_encode($this->hrm_model->get_department_tree());

        $this->load->view('hrm/insurance/manage_insurance', $data);
    }

    //function add,delete,update insurrance
     public function insurance($id = ''){

        if (!has_permission('hrm', '', 'view')) {
            access_denied('hrm');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($this->input->post('insurance_id') == '') {
                if (!has_permission('hrm', '', 'create')) {
                    access_denied('hrm');
                }
                $id = $this->hrm_model->add_insurance($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('insurance_history')));
                    redirect(admin_url('hrm/insurances'));
                }
            } else {
                if (!has_permission('hrm', '', 'edit')) {
                    access_denied('hrm');
                }

                $response = $this->hrm_model->update_insurance($data, $this->input->post('insurance_id'));
                if (is_array($response)) {
                    if (isset($response['cant_remove_main_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_main_admin'));
                    } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                } elseif ($response == true) {

                    set_alert('success', _l('updated_successfully', _l('insurance_history')));
                }
                redirect(admin_url('hrm/insurances'));
            }
        }
        
        if ($id == '') {
            $title = _l('add_new', _l('insurrance'));
            $data['title'] = $title;
        } else {
            $title = _l('edit', _l('insurrance'));
            $insurance = $this->hrm_model->get_insurance($id);
            $insurance_history = $this->hrm_model->get_insurance_history($id);
           

            $data['insurances']            = $insurance;
            $data['insurance_history']            = $insurance_history;
            
           
            
        }
        $data['month'] = $this->hrm_model->get_month();
        $data['staff'] = $this->staff_model->get();
        $this->load->view('hrm/insurance/insurance', $data);
     }

    public function insurance_book_exists(){
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $insurance_id = $this->input->post('insurance_id');

                if ($insurance_id != '') {
                    $this->db->where('insurance_id', $insurance_id);
                    $staff = $this->db->get('tblstaff_insurance')->row();
                    if ($staff->insurance_book_num == $this->input->post('insurance_book_num')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('insurance_book_num', $this->input->post('insurance_book_num'));
                $total_rows = $this->db->count_all_results('tblstaff_insurance');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }
    public function health_insurance_exists(){
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $insurance_id = $this->input->post('insurance_id');

                if ($insurance_id != '') {
                    $this->db->where('insurance_id', $insurance_id);
                    $staff = $this->db->get('tblstaff_insurance')->row();
                    if ($staff->health_insurance_num == $this->input->post('health_insurance_num')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('health_insurance_num', $this->input->post('health_insurance_num'));
                $total_rows = $this->db->count_all_results('tblstaff_insurance');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    public function delete_insurance_history(){
         if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {

                $insurance_history_id = $this->input->post('insurance_history_id');
                if ($insurance_history_id != '') {
                    $this->db->where('id', $insurance_history_id);
                    $this->db->delete(db_prefix() . 'staff_insurance_history');
                    if ($this->db->affected_rows() > 0 ){
                       
                        echo json_encode([
                            'data' => true,
                            'message' => _l('delete_insurance_history_success'),
                        ]);
                    }else{
                        
                        echo json_encode([
                            'data' => false,
                            'message' => _l('delete_insurance_history_false'),

                        ]);

                    }
                }
            }
        }
    }

    public function insurance_type(){
        if($this->input->post()){
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $add = $this->hrm_model->add_insurance_type($data); 
                if($add){
                    $message = _l('added_successfully', _l('insurance_type'));
                    set_alert('success',$message);
                }
                redirect(admin_url('hrm/setting?group=insurrance'));
            }else{
                $id = $data['id'];
                unset($data['id']);
                $success = $this->hrm_model->update_insurance_type($data,$id);
                if($success == true){
                    $message = _l('updated_successfully', _l('insurance_type'));
                    set_alert('success', $message);
                }
                redirect(admin_url('hrm/setting?group=insurrance'));
            }

        }
    }
    public function delete_insurance_type($id){
        if (!$id) {
            redirect(admin_url('hrm/setting?group=insurrance'));
        }
        $response = $this->hrm_model->delete_insurance_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('insurance_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('insurance_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('insurance_type')));
        }
        redirect(admin_url('hrm/setting?group=insurrance'));
    }

    public function get_hrm_formality(){
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('formality') == 'increase') {
                echo json_encode([
                    'sign_a_labor_contract'  => get_hrm_option('sign_a_labor_contract'),
                    'maternity_leave_to_return_to_work'  => get_hrm_option('maternity_leave_to_return_to_work'),
                    'unpaid_leave_to_return_to_work'  => get_hrm_option('unpaid_leave_to_return_to_work'),
                    'increase_the_premium'  => get_hrm_option('increase_the_premium'),
                ]);
                die();
                
            }elseif ($this->input->post('formality') == 'decrease') {
                echo json_encode([
                    'contract_paid_for_unemployment'  => get_hrm_option('contract_paid_for_unemployment'),
                    'maternity_leave_regime'  => get_hrm_option('maternity_leave_regime'),
                    'reduced_premiums'  => get_hrm_option('reduced_premiums'),
                ]);
                die();
                
            }

        }
    }

    public function get_hrm_staff(){
	
		\modules\hrm\core\Apiinit::ease_of_mind('hrm');
		\modules\hrm\core\Apiinit::the_da_vinci_code('hrm');
	
        if ($this->input->is_ajax_request()) {

            $staffid = $this->input->get('staffid');

            $total_rows = $this->db->query('select si.insurance_id from '.db_prefix().'staff_insurance as si where si.staff_id = '.$staffid)->result_array();
                if (count($total_rows) > 0) {
                $id = $total_rows[0]['insurance_id'];

                $insurance = $this->hrm_model->get_insurance($id);
                if(isset($insurance)){
                    foreach ($insurance as $key => $insuran) {
                        $insurance_book_num = $insuran['insurance_book_num'];
                        $health_insurance_num = $insuran['health_insurance_num'];
                        $city_code = $insuran['city_code'];
                        $registration_medical = $insuran['registration_medical'];
                    }
                }
                $insurance_history = $this->hrm_model->get_insurance_history($id);
                $month = $this->hrm_model->get_month();
                $staff = $this->staff_model->get();

                $data_insert ='';
                  if(isset($insurance_history) && count($insurance_history) != 0){
                        foreach ($insurance_history as $keydetails => $value) {
                        $keydetails = $keydetails +1;
                                             
            $data_insert .= '<div class="row insurance-history ">';
            $data_insert .=     '<div class="col-md-2">';
                                $from_month = (isset($value['from_month']) ? $value['from_month'] : '');
            $data_insert .=   '<label for="from_month['.$keydetails .']">'. _l('from_month').'</label>';

            $data_insert .=   '<select name="from_month['. $keydetails.']" class="selectpicker"';
            $data_insert .=    'id="from_month['.$keydetails.']" data-width="100%"';
            $data_insert .=    'data-none-selected-text="'. _l('dropdown_non_selected_tex').'">' ;

            $data_insert .=   '<option value=""></option>'; 
                                 if(isset($from_month)){
                                  $exploded = explode("-", $from_month);
                                  $exploded = array_reverse($exploded);
                                  $newFormat = implode("/", $exploded);
                                }
                        foreach($month as $m){                             
            $data_insert .=    '<option value="'. $m['id'].'"';
                                 if(isset($from_month) && $newFormat == $m['id'] ){

            $data_insert .=         'selected';
                                    }
            $data_insert .=        '>'. $m['name'].'</option>';
                                }
            $data_insert .=         '</select>';
            
            $data_insert .=         '</div>';
            $data_insert .=        '<div class="col-md-3">';
                                    $formality = isset($value['formality']) ? $value['formality'] : '' ;
            $data_insert .=         '<label for="formality['. $keydetails .']" class="control-label">'._l(                      'formality').'</label>';
            
            $data_insert .=    '<select onchange="OnSelectReason (this)"';
            $data_insert .=      'name="formality['. $keydetails .']" class="selectpicker"';
            $data_insert .=     'id="formality['. $keydetails .']" data-width="100%" data-none-selected-text="'._l('fillter_by_status').'">';
            $data_insert .=     '  <option value=""></option>';
            $data_insert .=     '  <option value="increase"';
                                 if(isset($formality) && $formality == 'increase'){
            $data_insert .=         'selected';
                                    }
             $data_insert .=        '>'._l('increase').'</option><option value="decrease"';
                 if(isset($formality) && $formality == 'decrease'){
             $data_insert .=       'selected';
                                    }
             $data_insert .=        '>'. _l('decrease').'</option></select></div>                      
                                            <div class="col-md-3">';
                                    $reason = isset($value['reason']) ? $value['reason'] : '';
            $data_insert .=         '<label for="reason['.$keydetails .']" class="control-label">'. _l('reason_').'</label><select  name="reason['.$keydetails .']" class="selectpicker" id="reason['.$keydetails .']" data-width="100%" data-none-selected-text="'. _l('fillter_by_formality').'"><option value=""></option><option value="'.$reason.'"  selected><'._l(''.$reason.'') .'></option></select></div>';
            
            $data_insert .=           '<div class="col-md-3">';
                            $premium_rates = isset($value['premium_rates']) ? $value['premium_rates'] : '' ;
                            $attr = array();
                            $attr = ['data-type' => 'currency'];
                                            
            $data_insert .= render_input('premium_rates['. $keydetails .']','premium_rates', app_format_money((int)$premium_rates,''),'text', $attr);
             $data_insert .=        '</div>';
                                    if($keydetails == 1){
            $data_insert .= '<div class="col-md-1 hrm-nowrap hrm-lineheight84" name="add_insurance_history">';
            $data_insert .= '<button name="add_new_insurance_history" class="btn new_insurance_history btn-success hrm-radius20" data-ticket="true" type="button"php title="'. _l('add') .'" ><i class="fa fa-plus" ></i>';
            $data_insert .=    form_hidden('id_history['.$keydetails.']',$value['id']);
            $data_insert .=     '</button>';
            $data_insert .=     '</div>';
                                    } else {
            $data_insert .=     '<div class="col-md-1 hrm-nowrap hrm-lineheight84" name="add_insurance_history">';
            $data_insert .=    '<button name="add_new_insurance_history" class="btn remove_insurance_history btn-danger hrm-radius20" data-ticket="true" type="button" title="'._l('delete').'" ><i class="fa fa-minus"></i>';
            $data_insert .=     form_hidden('id_history['.$keydetails.']',$value['id']);
            $data_insert .=     '</button>';
            $data_insert .=     '</div>';
                                        } 
            $data_insert .=     '</div>';

                        }    
                    }


                    echo json_encode([
                        'id' => $id,
                        'data' => $data_insert,
                        'insurance_book_num'   => $insurance_book_num,
                        'health_insurance_num' => $health_insurance_num,
                        'city_code'            => $city_code,
                        'registration_medical'  => $registration_medical,

                    ]);
                    die();
                }else{
        $month = $this->hrm_model->get_month();
                $staff = $this->staff_model->get();
                $data_null ='';
        $data_null  .=    '<div class="row insurance-history ">';
        $data_null  .=    '<div class="col-md-2">';
                $from_month = (isset($from_month) ? $from_month : '');
        $data_null  .=        '<div class="form-group">';
        $data_null  .=        '<label for="from_month[1]">'. _l('from_month').'</label>';
        $data_null  .=      '<select name="from_month[1]" class="selectpicker" id="from_month[1]" data-width="100%"';

        $data_null  .= 'data-none-selected-text="'. _l('dropdown_non_selected_tex').'">' ;
        $data_null  .=        '<option value=""></option>' ;

                foreach($month as $s){                             
        $data_null  .=         '<option value="'.$s['id'].'">'.$s['name'].'</option>';
                        }
        $data_null  .=     '</select>';
        $data_null  .=        '</div>';
        $data_null  .=    '</div>';
        $data_null  .=   '<div class="col-md-3">';
                $formality = isset($formality) ? $formality : '' ;
        $data_null  .=    '<label for="formality[1]" class="control-label">'. _l('formality').'</label>';
        $data_null  .=    '<select onchange="OnSelectReason (this)" name="formality[1]" class="selectpicker" id="';
        $data_null  .= 'formality[1]" data-width="100%" data-none-selected-text="'. _l('fillter_by_status').'">'; 
        $data_null  .=        '<option value=""></option>';
        $data_null  .=        '<option value="increase">'. _l('increase').'</option>';
        $data_null  .=        '<option value="decrease">'._l('decrease').'</option>';
        $data_null  .=    '</select>';
        $data_null  .=    '</div>';

        $data_null  .=    '<div class="col-md-3">';
                $reason = isset($reason) ? $reason : '' ;
        $data_null  .=    '<label for="reason[1]" class="control-label">'. _l('reason_').'</label>';
        $data_null  .=    '<select  name="reason[1]" class="selectpicker" id="reason[1]" data-width="100%"';

        $data_null  .= 'data-none-selected-text="'. _l('fillter_by_formality').'">' ;
        $data_null  .=        '<option value=""></option>';
        $data_null  .=    '</select>';
        $data_null  .=    '</div>';

        $data_null  .=    '<div class="col-md-3">';
                $premium_rates = isset($premium_rates) ? $premium_rates : '' ;
            
            $attr = array();
            $attr = ['data-type' => 'currency'];
        $data_null  .=    render_input('premium_rates[1]','premium_rates', $premium_rates,'text', $attr);
        $data_null  .=    '</div>';

    $data_null  .= '<div class="col-md-1 hrm-nowrap hrm-lineheight84" name="add_insurance_history">';
        $data_null  .=    '<button name="add_new_insurance_history" class="btn new_insurance_history btn-success hrm-radius20"'; 
        $data_null  .=  'data-ticket="true" type="button" title="'. _l('add') .'"><i class="fa fa-plus"></i></button>';
        $data_null  .=    '</div>';

        $data_null  .= '</div>';
                    echo json_encode([
                        'id' => '',
                        'data_null' => $data_null,
                    ]);
                    die();
                } 
        }
    }

    public function timekeeping(){
        $this->load->model('departments_model');
        $this->load->model('staff_model');

        $data['group'] = $this->input->get('group');
        $data['title'] = _l($data['group']);
        $data['tab'][] = 'manage_dayoff';
        $data['tab'][] = 'allocate_shiftwork';
        $data['tab'][] = 'table_shiftwork';
        

        if($data['group'] == ''){
            $data['group'] = 'manage_dayoff';
            $data['title'] = _l($data['group']);
        }
        $data['departments'] = $this->departments_model->get();
        $data['positions'] = $this->hrm_model->get_job_position();
        $data['holiday'] = $this->hrm_model->get_break_dates('holiday');
        $data['event_break'] = $this->hrm_model->get_break_dates('event_break');
        $data['unexpected_break'] = $this->hrm_model->get_break_dates('unexpected_break');
        $data['shifts'] = $this->hrm_model->get_shifts();


        $data['day_by_month'] = [];
        $data['day_by_month_tk'] = [];
        $data['day_by_month'][] = _l('staff');
        $data['day_by_month_tk'][] = _l('staff_id');
        $data['day_by_month_tk'][] = _l('hr_code');
        $data['day_by_month_tk'][] = _l('staff');

        $data['set_col'] = [];
        $data['set_col_tk'] = [];
        $data['set_col_tk'][] = ['data' => _l('staff_id'), 'type' => 'text'];
        $data['set_col_tk'][] = ['data' => _l('hr_code'), 'type' => 'text','readOnly' => true];
        $data['set_col_tk'][] = ['data' => _l('staff'), 'type' => 'text','readOnly' => true];
        $data['set_col'][] = ['data' => _l('staff'), 'type' => 'text'];

        $month      = date('m');
        $month_year = date('Y');
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $month_year);
            if (date('m', $time) == $month) {
                array_push($data['day_by_month_tk'], date('d/m D', $time));
                array_push($data['day_by_month'], date('d/m D', $time));
                array_push($data['set_col'],[ 'data' => date('d/m D', $time), 'type' => 'text']);
                array_push($data['set_col_tk'],[ 'data' => date('d/m D', $time), 'type' => 'text']);
            }
        }

        $data['day_by_month'] = json_encode($data['day_by_month']);
        $data['day_by_month_tk'] = json_encode($data['day_by_month_tk']);

        $data['set_col'] = json_encode($data['set_col']);
        $data['set_col_tk'] = json_encode($data['set_col_tk']);

        $data_ts = $this->hrm_model->get_hrm_ts_by_month(date('m'));

        if(isset($data['shifts'][0])){
            $work_shift = $data['shifts'][0];
            $work_shift['shift_s'] = $this->hrm_model->get_data_edit_shift($work_shift['id']);
        }

        $data_map = [];
        foreach($data_ts as $ts){
            $staff_info = array();
            $staff_info['date'] = date('d/m D', strtotime($ts['date_work']));

            
            $ts_type = $this->hrm_model->get_ts_by_date_and_staff($ts['date_work'],$ts['staff_id']);
            if(count($ts_type) <= 1){
                 $staff_info['ts'] = $ts['type'].':'.$ts['value'];
                
            }else{
                $str = '';
                foreach($ts_type as $tp){
                    if($str == ''){
                        $str .= $tp['type'].':'.$tp['value'];
                    }else{
                        $str .= '-'.$tp['type'].':'.$tp['value'];
                    }
                }
                $staff_info['ts'] = $str;
            }
              
            
            
            if(!isset($data_map[$ts['staff_id']])){
                $data_map[$ts['staff_id']] = array();
            }
            $data_map[$ts['staff_id']][$staff_info['date']] = $staff_info;
        }
  

        $data['staff_row_tk'] = [];
        $data['staff_row'] = [];
        $staffs = $this->staff_model->get();
        $shift_staff = [];
        foreach($staffs as $s){
            
            $shift_staff = [_l('staff') => $s['firstname'].' '.$s['lastname']];
                if(isset($work_shift['shift_s'])){
                    for ($d = 1; $d <= 31; $d++) {
                        $time = mktime(12, 0, 0, $month, $d, $month_year);
                        if (date('m', $time) == $month) {
                            if(date('N', $time) == 1){
                                $shift_staff[date('d/m D', $time)] = _l('time_working').': '.$work_shift['shift_s'][0]['monday'] .' - '.$work_shift['shift_s'][1]['monday'].'  '._l('time_lunch').': '.$work_shift['shift_s'][2]['monday'].' - '.$work_shift['shift_s'][3]['monday'];
                            }elseif(date('N', $time) == 2){
                                $shift_staff[date('d/m D', $time)] = _l('time_working').': '.$work_shift['shift_s'][0]['tuesday'] .' - '.$work_shift['shift_s'][1]['tuesday'].'  '._l('time_lunch').': '.$work_shift['shift_s'][2]['tuesday'].' - '.$work_shift['shift_s'][3]['tuesday'];
                            }elseif(date('N', $time) == 3){
                                $shift_staff[date('d/m D', $time)] = _l('time_working').': '.$work_shift['shift_s'][0]['wednesday'] .' - '.$work_shift['shift_s'][1]['wednesday'].'  '._l('time_lunch').': '.$work_shift['shift_s'][2]['wednesday'].' - '.$work_shift['shift_s'][3]['wednesday'];
                            }elseif(date('N', $time) == 4){
                                $shift_staff[date('d/m D', $time)] = _l('time_working').': '.$work_shift['shift_s'][0]['thursday'] .' - '.$work_shift['shift_s'][1]['thursday'].'  '._l('time_lunch').': '.$work_shift['shift_s'][2]['thursday'].' - '.$work_shift['shift_s'][3]['thursday'];
                            }elseif(date('N', $time) == 5){
                                $shift_staff[date('d/m D', $time)] = _l('time_working').': '.$work_shift['shift_s'][0]['friday'] .' - '.$work_shift['shift_s'][1]['friday'].'  '._l('time_lunch').': '.$work_shift['shift_s'][2]['friday'].' - '.$work_shift['shift_s'][3]['friday'];
                            }elseif(date('N', $time) == 7){
                                $shift_staff[date('d/m D', $time)] = _l('time_working').': '.$work_shift['shift_s'][0]['sunday'] .' - '.$work_shift['shift_s'][1]['sunday'].'  '._l('time_lunch').': '.$work_shift['shift_s'][2]['sunday'].' - '.$work_shift['shift_s'][3]['sunday'];
                            }elseif(date('N', $time) == 6 && (date('d', $time)%2) == 1){
                                $shift_staff[date('d/m D', $time)] = _l('time_working').': '.$work_shift['shift_s'][0]['saturday_odd'] .' - '.$work_shift['shift_s'][1]['saturday_odd'].'  '._l('time_lunch').': '.$work_shift['shift_s'][2]['saturday_odd'].' - '.$work_shift['shift_s'][3]['saturday_odd'];
                            }elseif(date('N', $time) == 6 && (date('d', $time)%2) == 0){
                                $shift_staff[date('d/m D', $time)] = _l('time_working').': '.$work_shift['shift_s'][0]['saturday_even'] .' - '.$work_shift['shift_s'][1]['saturday_even'].'  '._l('time_lunch').': '.$work_shift['shift_s'][2]['saturday_even'].' - '.$work_shift['shift_s'][3]['saturday_even'];
                            }
                        }
                    }
                }
            array_push($data['staff_row'], $shift_staff);


            $ts_date = '';
            $ts_ts = '';
            $result_tb = [];
            if(isset($data_map[$s['staffid']])){

                foreach ($data_map[$s['staffid']] as $key => $value) {
                    $ts_date = $data_map[$s['staffid']][$key]['date'];
                    $ts_ts =  $data_map[$s['staffid']][$key]['ts'];
                    $result_tb[] = [$ts_date => $ts_ts];
                }
               
            }
            $dt_ts = [];
            $dt_ts = [_l('staff_id') => $s['staffid'],_l('hr_code') => $s['staff_identifi'],_l('staff') => $s['firstname'].' '.$s['lastname']];
            foreach ($result_tb as $key => $rs) {
                foreach ($rs as $day => $val) {
                   $dt_ts[$day] = $val;
                }
            }

            array_push($data['staff_row_tk'], $dt_ts);
            
        }

        $data['tabs']['view'] = 'timekeeping/'.$data['group'];
        $this->load->view('timekeeping/manage_timekeeping', $data);
    }

    public function day_off(){
        if($this->input->post()){
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $add = $this->hrm_model->add_day_off($data); 
                if($add > 0){
                    $message = _l('day_off').' '. _l('added_successfully');
                    set_alert('success',$message);
                }
                redirect(admin_url('hrm/timekeeping?group=manage_dayoff'));
            }else{
                $id = $data['id'];
                unset($data['id']);
                $success = $this->hrm_model->update_day_off($data,$id);
                if($success == true){
                    $message = _l('day_off').' '._l('updated_successfully');
                    set_alert('success', $message);
                }
                redirect(admin_url('hrm/timekeeping?group=manage_dayoff'));
            }

        }
    }
    public function delete_day_off($id){
        if (!$id) {
            redirect(admin_url('hrm/timekeeping?group=manage_dayoff'));
        }
        $response = $this->hrm_model->delete_day_off($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced').' '. _l('day_off'));
        } elseif ($response == true) {
            set_alert('success', _l('deleted').' '._l('day_off'));
        } else {
            set_alert('warning', _l('problem_deleting').' '. _l('day_off'));
        }
        redirect(admin_url('hrm/timekeeping?group=manage_dayoff'));
    }
    
    public function shifts(){
        if($this->input->post()){
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $add = $this->hrm_model->add_work_shift($data); 
                if($add > 0){
                    $message = _l('shift') . '' . _l('added_successfully');
                    set_alert('success',$message);
                }
                redirect(admin_url('hrm/timekeeping?group=allocate_shiftwork'));
            }else{
                $id = $data['id'];
                unset($data['id']);
                $success = $this->hrm_model->update_work_shift($data,$id);
                if($success == true){
                    $message = _l('shift').' '._l('updated_successfully');
                    set_alert('success', $message);
                }
                redirect(admin_url('hrm/timekeeping?group=allocate_shiftwork'));
            }
        }   
    }

    public function get_data_edit_shift($id){
        $shift_handson = $this->hrm_model->get_data_edit_shift($id);
        $result = [];
        $node = [];
        foreach ($shift_handson as $key => $value) {
            foreach ($value as $col => $val) {
                if($col == 'detail'){
                    if($key == 0){
                        $node[_l($col)] =  _l('time_start_work');
                    }elseif ($key == 1) {
                       $node[_l($col)] =  _l('time_end_work');
                    }elseif($key == 2){
                        $node[_l($col)] =  _l('start_lunch_break_time');
                    }elseif($key == 3){
                        $node[_l($col)] =  _l('end_lunch_break_time');
                    }
                }else{

                    $node[_l($col)] = $val;

                }
            }
            $result[] = $node; 
        }
        echo json_encode([
            'handson' => $result,
        ]);
    }

    public function delete_shift($id){
        if (!$id) {
            redirect(admin_url('hrm/timekeeping?group=allocate_shiftwork'));
        }
        $response = $this->hrm_model->delete_shift($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced').' '. _l('shift'));
        } elseif ($response == true) {
            set_alert('success', _l('deleted').' '._l('shift'));
        } else {
            set_alert('warning', _l('problem_deleting').' '. _l('shift'));
        }
        redirect(admin_url('hrm/timekeeping?group=allocate_shiftwork'));
    }

    public function delete_payroll_table($id){
        if (!$id) {
            redirect(admin_url('hrm/payroll?group=payslip'));
        }
        $response = $this->hrm_model->delete_payroll_table($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced').' '. _l('payslip'));
        } elseif ($response == true) {
            set_alert('success', _l('deleted').' '._l('payslip'));
        } else {
            set_alert('warning', _l('problem_deleting').' '. _l('payslip'));
        }
        redirect(admin_url('hrm/payroll?group=payslip'));
    }

    public function paysplit_exists(){
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $payroll_month = $this->input->post('payroll_month');
                $payroll_type = $this->input->post('payroll_type');
                
                if(strlen($payroll_month) != 0 && strlen($payroll_type) != 0){
 
                $this->db->where('payroll_month', to_sql_date($payroll_month));
                $this->db->where('payroll_type', (int)$payroll_type);
                $total_rows = $this->db->count_all_results('tblpayroll_table');
                    if ($total_rows > 0) {
                        echo json_encode(false);
                    } else {
                        echo json_encode(true);
                    }
                    die();
                }
            }
        }
    }

    public function profile($id = '')
    {
	
		\modules\hrm\core\Apiinit::ease_of_mind('hrm');
		\modules\hrm\core\Apiinit::the_da_vinci_code('hrm');
        $this->load->model('departments_model');
        if ($id == '') {
            $id = get_staff_user_id();
        }

        $member = $this->staff_model->get($id);
        if (!$member) {
            blank_page('Staff Member Not Found', 'danger');
        }
        $data['member']            = $member;
        $title                     = $member->firstname . ' ' . $member->lastname;
        $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);


        $data['staff_departments'] = $this->departments_model->get_staff_departments($data['member']->staffid);
        $data['departments']       = $this->departments_model->get();


        $this->load->view('hrm/profile', $data);
    }


}
