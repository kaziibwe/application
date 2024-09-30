<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Delivery Note
Description: Delivery note module for sales. It allows  you to create delivery notes (DN). You can convert Purhcase order (PO) and Estimates to DVL and DVL can be converted to PO and invoices. 
Version: 1.0.7
Requires at least: 3.0.*
Author: ulutfa
Author URI: https://codecanyon.net/user/ulutfa
*/

defined('DELIVERY_NOTE_MODULE_NAME') or define('DELIVERY_NOTE_MODULE_NAME', 'delivery_notes');

$CI = &get_instance();

/**
 * Load the helpers
 */
$CI->load->helper(DELIVERY_NOTE_MODULE_NAME . '/' . DELIVERY_NOTE_MODULE_NAME);


/**
 * Load the models
 */
$CI->load->model(DELIVERY_NOTE_MODULE_NAME . '/' . DELIVERY_NOTE_MODULE_NAME . '_model');

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(DELIVERY_NOTE_MODULE_NAME, [DELIVERY_NOTE_MODULE_NAME]);

/**
 * Register activation module hook
 */
register_activation_hook(DELIVERY_NOTE_MODULE_NAME, function () {
    require(__DIR__ . '/install.php');
});

/**
 * Register merge fields
 */
register_merge_fields(DELIVERY_NOTE_MODULE_NAME . '/merge_fields/delivery_note_merge_fields');
register_merge_fields(DELIVERY_NOTE_MODULE_NAME . '/merge_fields/editor_merge_fields');
hooks()->add_filter('available_merge_fields', 'delivery_note_allow_staff_client_merge_fields');


/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
hooks()->add_action('admin_init', function () use ($CI) {
    // Register menu
    if (staff_can('view_own', DELIVERY_NOTE_MODULE_NAME) || staff_can('view', DELIVERY_NOTE_MODULE_NAME)) {
        $CI->app_menu->add_sidebar_children_item('sales', [
            'slug' => DELIVERY_NOTE_MODULE_NAME,
            'name' => _l(DELIVERY_NOTE_MODULE_NAME),
            'icon' => '',
            'href' => admin_url('delivery_notes'),
            'position' => 15,
        ]);
    }

    // Register permssion
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'view_own' => _l('permission_view_own'),
        'sign' => _l('delivery_note_permission_sign'),
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('delivery_notes', $capabilities, _l(DELIVERY_NOTE_MODULE_NAME));
}, PHP_INT_MAX);

/**
 * Register admin footer hook
 */
hooks()->add_action('app_admin_footer', function () {
    //load common admin asset
    $CI = &get_instance();
    $CI->load->view(DELIVERY_NOTE_MODULE_NAME . '/admin/scripts/common');
});

/**
 * Add settings to finance setting group
 */
hooks()->add_action('after_finance_settings_last_tab', function () {
    echo '<li role="presentation">
    <a href="#delivery_notes" aria-controls="delivery_notes" role="tab"
        data-toggle="tab">' . _l('delivery_notes') . '</a>
</li>';
});
hooks()->add_action('after_finance_settings_tabs_content', function () {
    get_instance()->load->view(DELIVERY_NOTE_MODULE_NAME . '/admin/' . DELIVERY_NOTE_MODULE_NAME . '/settings');
});


/**
 * CRUD and relation hooks
 */
hooks()->add_filter('before_delivery_note_added', '_format_data_sales_feature');
hooks()->add_filter('before_delivery_note_updated', '_format_data_sales_feature');

// Global search result query filter for Delivery Note Items
hooks()->add_filter('global_search_result_query', 'delivery_note_global_search_result_query', 10, 3);

// Task modal rel_type_select action
hooks()->add_action('task_modal_rel_type_select', 'delivery_note_task_modal_rel_type_select');

// Relation values filter
hooks()->add_filter('relation_values', 'delivery_note_relation_values', 10, 2);

// Get relation data filter
hooks()->add_filter('get_relation_data', 'delivery_note_get_relation_data', 10, 4);

// Tasks table row data filter
hooks()->add_filter('tasks_table_row_data', 'delivery_note_tasks_table_row_data', 10, 3);


