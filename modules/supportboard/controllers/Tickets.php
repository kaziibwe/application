<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tickets extends ClientsController {

    public function __construct() {
        parent::__construct();
		hooks()->do_action('after_clients_area_init', $this);
    }

    public function index() {
        $this->view('tickets');
        $this->layout();
    }

}

?>