<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Whatsapp_api_model extends App_Model
{

    private $whatsapp_api_log_table;

    public function __construct()
    {
        parent::__construct();
        $this->whatsapp_api_log_table = db_prefix() . 'whatsapp_api_debug_log';
    }

    public function get_template_data($id)
    {
        return $this->db->get_where(db_prefix() . 'whatsapp_templates', ['id' => $id])->row();
    }

    public function save_template_map_info($map_info)
    {
        return $this->db->insert(db_prefix() . 'whatsapp_templates_mapping', $map_info);
    }

    public function update_template_map_info($map_info, $where)
    {
        return $this->db->update(db_prefix() . 'whatsapp_templates_mapping', $map_info, $where);
    }

    public function delete_whatsapp_templates_mapping($id)
    {
        return $this->db->delete(db_prefix() . 'whatsapp_templates_mapping', $id);
    }

    public function get_mapping_data($where)
    {
        return $this->db
            ->select(db_prefix() . 'whatsapp_templates_mapping.*')
            ->select('wt.template_name, wt.language, wt.header_data_format, wt.header_data_text, wt.body_data, wt.footer_data, wt.buttons_data, wt.header_params_count, wt.body_params_count, wt.footer_params_count, whatsapp_templates_mapping.send_to')
            ->join(db_prefix() . 'whatsapp_templates wt', db_prefix() . 'whatsapp_templates_mapping.template_id = wt.id')
            ->get_where(db_prefix() . 'whatsapp_templates_mapping', $where)->result();
    }

    public function clear_webhook_log()
    {
        if ($this->db->truncate($this->whatsapp_api_log_table)) {
            return true;
        }

        return false;
    }

    public function get_whatsapp_api_log_info($id)
    {
        return $this->db->get_where(db_prefix() . 'whatsapp_api_debug_log', ['id' => $id])->row();
    }

    public function change_whatsapp_template_status($data, $where)
    {
        if ($this->db->update(db_prefix() . 'whatsapp_templates_mapping', $data, $where)) {
            return true;
        }

        return false;
    }

    public function add_request_log($data)
    {
        return $this->db->insert(db_prefix() . 'whatsapp_api_debug_log', $data);
    }

    public function updateWhatsappAuthStatus($data)
    {
        $status = $data['whatsapp_auth_enabled'];

        if ($status == '1') {
            $data['two_factor_auth_enabled'] = '0';
        }

        return $this->db->update(db_prefix() . 'staff', $data, ['staffid' => get_staff_user_id()]);
    }

    public function isWhatsappAuthCodeValid($code, $email)
    {
        $this->db->select('whatsapp_auth_code_requested');
        $this->db->where('whatsapp_auth_code', $code);
        $this->db->where('email', $email);
        $user = $this->db->get(db_prefix() . 'staff')->row();

        if (!$user) {
            return false;
        }

        $timestamp_minus_1_hour = time() - (60 * 60);
        $new_code_key_requested = strtotime($user->whatsapp_auth_code_requested);
        
        if ($timestamp_minus_1_hour > $new_code_key_requested) {
            return false;
        }

        return true;
    }

    public function getUserByWhatsappAuthCode($code)
    {
        $this->db->where('whatsapp_auth_code', $code);

        return $this->db->get(db_prefix() . 'staff')->row();
    }

    public function clearWhatsappAuthCode($id)
    {
        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', [
            'whatsapp_auth_code' => null,
        ]);

        return true;
    }

    public function whatsappAuthLogin($user)
    {
        $this->session->set_userdata([
            'staff_user_id'   => $user->staffid,
            'staff_logged_in' => true,
        ]);

        $remember = null;
        if ($this->session->has_userdata('tfa_remember')) {
            $remember = true;
            $this->session->unset_userdata('tfa_remember');
        }

        if ($remember) {
            $this->create_autologin($user->staffid, true);
        }

        $this->update_login_info($user->staffid, true);

        return true;
    }

    private function create_autologin($user_id, $staff)
    {
        $this->load->helper('cookie');
        $key = substr(md5(uniqid(rand() . get_cookie($this->config->item('sess_cookie_name')))), 0, 16);
        $this->user_autologin->delete($user_id, $key, $staff);
        if ($this->user_autologin->set($user_id, md5($key), $staff)) {
            set_cookie([
                'name'  => 'autologin',
                'value' => serialize([
                    'user_id' => $user_id,
                    'key'     => $key,
                ]),
                'expire' => 60 * 60 * 24 * 31 * 2, // 2 months
            ]);

            return true;
        }

        return false;
    }

    private function update_login_info($user_id, $staff)
    {
        $table = db_prefix() . 'contacts';
        $_id   = 'id';
        if ($staff == true) {
            $table = db_prefix() . 'staff';
            $_id   = 'staffid';
        }
        $this->db->set('last_ip', $this->input->ip_address());
        $this->db->set('last_login', date('Y-m-d H:i:s'));
        $this->db->where($_id, $user_id);
        $this->db->update($table);

        log_activity('User Successfully Logged In [User Id: ' . $user_id . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');
    }

}