/** Helpers to convert from estimate */
hooks()->add_action('after_admin_estimate_preview_template_tab_content_last_item', function ($estimate) {
    $CI = &get_instance();
    $CI->load->view(DELIVERY_NOTE_MODULE_NAME . '/admin/scripts/convert_from_estimate', ['estimate' => $estimate]);
});

/**
 * Helpers to convert from purchase order
 */
// Hooks to add menu item to convert purchase order to delivery note. 
hooks()->add_action('after_admin_purchase_order_preview_template_tab_content_last_item', function ($purchase_order) {
    $CI = &get_instance();
    $CI->load->view(DELIVERY_NOTE_MODULE_NAME . '/admin/scripts/convert_from_purchase_order', ['purchase_order' => $purchase_order]);
});

/** Helpers to convert from invoice */
hooks()->add_action('after_admin_invoice_preview_template_tab_content_last_item', function ($invoice) {
    $CI = &get_instance();
    $CI->load->view(DELIVERY_NOTE_MODULE_NAME . '/admin/scripts/convert_from_invoice', ['invoice' => $invoice]);
});


/**
 * Show DN on client profile and project tab
 */
hooks()->add_action('admin_init', function ()  use ($CI) {

    // Show on customer profile tab with badge
    $CI->app_tabs->add_customer_profile_tab(DELIVERY_NOTE_MODULE_NAME, [
        'name'     => _l(DELIVERY_NOTE_MODULE_NAME),
        'icon'     => 'fa-regular fa-file-lines',
        'view'     => 'delivery_notes/admin/delivery_notes/groups/client',
        'position' => 47,
        'badge'    => [],
    ]);
    hooks()->add_filter('customers_profile_tab_badge', function ($data) use ($CI) {
        if ($data['feature'] === DELIVERY_NOTE_MODULE_NAME) {
            $customerid = $data['customer_id'];

            if (staff_cant('view', 'delivery_notes')) {
                $where = get_delivery_notes_where_sql_for_staff(get_staff_user_id());
                $CI->db->where($where);
            }

            $count = $CI->db->where_not_in('status', [3, 4])->where('clientid', $customerid)->count_all_results('delivery_notes');
            if ($count > 0) {
                $badge = [
                    'value' => $count,
                    'color' => '',
                    'type'  => 'default',
                ];
                $data['badge'] = $badge;
            }
        }
        return $data;
    });

    // Show on project tab
    $CI->app_tabs->add_project_tab_children_item('sales', [
        'slug'     => DELIVERY_NOTE_MODULE_NAME,
        'name'     => _l(DELIVERY_NOTE_MODULE_NAME),
        'view'     => 'delivery_notes/admin/delivery_notes/groups/project',
        'position' => 12,
        'visible'  => (staff_can('view',  DELIVERY_NOTE_MODULE_NAME) || staff_can('view_own',  DELIVERY_NOTE_MODULE_NAME) || (get_option('allow_staff_view_' . DELIVERY_NOTE_MODULE_NAME . '_assigned') == 1 && staff_has_assigned_delivery_notes())),
    ]);
});

// Display email list on email template list
hooks()->add_action('after_email_templates', function () use ($CI) {
    $type = 'delivery_note';
    $CI->load->model('emails_model');
    $CI->load->view('delivery_notes/admin/email_templates', ['templates' => $CI->emails_model->get(['type' => $type, 'language' => 'english']), 'email_type' => $type]);
});

// Add delivery note to customer portal
hooks()->add_action('clients_init', function () {
    if (is_client_logged_in()) {
        if (has_contact_permission(DELIVERY_NOTE_MODULE_NAME))
            add_theme_menu_item(DELIVERY_NOTE_MODULE_NAME, [
                'name' =>  _l(DELIVERY_NOTE_MODULE_NAME . '_client_menu'),
                'href' => base_url(DELIVERY_NOTE_MODULE_NAME . '/client'),
                'position' => 10,
            ]);
    }
});

hooks()->add_filter('get_contact_permissions', function ($permissions) {
    $permissions[] = [
        'id'         => DELIVERY_NOTE_MODULE_NAME,
        'name'       => _l('customer_permission_delivery_notes'),
        'short_name' => DELIVERY_NOTE_MODULE_NAME,
    ];
    return $permissions;
});

