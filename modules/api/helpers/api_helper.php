<?php 

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('insert')) 
{
    function insert($table_name, $insert_data)
    {
        $CI =& get_instance();
        return $CI->db->insert($table_name, $insert_data);
    }
}

function get_relation_data_api($type, $search = '')
{
    $CI =& get_instance();
    $q = '';
    if ($search != '') {
        $q = $search;
        $q = trim($q);
    }
    $data = [];
    if ($type == 'customer' || $type == 'customers') {
        $where_clients = 'tblclients.active=1';
        if ($q) {
            $where_clients .= ' AND types = "customer" AND (company LIKE "%' . $q . '%" OR CONCAT(firstname, " ", lastname) LIKE "%' . $q . '%" OR email LIKE "%' . $q . '%")';
        }

        $data = $CI->clients_model->get('', $where_clients);
    } elseif ($type == 'ticket') {
        $search = $CI->api_model->_search_tickets($q, 0, true);
        $data   = $search['result'];
    } elseif ($type == 'lead' || $type == 'leads') {
        $search = $CI->api_model->_search_leads($q, 0, ['junk' => 0,], true);
        $data = $search['result'];
    } elseif ($type == 'project') {
        $where_projects = '';
        if ($CI->input->post('customer_id')) {
            $where_projects .= '(clientid=' . $CI->input->post('customer_id').' or clientid in (select id from tblleads where client_id='.$CI->input->post('customer_id').') )';
        }
        if ($CI->input->post('rel_type')) {
            $where_projects .= ' and rel_type="' . $CI->input->post('rel_type').'" ' ;
        }
        $search = $CI->api_model->_search_projects($q, 0, $where_projects,$CI->input->post('rel_type'), true);
        $data   = $search['result'];
    } elseif ($type == 'staff') {
        $search = $CI->api_model->_search_staff($q, 0, true);
        $data   = $search['result'];
    } elseif ($type == 'tasks') {
        $search = $CI->api_model->_search_tasks($q, 0, true);
        $data   = $search['result'];
    }

    return $data;
}

function get_available_api_permissions($data = [])
{
    $viewGlobalName = _l('permission_view') . '(' . _l('permission_global') . ')';

    $firstPermissionsArray = [
        'get'           => _l('permission_get'),
        'search_get'    => _l('permission_search'),
        'post'          => _l('permission_create'),
        'delete'        => _l('permission_delete'),
        'put'           => _l('permission_update'),
    ];
    $secondPermissionsArray = [
        'get'           => _l('permission_list'),
        'search_get'    => _l('permission_search'),
    ];
    $thirdPermissionsArray = [
        'get'           => _l('permission_list'),
        'post'          => _l('permission_create'),
        'delete'        => _l('permission_delete'),
    ];
    $forthPermissionsArray = [
        'get'           => _l('permission_get'),
    ];
    $fifthPermissionsArray = [
        'get'           => _l('permission_get'),
        'post'          => _l('permission_create'),
        'delete'        => _l('permission_delete'),
        'get_value'     => _l('permission_get_value'),
        'search_get'    => _l('permission_search'),
        'put'           => _l('permission_update'),
    ];
    $sixthPermissionsArray = [
        'get'           => _l('permission_get'),
    ];

    $apiPermissions = [
        'customers' => [
            'name'         => _l('clients'),
            'capabilities' => $firstPermissionsArray,
        ],
        'contacts' => [
            'name'         => _l('contacts'),
            'capabilities' => $firstPermissionsArray,
        ],
        'invoices' => [
            'name'         => _l('invoices'),
            'capabilities' => $firstPermissionsArray,
        ],
        'items' => [
            'name'         => _l('items'),
            'capabilities' => $secondPermissionsArray,
        ],
        'leads' => [
            'name'         => _l('leads'),
            'capabilities' => $firstPermissionsArray,
        ],
        'milestones' => [
            'name'         => _l('milestones'),
            'capabilities' => $firstPermissionsArray,
        ],
        'projects' => [
            'name'         => _l('projects'),
            'capabilities' => $firstPermissionsArray,
        ],
        'staffs' => [
            'name'         => _l('staffs'),
            'capabilities' => $firstPermissionsArray,
        ],
        'tasks' => [
            'name'         => _l('tasks'),
            'capabilities' => $firstPermissionsArray,
        ],
        'tickets' => [
            'name'         => _l('tickets'),
            'capabilities' => $firstPermissionsArray,
        ],
        'contracts' => [
            'name'         => _l('contracts'),
            'capabilities' => $firstPermissionsArray,
        ],
        'credit_notes' => [
            'name'         => _l('credit_notes'),
            'capabilities' => $firstPermissionsArray,
        ],
        'custom_fields' => [
            'name'         => _l('custom_fields'),
            'capabilities' => $firstPermissionsArray,
        ],
        'estimates' => [
            'name'         => _l('estimates'),
            'capabilities' => $firstPermissionsArray,
        ],
        'expense_categories' => [
            'name'         => _l('expense_categories'),
            'capabilities' => $forthPermissionsArray,
        ],
        'expenses' => [
            'name'         => _l('expenses'),
            'capabilities' => $firstPermissionsArray,
        ],
        'taxes' => [
            'name'         => _l('taxes'),
            'capabilities' => $forthPermissionsArray,
        ],
        'payment_methods' => [
            'name'         => _l('payment_methods'),
            'capabilities' => $forthPermissionsArray,
        ],
        'payments' => [
            'name'         => _l('payments'),
            'capabilities' => $secondPermissionsArray,
        ],
        'proposals' => [
            'name'         => _l('proposals'),
            'capabilities' => $firstPermissionsArray,
        ],
         'calendar' => [
            'name'         => _l('calendar'),
            'capabilities' => $firstPermissionsArray,
        ],
         'subscriptions' => [
            'name'         => _l('subscriptions'),
            'capabilities' => $firstPermissionsArray,
        ],
         'timesheets' => [
            'name'         => _l('timesheets'),
            'capabilities' => $firstPermissionsArray,
        ],
    ];

    return hooks()->apply_filters('api_permissions', $apiPermissions, $data);
}

function api_can($api_id, $feature = '', $capability = '')
{
    $CI =& get_instance();
    $permissions = $CI->api_model->get_permissions($api_id, $feature, $capability);
    if (count($permissions)) {
        return true;
    }
    
    return false;
}