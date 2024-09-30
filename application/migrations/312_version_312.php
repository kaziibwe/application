<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_312 extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        add_option('allow_non_admin_members_to_delete_tickets_and_replies', '1');
        add_option('required_register_fields', '[]');
    }
}
