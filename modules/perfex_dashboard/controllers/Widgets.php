<?php

use function GuzzleHttp\json_encode;

defined('BASEPATH') or exit('No direct script access allowed');

class Widgets extends AdminController
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('dashboard_model');
    $this->load->model('perfex_dashboard_model');
  }

  public function template_demo()
  {
    $data['title'] = "Template HTML CSS demo";
    $this->load->view('template_demo', $data);
  }

  public function index()
  {
    if (!has_permission('perfex_dashboard', '', 'widget_view')) {
      access_denied('perfex_dashboard');
    }

    $data['bodyclass'] = 'dashboard invoices-total-manual';
    add_calendar_assets();
    $data['dashboard'] = true;

    $data = hooks()->apply_filters('before_dashboard_render', $data);

    $categories = perfex_dashboard_get_categories();
    $data['categories'] = $categories;

    $category = $this->input->get('category');
    if (!isset($category)) {
      $category = '';
    }
    $data['active_category'] = $category;

    $search = $this->input->get('search');
    if (!isset($search) || $search == '') {
      $search = null;
    }
    $data['active_search'] = $search;

    $widgets = $this->perfex_dashboard_model->get_widgets([
      'category' => $category,
      'search' => $search,
    ]);
    $data['widgets'] = $widgets;

    $scan_all_widgets = perfex_dashboard_scan_widgets_2();
    $data['scan_all_widgets'] = $scan_all_widgets;

    $data['title'] = _l('perfex_dashboard_widgets_title_page');
    $this->load->view('list', $data);
  }

  public function store_widget()
  {
    if (!has_permission('perfex_dashboard', '', 'widget_create')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      show_404();
      die();
    }

    $name = $this->input->post('name');
    if (!isset($name)) {
      show_404();
      die();
    }

    $note = $this->input->post('note');
    if (!isset($note)) {
      show_404();
      die();
    }

    $category = $this->input->post('category');
    if (!isset($category)) {
      show_404();
      die();
    }

    $widget_name = $this->input->post('widget_name');
    if (!isset($widget_name)) {
      show_404();
      die();
    }

    $this->perfex_dashboard_model->create_widget($name, $note, $category, 'widget-' . $widget_name);

    set_alert('success', _l('perfex_dashboard_message_success_create_widget'));
    redirect(admin_url('perfex_dashboard/widgets'));
  }

  public function update_widget()
  {
    if (!has_permission('perfex_dashboard', '', 'widget_edit')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      show_404();
      die();
    }

    $id = $this->input->post('id');
    if (!isset($id)) {
      show_404();
      die();
    }

    $name = $this->input->post('name');
    if (!isset($name)) {
      show_404();
      die();
    }

    $note = $this->input->post('note');
    if (!isset($note)) {
      show_404();
      die();
    }

    $category = $this->input->post('category');
    if (!isset($category)) {
      show_404();
      die();
    }

    $widget_name = $this->input->post('widget_name');
    if (!isset($widget_name)) {
      show_404();
      die();
    }

    $widget_rows = $this->perfex_dashboard_model->select_widgets_by_ids([$id]);
    if (count($widget_rows) <= 0) {
      show_404();
      die();
    }

    $this->perfex_dashboard_model->update_widget_by_id($id, $name, $note, $category, 'widget-' . $widget_name);

    set_alert('success', _l('perfex_dashboard_message_success_update_widget'));
    redirect($_SERVER['HTTP_REFERER']);
  }

  public function destroy_widget()
  {
    if (!has_permission('perfex_dashboard', '', 'widget_delete')) {
      access_denied('perfex_dashboard');
    }
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
      show_404();
      die();
    }

    $id = $this->input->post('id');
    if (!isset($id)) {
      show_404();
      die();
    }

    $widget_rows = $this->perfex_dashboard_model->select_widgets_by_ids([$id]);
    if (count($widget_rows) <= 0) {
      show_404();
      die();
    }

    $this->perfex_dashboard_model->remove_widget_by_id($id);

    set_alert('success', _l('perfex_dashboard_message_success_destroy_widget'));
    redirect(admin_url('perfex_dashboard/widgets'));
  }

  public function api_get_widget_data()
  {
    if ($this->input->server('REQUEST_METHOD') !== 'GET') {
      show_404();
      die();
    }

    $widget_id = $this->input->get('widget_id');
    if (!isset($widget_id)) {
      show_404();
      die();
    }

    $widget_rows = $this->perfex_dashboard_model->select_widgets_by_ids([$widget_id]);
    if (count($widget_rows) > 0) {
      echo json_encode($widget_rows[0]);
    } else {
      echo json_encode(null);
    }

    die();
  }

  public function api_get_calendar_data()
  {
    echo json_encode($this->perfex_dashboard_model->get_calendar_data(
      date('Y-m-d', strtotime($this->input->get('start'))),
      date('Y-m-d', strtotime($this->input->get('end'))),
      '',
      '',
      $this->input->get()
    ));
    die();
  }
}
