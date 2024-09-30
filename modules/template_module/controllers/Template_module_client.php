<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Template_module_client extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        echo 'Hello Client Side';
    }
}
