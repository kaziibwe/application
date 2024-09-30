<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api extends AdminController
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('perfex_dashboard_model');
  }

  public function lang()
  {
    $res_langs = [];

    if ($this->input->server('REQUEST_METHOD') !== 'GET') {
      echo json_encode($res_langs);
      die();
    }

    $req_langs = $this->input->get('langs');
    if(!isset($req_langs)) {
      echo json_encode($res_langs);
      die();
    }

    foreach ($req_langs as $key => $req_lang) {
      $res_langs[$req_lang] = _l($req_lang);
    }

    echo json_encode($res_langs);
    die();
  }

  public function convert_string_to_key(){
      $string = "Task to deadline this week";
      $str = preg_replace('/\s+/', '_', $string);
      echo strtolower($str); die;
  }
}
