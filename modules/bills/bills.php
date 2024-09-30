<?php

/**
* Ensures that the module init file can't be accessed directly, only within the application.
*/
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Contas a Pagar
Description: Módulo de contas a pagar
Version: 2.3.0
Requires at least: 2.3.*
*/

define('BILLS_MODULE_NAME', 'bills');

hooks()->add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1);
hooks()->add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1);
hooks()->do_action($tag, $arg = '');
hooks()->apply_filters($tag, $value, $additionalParams);
hooks()->add_action('admin_init', 'bills_register_permissions');

hooks()->add_action('admin_init', 'bills_init_menu_items');

/**
* Register activation module hook
*/
register_activation_hook(BILLS_MODULE_NAME, 'bills_module_activation_hook');

function bills_module_activation_hook()
{
    $path = getcwd();

    $files = [
        $path.'/modules/bills/views/tables/invoices.php' => $path.'/application/views/admin/tables/invoices.php',
        $path. '/modules/bills/views/tables/bills.php' => $path . '/application/views/admin/tables/bills.php',
        $path. '/modules/bills/views/tables/recurring_bills.php' => $path. '/application/views/admin/tables/recurring_bills.php',
        $path. '/modules/bills/models/Cron_model.php' => $path. '/application/models/Cron_model.php',
        $path. '/modules/bills/helpers/bills_helper.php' => $path. '/application/helpers/invoices_helper.php',
        $path. '/modules/bills/models/Invoices_model.php' => $path. '/application/models/Invoices_model.php',
        $path. '/modules/bills/models/Reports_model.php' => $path. '/application/models/Reports_model.php',
        $path. '/modules/bills/controllers/Reports.php' => $path. '/application/controllers/admin/Reports.php',
        $path. '/modules/bills/views/reports/includes/sales_js.php' => $path. '/application/views/admin/reports/includes/sales_js.php',
        $path. '/modules/bills/views/reports/includes/bills_income.php' => $path. '/application/views/admin/reports/includes/bills_income.php',
        $path. '/modules/bills/views/reports/includes/sales_bills.php' => $path. '/application/views/admin/reports/includes/sales_bills.php',
        $path. '/modules/bills/helpers/widgets_helper.php' => $path. '/application/helpers/widgets_helper.php',
        $path. '/modules/bills/views/dashboard/widgets/bills.php' => $path. '/application/views/admin/dashboard/widgets/bills.php',
        $path. '/modules/bills/views/dashboard/dashboard_js.php' => $path. '/application/views/admin/dashboard/dashboard_js.php',
        $path. '/modules/bills/controllers/Dashboard.php' => $path. '/application/controllers/admin/Dashboard.php',
        $path. '/modules/bills/views/reports/sales.php' => $path. '/application/views/admin/reports/sales.php',
        $path. '/modules/bills/views/tables/payments.php' => $path . '/application/views/admin/tables/payments.php',
        $path. '/modules/bills/models/Dashboard_model.php' => $path. '/application/models/Dashboard_model.php',
        $path. '/modules/bills/models/Statement_model.php' => $path. '/application/models/Statement_model.php',
        $path. '/modules/bills/views/dashboard/widgets/top_stats.php' => $path. '/application/views/admin/dashboard/widgets/top_stats.php',
    ];

    foreach($files as $origin_file_path => $dest_file_path) {
        $copy_success = @copy($origin_file_path, $dest_file_path);
        if (!$copy_success) {
            set_alert(
                'danger',
                'Erro ao importar módulo Contas a Pagar. Verifique as permissões de pastas.'
            );
        }
    }
    require_once(__DIR__ . '/install.php');
}

function debugger($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';exit;
}

function bills_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_children_item('sales', [
        'slug'     => 'contas_a_pagar',
        'name'     => 'Contas a Pagar',
        'href'     => admin_url('bills'),
    ]);

    $CI->app_menu->add_sidebar_children_item('reports', [
        'slug'     => 'renda_contas_a_pagar',
        'name'     => 'Contas a Pagar vs Renda',
        'href'     => admin_url('bills/bills_income'),
    ]);
}


/**

 * Hook for assigning staff permissions for apoointments module

 *

 * @return void

 */

function bills_register_permissions()

{

    $capabilities = [];



    $capabilities['capabilities'] = [

        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',

        'view_own'   => _l('permission_view_own'),

        'create' => _l('permission_create'),

        'edit'   => _l('permission_edit'),

        'delete' => _l('permission_delete'),

    ];



    register_staff_capabilities('bills', $capabilities, _l('bills_permissions'));

}
