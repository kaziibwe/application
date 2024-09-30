<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed');}

class Template_mapping extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(WHATSAPP_API_MODULE . '/whatsapp_api_model');
    }

    public function index()
    {
        $data['title'] = _l('whatsapp_template_mapping');

        if (!has_permission('whatsapp_api', '', 'template_mapping_view')) {
            access_denied('template_mapping_view');
        }

        $this->load->view('whatsapp/template_mapping', $data);
    }

    public function template_mapping_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(MMB_MODULE, 'whatsapp/tables/template_mapping_table'));
        }
    }

    public function add($id = '')
    {
        if (!has_permission('whatsapp_api', '', 'template_mapping_add')) {
            access_denied('template_mapping_create');
        }
        $all_merge_fields         = $this->app_merge_fields->all();
        $data['all_merge_fields'] = $all_merge_fields;

        if (is_numeric($id)) {
            $result                        = $this->whatsapp_api_model->get_mapping_data([db_prefix() . 'whatsapp_templates_mapping.id' => $id]);
            $mapping_data['template_info'] = $result[array_key_first($result)];
            if (!$mapping_data['template_info']) {
                set_alert('danger', _l('something_went_wrong'));
                redirect(admin_url(WHATSAPP_API_MODULE . '/template_mapping'));
            }
            $mapping_data['available_merge_fields'] = get_category_wise_merge_fields([$mapping_data['template_info']->category, 'other']);
            $this->load->view('whatsapp/add_template_mapping', $mapping_data);
        } else {
            $this->load->view('whatsapp/add_template_mapping', $data);
        }
    }

    public function get_template_map()
    {
        if ($this->input->is_ajax_request()) {
            $post                                   = $this->input->post();
            $mapping_data['available_merge_fields'] = get_category_wise_merge_fields([$post['category'], 'other']);
            $mapping_data['template_info']          = $this->whatsapp_api_model->get_template_data($post['template_id']);
            if (empty($mapping_data['template_info']->header_data_format) || 'TEXT' == $mapping_data['template_info']->header_data_format || 'DOCUMENT' == $mapping_data['template_info']->header_data_format) {
                echo $this->load->view('whatsapp/mapping_form', $mapping_data, true);
            } else {
                echo "<div class='alert alert-danger'> Currently <strong>" . ucwords(strtolower($mapping_data['template_info']->header_data_format)) . '</strong> template type is not supported!</div>';
            }
        }
    }

    public function save($id = '')
    {
        $post = $this->input->post();

        $header_params = '{}';
        if (isset($post['header_params'])) {
            $header_params = json_encode($post['header_params']);
        }

        $body_params = '{}';
        if (isset($post['body_params'])) {
            $body_params = json_encode($post['body_params']);
        }

        $footer_params = '{}';
        if (isset($post['footer_params'])) {
            $footer_params = json_encode($post['footer_params']);
        }

        $map_info = [
            'template_id'   => $post['template_name'],
            'category'      => $post['category'],
            'send_to'       => $post['send_to'],
            'header_params' => $header_params,
            'body_params'   => $body_params,
            'footer_params' => $footer_params,
        ];

        if (is_numeric($id)) {
            $where['id'] = $id;
            if ($this->whatsapp_api_model->update_template_map_info($map_info, $where)) {
                set_alert('success', _l('updated_successfully', _l('mapped_template')));
            }
        } else {
            if ($this->whatsapp_api_model->save_template_map_info($map_info)) {
                set_alert('success', _l('added_successfully', _l('mapped_template')));
            }
        }
        redirect(admin_url(WHATSAPP_API_MODULE . '/template_mapping'), 'refresh');
    }

    public function delete($template_id)
    {
        $this->load->model(WHATSAPP_API_MODULE . '/whatsapp_api_model');
        if ($this->whatsapp_api_model->delete_whatsapp_templates_mapping(['id' => $template_id])) {
            set_alert('success', _l('deleted', _l('mapped_template')));
        }
        redirect(admin_url(WHATSAPP_API_MODULE . '/template_mapping'));
    }

    public function change_status_hook($id, $status)
    {
        $this->load->model(WHATSAPP_API_MODULE . '/whatsapp_api_model');
        $data['active'] = $status;
        $where['id']    = $id;
        if ($this->whatsapp_api_model->change_whatsapp_template_status($data, $where)) {
            echo json_encode(['success' => true]);

            return true;
        }
        echo json_encode(['success' => false]);

        return false;
    }

    public function change_debug_status_hook($id, $status)
    {
        $this->load->model(WHATSAPP_API_MODULE . '/whatsapp_api_model');
        $data['debug_mode'] = $status;
        $where['id']        = $id;
        if ($this->whatsapp_api_model->change_whatsapp_template_status($data, $where)) {
            echo json_encode(['success' => true]);

            return true;
        }
        echo json_encode(['success' => false]);

        return false;
    }
}
