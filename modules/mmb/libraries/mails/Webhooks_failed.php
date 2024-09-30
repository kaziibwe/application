<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Webhooks_failed extends App_mail_template
{
    protected $for = 'staff';

    protected $admin_email;

    protected $response_code;

    public $slug = 'webhook-failed';

    public function __construct($admin_email, $response_code)
    {
        parent::__construct();

        $this->admin_email   = $admin_email;
        $this->response_code = $response_code;
    }

    public function build()
    {
        $this->ci->load->library(MMB_MODULE . '/merge_fields/webhooks_merge_fields');
        $this->to($this->admin_email)->set_merge_fields('webhooks_merge_fields', $this->response_code);
    }
}
