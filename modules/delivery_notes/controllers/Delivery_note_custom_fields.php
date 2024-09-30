<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/admin/Custom_fields.php');

class Delivery_note_custom_fields extends Custom_fields
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('delivery_note_custom_fields_model');

        /** !important that we override the model with custom model here */
        $this->custom_fields_model = $this->delivery_note_custom_fields_model;
    }


    public function field($id = '')
    {
        parent::field($id);
    }
}
