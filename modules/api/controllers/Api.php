<?php

use \WpOrg\Requests\Requests as RestapiRequests;

defined('BASEPATH') or exit('No direct script access allowed');

class Api extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('api_model');
        $this->load->library('app_modules');

        if (!$this->app_modules->is_active('api')) {
            access_denied("Api");
        }
    }

    public function api_management()
    {
        $data['user_api'] = $this->api_model->get_user();
        $data['title'] = _l('api_management');
        $this->load->view('api_management', $data);
    }

    public function api_guide()
    { 
        fopen(APP_MODULES_PATH . 'api/views/apidoc/index.html', 'r');
    }

    /* Add new user or update existing*/
    public function user()
    {
        if (!is_admin()) {
            access_denied('Ticket Priorities');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->api_model->add_user($this->input->post());
               
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('user_api')));
                }
                redirect(admin_url('api/api_management'));
            } else {
                $data           = $this->input->post();
                $id             = $data['id'];
                unset($data['id']);
                $success = $this->api_model->update_user($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('user_api')));
                }
                redirect(admin_url('api/api_management'));
            }
            die;
        }
    }

    /* Edit user */
    public function create_user()
    {
        if (!is_admin()) {
            access_denied('User');
        }
        $data['title'] = _l('new_user_api');
        $this->load->view('create_user_api', $data);
    }

    /* Edit user */
    public function edit_user($id)
    {
        if (!is_admin()) {
            access_denied('User');
        }
        if (!$id) {
            redirect(admin_url('api/api_management'));
        }
        $user_api = $this->api_model->get_user($id);
        $data['user_api'] = $user_api && count($user_api) ? $user_api[0] : null;
        $data['title'] = _l('edit_user_api');
        $this->load->view('edit_user_api', $data);
    }

    /* Delete user */
    public function delete_user($id)
    {
        if (!is_admin()) {
            access_denied('User');
        }
        if (!$id) {
            redirect(admin_url('api/api_management'));
        }
        $response = $this->api_model->delete_user($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('user_api')));
        }
        redirect(admin_url('api/api_management'));
    }
}