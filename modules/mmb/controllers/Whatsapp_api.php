<?php

defined('BASEPATH') || exit('No direct script access allowed');

use WpOrg\Requests\Requests as Whatsapp_api_Requests;

class Whatsapp_api extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library(WHATSAPP_API_MODULE . '/whatsapp_api_lib');
        $this->load->helper(WHATSAPP_API_MODULE . '/whatsapp_api');
    }

    public function index()
    {
        $data['title']                 = _l('whatsapp_template_details');
        if (!has_permission('whatsapp_api', '', 'list_templates_view')) {
            access_denied('list_templates_view');
        }
        $this->load->view('whatsapp/template_list', $data);
    }

    public function datatable()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(MMB_MODULE, 'whatsapp/tables/template_list_table'));
        }
    }

    public function get_business_information()
    {
        $whatsapp_business_account_id = get_option('whatsapp_business_account_id');
        $whatsapp_access_token        = get_option('whatsapp_access_token');
        $request                      = Whatsapp_api_Requests::get(
            'https://graph.facebook.com/v14.0/' . $whatsapp_business_account_id . '?fields=id,name,message_templates,phone_numbers&access_token=' . $whatsapp_access_token
        );

        $response = json_decode($request->body);

        // if there is any error from api then display appropriate message
        if (property_exists($response, 'error')) {
            echo json_encode([
                'success' => false,
                'type'    => 'danger',
                'message' => $response->error->message,
            ]);
            exit();
        }
        $data        = $response->message_templates->data;
        $insert_data = [];

        foreach ($data as $key => $template_data) {
            //only consider "APPROVED" templates
            if ('APPROVED' == $template_data->status) {
                $insert_data[$key]['template_id']   = $template_data->id;
                $insert_data[$key]['template_name'] = $template_data->name;
                $insert_data[$key]['language']      = $template_data->language;

                $insert_data[$key]['status']   = $template_data->status;
                $insert_data[$key]['category'] = $template_data->category;

                $components = array_column($template_data->components, null, 'type');

                $insert_data[$key]['header_data_format']     = $components['HEADER']->format ?? '';
                $insert_data[$key]['header_data_text']          = $components['HEADER']->text ?? null;
                $insert_data[$key]['header_params_count'] = preg_match_all('/{{(.*?)}}/i', $components['HEADER']->text ?? '', $matches);

                $insert_data[$key]['body_data']            = $components['BODY']->text ?? null;
                $insert_data[$key]['body_params_count'] = preg_match_all('/{{(.*?)}}/i', $components['BODY']->text, $matches);

                $insert_data[$key]['footer_data']            = $components['FOOTER']->text ?? null;
                $insert_data[$key]['footer_params_count'] = preg_match_all('/{{(.*?)}}/i', $components['FOOTER']->text ?? '', $matches);

                $insert_data[$key]['buttons_data']     = json_encode($components['BUTTONS'] ?? []);
            }
        }
        $insert_data_id    = array_column($insert_data, 'template_id');
        $existing_template = $this->db->where_in(array_column($insert_data, 'template_id'))->get(db_prefix() . 'whatsapp_templates')->result();

        $existing_data_id = array_column($existing_template, 'template_id');

        $new_template_id = array_diff($insert_data_id, $existing_data_id);
        $new_template    = array_filter($insert_data, function ($val) use ($new_template_id) {
            return in_array($val['template_id'], $new_template_id);
        });

        //No need to update template data in db because you can't edit template in meta dashboard
        if (!empty($new_template)) {
            $this->db->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");
            $this->db->insert_batch(db_prefix() . 'whatsapp_templates', $new_template);
        }

        // GET TEMPLATE: END
        echo json_encode([
            'success' => true,
            'type'    => 'success',
            'message' => _l('template_data_loaded'),
        ]);
    }

    public function broadcast_messages()
    {
        $viewData['title'] = _l('broadcast_messages');

        if ($this->input->post()) {
            $post_data = $this->input->post();

            $image_url = base_url("assets/images/preview-not-available.jpg");
            if (!empty($_FILES['image']['name'])) {
                $image_name = handle_image_upload();
                if ($image_name) {
                    $image_url = module_dir_url("whatsapp_api", "uploads/broadcast_images/" . $image_name);
                }
            }

            $receiverData = $post_data['rel_id'] ?? [];

            switch ($post_data['rel_type']) {
                case 'leads':
                    $this->load->model('leads_model');
                    $receiverData = array_column($this->leads_model->get(), null, "id");
                    break;

                case 'staff':
                    $this->load->model('staff_model');
                    $receiverData = array_column($this->staff_model->get(), null, "staffid");
                    break;

                case 'customer':
                    $this->load->model('clients_model');
                    $clientData = $this->clients_model->get();
                    $contactData = array_map(function ($client) {
                        $primaryContact = get_primary_contact_user_id($client['userid']);
                        $contact         = $this->clients_model->get_contact($primaryContact);
                        if (!empty($contact)) {
                            $client['phonenumber'] = $contact->phonenumber;
                        }
                        return $client;
                    }, $clientData);
                    $receiverData = array_column(array_filter($contactData), null, "userid");
                    break;
            }

            if (isset($post_data['rel_id'])) {

                $rel_id = $post_data["rel_id"];

                $receiverData = array_filter($receiverData, function ($key) use ($rel_id) {
                    return in_array($key, $rel_id);
                }, ARRAY_FILTER_USE_KEY);
            }

            if ($post_data['rel_type'] == 'customer') {
                $post_data['rel_type'] = 'client';
            }

            $this->load->library(WHATSAPP_API_MODULE . '/whatsapp_api_lib');
            $this->whatsapp_api_lib->send_custom_message($receiverData, $post_data['template_name'], $post_data['broadcast_message'], $image_url, $post_data['debug_mode'] ?? 0, $post_data['rel_type']);
            echo json_encode([
                'success' => true,
            ]);
            die();
        }

        $this->load->view('whatsapp/broadcast_messages_view', $viewData);
    }

    public function get_template_data($id = '')
    {
        if ($this->input->is_ajax_request()) {
            $res = $this->whatsapp_api_model->get_template_data($id);
            echo json_encode($res ?? []);
        }
    }

    public function updateWhatsappAuthStatus()
    {
        $postData = $this->input->post();
        $res = $this->whatsapp_api_model->updateWhatsappAuthStatus($postData);
        $res ? set_alert('success', _l('two_factor_authentication_via_whatsapp_status_enabled')) : set_alert('danger', _l('something_went_wrong'));
        redirect(admin_url('staff/edit_profile/' . get_staff_user_id()));
    }
}

/* End of file Whatsapp_api.php */