<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('perfex_dashboard_model');
  }

  public function index()
  {
    if (!has_permission('perfex_dashboard', '', 'dashboard_settings')) {
        access_denied('perfex_dashboard');
    }
    $data['title'] = _l('settings_dashboard');
    $this->load->view('settings/index', $data);
  }
}
