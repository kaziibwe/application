<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Quickbooks
Description: Quickbooks Integration Module (Invoices/Payments/Expenses)
Module URI: https://codecanyon.net/item/quickbooks-module-for-perfex-crm-synchronize-invoices-payments-and-expenses/49908117
Version: 1.0.0
Requires at least: 2.3.*
Author: Themesic Interactive
Author URI: https://codecanyon.net/user/themesic/portfolio
*/
define('QUICKBOOKS_MODULE', 'quickbooks');
require_once __DIR__.'/vendor/autoload.php';
modules\quickbooks\core\Apiinit::the_da_vinci_code(QUICKBOOKS_MODULE);
modules\quickbooks\core\Apiinit::ease_of_mind(QUICKBOOKS_MODULE);

$CI = &get_instance();
hooks()->add_action('admin_init', 'quickbooks_init_menu_items');

/**
 * Load the module helper
 */
$CI->load->helper(QUICKBOOKS_MODULE . '/quickbooks');
/**
 * Register activation module hook
 */

register_activation_hook(QUICKBOOKS_MODULE, 'quickbooks_activation_hook');
function quickbooks_activation_hook()
{

    require_once(__DIR__ . '/install.php');
}
/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(QUICKBOOKS_MODULE, [QUICKBOOKS_MODULE]);
/**
 * Actions for inject the custom styles
 */
hooks()->add_filter('module_quickbooks_action_links', 'module_quickbooks_action_links');
/**
 * Add additional settings for this module in the module list area
 * @param  array $actions current actions
 * @return array
 */
function module_quickbooks_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('quickbooks/quickbooks_process') . '">' . _l('settings') . '</a>';
    return $actions;
}

function quickbooks_init_menu_items()
{
    if (is_admin()) {
        $CI = &get_instance();
        $CI->app_menu->add_setup_menu_item('quickbooks', [
            'name'     => _l('quickbook_builder'),
            'position' => 65,
            'href'       => admin_url('quickbooks/quickbooks_process')
        ]);
    }
}

hooks()->add_action('after_payment_added', 'payment_recorded_in_quickbook');
hooks()->add_action('after_invoice_added', 'invoice_added_in_quickbook');
function invoice_added_in_quickbook($id)
{
    $CI = &get_instance();
    $CI->load->library(QUICKBOOKS_MODULE . '/quickbooks');
    $CI->load->model('payments_model');
    $CI->load->model('invoices_model');
    $invoice_data = $CI->invoices_model->get($id);
    $CI->quickbooks->refresh_token();
    $CI->quickbooks->create_invoice($invoice_data, 1);
}
// Create Payments in QucikBooks
function payment_recorded_in_quickbook($payment_id)
{

    $CI = &get_instance();
    $CI->load->model('payments_model');
    $payment_data = $CI->payments_model->get($payment_id);
    if (get_quickBooks_company_realmId($payment_data->invoiceid) == get_option('realmId') || get_quickBooks_company_realmId($payment_data->invoiceid) == 0) {

        $CI->load->library(QUICKBOOKS_MODULE . '/quickbooks');

        $CI->load->model('invoices_model');


        if (empty($payment_data)) {
            return false;
        } else {
            $invoice_data = $CI->invoices_model->get($payment_data->invoiceid);
        }
        if (!empty($invoice_data)) {
            foreach ($invoice_data->payments as $key => $payment) {
                if ($payment['paymentid'] != $payment_id) {
                    unset($invoice_data->payments[$key]);
                }
            }

            $CI->quickbooks->refresh_token();
            $CI->quickbooks->create_invoice($invoice_data);
        }
    } else {
        add_notification(array('description' => "Payment ID#" . $payment_id . " against Perfex Invoice#" . format_invoice_number($payment_data->invoiceid) . " created in Perfex only not in Xero because invoice is already recoded in another Company.", 'fromuserid' => get_staff_user_id(), 'touserid' => get_staff_user_id(), 'fromcompany' => get_staff_user_id(), 'link' => 'invoices/invoice/' . $invoice_data->id, 'isread' => 0));
        log_activity("Payment ID#" . $payment_id . " against Perfex Invoice#" . format_invoice_number($payment_data->invoiceid) . " created in Perfex only not in Xero because invoice is already recoded in another Company.");
    }
}

register_cron_task('qb_expense_recording_cron');
// Create expenses in QuickBooks
function qb_expense_recording_cron()
{
    $CI = &get_instance();

    $CI->load->model('expenses_model');
    $CI->load->library(QUICKBOOKS_MODULE . '/quickbook_expenses');

    $db_data = $CI->expenses_model->get();
   
    foreach ($db_data as $key => $db_values) {
        if ($db_values['is_expense_created_in_quickBook'] != $db_values['id']) {
            $CI->quickbook_expenses->create_expense_data($db_values);
        }
    }
}


hooks()->add_action('app_init', QUICKBOOKS_MODULE.'_actLib');
function quickbooks_actLib()
{
    $CI = &get_instance();
    $CI->load->library(QUICKBOOKS_MODULE.'/Quickbooks_aeiou');
    $envato_res = $CI->quickbooks_aeiou->validatePurchase(QUICKBOOKS_MODULE);
    if (!$envato_res) {
        set_alert('danger', 'One of your modules failed its verification and got deactivated. Please reactivate or contact support.');
    }
}

hooks()->add_action('pre_activate_module', QUICKBOOKS_MODULE.'_sidecheck');
function quickbooks_sidecheck($module_name)
{
    if (QUICKBOOKS_MODULE == $module_name['system_name']) {
        modules\quickbooks\core\Apiinit::activate($module_name);
    }
}

hooks()->add_action('pre_deactivate_module', QUICKBOOKS_MODULE.'_deregister');
function quickbooks_deregister($module_name)
{
    if (QUICKBOOKS_MODULE == $module_name['system_name']) {
        delete_option(QUICKBOOKS_MODULE.'_verification_id');
        delete_option(QUICKBOOKS_MODULE.'_last_verification');
        delete_option(QUICKBOOKS_MODULE.'_product_token');
        delete_option(QUICKBOOKS_MODULE.'_heartbeat');
    }
}