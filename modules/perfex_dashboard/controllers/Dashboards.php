<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboards extends AdminController
{
  protected static $DEFAULT_DASHBOARD_NAME = 'New Dashboard';

  public function __construct()
  {
    parent::__construct();
    $this->load->model('perfex_dashboard_model');
  }

  public function my_dashboard()
  {
    if (!has_permission('perfex_dashboard', '', 'my_dashboard_view')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'GET') {
      show_404();
      die();
    }

    $dashboard_id = $this->input->get('dashboard_id');

    $user = get_staff($this->session->userdata('tfa_staffid'));

    $user_dashboards = $this->perfex_dashboard_model->get_dashboards_by_user($user->staffid);
    $data['user_dashboards'] = $user_dashboards;

    $dashboard = null;
    if (isset($dashboard_id)) {
      foreach ($user_dashboards as $user_dashboard) {
        if ($user_dashboard['id'] == $dashboard_id) {
          $dashboard = $user_dashboard;
          $dashboard['dashboard_widgets'] = unserialize($dashboard['dashboard_widgets']);
          break;
        }
      }
    }

    if (!isset($dashboard) && count($user_dashboards) > 0) {
      $dashboard = $user_dashboards[0];
      $dashboard['dashboard_widgets'] = unserialize($dashboard['dashboard_widgets']);
    }

    $data['dashboard'] = $dashboard;

    $data['active_dashboard_id'] = isset($dashboard) ? $dashboard['id'] : '';

    add_calendar_assets();

    $data['title'] = _l('my_dashboard');
    $this->load->view('dashboards/my_dashboard', $data);
  }

  public function index()
  {
    if (!has_permission('perfex_dashboard', '', 'all_dashboard_view')) {
      access_denied('perfex_dashboard');
    }
    $dashboards = $this->perfex_dashboard_model->get_dashboards();
    $data['dashboards'] = $dashboards;

    $data['title'] = _l('perfex_dashboard_dashboard_index_title_page');
    $this->load->view('dashboards/index', $data);
  }

  public function edit_dashboard()
  {
    if (!has_permission('perfex_dashboard', '', 'dashboard_edit')) {
      access_denied('perfex_dashboard');
    }
    $dashboard_id = $this->input->get('dashboard_id');
    if (!isset($dashboard_id)) {
      show_404();
      die();
    }

    $dashboard_rows = $this->perfex_dashboard_model->find_dashboard($dashboard_id);
    if (count($dashboard_rows) == 0) {
      show_404();
      die();
    }

    $dashboard = $dashboard_rows[0];
    $dashboard['dashboard_widgets'] = unserialize($dashboard['dashboard_widgets']);

    $data['dashboard'] = $dashboard;
    add_calendar_assets();

    $data['staff'] = $this->staff_model->get('', ['active' => 1]);

    $dashboard_staff = $this->perfex_dashboard_model->get_dashboard_staff($dashboard['id']);
    $data['dashboard_staff'] = $dashboard_staff;

    $data['title'] = _l('perfex_dashboard_edit_dashboard_title_page', $dashboard['name']);
    $this->load->view('dashboards/edit', $data);
  }

  public function store_dashboard()
  {
    if (!has_permission('perfex_dashboard', '', 'dashboard_create')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      show_404();
      die();
    }

    $dashboard_name = $this->input->post('name');
    if (!isset($dashboard_name)) {
      $dashboard_name = static::$DEFAULT_DASHBOARD_NAME;
    }

    $note = $this->input->post('note');
    if (!isset($note)) {
      $note = '';
    }

    $dashboard_id = $this->perfex_dashboard_model->create_dashboard($dashboard_name, $note);

    set_alert('success', _l('perfex_dashboard_message_success_create_dashboard'));
    redirect(admin_url('perfex_dashboard/dashboards'));
  }

  public function clone_dashboard()
  {
    if (!has_permission('perfex_dashboard', '', 'dashboard_clone')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      show_404();
      die();
    }

    $clone_id = $this->input->post('clone_id');
    if (!isset($clone_id)) {
      show_404();
      die();
    }

    $dashboard_name = $this->input->post('name');
    if (!isset($dashboard_name)) {
      $dashboard_name = static::$DEFAULT_DASHBOARD_NAME;
    }

    $note = $this->input->post('note');
    if (!isset($note)) {
      $note = '';
    }

    $dashboard_id = $this->perfex_dashboard_model->clone_dashboard($clone_id, $dashboard_name, $note);

    set_alert('success', _l('perfex_dashboard_message_success_clone_dashboard'));
    redirect(admin_url('perfex_dashboard/dashboards/edit_dashboard?dashboard_id=' . $dashboard_id));
  }

  public function update_dashboard()
  {
    if (!has_permission('perfex_dashboard', '', 'dashboard_edit')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      show_404();
      die();
    }

    $dashboard_id = $this->input->post('dashboard_id');
    if (!isset($dashboard_id)) {
      show_404();
      die();
    }

    $dashboard_rows = $this->perfex_dashboard_model->find_dashboard($dashboard_id);
    if (count($dashboard_rows) == 0) {
      show_404();
      die();
    }

    $dashboard = $dashboard_rows[0];
    $dashboard['dashboard_widgets'] = unserialize($dashboard['dashboard_widgets']);

    $dashboard_name = $this->input->post('name');
    if (!isset($dashboard_name)) {
      $dashboard_name = static::$DEFAULT_DASHBOARD_NAME;
    }

    $note = $this->input->post('note');
    if (!isset($note)) {
      $note = '';
    }

    $this->perfex_dashboard_model->update_dashboard_info($dashboard['id'], $dashboard_name, $note);

    set_alert('success', _l('perfex_dashboard_message_success_update_dashboard'));
    redirect(admin_url('perfex_dashboard/dashboards/edit_dashboard?dashboard_id=' . $dashboard_id));
  }

  public function update_staff()
  {
    if (!has_permission('perfex_dashboard', '', 'dashboard_edit')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      show_404();
      die();
    }

    $dashboard_id = $this->input->post('dashboard_id');
    if (!isset($dashboard_id)) {
      show_404();
      die();
    }

    $dashboard_rows = $this->perfex_dashboard_model->find_dashboard($dashboard_id);
    if (count($dashboard_rows) == 0) {
      show_404();
      die();
    }

    $dashboard_staff = $this->input->post('dashboard_staff');
    if (!isset($dashboard_staff)) {
      $dashboard_staff = [];
    }

    $this->perfex_dashboard_model->update_dashboard_staff($dashboard_id, $dashboard_staff);

    set_alert('success', _l('perfex_dashboard_message_success_update_dashboard_staff'));
    redirect(admin_url('perfex_dashboard/dashboards/edit_dashboard?dashboard_id=' . $dashboard_id));
  }

  public function add_widget()
  {
    if (!has_permission('perfex_dashboard', '', 'dashboard_edit')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      show_404();
      die();
    }

    $dashboard_id = $this->input->post('dashboard_id');
    if (!isset($dashboard_id)) {
      show_404();
      die();
    }

    $dashboard_rows = $this->perfex_dashboard_model->find_dashboard($dashboard_id);
    if (count($dashboard_rows) == 0) {
      show_404();
      die();
    }

    $dashboard = $dashboard_rows[0];
    $dashboard['dashboard_widgets'] = unserialize($dashboard['dashboard_widgets']);

    $widget_id = $this->input->post('widget_id');
    if (!isset($widget_id)) {
      show_404();
      die();
    }

    $new_widget_rows = $this->perfex_dashboard_model->select_widgets_by_ids([$widget_id]);
    if (count($new_widget_rows) <= 0) {
      show_404();
      die();
    }

    $widget_container = $this->input->post('widget_container');
    if (!isset($widget_container)) {
      show_404();
      die();
    }

    $dashboard_widgets = $dashboard['dashboard_widgets'];
    if (isset($dashboard_widgets[$widget_container])) {
      array_push($dashboard_widgets[$widget_container], $widget_id);
    } else {
      $dashboard_widgets[$widget_container] = [$widget_id];
    }

    $this->perfex_dashboard_model->update_dashboard_widgets($dashboard['id'], $dashboard_widgets);

    set_alert('success', _l('perfex_dashboard_message_success_add_widget'));
    redirect(admin_url('perfex_dashboard/dashboards/edit_dashboard?dashboard_id=' . $dashboard_id));
  }

  public function api_update_widgets_order()
  {
    if (!has_permission('perfex_dashboard', '', 'dashboard_edit')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      show_404();
      die();
    }

    $order = $this->input->post('order');
    if (!isset($order)) {
      show_404();
      die();
    }

    $dashboard_id = $this->input->post('dashboard_id');
    if (!isset($dashboard_id)) {
      show_404();
      die();
    }

    $dashboard_rows = $this->perfex_dashboard_model->find_dashboard($dashboard_id);
    if (count($dashboard_rows) == 0) {
      show_404();
      die();
    }

    $dashboard = $dashboard_rows[0];

    $this->perfex_dashboard_model->update_dashboard_widgets($dashboard['id'], $order);

    die();
  }

  public function delete_dashboard()
  {
    if (!has_permission('perfex_dashboard', '', 'dashboard_delete')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      show_404();
      die();
    }

    $dashboard_id = $this->input->post('dashboard_id');
    if (!isset($dashboard_id)) {
      show_404();
      die();
    }

    $dashboard_rows = $this->perfex_dashboard_model->find_dashboard($dashboard_id);
    if (count($dashboard_rows) == 0) {
      show_404();
      die();
    }

    $dashboard = $dashboard_rows[0];
    $dashboard['dashboard_widgets'] = unserialize($dashboard['dashboard_widgets']);

    $this->perfex_dashboard_model->delete_dashboard($dashboard['id']);

    set_alert('success', _l('perfex_dashboard_message_success_delete_dashboard'));
    redirect(admin_url('perfex_dashboard/dashboards'));
  }

  public function api_available_widgets()
  {
    $available_widgets = [];

    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      echo json_encode($available_widgets);
      die();
    }

    $dashboard_id = $this->input->post('dashboard_id');
    if (!isset($dashboard_id)) {
      echo json_encode($available_widgets);
      die();
    }

    $dashboard_rows = $this->perfex_dashboard_model->find_dashboard($dashboard_id);
    if (count($dashboard_rows) == 0) {
      echo json_encode($available_widgets);
      die();
    }

    $dashboard = $dashboard_rows[0];
    $dashboard['dashboard_widgets'] = unserialize($dashboard['dashboard_widgets']);

    $available_widgets = perfex_dashboard_get_available_widgets($dashboard);

    echo json_encode($available_widgets);
    die();
  }
}