hooks()->add_action('after_customers_area_project_overview_tab', function ($project) {
    if (has_contact_permission(DELIVERY_NOTE_MODULE_NAME) && ($project->settings->available_features[DELIVERY_NOTE_MODULE_NAME] ?? 1) == 1) {
        echo '
        <li role="presentation" class="project_tab_' . DELIVERY_NOTE_MODULE_NAME . '">
            <a data-group="delivery_notes"
                href="' . site_url('delivery_notes/client/project/' . $project->id . '?group=' . DELIVERY_NOTE_MODULE_NAME) . '"
                role="tab">
                <i class="fa fa-file-circle-check menu-icon" aria-hidden="true"></i>
                ' . _l(DELIVERY_NOTE_MODULE_NAME . '_client_menu') . '
            </a>
        </li>';
        echo '<script>document.addEventListener("DOMContentLoaded",()=>{$(".project_tab_delivery_notes").insertAfter(".project_tab_estimates");if(window.location.search.includes("delivery_notes"))$(".project_tab_delivery_notes")[0].scrollIntoView();});</script>';
    }
});

// Set upload path
hooks()->add_filter('get_upload_path_by_type', function ($path, $type) {
    if ($type === DELIVERY_NOTE_MODULE_NAME || $type === 'delivery_note') {
        $path = FCPATH . 'uploads/' . DELIVERY_NOTE_MODULE_NAME . '/';
        if (!is_dir($path))
            mkdir($path, 0755);
    }
    return $path;
}, 10, 2);

// Add pdf config 
hooks()->add_action('after_pdf_signature_settings_fields', function () {
    echo '<div class="delivery-note">';
    render_yes_no_option('show_pdf_signature_delivery_note', 'show_pdf_signature_delivery_note');
    echo '<hr /></div>';
    echo '<script>window.addEventListener("DOMContentLoaded", ()=>{$(".delivery-note").insertBefore($(".tab-pane#signature>.form-group").slice(-1)); });</script>';
});

// Add identity confirmation for delivery
hooks()->add_action('after_settings_e_sign_fields', function () {
    echo '<div class="delivery-note">';
    echo '<p class="bold">' . _l('delivery_note') . '</p>';
    render_yes_no_option('delivery_note_accept_identity_confirmation', 'accept_identity_confirmation_and_signature_sign');
    echo '<hr /></div>';
    echo '<script>window.addEventListener("DOMContentLoaded", ()=>{$(".delivery-note").insertBefore($("form .panel-body>.form-group").slice(-1)); });</script>';
});

// Encode array settings
hooks()->add_filter('before_settings_updated', function ($data) {

    $settings_array_fields = [
        'delivery_note_items_hidden_fields',
        'delivery_note_signatory_allowed_fields'
    ];

    foreach ($settings_array_fields as $key) {
        if (isset($data['settings'][$key])) {
            $data['settings'][$key] = json_encode($data['settings'][$key]);
        }
    }

    return $data;
});

// Custom fields dropdown options
hooks()->add_action('after_custom_fields_select_options', 'delivery_note_custom_filed_select_option');

// Exlude not importable columns
hooks()->add_filter('not_importable_clients_fields', function ($fields) {
    $fields[] = 'delivery_note_emails';
    return $fields;
});


// Allow customization of items column
hooks()->add_filter('custom_sales_resources_type_list', function ($resources) {
    $resources[] = 'delivery_note';
    return $resources;
});
hooks()->add_filter('custom_sales_resources_type_hidden_fields', function ($data) {
    if ($data['type'] == 'delivery_note' && empty($data['hidden_fields'])) {
        $data['hidden_fields'] = delivery_note_items_hidden_fields();
    }
    return $data;
});
hooks()->add_filter('custom_sales_can_show_column', function ($data) {
    if ($data['type'] == 'delivery_note') {
        $viewing_as_customer = get_instance() instanceof ClientsController;
        if (is_staff_logged_in() && !$viewing_as_customer) {
            $data['can_show'] = !in_array($data['field'], $data['hidden_fields']);
        }
    }
    return $data;
});

// Dashboard widget
hooks()->add_action('after_dashboard_top_container', function () {
    $CI = &get_instance();
    $CI->load->view(DELIVERY_NOTE_MODULE_NAME . '/admin/scripts/dashboard_widget');
});
