<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_121 extends App_module_migration
{
    public function up()
    {
        $CI =& get_instance();
        $table_name = db_prefix().'whatsapp_api_debug_log';
        $CI->db->update($table_name, ['message_category' =>'Boradcast Message'], ['message_category' => 'Boradcast Message']);
    }
}