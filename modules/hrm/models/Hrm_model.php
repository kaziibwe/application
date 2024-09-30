<?php

defined('BASEPATH') or exit('No direct script access allowed');

class hrm_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single goal
     */
    public function add_staff($data)
    {
        $data['birthday']             = to_sql_date($data['birthday']);
        $data['days_for_identity']    = to_sql_date($data['days_for_identity']);
        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }
        // First check for all cases if the email exists.
        $this->db->where('email', $data['email']);
        $email = $this->db->get(db_prefix() . 'staff')->row();
        if ($email) {
            die('Email already exists');
        }
        $data['admin'] = 0;
        if (is_admin()) {
            if (isset($data['administrator'])) {
                $data['admin'] = 1;
                unset($data['administrator']);
            }
        }

        $send_welcome_email = true;
        $original_password  = $data['password'];
        if (!isset($data['send_welcome_email'])) {
            $send_welcome_email = false;
        } else {
            unset($data['send_welcome_email']);
        }

        $data['password']        = app_hash_password($data['password']);
        $data['datecreated']     = date('Y-m-d H:i:s');
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }

        $permissions = [];
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        if (isset($data['nationality'])) {
            unset($data['nationality']);
        }
        if ($data['admin'] == 1) {
            $data['is_not_staff'] = 0;
        }

        $this->db->insert(db_prefix() . 'staff', $data);
        $staffid = $this->db->insert_id();
        if ($staffid) {
			
			if (!isset($data['lastname'])) { $data['lastname'] = ''; }
			
            $slug = $data['firstname'] . ' ' . $data['lastname'];

            if ($slug == ' ') {
                $slug = 'unknown-' . $staffid;
            }

            if ($send_welcome_email == true) {
                send_mail_template('staff_created', $data['email'], $staffid, $original_password);
            }

            $this->db->where('staffid', $staffid);
            $this->db->update(db_prefix() . 'staff', [
                'media_path_slug' => slug_it($slug),
            ]);

            if (isset($custom_fields)) {
                handle_custom_fields_post($staffid, $custom_fields);
            }
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->insert(db_prefix() . 'staff_departments', [
                        'staffid'      => $staffid,
                        'departmentid' => $department,
                    ]);
                }
            }

            // Delete all staff permission if is admin we dont need permissions stored in database (in case admin check some permissions)
            $this->update_permissions($data['admin'] == 1 ? [] : $permissions, $staffid);

            log_activity('New Staff Member Added [ID: ' . $staffid . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');

            // Get all announcements and set it to read.
            $this->db->select('announcementid');
            $this->db->from(db_prefix() . 'announcements');
            $this->db->where('showtostaff', 1);
            $announcements = $this->db->get()->result_array();
            foreach ($announcements as $announcement) {
                $this->db->insert(db_prefix() . 'dismissed_announcements', [
                    'announcementid' => $announcement['announcementid'],
                    'staff'          => 1,
                    'userid'         => $staffid,
                ]);
            }
            hooks()->do_action('staff_member_created', $staffid);

            return $staffid;
        }

        return false;
    }
    public function update_staff($data, $id)
    {
        
        if(isset($data['DataTables_Table_0_length'])){
            unset($data['DataTables_Table_0_length']);
        }
        $data['date_update']          = date('Y-m-d');
        $data['birthday']             = to_sql_date($data['birthday']);
        $data['days_for_identity']    = to_sql_date($data['days_for_identity']);
        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }
        if (isset($data['nationality'])) {
            unset($data['nationality']);
        }
        if (isset($data['DataTables_Table_0_length'])) {
            unset($data['DataTables_Table_0_length']);
        }
        $data = hooks()->apply_filters('before_update_staff_member', $data, $id);

        if (is_admin()) {
            if (isset($data['administrator'])) {
                $data['admin'] = 1;
                unset($data['administrator']);
            } else {
                if ($id != get_staff_user_id()) {
                    if ($id == 1) {
                        return [
                            'cant_remove_main_admin' => true,
                        ];
                    }
                } else {
                    return [
                        'cant_remove_yourself_from_admin' => true,
                    ];
                }
                $data['admin'] = 0;
            }
        }

        $affectedRows = 0;
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }

        $permissions = [];
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password']             = app_hash_password($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }


        if (isset($data['two_factor_auth_enabled'])) {
            $data['two_factor_auth_enabled'] = 1;
        } else {
            $data['two_factor_auth_enabled'] = 0;
        }

        if (isset($data['is_not_staff'])) {
            $data['is_not_staff'] = 1;
        } else {
            $data['is_not_staff'] = 0;
        }

        if (isset($data['admin']) && $data['admin'] == 1) {
            $data['is_not_staff'] = 0;
        }

        $data['email_signature'] = nl2br_save_html($data['email_signature']);

        $this->load->model('departments_model');
        $staff_departments = $this->departments_model->get_staff_departments($id);
        if (sizeof($staff_departments) > 0) {
            if (!isset($data['departments'])) {
                $this->db->where('staffid', $id);
                $this->db->delete(db_prefix() . 'staff_departments');
            } else {
                foreach ($staff_departments as $staff_department) {
                    if (isset($departments)) {
                        if (!in_array($staff_department['departmentid'], $departments)) {
                            $this->db->where('staffid', $id);
                            $this->db->where('departmentid', $staff_department['departmentid']);
                            $this->db->delete(db_prefix() . 'staff_departments');
                            if ($this->db->affected_rows() > 0) {
                                $affectedRows++;
                            }
                        }
                    }
                }
            }
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->where('staffid', $id);
                    $this->db->where('departmentid', $department);
                    $_exists = $this->db->get(db_prefix() . 'staff_departments')->row();
                    if (!$_exists) {
                        $this->db->insert(db_prefix() . 'staff_departments', [
                            'staffid'      => $id,
                            'departmentid' => $department,
                        ]);
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            }
        } else {
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->insert(db_prefix() . 'staff_departments', [
                        'staffid'      => $id,
                        'departmentid' => $department,
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($this->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $permissions), $id)) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            hooks()->do_action('staff_member_updated', $id);
            log_activity('Staff Member Updated [ID: ' . $id . ']');

            return true;
        }

        return false;
    }
    public function delete_staff($id, $transfer_data_to)
    {
        if (!is_numeric($transfer_data_to)) {
            return false;
        }

        if ($id == $transfer_data_to) {
            return false;
        }

        hooks()->do_action('before_delete_staff_member', [
            'id'               => $id,
            'transfer_data_to' => $transfer_data_to,
        ]);

        $name           = get_staff_full_name($id);
        $transferred_to = get_staff_full_name($transfer_data_to);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('sale_agent', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'sale_agent' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'invoices', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('sale_agent', $id);
        $this->db->update(db_prefix() . 'invoices', [
            'sale_agent' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'expenses', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'notes', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'newsfeed_post_comments', [
            'userid' => $transfer_data_to,
        ]);

        $this->db->where('creator', $id);
        $this->db->update(db_prefix() . 'newsfeed_posts', [
            'creator' => $transfer_data_to,
        ]);

        $this->db->where('staff_id', $id);
        $this->db->update(db_prefix() . 'projectdiscussions', [
            'staff_id' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'projects', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'creditnotes', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('staff_id', $id);
        $this->db->update(db_prefix() . 'credits', [
            'staff_id' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'project_files', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'proposal_comments', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'proposals', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'task_comments', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->where('is_added_from_contact', 0);
        $this->db->update(db_prefix() . 'tasks', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'files', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('renewed_by_staff_id', $id);
        $this->db->update(db_prefix() . 'contract_renewals', [
            'renewed_by_staff_id' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'task_checklist_items', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('finished_from', $id);
        $this->db->update(db_prefix() . 'task_checklist_items', [
            'finished_from' => $transfer_data_to,
        ]);

        $this->db->where('admin', $id);
        $this->db->update(db_prefix() . 'ticket_replies', [
            'admin' => $transfer_data_to,
        ]);

        $this->db->where('admin', $id);
        $this->db->update(db_prefix() . 'tickets', [
            'admin' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'leads', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('assigned', $id);
        $this->db->update(db_prefix() . 'leads', [
            'assigned' => $transfer_data_to,
        ]);

        $this->db->where('staff_id', $id);
        $this->db->update(db_prefix() . 'taskstimers', [
            'staff_id' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'contracts', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('assigned_from', $id);
        $this->db->where('is_assigned_from_contact', 0);
        $this->db->update(db_prefix() . 'task_assigned', [
            'assigned_from' => $transfer_data_to,
        ]);

        $this->db->where('responsible', $id);
        $this->db->update(db_prefix() . 'leads_email_integration', [
            'responsible' => $transfer_data_to,
        ]);

        $this->db->where('responsible', $id);
        $this->db->update(db_prefix() . 'web_to_lead', [
            'responsible' => $transfer_data_to,
        ]);

        $this->db->where('created_from', $id);
        $this->db->update(db_prefix() . 'subscriptions', [
            'created_from' => $transfer_data_to,
        ]);

        $this->db->where('notify_type', 'specific_staff');
        $web_to_lead = $this->db->get(db_prefix() . 'web_to_lead')->result_array();

        foreach ($web_to_lead as $form) {
            if (!empty($form['notify_ids'])) {
                $staff = unserialize($form['notify_ids']);
                if (is_array($staff)) {
                    if (in_array($id, $staff)) {
                        if (($key = array_search($id, $staff)) !== false) {
                            unset($staff[$key]);
                            $staff = serialize(array_values($staff));
                            $this->db->where('id', $form['id']);
                            $this->db->update(db_prefix() . 'web_to_lead', [
                                'notify_ids' => $staff,
                            ]);
                        }
                    }
                }
            }
        }

        $this->db->where('id', 1);
        $leads_email_integration = $this->db->get(db_prefix() . 'leads_email_integration')->row();

        if ($leads_email_integration->notify_type == 'specific_staff') {
            if (!empty($leads_email_integration->notify_ids)) {
                $staff = unserialize($leads_email_integration->notify_ids);
                if (is_array($staff)) {
                    if (in_array($id, $staff)) {
                        if (($key = array_search($id, $staff)) !== false) {
                            unset($staff[$key]);
                            $staff = serialize(array_values($staff));
                            $this->db->where('id', 1);
                            $this->db->update(db_prefix() . 'leads_email_integration', [
                                'notify_ids' => $staff,
                            ]);
                        }
                    }
                }
            }
        }

        $this->db->where('assigned', $id);
        $this->db->update(db_prefix() . 'tickets', [
            'assigned' => 0,
        ]);

        $this->db->where('staff', 1);
        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'dismissed_announcements');

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'newsfeed_comment_likes');

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'newsfeed_post_likes');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'customer_admins');

        $this->db->where('fieldto', 'staff');
        $this->db->where('relid', $id);
        $this->db->delete(db_prefix() . 'customfieldsvalues');

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'events');

        $this->db->where('touserid', $id);
        $this->db->delete(db_prefix() . 'notifications');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'user_meta');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'project_members');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'project_notes');

        $this->db->where('creator', $id);
        $this->db->or_where('staff', $id);
        $this->db->delete(db_prefix() . 'reminders');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'staff_departments');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'todos');

        $this->db->where('staff', 1);
        $this->db->where('user_id', $id);
        $this->db->delete(db_prefix() . 'user_auto_login');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'staff_permissions');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'task_assigned');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'task_followers');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'pinned_projects');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'staff');
        log_activity('Staff Member Deleted [Name: ' . $name . ', Data Transferred To: ' . $transferred_to . ']');

        hooks()->do_action('staff_member_deleted', [
            'id'               => $id,
            'transfer_data_to' => $transfer_data_to,
        ]);

        return true;
    }
    public function get_staff($id = '', $where = [])
    {
        $select_str = '*,CONCAT(firstname," ",lastname) as full_name';

        // Used to prevent multiple queries on logged in staff to check the total unread notifications in core/AdminController.php
        if (is_staff_logged_in() && $id != '' && $id == get_staff_user_id()) {
            $select_str .= ',(SELECT COUNT(*) FROM ' . db_prefix() . 'notifications WHERE touserid=' . get_staff_user_id() . ' and isread=0) as total_unread_notifications, (SELECT COUNT(*) FROM ' . db_prefix() . 'todos WHERE finished=0 AND staffid=' . get_staff_user_id() . ') as total_unfinished_todos';
        }

        $this->db->select($select_str);
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('staffid', $id);
            $staff = $this->db->get(db_prefix() . 'staff')->row();

            if ($staff) {
                $staff->permissions = $this->get_staff_permissions($id);
            }

            return $staff;
        }
        $this->db->order_by('firstname', 'desc');

        return $this->db->get(db_prefix() . 'staff')->result_array();
    }
    public function get_staff_permissions($id)
    {
        // Fix for version 2.3.1 tables upgrade
        if (defined('DOING_DATABASE_UPGRADE')) {
            return [];
        }

        $permissions = $this->app_object_cache->get('staff-' . $id . '-permissions');

        if (!$permissions && !is_array($permissions)) {
            $this->db->where('staff_id', $id);
            $permissions = $this->db->get('staff_permissions')->result_array();

            $this->app_object_cache->add('staff-' . $id . '-permissions', $permissions);
        }

        return $permissions;
    }
    public function get_department_name($departmentid){
        return $this->db->query('select tbldepartments.name from tbldepartments where departmentid = '.$departmentid)->result_array();
    }
    public function update_permissions($permissions, $id)
    {
        $this->db->where('staff_id', $id);
        $this->db->delete('staff_permissions');

        $is_staff_member = is_staff_member($id);

        foreach ($permissions as $feature => $capabilities) {
            foreach ($capabilities as $capability) {

                // Maybe do this via hook.
                if ($feature == 'leads' && !$is_staff_member) {
                    continue;
                }

                $this->db->insert('staff_permissions', ['staff_id' => $id, 'feature' => $feature, 'capability' => $capability]);
            }
        }

        return true;
    }
    public function get_job_position($id = false)
    {

        if (is_numeric($id)) {
        $this->db->where('position_id', $id);

            return $this->db->get(db_prefix() . 'job_position')->row();
        }

        if ($id == false) {
            return $this->db->query('select * from tbljob_position')->result_array();
        }

    }

    public function get_job_position_arrayid()
    {
        $position = $this->db->query('select * from tbljob_position')->result_array();
        $position_arrray = [];
        foreach ($position as $value) {
            array_push($position_arrray, $value['position_id']);
        }
        return $position_arrray;
    }
    public function add_job_position($data){
        $this->db->insert(db_prefix() . 'job_position', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function update_job_position($data, $id)
    {   
        $this->db->where('position_id', $id);
        $this->db->update(db_prefix() . 'job_position', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function delete_job_position($id){
        $this->db->where('position_id', $id);
        $this->db->delete(db_prefix() . 'job_position');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    public function get_workplace()
    {
        return $this->db->query('select * from tblworkplace')->result_array();
    }
    public function get_workplace_array_id()
    {
        $workplace = $this->db->query('select * from tblworkplace')->result_array();
        $workpalce_array =[];
        foreach ($workplace as $value) {
            array_push($workpalce_array, $value['workplace_id']);
        }
        return $workpalce_array;
    }
    public function add_workplace($data){
        $this->db->insert(db_prefix() . 'workplace', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function update_workplace($data, $id)
    {   
        $this->db->where('workplace_id', $id);
        $this->db->update(db_prefix() . 'workplace', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function delete_workplace($id){
        $this->db->where('workplace_id', $id);
        $this->db->delete(db_prefix() . 'workplace');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    public function get_contracttype_by_id($id){
        return $this->db->query('select * from tblstaff_contracttype where id_contracttype = '.$id)->result_array();
    }
    public function get_contracttype(){
        return $this->db->query('select * from tblstaff_contracttype')->result_array();
    }
    public function add_contract_type($data){
        $this->db->insert(db_prefix() . 'staff_contracttype', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function update_contract_type($data, $id)
    {   
        $this->db->where('id_contracttype', $id);
        $this->db->update(db_prefix() . 'staff_contracttype', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function delete_contract_type($id){
        $this->db->where('id_contracttype', $id);
        $this->db->delete(db_prefix() . 'staff_contracttype');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    public function get_allowance_type($id = false){
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'allowance_type')->row();
        }

        if ($id == false) {
           return  $this->db->get(db_prefix() . 'allowance_type')->result_array();
        }

    }
    public function add_allowance_type($data){
        $this->db->insert(db_prefix() . 'allowance_type', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function update_allowance_type($data, $id)
    {   
        $this->db->where('type_id', $id);
        $this->db->update(db_prefix() . 'allowance_type', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function delete_allowance_type($id){
        $this->db->where('type_id', $id);
        $this->db->delete(db_prefix() . 'allowance_type');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    public function get_salary_form($id = false){
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'salary_form')->row();
        }

        if ($id == false) {
        return $this->db->query('select * from tblsalary_form')->result_array();
        }

    }
    public function add_salary_form($data){
        $this->db->insert(db_prefix() . 'salary_form', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function update_salary_form($data, $id)
    {   
        $this->db->where('form_id', $id);
        $this->db->update(db_prefix() . 'salary_form', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function delete_salary_form($id){
        $this->db->where('form_id', $id);
        $this->db->delete(db_prefix() . 'salary_form');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    public function get_contract($id){
        return $this->db->query('select * from tblstaff_contract where id_contract = '.$id)->result_array();
    }
    public function get_contract_detail($id){
        return $this->db->query('select * from tblstaff_contract_detail where staff_contract_id = '.$id)->result_array();
    }
    public function add_contract($data){
        $data['start_valid']             = to_sql_date($data['start_valid']);
        $data['end_valid']    = to_sql_date($data['end_valid']);
        $data['sign_day']    = to_sql_date($data['sign_day']);

        if(isset($data['since_date'])){
            $since_dates = $data['since_date'];
            unset($data['since_date']);
        }
        if(isset($data['contract_note'])){
            $contract_note = $data['contract_note'];
            unset($data['contract_note']);
        }

        if(isset($data['salary_form'])){
            $salary_forms = $data['salary_form'];
            unset($data['salary_form']);
        }
        if(isset($data['contract_expense'])){
            $contract_expenses = $data['contract_expense'];
            unset($data['contract_expense']);
        }

        if(isset($data['allowance_type'])){
            $allowance_type = $data['allowance_type'];
            unset($data['allowance_type']);
        }
        if(isset($data['allowance_expense'])){
            $allowance_expense = $data['allowance_expense'];
            unset($data['allowance_expense']);
        }

        if(isset($data['job_position'])){
            $job_position = $data['job_position'];
            unset($data['job_position']);
        }


        $this->db->insert(db_prefix() . 'staff_contract', $data);
        $insert_id = $this->db->insert_id();

        if($insert_id){

            foreach($since_dates as $key => $value){
                $row = [];
                $row['staff_contract_id'] = $insert_id;
                $row['since_date'] = to_sql_date($value);
                $expenses = [];
                foreach($salary_forms[$key] as $k => $value){
                    $note = [];
                    $note['type'] = $value;
                    $note['value'] = $contract_expenses[$key][$k];
                    $expenses[] = $note;
                }

                $allowance = [];
                foreach($allowance_type[$key] as $h => $value){
                    $note = [];
                    $note['type'] = $value;
                    $note['value'] = $allowance_expense[$key][$h];
                    $allowance[] = $note;
                }

                
                $row['contract_note'] = $contract_note[$key];
                $row['contract_salary_expense'] = json_encode($expenses);
                $row['contract_allowance_expense'] = json_encode($allowance);
                $this->db->insert(db_prefix() . 'staff_contract_detail', $row);
            }

        }

        return $insert_id;
    }
    public function update_contract($data, $id)
    {   
        $data['start_valid']             = to_sql_date($data['start_valid']);
        $data['end_valid']    = to_sql_date($data['end_valid']);
        $data['sign_day']    = to_sql_date($data['sign_day']);

        if(isset($data['since_date'])){
            $since_dates = $data['since_date'];
            unset($data['since_date']);
        }
        if(isset($data['contract_note'])){
            $contract_note = $data['contract_note'];
            unset($data['contract_note']);
        }
        if(isset($data['salary_form'])){
            $salary_forms = $data['salary_form'];
            unset($data['salary_form']);
        }
        if(isset($data['contract_expense'])){
            $contract_expenses = $data['contract_expense'];
            unset($data['contract_expense']);
        }
        if(isset($data['allowance_type'])){
            $allowance_type = $data['allowance_type'];
            unset($data['allowance_type']);
        }
        if(isset($data['allowance_expense'])){
            $allowance_expense = $data['allowance_expense'];
            unset($data['allowance_expense']);
        }

        if(isset($data['job_position'])){
            $job_position = $data['job_position'];
            unset($data['job_position']);
        }

        $this->db->where('id_contract', $id);
        $this->db->update(db_prefix() . 'staff_contract', $data);

        $contract_detail_id = $this->db->query('select cd.contract_detail_id from '.db_prefix().'staff_contract_detail as cd where cd.staff_contract_id = '.$id)->result_array();

        foreach($since_dates as $key => $value){

            if($key > count($contract_detail_id)){
                $row_insert = [];
                $row_insert['staff_contract_id'] = $id;
                $row_insert['since_date'] = to_sql_date($value);
                $expenses = [];
                foreach($salary_forms[$key] as $k => $value){
                    $note_insert = [];
                    $note_insert['type'] = $value;
                    $note_insert['value'] = $contract_expenses[$key][$k];
                    $expenses[] = $note_insert;
                }

                $allowance = [];
                foreach($allowance_type[$key] as $h => $value){
                    $note_insert = [];
                    $note_insert['type'] = $value;
                    $note_insert['value'] = $allowance_expense[$key][$h];
                    $allowance[] = $note_insert;
                }

                
                $row_insert['contract_note'] = $contract_note[$key];
                $row_insert['contract_salary_expense'] = json_encode($expenses);
                $row_insert['contract_allowance_expense'] = json_encode($allowance);
                $this->db->insert(db_prefix() . 'staff_contract_detail', $row_insert);

            }else{

            $row = [];
            $row['since_date'] = to_sql_date($value);
            $expenses = [];
            foreach($salary_forms[$key] as $k => $value){
                $note = [];
                $note['type'] = $value;
                $note['value'] = $contract_expenses[$key][$k];
                $expenses[] = $note;
            }

            $allowance = [];
            foreach($allowance_type[$key] as $h => $value){
                $note = [];
                $note['type'] = $value;
                $note['value'] = $allowance_expense[$key][$h];
                $allowance[] = $note;
            }
            $row['staff_contract_id'] = $id;
            $row['contract_note'] = $contract_note[$key];
            $row['contract_salary_expense'] = json_encode($expenses);
            $row['contract_allowance_expense'] = json_encode($allowance);

            $this->db->where('contract_detail_id', $contract_detail_id[$key - 1]['contract_detail_id']);
            $this->db->update(db_prefix() . 'staff_contract_detail', $row);
        }
        $count_contract_dt = count($contract_detail_id);
        if(count($since_dates) < $count_contract_dt){
            foreach ($contract_detail_id as $key_contract_dt => $contract_dt_value) {
                if(($key_contract_dt + 1) > count($since_dates) ){
                    $this->db->where('contract_detail_id', $contract_dt_value['contract_detail_id']);
                    $this->db->delete(db_prefix() . 'staff_contract_detail');
                }
            }
        }


    }
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    public function delete_contract($id){
        $this->db->where('id_contract', $id);
        $this->db->delete(db_prefix() . 'staff_contract');
        $this->db->where('staff_contract_id', $id);
        $this->db->delete(db_prefix() . 'staff_contract_detail');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    public function get_duration(){
        return $this->db->query('SELECT duration, unit FROM tblstaff_contracttype group by duration, unit')->result_array();
    }
    public function contract_form($data)
    {
    //hrm_contract_form
      $save = json_decode(get_hrm_option('hrm_contract_form')); 
      $save[] = $data;
      $val = '';
      foreach($save as $s){
        if($val == ''){
            $val .= '["'.$s.'"';
        }else
        {   
            $val .= ', "'. $s.'"';
        }
      }
      if($val != ''){
        $val .= ']';
      }
      $this->db->where('option_name', 'hrm_contract_form');
      $this->db->update(db_prefix() . 'hrm_option', [
                    'option_val' => $val,
                ]);
    }

    public function get_records_received($id)
    {
        return $this->db->query('select tblstaff.records_received from tblstaff where staffid = '.$id)->row();

    }

    public function add_attachment_to_database($rel_id, $rel_type, $attachment, $external = false)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['rel_id']    = $rel_id;
        if (!isset($attachment[0]['staffid'])) {
            $data['staffid'] = get_staff_user_id();
        } else {
            $data['staffid'] = $attachment[0]['staffid'];
        }

        if (isset($attachment[0]['task_comment_id'])) {
            $data['task_comment_id'] = $attachment[0]['task_comment_id'];
        }

        $data['rel_type'] = $rel_type;

        if (isset($attachment[0]['contact_id'])) {
            $data['contact_id']          = $attachment[0]['contact_id'];
            $data['visible_to_customer'] = 1;
            if (isset($data['staffid'])) {
                unset($data['staffid']);
            }
        }

        $data['attachment_key'] = app_generate_hash();

        if ($external == false) {
            $data['file_name'] = $attachment[0]['file_name'];
            $data['filetype']  = $attachment[0]['filetype'];
        } else {
            $path_parts            = pathinfo($attachment[0]['name']);
            $data['file_name']     = $attachment[0]['name'];
            $data['external_link'] = $attachment[0]['link'];
            $data['filetype']      = !isset($attachment[0]['mime']) ? get_mime_by_extension('.' . $path_parts['extension']) : $attachment[0]['mime'];
            $data['external']      = $external;
            if (isset($attachment[0]['thumbnailLink'])) {
                $data['thumbnail_link'] = $attachment[0]['thumbnailLink'];
            }
        }
        $this->db->insert(db_prefix() . 'files', $data);
        $insert_id = $this->db->insert_id();


        return $insert_id;
    }

    public function get_hrm_attachments($staffid){


        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $staffid);
        $this->db->where('rel_type', 'hrm_staff_file');

        return $this->db->get(db_prefix() . 'files')->result_array();

    }

    public function get_hrm_attachments_delete($id){

        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'files')->row();
        }
    }

    //function get file for hrm staff
    public function get_file($id, $rel_id = false)
    {
        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        $this->db->where('id', $id);
        $file = $this->db->get('tblfiles')->row();

        if ($file && $rel_id) {
            if ($file->rel_id != $rel_id) {
                return false;
            }
        }

        return $file;
    }

    //delete staff attchement
        public function delete_hrm_staff_attachment($attachment_id)
    {
        $deleted    = false;
        $attachment = $this->get_hrm_attachments_delete($attachment_id);
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(HRM_MODULE_UPLOAD_FOLDER.'/' .$attachment->rel_id.'/'.$attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Contract Attachment Deleted [ContractID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(HRM_MODULE_UPLOAD_FOLDER.'/' .$attachment->rel_id)) {

                $other_attachments = list_files(HRM_MODULE_UPLOAD_FOLDER.'/' .$attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(HRM_MODULE_UPLOAD_FOLDER.'/' .$attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    public function get_staff_role($staff_id){

        return $this->db->query('select r.name
            from '.db_prefix().'staff as s 
                left join '.db_prefix().'roles as r on r.roleid = s.role
            where s.staffid ='.$staff_id)->row();
    }
    public function update_insurance_conditions($data){
        $affectedRows = 0;
        if(isset($data['hrm_setting']['sign_a_labor_contract'])){
            $data['hrm_setting']['sign_a_labor_contract'] = 1;
        }else{
            $data['hrm_setting']['sign_a_labor_contract'] = 0;
        }

        if(isset($data['hrm_setting']['maternity_leave_to_return_to_work'])){
            $data['hrm_setting']['maternity_leave_to_return_to_work'] = 1;
        }else{
            $data['hrm_setting']['maternity_leave_to_return_to_work'] = 0;
        }

        if(isset($data['hrm_setting']['unpaid_leave_to_return_to_work'])){
            $data['hrm_setting']['unpaid_leave_to_return_to_work'] = 1;
        }else{
            $data['hrm_setting']['unpaid_leave_to_return_to_work'] = 0;
        }

        if(isset($data['hrm_setting']['increase_the_premium'])){
            $data['hrm_setting']['increase_the_premium'] = 1;
        }else{
            $data['hrm_setting']['increase_the_premium'] = 0;
        }

        if(isset($data['hrm_setting']['contract_paid_for_unemployment'])){
            $data['hrm_setting']['contract_paid_for_unemployment'] = 1;
        }else{
            $data['hrm_setting']['contract_paid_for_unemployment'] = 0;
        }

        if(isset($data['hrm_setting']['maternity_leave_regime'])){
            $data['hrm_setting']['maternity_leave_regime'] = 1;
        }else{
            $data['hrm_setting']['maternity_leave_regime'] = 0;
        }

        if(isset($data['hrm_setting']['reduced_premiums'])){
            $data['hrm_setting']['reduced_premiums'] = 1;
        }else{
            $data['hrm_setting']['reduced_premiums'] = 0;
        }
        foreach ($data['hrm_setting'] as $name => $val) {
            $this->db->where('option_name',$name);
            $this->db->update(db_prefix() . 'hrm_option', [
                    'option_val' => $val,
                ]);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        }
        return $affectedRows;
    }
    public function get_insurance_type(){
        return $this->db->get(db_prefix().'insurance_type')->result_array();
    }
    public function add_insurance_type($data){
        $data['from_month'] = to_sql_date($data['from_month']);
        $this->db->insert(db_prefix() . 'insurance_type', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function update_insurance_type($data, $id){
        $this->db->where('id',$id);
        $this->db->update(db_prefix().'insurance_type', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }else{
            return false;
        }

    }
    public function delete_insurance_type($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'insurance_type');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function add_insurance($data){
        if(isset($data['from_month'])){
            $from_months = $data['from_month'];
            unset($data['from_month']);
        }
        if(isset($data['formality'])){
            $formalitys = $data['formality'];
            unset($data['formality']);
        }
        if(isset($data['reason'])){
            $reasons = $data['reason'];
            unset($data['reason']);
        }
        if(isset($data['premium_rates'])){
            $premium_rates = $data['premium_rates'];
            unset($data['premium_rates']);
        }
        if(isset($data['id_history'])){
            $id_historis = $data['id_history'];
            unset($data['id_history']);
        }
        $this->db->insert(db_prefix() . 'staff_insurance', $data);
        $insert_id = $this->db->insert_id();
        if(isset($insert_id)){

        }

        return $insert_id;
    }

        public function update_insurance($data, $id){

        if(isset($data['from_month'])){
            $from_months = $data['from_month'];
            unset($data['from_month']);
        }
        if(isset($data['formality'])){
            $formalitys = $data['formality'];
            unset($data['formality']);
        }
        if(isset($data['reason'])){
            $reasons = $data['reason'];
            unset($data['reason']);
        }
        if(isset($data['premium_rates'])){
            $premium_rates = $data['premium_rates'];
            unset($data['premium_rates']);
        }
        if(isset($data['id_history'])){
            $id_historis = $data['id_history'];
            unset($data['id_history']);
        }

        $this->db->where('insurance_id', $id);
        $this->db->update(db_prefix() . 'staff_insurance', $data);



        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

     public function get_month()
    {
        $date = getdate();
        $date_1 = mktime(0, 0, 0, ($date['mon'] - 5), 1, $date['year']);
        $date_2 = mktime(0, 0, 0, ($date['mon'] - 4), 1, $date['year']);
        $date_3 = mktime(0, 0, 0, ($date['mon'] - 3), 1, $date['year']);
        $date_4 = mktime(0, 0, 0, ($date['mon'] - 2), 1, $date['year']);
        $date_5 = mktime(0, 0, 0, ($date['mon'] - 1), 1, $date['year']);
        $date_6 = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
        $date_7 = mktime(0, 0, 0, ($date['mon'] + 1), 1, $date['year']);
        $date_8 = mktime(0, 0, 0, ($date['mon'] + 2), 1, $date['year']);
        $date_9 = mktime(0, 0, 0, ($date['mon'] + 3), 1, $date['year']);
        $date_10 = mktime(0, 0, 0, ($date['mon'] + 4), 1, $date['year']);
        $date_11 = mktime(0, 0, 0, ($date['mon'] + 5), 1, $date['year']);
        $date_12 = mktime(0, 0, 0, ($date['mon'] + 6), 1, $date['year']);
        $month = [ '1' => ['id' => date('Y-m-d', $date_1), 'name' => date('m/Y', $date_1)],
                    '2' => ['id' => date('Y-m-d', $date_2), 'name' => date('m/Y', $date_2)],
                    '3' => ['id' => date('Y-m-d', $date_3), 'name' => date('m/Y', $date_3)],
                    '4' => ['id' => date('Y-m-d', $date_4), 'name' => date('m/Y', $date_4)],
                    '5' => ['id' => date('Y-m-d', $date_5), 'name' => date('m/Y', $date_5)],
                    '6' => ['id' => date('Y-m-d', $date_6), 'name' => date('m/Y', $date_6)],
                    '7' => ['id' => date('Y-m-d', $date_7), 'name' => date('m/Y', $date_7)],
                    '8' => ['id' => date('Y-m-d', $date_8), 'name' => date('m/Y', $date_8)],
                    '9' => ['id' => date('Y-m-d', $date_9), 'name' => date('m/Y', $date_9)],
                    '10' => ['id' => date('Y-m-d', $date_10), 'name' => date('m/Y', $date_10)],
                    '11' => ['id' => date('Y-m-d', $date_11), 'name' => date('m/Y', $date_11)],
                    '12' => ['id' => date('Y-m-d', $date_12), 'name' => date('m/Y', $date_12)],
            ];

        return $month;
    }

    public function get_insurance($id){
        return $this->db->query('select * from '.db_prefix().'staff_insurance where insurance_id = '.$id)->result_array();
    }

    public function get_insurance_history($id){
        return $this->db->query('select * from '.db_prefix().'staff_insurance_history where insurance_id = '.$id)->result_array();
    }
    public function get_insurance_form_staffid($id){
        return $this->db->query('select * from '.db_prefix().'staff_insurance where staff_id = '.$id)->result_array();
    }

    public function get_insurance_history_from_staffid($id){
        return $this->db->query('select * from '.db_prefix().'staff_insurance_history where staff_id = '.$id)->result_array();
    }
    public function get_province(){
        return $this->db->get(db_prefix().'province_city')->result_array();
    }

    public function payment_company($month, $premium){
        $insurancetypes =   $this->db->query(' select * from '.db_prefix().'insurance_type as t  where t.from_month <= "'.$month.'"  order by t.from_month desc limit 1')->result_array();
            $social_company         = 0;
            $labor_accident_company = 0;
            $health_company         = 0;
            $unemployment_company   = 0;

        if(count($insurancetypes) != 0){
            foreach ($insurancetypes as $key => $insurancetype) {
            $social_company         = (float)($insurancetype["social_company"]);
            $labor_accident_company = (float)($insurancetype["labor_accident_company"]);
            $health_company         = (float)($insurancetype["health_company"]);
            $unemployment_company   = (float)($insurancetype["unemployment_company"]);
            }
        }else{
            $social_company         = 0;
            $labor_accident_company = 0;
            $health_company         = 0;
            $unemployment_company   = 0;
        }
    $premium                = (float)($premium);
    return (($premium * $social_company)+ ($premium * $labor_accident_company)+($premium * $health_company)+($premium * $unemployment_company))/100;


    }
    public function payment_worker($month, $premium){
        $insurancetypes =   $this->db->query(' select * from '.db_prefix().'insurance_type as t  where t.from_month <= "'.$month.'"  order by t.from_month desc limit 1')->result_array();
            $social_staff           = 0;
            $labor_accident_staff   = 0;
            $health_staff           = 0;
            $unemployment_staff     = 0;
        if(count($insurancetypes) != 0){
            foreach ($insurancetypes as $key => $insurancetype) {
            $social_staff           = (float)($insurancetype["social_staff"]);
            $labor_accident_staff   = (float)($insurancetype["labor_accident_staff"]);
            $health_staff           = (float)($insurancetype["health_staff"]);
            $unemployment_staff     = (float)($insurancetype["unemployment_staff"]);
            }
        }else{
            $social_staff           = 0;
            $labor_accident_staff   = 0;
            $health_staff           = 0;
            $unemployment_staff     = 0;
        }
    $premium                = (float)($premium);
    return (($premium * $social_staff)+ ($premium * $labor_accident_staff)+($premium * $health_staff)+($premium * $unemployment_staff))/100;
    }
    public function set_leave($data){
        $affectedRows = 0;
        foreach ($data['hrm_setting'] as $name => $val) {
            if($name == 'hrm_leave_position' || $name == 'hrm_leave_contract_type' || $name == 'contract_type_borrow'){
                $this->db->where('option_name',$name);
                $this->db->update(db_prefix() . 'hrm_option', [
                        'option_val' => implode(', ', $val),
                    ]);
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            }else{
                $this->db->where('option_name',$name);
                $this->db->update(db_prefix() . 'hrm_option', [
                        'option_val' => $val,
                    ]);
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            }
            
        }
        return $affectedRows;
    }
    public function add_day_off($data){
        $insert_success = 0;
        foreach ($data['break_date'] as $key => $val) {
            $this->db->insert(db_prefix().'day_off', [
                'off_reason' => $data['leave_reason'],
                'off_type' => $data['leave_type'],
                'break_date' => to_sql_date($val),
                'timekeeping' => $data['timekeeping'][$key],
                'department' => $data['department'][$key],
                'position' => $data['position'][$key],
                'add_from' => get_staff_user_id(),
            ]);
            $insert_id = $this->db->insert_id();
            if($insert_id){
               $insert_success++;
            }
        }
        return $insert_success;
    }
    public function update_day_off($data, $id){
        $this->db->where('id',$id);
        $this->db->update(db_prefix().'day_off',[
            'off_reason' => $data['leave_reason'],
            'off_type' => $data['leave_type'],
            'break_date' => to_sql_date($data['break_date']),
            'timekeeping' => $data['timekeeping'],
            'department' => $data['department'],
            'position' => $data['position'],
            'add_from' => get_staff_user_id(),
        ]);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function get_break_dates($type = ''){
        if($type != ''){
            $this->db->where('off_type', $type);
            return $this->db->get(db_prefix().'day_off')->result_array();
        }else{
            return $this->db->get(db_prefix().'day_off')->result_array();
        }
    }
    public function delete_day_off($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'day_off');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

        public function add_payroll_type($data){

        if(isset($data['financial'])){
            $financials = $data['financial'];
            unset($data['financial']);
        }
        if(isset($data['inputTitleColumn'])){
            $inputTitleColumns = $data['inputTitleColumn'];
            unset($data['inputTitleColumn']);
        }
        $data['department_id'] = json_encode(($data['department_id']));
        $data['role_id'] = json_encode(($data['role_id']));
        $data['position_id'] = json_encode(($data['position_id']));



        $templates = [];

        $data['template'] = json_encode($templates);

        $this->db->insert(db_prefix() . 'payroll_type', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function get_payroll_type($id = false){
        
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'payroll_type')->row();
        }

        if ($id == false) {
           return $departments = $this->db->get(db_prefix() . 'payroll_type')->result_array();
        }

    }

    public function delete_payroll_type($id){
    $this->db->where('id', $id);
    $this->db->delete(db_prefix() . 'payroll_type');
    if ($this->db->affected_rows() > 0) {
        return true;
    }

        return false;
    }

    public function update_payroll_type($data, $id){
        if(isset($data['financial'])){
        $financials = $data['financial'];
        unset($data['financial']);
        }
        if(isset($data['inputTitleColumn'])){
            $inputTitleColumns = $data['inputTitleColumn'];
            unset($data['inputTitleColumn']);
        }

        $data['department_id'] = json_encode(($data['department_id']));
        $data['role_id'] = json_encode(($data['role_id']));
        $data['position_id'] = json_encode(($data['position_id']));


        $templates = [];

        $data['template'] = json_encode($templates);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'payroll_type', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function get_requisition($type){
        return $this->db->query('SELECT rq.id, rq.name, rq.addedfrom, rq.date_create, rq.approval_deadline, rq.status 
        FROM '.db_prefix().'request rq
        LEFT JOIN '.db_prefix().'request_type rqt ON rqt.id = rq.request_type_id
        where rqt.related_to = '.$type)->result_array();
    }
    public function add_work_shift($data){
        $data['from_date'] = to_sql_date($data['from_date']);
        $data['to_date'] = to_sql_date($data['to_date']);
        $data['date_create'] = date('Y-m-d');
        $data['add_from'] = get_staff_user_id();

        $this->db->insert(db_prefix().'work_shift',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            return $insert_id;
        }
    }
    public function update_work_shift($data,$id){
        $data['from_date'] = to_sql_date($data['from_date']);
        $data['to_date'] = to_sql_date($data['to_date']);
        $data['date_create'] = date('Y-m-d');
        $data['add_from'] = get_staff_user_id();
        
        $this->db->where('id',$id);
        $this->db->update(db_prefix().'work_shift',$data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
       
    }
    public function get_data_edit_shift($work_shift){
        $this->db->where('id',$work_shift);
        $shift = $this->db->get(db_prefix().'work_shift')->row();
        $data['shifts_detail'] = $shift->shifts_detail;
        if(isset($data['shifts_detail'])){
             $data['shifts_detail'] = explode ( ',', $data['shifts_detail']);
             $shifts_detail_col = ['detail','monday','tuesday','wednesday','thursday','friday','saturday_even','saturday_odd','sunday'];
             $row = [];
             $shifts_detail = [];
             for ($i=0; $i < count($data['shifts_detail']); $i++) {
                    $row[] = $data['shifts_detail'][$i];
                if((($i+1)%9) == 0){
                    $shifts_detail[] = array_combine($shifts_detail_col, $row);
                    $row = [];
                }
            }
            unset($data['shifts_detail']);
            
        }
        return $shifts_detail;
        
    }
    public function get_shifts($id = ''){
        if($id != ''){
            $this->db->where('id',$id);
            return $this->db->get(db_prefix().'work_shift')->row();
        }else{
            return $this->db->get(db_prefix().'work_shift')->result_array();
        }
    }
    public function delete_shift($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'work_shift');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    
    }

    public function add_payroll_table($data){
       
        $get_payroletype = $this->db->query('select * from '.db_prefix().'payroll_type as pt where pt.id ='. $data['payroll_type']) -> row();

        //get array staff form dp, role, position

        $sql = "SELECT staffid, staff_identifi, email, firstname, phonenumber, sex, birthday, name_account, account_number, issue_bank FROM ".db_prefix().'staff WHERE 1 = 1';

        if($get_payroletype->department_id != 'null'){
            $searchVal = array('[', ']', '"');
            $replaceVal = array('(', ')', '');
           $department_array = str_replace($searchVal, $replaceVal, $get_payroletype->department_id);
        }
        if($get_payroletype->position_id != 'null'){
            $searchVal = array('[', ']', '"');
            $replaceVal = array('(', ')', '');
           $position_array = str_replace($searchVal, $replaceVal, $get_payroletype->position_id);
        }
        if($get_payroletype->role_id != 'null'){
            $searchVal = array('[', ']', '"');
            $replaceVal = array('(', ')', '');
           $role_array = str_replace($searchVal, $replaceVal, $get_payroletype->role_id);
        }

        if(isset($get_payroletype->department_id) && $get_payroletype->department_id != 'null' && $get_payroletype->department_id != '0'){
            $sql .= ' AND staffid in ( select staffid from tblstaff_departments where departmentid in '.$department_array.' )';
        }
        if(isset($get_payroletype->role_id) && $get_payroletype->role_id != 'null' && $get_payroletype->role_id != '0'){
            $sql .= ' AND role in '.$role_array.'';
        }        
        if(isset($get_payroletype->position_id) && $get_payroletype->position_id != 'null' && $get_payroletype->position_id != '0'){
            $sql .= ' AND job_position in ( select position_id from tbljob_position where position_id in '.$position_array.' )';
        }

        $CI = & get_instance();
        $staff_array = $this->db->query($sql)->result_array();
        $date = DateTime::createFromFormat('Y-m-d',$data['payroll_month']);
        $data['payroll_month'] = $date->format('Y-m-d');
        $data_object = [];
        $template = json_decode($get_payroletype->template, true);
        $column_key =[];
        $column_value =[];
        $column_calculation =[];
        $c = 0;

        foreach ($template as $kk => $value) {
            foreach ($value as $key => $v){
                if($key == 'column_value'){
                    $column_value[] = $v;
                }
                if($key == 'column_title'){
                    $column_title[] = $v;
                }
                if($key == 'column_key'){
                    $column_key[] = strtolower($v);
                }
                if($key == 'calculation'){
                    $column_calculation[$column_value[$c%count($column_value)]] = strtolower($v);
                }
            }
            $c++;
        }
        $column_key = sort_array_by_char($column_key,'_');
        foreach ($staff_array as $st_value) {
            $data_object[] = $this->get_data_staff_payroll($st_value['staffid'], $column_key, $column_calculation , $data['payroll_month'] );
        }
        //get payroll template

        $data['template_data'] = json_encode($data_object);
        
        $this->db->insert(db_prefix() . 'payroll_table', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function check_leftjoin_query($column, $select_qr){
        $contract = [];
        $contract = ['contract_code','contract_name','contract_type','official_company_date','business_salary'];
        if(in_array($column, $contract)){
            foreach($contract as $c){
                if (strpos($select_qr, $c) !== false) {
                    return false;
                }
            }
            return true;
        }
        return true;  
    }

    public function get_data_staff_payroll($staffid, $column_key, $column_calculation, $month){

        $this->load->model('goals/goals_model');
        //aray select tbl staff
        $arr_select_tblstaff = array();

        $arr_select_tblstaff['hr_code'] = 'tblstaff.staff_identifi as hr_code';
        $arr_select_tblstaff['firstname'] = 'tblstaff.firstname as firstname';
        $arr_select_tblstaff['sex'] = 'tblstaff.sex as sex';
        $arr_select_tblstaff['email'] = 'tblstaff.email as email';
        $arr_select_tblstaff['birthday'] = 'tblstaff.birthday as birthday';
        $arr_select_tblstaff['phonenumber'] = 'tblstaff.phonenumber as phonenumber';
        $arr_select_tblstaff['issue_bank'] = 'tblstaff.issue_bank as issue_bank';
        $arr_select_tblstaff['name_account'] = 'tblstaff.name_account as name_account';
        $arr_select_tblstaff['account_number'] = 'tblstaff.account_number as account_number';
        $arr_select_tblstaff['month'] = 'month("'.date('Y-m-d').'") as month';
        $arr_select_tblstaff['year'] = 'year("'.date('Y-m-d').'") as year';
        $arr_select_tblstaff['date_total'] = 'day("'.date("Y-m-t", strtotime(date('Y-m-d'))).'") as date_total';
        $arr_select_tblstaff['sunday_total'] = $this->total_day_in_month('sunday').' as sunday_total';
        $arr_select_tblstaff['saturday_total'] = $this->total_day_in_month('saturday').' as saturday_total';
        $arr_select_tblstaff['saturday_total_odd'] = $this->total_day_in_month('saturday_odd').' as saturday_total_odd';
        $arr_select_tblstaff['saturday_total_even'] = $this->total_day_in_month('saturday_even').' as saturday_total_even';
        $arr_select_tblstaff['company_name'] = '"'.get_option('companyname').'"'.' as company_name';

        $arr_leftjoin_select_tblstaff_contract = array();
        $sql_leftjoin_tblstaff_contract = ' LEFT JOIN tblstaff_contract on tblstaff.staffid = tblstaff_contract.staff';

        $arr_leftjoin_select = array();
        $arr_leftjoin_on = array();

        $arr_leftjoin_select['job_position'] = ' tbljob_position.position_name as job_position';
        $arr_leftjoin_on['job_position'] = ' LEFT JOIN tbljob_position on tbljob_position.position_id = tblstaff.job_position';

        $arr_leftjoin_select['name'] = ' tbldepartments.name as name';
        $arr_leftjoin_on['name'] = ' LEFT JOIN tblstaff_departments on tblstaff_departments.staffid = tblstaff.staffid LEFT JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid ' ;

        $arr_leftjoin_select['work_place'] = ' tblworkplace.workplace_name as work_place';
        $arr_leftjoin_on['work_place'] = ' LEFT JOIN tblworkplace on tblstaff.workplace = tblworkplace.workplace_id';

        $arr_leftjoin_select_tblstaff_contract['contract_code'] = ' tblstaff_contract.contract_code as contract_code';

        $arr_leftjoin_select_tblstaff_contract['contract_name'] = ' tblstaff_contract.name_contract as contract_name';
        

        $arr_leftjoin_select['contract_type'] = ' tblstaff_contracttype.name_contracttype as contract_type';
        $arr_leftjoin_on['contract_type'] = ' LEFT JOIN tblstaff_contracttype on tblstaff_contract.name_contract = tblstaff_contracttype.id_contracttype';

        $arr_leftjoin_select_tblstaff_contract['official_company_date'] = ' tblstaff_contract.contract_form as official_company_date';

        $arr_leftjoin_select['business_salary'] = ' tblstaffcontract_detail.contract_salary_expense as business_salary';
        $arr_leftjoin_on['business_salary'] = ' LEFT JOIN tblstaffcontract_detail on tblstaffcontract_detail.staff_contract_id = tblstaff_contract.id_contract';

        $arr_leftjoin_select2 = array();
        $arr_leftjoin_on2 = array();

        $arr_leftjoin_select2['work_number'] = ' COUNT(tblhrm_timesheet.value) as work_number'; 
        $arr_leftjoin_on2['work_number'] = ' LEFT JOIN tblhrm_timesheet on tblhrm_timesheet.staff_id = tblstaff.staffid and type = "W" and month(tblhrm_timesheet.date_work) = month("'.$month.'")';

        $arr_leftjoin_select2['number_work_late'] = ' COUNT(tblhrm_timesheet.value) as number_work_late'; 
        $arr_leftjoin_on2['number_work_late'] = ' LEFT JOIN tblhrm_timesheet on tblhrm_timesheet.staff_id = tblstaff.staffid and type = "L" and month(tblhrm_timesheet.date_work) = month("'.$month.'")';

        $arr_leftjoin_select2['number_leave_company_early'] = ' COUNT(tblhrm_timesheet.value) as number_leave_company_early'; 
        $arr_leftjoin_on2['number_leave_company_early'] = ' LEFT JOIN tblhrm_timesheet on tblhrm_timesheet.staff_id = tblstaff.staffid and type = "E" and month(tblhrm_timesheet.date_work) = month("'.$month.'")';

        $arr_leftjoin_select2['effort_work'] = ' COUNT(tblhrm_timesheet.value) as effort_work'; 
        $arr_leftjoin_on2['effort_work'] = ' LEFT JOIN tblhrm_timesheet on tblhrm_timesheet.staff_id = tblstaff.staffid and type = "W" and month(tblhrm_timesheet.date_work) = month("'.$month.'")';

        $arr_leftjoin_select2['total_work_time'] = ' SUM(tblhrm_timesheet.value) as total_work_time'; 
        $arr_leftjoin_on2['total_work_time'] = ' LEFT JOIN tblhrm_timesheet on tblhrm_timesheet.staff_id = tblstaff.staffid and type = "W" and month(tblhrm_timesheet.date_work) = month("'.$month.'")';

        $arr_leftjoin_select2['total_actual_working_hours'] = ' SUM(tblhrm_timesheet.value) as total_actual_working_hours'; 
        $arr_leftjoin_on2['total_actual_working_hours'] = ' LEFT JOIN tblhrm_timesheet on tblhrm_timesheet.staff_id = tblstaff.staffid and type = "W" and month(tblhrm_timesheet.date_work) = month("'.$month.'")';

        $arr_leftjoin_select2['number_minu_late'] = ' SUM(tblhrm_timesheet.value) as number_minu_late'; 
        $arr_leftjoin_on2['number_minu_late'] = ' LEFT JOIN tblhrm_timesheet on tblhrm_timesheet.staff_id = tblstaff.staffid and type = "L" and month(tblhrm_timesheet.date_work) = month("'.$month.'")';

        $arr_leftjoin_select2['number_minu_early'] = ' SUM(tblhrm_timesheet.value) as number_minu_early'; 
        $arr_leftjoin_on2['number_minu_early'] = ' LEFT JOIN tblhrm_timesheet on tblhrm_timesheet.staff_id = tblstaff.staffid and type = "E" and month(tblhrm_timesheet.date_work) = month("'.$month.'")';

        $arr_leftjoin_select2['number_effort_leave'] = ' SUM(tblhrm_timesheet.value) as number_effort_leave'; 
        $arr_leftjoin_on2['number_effort_leave'] = ' LEFT JOIN tblhrm_timesheet on tblhrm_timesheet.staff_id = tblstaff.staffid and type = "P" and month(tblhrm_timesheet.date_work) = month("'.$month.'")';

        $arr_leftjoin_select2['number_effort_no_leave'] = ' SUM(tblhrm_timesheet.value) as number_effort_no_leave'; 
        $arr_leftjoin_on2['number_effort_no_leave'] = ' LEFT JOIN tblhrm_timesheet on tblhrm_timesheet.staff_id = tblstaff.staffid and type = "A" and month(tblhrm_timesheet.date_work) = month("'.$month.'")';

        $arr_leftjoin_select2['business_sales'] = ' SUM(tblinvoices.total) as business_sales'; 
        $arr_leftjoin_on2['business_sales'] = ' LEFT JOIN tblinvoices on tblinvoices.sale_agent = tblstaff.staffid and (month(tblinvoices.date) <= month("'.$month.'") and tblinvoices.duedate >= "'.$month.'")';

        $arr_leftjoin_select2['actual_sales_turnover'] = ' SUM(tblinvoicepaymentrecords.amount) as actual_sales_turnover'; 
        $arr_leftjoin_on2['actual_sales_turnover'] = ' LEFT JOIN tblinvoices on tblinvoices.sale_agent = tblstaff.staffid and (month(tblinvoices.date) <= month("'.$month.'") and tblinvoices.duedate >= "'.$month.'") LEFT JOIN tblinvoicepaymentrecords on tblinvoicepaymentrecords.invoiceid = tblinvoices.id';

        $arr_leftjoin_select2['business_contract_number'] = ' COUNT(tblcontracts.id) as business_contract_number'; 
        $arr_leftjoin_on2['business_contract_number'] = ' LEFT JOIN tblcustomer_admins on tblcustomer_admins.staff_id = tblstaff.staffid LEFT JOIN tblclients on tblclients.userid = tblcustomer_admins.customer_id LEFT JOIN tblcontracts on tblcontracts.client = tblclients.userid and ( month(tblcontracts.datestart) <= month("'.$month.'") and tblcontracts.dateend >= "'.$month.'" )';

        $arr_leftjoin_select2['business_order_number'] = ' COUNT(tblinvoices.total) as business_order_number'; 
        $arr_leftjoin_on2['business_order_number'] = ' LEFT JOIN tblinvoices on tblinvoices.sale_agent = tblstaff.staffid and (month(tblinvoices.date) <= month("'.$month.'") and tblinvoices.duedate >= "'.$month.'")';

        $arr_salary_position = array();
        $arr_salary_position['salary_l'] = 'SELECT contract_salary_expense, staff_contract_id, since_date FROM tblstaff_contract_detail WHERE since_date=(SELECT MAX(since_date) FROM tblstaff_contract_detail WHERE staff_contract_id = (SELECT id_contract FROM tblstaff_contract WHERE end_valid = (SELECT MAX(end_valid) FROM tblstaff_contract where staff = '.$staffid.') and staff = '.$staffid.'));';

        $arr_salary_allowance = array();
        $arr_salary_allowance['salary_alowance'] = 'SELECT contract_allowance_expense, staff_contract_id, since_date FROM tblstaff_contract_detail WHERE since_date=(SELECT MAX(since_date) FROM tblstaff_contract_detail WHERE staff_contract_id = (SELECT id_contract FROM tblstaff_contract WHERE end_valid = (SELECT MAX(end_valid) FROM tblstaff_contract where staff = '.$staffid.') and staff = '.$staffid.'));';

        $arr_effort_ceremony = array();
        $arr_effort_ceremony['effort_ceremony'] = 'SELECT COUNT(id) as effort_ceremony  FROM tblday_off where month(break_date) = month("'.$month.'") AND off_type = "holiday"';
        
        $arr_monthly_KPI_points = array();
        $arr_monthly_KPI_points['monthly_KPI_points'] = 'SELECT tblgoals.id from  tblgoals where tblgoals.staff_id = "'.$staffid.'" and (month(tblgoals.start_date) <= month("'.$month.'") and tblgoals.end_date >= "'.$month.'")';

        $arr_date_entered_company = array();
        $arr_date_entered_company['date_entered_company'] = 'SELECT MIN(start_valid) as date_entered_company FROM tblstaff_contract where staff = '.$staffid;
        if(isset($column_key['hr_code'])){
            $init_select = ' SELECT  ';
        }else{
            $init_select = ' SELECT tblstaff.staff_identifi as hr_code, ';
        }
        $query_1_select = $init_select;
        $query_1_from = ' FROM tblstaff';
        $query_1_join = '';
        $query_1_where = ' AND tblstaff.staffid = '.$staffid;

        $salary_position = '';
        $salary_alowance = '';
        $effort_ceremony = '';
        $monthly_KPI_points = '';
        $date_entered_company = '';

        $query_2_select = $init_select;
        $query_2_join = '';
        $res1 = array();
        $res2 = array();

        $arr_constants = array();
        $arr_constants['individual_deduction_level'] = 'individual_deduction_level';
        $arr_constants['tax_exemption_level'] = 'tax_exemption_level';
        $arr_constants['hours_date'] = 'hours_date';
        $arr_constants['hours_week'] = 'hours_week';
        $arr_constants['work_time_by_round'] = 'work_time_by_round';
        $arr_constants['total_work_time_by_round'] = 'total_work_time_by_round';
        $arr_constants['effort_by_round'] = 'effort_by_round';
        $arr_constants['business_commission'] = 'business_commission';
        $arr_constants['hours_salary'] = 'hours_salary';
        $arr_constants['salary_day'] = 'salary_day';
        $arr_constants['number_of_dependents'] = 'number_of_dependents';

        for ($i = 0; $i < count($column_key); $i++) {
            $column = $column_key[$i];
            $comma = ',';            
            if(isset($arr_select_tblstaff[$column])){
                $query_1_select.= $arr_select_tblstaff[$column].$comma;
            }
            if(isset($arr_leftjoin_select[$column])){
                $query_1_select.= $arr_leftjoin_select[$column].$comma;
                $query_1_join.= $arr_leftjoin_on[$column];
            }
            if(isset($arr_leftjoin_select_tblstaff_contract[$column])){
                if($this->check_leftjoin_query($column,$query_1_select)){
                    $query_1_join .= $sql_leftjoin_tblstaff_contract;
                }
                $query_1_select.= $arr_leftjoin_select_tblstaff_contract[$column].$comma;
            }

            if(isset($arr_leftjoin_select2[$column])){

                $query_2_select = ' SELECT '. $arr_leftjoin_select2[$column].$comma;
                $query_2_join = $arr_leftjoin_on2[$column];
                if (strpos($query_2_select, ',') !== false) {
                    $query_2_select = substr($query_2_select, 0, -1);
                }
                $query_2 = $query_2_select.$query_1_from.$query_2_join.' where 1 = 1'.$query_1_where.';';
                
                if(strlen($query_2_select) > strlen($init_select)){
                    $res2[$column] = (float)$this->db->query($query_2)->row()->$column;
                }
            }

            if(isset($arr_salary_position[$column])){
                $salary_position = $arr_salary_position[$column];
            }

            if(isset($arr_salary_allowance[$column])){
                $salary_alowance = $arr_salary_allowance[$column];
            }

            if(isset($arr_effort_ceremony[$column])){
                $effort_ceremony = $arr_effort_ceremony[$column];
            }

            if(isset($arr_monthly_KPI_points[$column])){
                $monthly_KPI_points = $arr_monthly_KPI_points[$column];
            }

            if(isset($arr_date_entered_company[$column])){
                $date_entered_company = $arr_date_entered_company[$column];
            }

            if(isset($arr_constants[$column])){
                $res2[$column] = $column_calculation[$column];
            }
        }

        if (strpos($query_1_select, ',') !== false) {
            $query_1_select = substr($query_1_select, 0, -1);
        }
        
        $query_1 = $query_1_select.$query_1_from.$query_1_join.' where 1 = 1'.$query_1_where.';';
        
        if(strlen($query_1_select) > strlen($init_select)){
            $res1 = $this->db->query($query_1)->result_array();
        }

        if($salary_position != ''){
            $js = $this->db->query($salary_position)->row();
            $rs_salary_position = [];
            $rs_salary_position['salary_l'] = 0;
            if(isset($js)){
                $res_salary_position = json_decode($js->contract_salary_expense);
                if($res_salary_position != ''){
                    foreach($res_salary_position as $salary){
                        $rs_salary_position['salary_l'] += $salary->value;
                    }
                }
                
            } 
            array_push($res1, $rs_salary_position); 
        } 

        if($salary_alowance != ''){
            $js = $this->db->query($salary_alowance)->row();
            $rs_salary_alowance = [];
            $rs_salary_alowance['salary_alowance'] = 0;
            if(isset($js)){
                $res_salary_alowance = json_decode($js->contract_allowance_expense);
                if($res_salary_alowance){
                    foreach($res_salary_alowance as $salary){
                        $rs_salary_alowance['salary_alowance'] += $salary->value;
                    }
                }
            } 
            array_push($res1, $rs_salary_alowance);
        } 

        if($effort_ceremony != ''){
            $res_effort_ceremony = [];
            $res_effort_ceremony['effort_ceremony'] = $this->db->query($effort_ceremony)->row()->effort_ceremony;
            
           array_push($res1, $res_effort_ceremony); 
        } 

        if($monthly_KPI_points != ''){
            $res_monthly_KPI_points = [];
            $res_monthly_KPI_points['monthly_KPI_points'] = $this->db->query($monthly_KPI_points)->result_array();
            $rs_monthly_KPI_points = [];
            $avg = 0;
            $mark = 0;
            foreach($res_monthly_KPI_points['monthly_KPI_points'] as $mkp){
                $mark += $this->goals_model->calculate_goal_achievement($mkp['id'])['percent'];
                $avg++;   
            }
            $rs_monthly_KPI_points['monthly_KPI_points'] = number_format($mark/$avg,2);
            array_push($res1, $rs_monthly_KPI_points); 
        }
        
        if($date_entered_company != ''){
            $res_date_entered_company = [];
            $res_date_entered_company['date_entered_company'] = $this->db->query($date_entered_company)->row()->date_entered_company;
            
           array_push($res1, $res_date_entered_company); 
        }

        array_push($res1, $res2);
        
        $data_return = [];
        foreach($res1 as $rt){
            if( is_array($rt) > 0){
                $data_return = array_merge($data_return,$rt);
            }
  
        }

        $arr_formula = array();
        $arr_formula['salary_insurance_'] = 'salary_insurance_';
        $arr_formula['salary_allowance_tax'] = 'salary_allowance_tax';
        $arr_formula['salary_allowance_no_taxable'] = 'salary_allowance_no_taxable';
        $arr_formula['penalty_timekeeping'] = 'penalty_timekeeping';
        $arr_formula['effort_work_late'] = 'effort_work_late';
        $arr_formula['effort_work_early'] = 'effort_work_early';
        $arr_formula['effort_leave_without_reason'] = 'effort_leave_without_reason';
        $arr_formula['total_money'] = 'total_money';
        $arr_formula['salary_transferred_company_account'] = 'salary_transferred_company_account';
        $arr_formula['salary_transferred_personal_account'] = 'salary_transferred_personal_account';
        $arr_formula['salary_paid'] = 'salary_paid';
        $arr_formula['unpaid_wages'] = 'unpaid_wages';
        $arr_formula['total_income'] = 'total_income';
        $arr_formula['income_taxes'] = 'income_taxes';
        $arr_formula['personal_income_tax'] = 'personal_income_tax';
        $arr_formula['formula'] = 'formula';

        foreach ($arr_formula as $key => $value) {

            if(in_array($key,$column_key)){
                $str_fomular = $column_calculation[$key];
                for ($i=0;$i<count($column_key);$i++) {
                    $value = $column_key[$i];
                    if(isset($data_return[$value])){
                        $str_fomular = str_replace($value, $data_return[$value], $str_fomular);

                    }elseif(isset($arr_constants[$value])){
                        $str_fomular = str_replace($value, $column_calculation[$value], $str_fomular);
                    }

                }

                $data_return[$key] = eval('return '.$str_fomular.';');
            } 
        }
        return $data_return;
       
    }
    public function total_day_in_month($type){
        $month      = date('m');
        $month_year = date('Y');
        $resultsun = 0;
        $result_satur = 0;
        $result_sat_even = 0;
        $result_sat_odd = 0;
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $month_year);
            if(date('N', $time) == 7){
                $resultsun++ ;
            }elseif(date('N', $time) == 6 && (date('d', $time)%2) == 1){
                $result_sat_odd++ ;
            }elseif(date('N', $time) == 6 && (date('d', $time)%2) == 0){
                $result_sat_even++ ;
            }

            if(date('N', $time) == 6 ){
                $result_satur++;
            }
        }
        if($type == 'sunday'){
            return $resultsun;
        }elseif($type == 'saturday_odd'){
            return $result_sat_odd;
        }elseif($type == 'saturday_even'){
            return $result_sat_even;
        }elseif ($type == 'saturday') {
            return $result_satur;
        }          
    }


    public function get_payroll_table($id = false){
        
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'payroll_table')->row();
        }

        if ($id == false) {
           return  $this->db->get(db_prefix() . 'payroll_table')->result_array();
        }

    }

public function evalmath($equation)
{
    $result = 0;
    // sanitize imput
    $equation = preg_replace("/[^a-z0-9+\-.*\/()%]/","",$equation);
    // convert alphabet to $variabel 
    $equation = preg_replace("/([a-z])+/i", "\$$0", $equation); 
    // convert percentages to decimal
    $equation = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$equation);
    $equation = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$equation);
    $equation = preg_replace("/([0-9]{1})(%)/",".0\$1",$equation);
    $equation = preg_replace("/([0-9]+)(%)/",".\$1",$equation);
    if ( $equation != "" ){
        $result = @eval("return " . $equation . ";" );
    }
    if ($result == null) {
        throw new Exception("Unable to calculate equation");
    }
   return $result;
}
    public function add_update_timesheet($data){
        $results = 0;
        foreach ($data as $row) {
            foreach($row as $key => $val){
                if($key != 'staff_id' && $key != 'hr_code' && $key != 'staff_name'){
                    $ts = explode('-', $val);
                    foreach($ts as $ex){
                    $value = explode(':', $ex);
                    $this->db->where('staff_id', $row['staff_id']);
                    $this->db->where('date_work', $key);
                    $this->db->where('type', $value[0]);
                    $isset = $this->db->get(db_prefix().'hrm_timesheet')->row();

                    if(isset($isset)){
                            $this->db->where('staff_id', $row['staff_id']);
                            $this->db->where('date_work', $key);
                            $this->db->where('type', $value[0]);
                            $this->db->update(db_prefix().'hrm_timesheet',[
                                'value' => $value[1],
                                'add_from' => get_staff_user_id(),
                                'type' => $value[0],
                            ]);
                            if($this->db->affected_rows() > 0){
                               $results++;
                            }
                        
                        
                    }else{
                        if($val != ''){
                                $this->db->insert(db_prefix().'hrm_timesheet',[
                                    'staff_id' => $row['staff_id'],
                                    'date_work' => $key,
                                    'value' => $value[1],
                                    'add_from' => get_staff_user_id(),
                                    'type' => $value[0],
                                ]);
                                $insert_id = $this->db->insert_id();
                                if($insert_id){
                                    $results++;
                                }
                            }
                        
                        }
                    }
                }
            }
        }

        return $results;
    }

    public function get_hrm_ts_by_month($month){
        return $this->db->query('select * from '.db_prefix().'hrm_timesheet where month(date_work) = '.$month)->result_array();
    }
    public function get_ts_by_date_and_staff($date,$staff){
        $this->db->where('date_work', $date);
        $this->db->where('staff_id', $staff);
        return $this->db->get(db_prefix().'hrm_timesheet')->result_array();
    }
    public function get_hrm_dashboard_data(){
        $data_hrm = [];
        $staff = $this->staff_model->get();
        $total_staff = count($staff);
        $new_staff_in_month = $this->db->query('SELECT * FROM tblstaff WHERE MONTH(datecreated) = '.date('m').' AND YEAR(datecreated) = '.date('Y'))->result_array();
        $staff_working = $this->db->query('SELECT * FROM tblstaff WHERE status_work = "working"')->result_array();
		$staff_birthday = $this->db->query('SELECT * FROM tblstaff WHERE DATEDIFF(NOW(),`birthday`)%365 BETWEEN 0 AND 7 ORDER BY birthday ASC')->result_array();
        $staff_inactivity = $this->db->query('SELECT * FROM tblstaff WHERE status_work = "inactivity" AND MONTH(date_update) = '.date('m').' AND YEAR(date_update) = '.date('Y'))->result_array();
        $overdue_contract = $this->db->query('SELECT * FROM tblstaff_contract WHERE end_valid < "'.date('Y-m-d').'"')->result_array();
        $expire_contract = $this->db->query('SELECT * FROM tblstaff_contract WHERE end_valid <= "'.date('Y-m-d',strtotime('+7 day',strtotime(date('Y-m-d')))).'" AND end_valid >= "'.date('Y-m-d').'"')->result_array();
        $data_hrm['staff_birthday'] = $staff_birthday;
        $data_hrm['total_staff'] = $total_staff;
        $data_hrm['new_staff_in_month'] = count($new_staff_in_month);
        $data_hrm['staff_working'] = count($staff_working);
        $data_hrm['staff_inactivity'] = count($staff_inactivity);
        $data_hrm['overdue_contract'] = count($overdue_contract);
        $data_hrm['expire_contract'] = count($expire_contract);
        $data_hrm['overdue_contract_data'] = $overdue_contract;
        $data_hrm['expire_contract_data'] = $expire_contract;
        return $data_hrm;
    }
    
    public function staff_chart_by_age()
    {
        $staffs = $this->staff_model->get();

        $chart = [];
        $status_1 = ['name' => _l('18-24'), 'color' => '#777', 'y' => 0, 'z' => 100];
        $status_2 = ['name' => _l('25-29'), 'color' => '#fc2d42', 'y' => 0, 'z' => 100];
        $status_3 = ['name' => _l('30-39'), 'color' => '#03a9f4', 'y' => 0, 'z' => 100];
        $status_4 = ['name' => _l('40-60'), 'color' => '#ff6f00', 'y' => 0, 'z' => 100];
        
        foreach ($staffs as $staff) {

        $diff = date_diff(date_create(), date_create($staff['birthday']?? ''));
        $age = $diff->format('%Y');
		
          if($age >= 18 && $age <= 24)
          {
            $status_1['y'] += 1;
          }elseif ($age >= 25 && $age <= 29) {
            $status_2['y'] += 1;
          }elseif ($age >= 30 && $age <= 39) {
            $status_3['y'] += 1;
          }elseif ($age >= 40 && $age <= 60) {
            $status_4['y'] += 1;
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

        return $chart;
    }

    public function contract_type_chart()
    {
        $contracts = $this->db->query('SELECT * FROM tblstaff_contract')->result_array();
        $statuses = $this->get_contracttype();
        $color_data = ['#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
        '#63b598', '#ce7d78', '#ea9e70', '#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1' ,
        '#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];


        $_data                         = [];
        //percent
        $total_value =0;
        $has_permission = has_permission('projects', '', 'view');
        $sql            = '';
        
        foreach ($statuses as $status) {
            $sql .= ' SELECT COUNT(*) as total';
            $sql .= ' FROM ' . db_prefix() . 'staff_contract';
            $sql .= ' WHERE name_contract=' . $status['id_contracttype'];
            $sql .= ' UNION ALL ';
            $sql = trim($sql);
        }

        $result = [];
        if ($sql != '') {
            // Remove the last UNION ALL
            $sql    = substr($sql, 0, -10);
            $result = $this->db->query($sql)->result();
        }
        foreach ($statuses as $key => $status) {
              $total_value+=(int)$result[$key]->total;
			  if ( $total_value == null ) { $total_value = '1'; }
          }

        foreach ($statuses as $key => $status) {
         array_push($_data,
            [ 
                'name' => $status['name_contracttype'],
                'y'    => (int)$result[$key]->total,
                'z'    => (number_format(((int)$result[$key]->total/$total_value), 4, '.',""))*100,
                'color'=>$color_data[$key]
            ]);
        }
        return $_data;
    }
    public function get_department_tree(){
        $department =  $this->db->query('select  departmentid as id, parent_id as pid, name
            from    (select departmentid, parent_id, d.name as name
        from '.db_prefix().'departments as d         
        order by d.parent_id, d.departmentid) departments_sorted,
        (select @pv := "0") initialisation
        where   find_in_set(parent_id, @pv)
                and     length(@pv := concat(@pv, ",", departmentid))')->result_array();
        
        $dep_tree = array();
        foreach ($department as $dep) {
            if($dep['pid']==0){
                $node = array();
                $node['id'] = $dep['id'];
                $node['title'] = $dep['name'];
                $node['subs'] = $this->get_child_node($dep['id'], $department);

                $dep_tree[] = $node;
            } else {
                break;
            }            
        }                      
        return $dep_tree;
    }

    /**
     * Get child node of department tree
     * @param  $id      current department id
     * @param  $arr_dep department array
     * @return current department tree
     */
    private function get_child_node($id, $arr_dep){
        $dep_tree = array();
        foreach ($arr_dep as $dep) {
            if($dep['pid']==$id){   
                $node = array();             
                $node['id'] = $dep['id'];
                $node['title'] = $dep['name'];
                $node['subs'] = $this->get_child_node($dep['id'], $arr_dep);
                if(count($node['subs'])==0){
                    unset($node['subs']);
                }
                $dep_tree[] = $node;
            } 
        } 
        return $dep_tree;
    }

    public function delete_payroll_table($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'payroll_table');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function update_payroll_table_status($id,$data){
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'payroll_table',$data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function get_paysplip_bystafff($id){
        $arr_data =[];
        $paysplip_header =[];
        $paysplip_month =[];
        $staff = $this->get_staff($id);
        if(isset($staff)){
            $hr_code = $staff->staff_identifi;
        }
        $this->db->where('status', 1);
        $js = $this->db->get(db_prefix().'payroll_table')->result_array();
        foreach ($js as $js_value) {
            if(isset($js_value)){
                $res_template = json_decode($js_value['template_data'],true);
                if($res_template){
                    foreach($res_template as  $re_value){
                        $arr_temp =[];

                            foreach ($re_value as $key => $value) {
                                $arr_temp[$key] = $value;

                            $paysplip_header[] = $re_value ;
                            $paysplip_month[] = $js_value['payroll_month'] ;
                            
                        
                        }

                    }
                }
            }
            
        }
        $arr_data[] = $paysplip_month;
        $arr_data[] = $paysplip_header;
       return $arr_data;
    }
    
    public function column_type ($header_key, $get_column = ''){

            $column_value_CT = ['salary_insurance_',
                    'salary_allowance_tax',
                    'salary_allowance_no_taxable',
                    'individual_deduction_level',
                    'tax_exemption_level',
                    'hours_date',
                    'hours_week',
                    'work_time_by_round',
                    'total_work_time_by_round',
                    'effort_by_round',
                    'penalty_timekeeping',
                    'effort_work_late',
                    'effort_work_early',
                    'effort_leave_without_reason',
                    'business_commission',
                    'salary_day',
                    'hours_salary',
                    'total_money',
                    'salary_transferred_company_account',
                    'salary_transferred_personal_account',
                    'salary_paid',
                    'unpaid_wages',
                    'number_of_dependents',
                    'total_income',
                    'income_taxes',
                    'personal_income_tax',
                    'formula',
                    'constant',
                    'salary_l',
                    'salary_alowance',
                    ];
              $column_value_L =['work_number',
                  'date_entered_company',
                  'contract_code',
                  'month',
                  'date_total',
                  'sunday_total',
                  'saturday_total',
                  'saturday_total_odd',
                  'saturday_total_even',
                  'total_work_time',
                  'effort_ceremony',
                  'number_work_late',
                  'number_leave_company_early',
                  'number_minu_late',
                  'number_minu_early',
                  'number_effort_leave',
                  'number_effort_no_leave',
                  'effort_work',
                  'total_actual_working_hours',
                  'business_sales',
                  'actual_sales_turnover',
                  'Business_contract_number',
                  'business_order_number'
              ];


        if(is_numeric($get_column)){
            return array_merge($column_value_CT, $column_value_L);
        }else{
            $column = [];
            foreach($header_key as $key){
                if(in_array($key,$column_value_CT)){
                    $column[] = ['data' => $key, 'type' => 'numeric','numericFormat' => ['pattern' => '0,0']];
                }else{
                    $column[] = ['data' => $key, 'type' => 'text'];
                }
            }

            return $column;
        }

    }

}

