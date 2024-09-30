<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_316 extends CI_Migration
{
    public function up()
    {
        add_option('allow_non_admin_members_to_edit_ticket_messages', '1');
        add_option('proposal_auto_convert_to_invoice_on_client_accept', '0');
    }
}
