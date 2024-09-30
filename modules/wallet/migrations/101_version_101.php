<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_101 extends App_module_migration
{
    public function up()
    {
        add_option('wallet_allow_withdraw', '0');
        add_option('wallet_withdrawal_methods', 'Paypal, Bank');
        wallet_activate_module();
    }
}