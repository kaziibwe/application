<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        // Update last project avialble features to include the module. This ensure new projects have delivery notes enabled by defautl.
        $CI->load->model('projects_model');
        $last_project_settings = $CI->projects_model->get_last_project_settings();
        if (count($last_project_settings)) {
            $key                                          = array_search('available_features', array_column($last_project_settings, 'name'));
            $last_project_settings[$key]['value'] = unserialize($last_project_settings[$key]['value']);
            $last_project_id = $last_project_settings[$key]['project_id'];
            $last_project_features = $last_project_settings[$key]['value'];
            if (!in_array('delivery_notes', $last_project_features)) {
                $last_project_features['delivery_notes'] = 1;
                $new_last_project_features = serialize($last_project_features);
                $CI->projects_model->db->where('name', 'available_features');
                $CI->projects_model->db->where('project_id', $last_project_id);
                $CI->projects_model->db->update(db_prefix() . 'project_settings', ['value' => $new_last_project_features]);
            }
        }

        require(__DIR__ . '/../install.php');
    }

    public function down()
    {
    }
}
