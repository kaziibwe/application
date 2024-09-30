<?php







defined('BASEPATH') or exit('No direct script access allowed');





$CI = &get_instance();

add_option('quickbooks', 'enable');
if (!$CI->db->field_exists('is_expense_created_in_quickBook', db_prefix() . 'expenses'))
{
    $CI->db->query('ALTER TABLE `tblexpenses` ADD `is_expense_created_in_quickBook` boolean NOT NULL DEFAULT 0 ;');  
   
}
if (!$CI->db->field_exists('quickBooks_company_realmId', db_prefix() . 'invoices'))
{
    $CI->db->query('ALTER TABLE `tblinvoices` ADD `quickBooks_company_realmId` VARCHAR(25) NULL DEFAULT 0 ;');  
   
}
if (!$CI->db->field_exists('quickBooks_company_realmId', db_prefix() . 'invoicepaymentrecords'))
{
    $CI->db->query('ALTER TABLE `tblinvoicepaymentrecords` ADD `quickBooks_company_realmId` VARCHAR(25) NULL DEFAULT 0 ;');  
   
}