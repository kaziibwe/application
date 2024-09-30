<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Authentication extends App_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->app->is_db_upgrade_required()) {
            redirect(admin_url());
        }

        load_admin_language();
        $this->load->model('Authentication_model');
        $this->load->library('form_validation');

        $this->form_validation->set_message('required', _l('form_validation_required'));
        $this->form_validation->set_message('valid_email', _l('form_validation_valid_email'));
        $this->form_validation->set_message('matches', _l('form_validation_matches'));

        hooks()->do_action('admin_auth_init');
    }

    public function index()
    {
        if (is_staff_logged_in()) {
            redirect(admin_url());
        }

        $this->form_validation->set_rules('whatsapp_auth_code', _l('two_factor_authentication_code'), 'required');

        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $whatsapp_auth_code  = $this->input->post('whatsapp_auth_code');
                $whatsapp_auth_code  = trim($whatsapp_auth_code);
                $email = $this->session->userdata('_whatsapp_auth_staff_email');

                if ($this->whatsapp_api_model->isWhatsappAuthCodeValid($whatsapp_auth_code, $email)) {
                    $this->session->unset_userdata('_whatsapp_auth_staff_email');

                    $user = $this->whatsapp_api_model->getUserByWhatsappAuthCode($whatsapp_auth_code);
                    $this->whatsapp_api_model->clearWhatsappAuthCode($user->staffid);
                    $this->whatsapp_api_model->whatsappAuthLogin($user);

                    $this->load->model('announcements_model');
                    $this->announcements_model->set_announcements_as_read_except_last_one(get_staff_user_id(), true);

                    maybe_redirect_to_previous_url();

                    hooks()->do_action('after_staff_login');
                    redirect(admin_url());
                } else {
                    set_alert('danger', _l('whatsapp_auth_code_not_valid'));
                    redirect(admin_url(MMB_MODULE . '/whatsapp_api_authentication'));
                }
            }
        }

        $this->load->view('set_two_factor_auth_code_via_whatsapp');
    }
}

/* End of file Whatsapp_api_authentication.php */
