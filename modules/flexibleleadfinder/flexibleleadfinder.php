<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Google Leads -  Import leads and Contact from Google
Description: Search and imports all businesses within the specified field and location
Version: 1.0.3
Requires at least: 2.3.*
Author: FlexiByte Team
Author URI: https://codecanyon.net/user/flexibyte88
*/
define('FLEXIBLELEADFINDER_MODULE_NAME', 'flexibleleadfinder');
define('FLEXIBLELEADFINDER_RECORDS_LIMIT_SETTING', 'flexibleleadfinder_records_limit');
define('FLEXIBLELEADFINDER_ASSIGNEE_SETTING', 'flexibleleadfinder_assignee');
define('FLEXIBLELEADFINDER_IMPORT_RESULTS_WITHOUT_EMAIL_SETTING', 'flexibleleadfinder_import_results_without_email');
define('FLEXIBLELEADFINDER_MAX_LEADS', 50);
define('FLEXIBLELEADFINDER_LEAD_SOURCE_SETTING', 'flexibleleadfinder_lead_source');
define('FLEXIBLELEADFINDER_LEAD_SOURCE_DEFAULT_NAME', 'Flexible Lead Finder');
define('FLEXIBLELEADFINDER_LEAD_STATUS_SETTING', 'flexibleleadfinder_lead_status');
define('FLEXIBLELEADFINDER_LEAD_STATUS_DEFAULT_NAME', 'Lead Finder Contact');

hooks()->add_filter('module_flexibleleadfinder_action_links', 'flexibleleadfinder_action_links');
hooks()->add_action('admin_init', 'flexibleleadfinder_module_init_menu_items');

/**
 * Add additional settings for this module in the module list area
 * @param  array $actions current actions
 * @return array
 */
function flexibleleadfinder_action_links($actions)
{
    $actions[] = '<a href="' . flexibleleadfinder_admin_url() . '">' .flexibleleadfinder_lang('lead-finder') . '</a>';

    return $actions;
}
/**
 * Register activation module hook
 */
register_activation_hook(FLEXIBLELEADFINDER_MODULE_NAME, 'flexibleleadfinder_module_activation_hook');

function flexibleleadfinder_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(FLEXIBLELEADFINDER_MODULE_NAME, [FLEXIBLELEADFINDER_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function flexibleleadfinder_module_init_menu_items(){
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('leadfinder-menu', [
        'name'     => flexibleleadfinder_lang('lead-finder'), // The name if the item
        'href'     => flexibleleadfinder_admin_url(), // URL of the item
        'position' => 40, // The menu position, see below for default positions.
        'icon'     => 'fa-solid fa-magnifying-glass', // Font awesome icon
    ]);
}

function flexibleleadfinder_lang($key)
{
    return _l(FLEXIBLELEADFINDER_MODULE_NAME.'_'.$key);
}

function flexibleleadfinder_get_contacts_count($leadfinder_id){
    $CI = &get_instance();

    $CI->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfindercontacts_model');

    return $CI->flexibleleadfindercontacts_model->count([
        'leadfinder_id' => $leadfinder_id
    ]);
}

function flexibleleadfinder_get_date(){
    return to_sql_date(date('Y-m-d H:i:s'), true);
}

function flexibleleadfinder_admin_url($path = ''){
    $url = FLEXIBLELEADFINDER_MODULE_NAME;

    if($path){
        $url .= '/' . $path;
    }

    return admin_url($url);
}

function flexibleleadfinder_sync_lead($leadfinder_contact_id)
{
    $CI = &get_instance();
    $CI->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfindercountry_model');
    $CI->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfindercontacts_model');
    $CI->load->model('leads_model');
    
    try {
        $conditions = [
            'id' => $leadfinder_contact_id
        ];
        
        $contact = $CI->flexibleleadfindercontacts_model->get($conditions);
        $emails = explode(', ', $contact['email']);
        $existing_lead = $CI->leads_model->get_lead_by_email($emails[0]);
    
        if (!$existing_lead) {
            $country_conditions = [
                'short_name like' => "%$contact[country]%"
            ];
            $country = $CI->flexibleleadfindercountry_model->get($country_conditions);

            $data = [
                'name' => $contact['name'],
                'email' => array_shift($emails),
                'company' => $contact['name'],
                'phonenumber' => $contact['phonenumber'],
                'source' => get_option(FLEXIBLELEADFINDER_LEAD_SOURCE_SETTING),
                'status' => get_option(FLEXIBLELEADFINDER_LEAD_STATUS_SETTING),
                'description' => count($emails) > 0 ? 'Additional emails: ' . implode(', ', $emails) : '',
                'address' => $contact['address'],
                'city' => $contact['city'],
                'state' => $contact['state'],
                'country' => $country['country_id'],
                'zip' => $contact['postal_code'],
                'assigned' => option_exists(FLEXIBLELEADFINDER_ASSIGNEE_SETTING) 
                    ? get_option(FLEXIBLELEADFINDER_ASSIGNEE_SETTING) : "",
            ];
            
            if($CI->leads_model->add($data)){
                $data = [
                    'synced' => 1
                ];
        
                $updated = $CI->flexibleleadfindercontacts_model->update($contact['id'], $data);
                
                return $updated;
            }
        }
    
        return true;
    } catch (\Throwable $th) {
        throw $th;
    }
}

function flexibleleadfinder_sync_all_leads($leadfinder_id)
{
    $CI = &get_instance();
    $CI->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfindercontacts_model');
    $CI->load->model('leads_model');

    try {
        $conditions = [
            'leadfinder_id' => $leadfinder_id
        ];
        
        $contacts = $CI->flexibleleadfindercontacts_model->all($conditions);
        
        foreach($contacts as $contact){
            flexibleleadfinder_sync_lead($contact['id']);
        }
    
        return true;
    } catch (\Throwable $th) {
        throw $th;
    }
}

function flexibleleadfinder_get_staff_members(){
    $CI = &get_instance();
    $CI->load->model('staff_model');

    return $CI->staff_model->get();
}