<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Client extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('assets_model');
    }

    public function index()
    {
        if (!is_client_logged_in() && !has_contact_permission('asset')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $data                       =[];
        $data['title']              = _l('assets');
        $client_user_id                                              = $this->session->userdata('client_user_id');
        $where["find_in_set('".$client_user_id."',`belongs_to`) <>"] = 0;
        $data['allocated_asset']                                     = $this->assets_model->get_clients_assign_assets('assets', $where);

        if (empty($data['allocated_asset'])) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $this->data($data);
        $this->view('assets/table_assets_client');
        $this->layout();
    }
}

// End of file Client.php
// Location: ./application/controllers/Client.php
