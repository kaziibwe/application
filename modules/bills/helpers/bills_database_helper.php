<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('init_bills_database_tables')) {

    /**

     * Init installation tables creation in database

     */

    function init_bills_database_tables()

    {

        $CI->db->query("ALTER TABLE " . db_prefix() . "invoices ADD column `bill` varchar(20) default `0`;");

    }

}