<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_201 extends App_module_migration
{
    public function up()
    {
        $this->ci->dbforge->add_column('user_api', array('permission_enable' => array('type' => 'TINYINT', 'default' => 0)));

        $this->ci->dbforge->add_field([
            'api_id' => [
                'type'          => 'INT',
                'constraint'    => 11,
            ],
            'feature' => [
                'type'          => 'VARCHAR',
                'constraint'    => 50,
            ],
            'capability' => [
                'type'          => 'VARCHAR',
                'constraint'    => 50,
            ],
        ]);
        $this->ci->dbforge->create_table('user_api_permissions', true);
    }
}