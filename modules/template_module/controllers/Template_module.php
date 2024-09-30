<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Template_module extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        echo 'ok';
    }
}
