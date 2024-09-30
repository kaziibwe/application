<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Controller for WhatsApp integration functionalities.
 */
class Whatsbot extends AdminController
{
    use modules\whatsbot\traits\Whatsapp;

    /**
     * Constructor for Whatsbot controller.
     * Loads necessary models.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['whatsbot_model', 'interaction_model']);
    }

    /**
     * Default entry point. Redirects to connect account page.
     */
    public function index()
    {
        redirect('whatsbot/connect_account');
    }

    /**
     * Manages the connection settings for a WhatsApp business account.
     * Checks permissions, handles form submissions, updates options, and manages redirection.
     */
    public function connect_account()
    {
        if (!staff_can('connect', 'wtc_connect_account')) {
            access_denied();
        }

        $data['title'] = _l('connect_whatsapp_business');
        if ($this->input->post()) {
            update_option('wac_business_account_id', $this->input->post('wac_business_account_id'), 0);
            update_option('wac_access_token', $this->input->post('wac_access_token'), 0);
            $response = $this->whatsbot_model->load_templates();
            if (false == $response['success']) {
                set_alert($response['type'], $response['message']);
            } else {
                $phone_numbers = $this->getPhoneNumbers();
                $profile_data = $this->getProfile();
                update_option('wac_phone_number_id', $phone_numbers['data'][array_key_first($phone_numbers['data'])]->id, 0);
                if (isset($profile_data['data']) && isset($profile_data['data']->profile_picture_url)) {
                    update_option('wac_profile_picture_url', $profile_data['data']->profile_picture_url, 0);
                } else {
                    update_option('wac_profile_picture_url', '');
                }
                $default_number = $phone_numbers['data'][array_key_first($phone_numbers['data'])]->display_phone_number;
                update_option('wac_default_phone_number', preg_replace('/\D/', '', $default_number), 0);
                set_alert('success', ('submit' == $this->input->post('submit')) ? _l('account_connected') : _l('settings_updated'));
            }
            redirect(admin_url('whatsbot/connect_account'));
        }
        $data['phone_numbers'] = [];
        if (option_exists('wac_business_account_id')) {
            $phone_numbers = $this->getPhoneNumbers();
            if ($phone_numbers['status']) {
                $data['phone_numbers'] = $phone_numbers['data'];
            }
        }

        $data['is_connected'] = false;
        if (!empty(get_option('wac_business_account_id')) && !empty(get_option('wac_access_token')) && !empty(get_option('wac_phone_number_id')) && !empty(get_option('wac_default_phone_number'))) {
            $data['is_connected'] = true;
        }
        $this->load->view('connect_account', $data);
    }

    /**
     * Sets the default phone number ID via an AJAX request.
     * Updates the 'wac_phone_number_id' option based on the submitted form data.
     */
    public function set_default_number_phone_number_id()
    {
        if (!$this->input->is_ajax_request()) {
            return;
        }

        update_option('wac_phone_number_id', $this->input->post('wac_phone_number_id'));
        update_option('wac_default_phone_number', $this->input->post('wac_default_phone_number'));

        set_alert('success', _l('default_number_updated'));
        echo json_encode(true);
    }

    /**
     * Displays the chat interface if the user has the necessary view permissions.
     */
    public function chat()
    {
        if (!staff_can('view', 'wtc_chat')) {
            access_denied();
        }

        $data['title'] = _l('chat');
        $this->load->view('interaction', $data);
    }

    /**
     * Disconnects the WhatsApp business account and clears related data.
     * Resets all related options and truncates the template table.
     */
    public function disconnect()
    {
        update_option('wac_business_account_id', '');
        update_option('wac_access_token', '');
        update_option('wac_phone_number_id', '');
        update_option('wac_default_phone_number', '');
        update_option('wac_profile_picture_url', '');
        $this->db->truncate(db_prefix() . 'wtc_templates');
        set_alert('danger', _l('account_disconnected'));
        redirect(admin_url('whatsbot/connect_account'));
    }

    /**
     * Fetches and sends interaction data as a JSON response.
     * Retrieves interaction data from the model and outputs it as JSON.
     */
    public function interactions()
    {
        $data['interactions'] = $this->interaction_model->get_interactions();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Loads the activity log view if the user has view permissions.
     * Displays the activity log related to WhatsApp interactions.
     */
    public function activity_log()
    {
        if (!staff_can('view', 'wtc_log_activity')) {
            access_denied('activity_log');
        }
        $data['title'] = _l('activity_log');
        $this->load->view('activity_log/whatsbot_activity_log', $data);
    }

    /**
     * Handles AJAX request for activity log table data.
     * Fetches and displays the activity log table via AJAX.
     */
    public function activity_log_table()
    {
        if (!$this->input->is_ajax_request()) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path(WHATSBOT_MODULE, 'tables/activity_log_table'));
    }

    /**
     * Loads the view for log details based on a specific log ID.
     * Retrieves detailed information about a specific log entry.
     */
    public function view_log_details($id = '')
    {
        $data['title'] = _l('activity_log');
        $data['log_data'] = $this->whatsbot_model->getWhatsappLogDetails($id);

        $this->load->view('activity_log/view_log_details', $data);
    }

    /**
     * Marks a chat interaction as read and returns the response as JSON.
     * Updates the status of a chat interaction to 'read'.
     */
    public function chat_mark_as_read()
    {
        $id       = $this->input->post('interaction_id');
        $response = $this->interaction_model->chat_mark_as_read($id);
        echo json_encode($response);
    }

    /**
     * Clears the activity log if the user has the necessary permissions.
     * Truncates the activity log table and sets an alert message.
     */
    public function clear_log()
    {
        if (staff_can('clear_log', 'wtc_log_activity')) {
            $this->db->truncate(db_prefix() . 'wtc_activity_log');
            set_alert('danger', _l('log_cleared_successfully'));
        }
        redirect(admin_url('whatsbot/activity_log'));
    }

    public function delete_log($id)
    {
        if (staff_can('clear_log', 'wtc_log_activity')) {
            $delete = $this->whatsbot_model->delete_log($id);
            set_alert('danger', $delete ? _l('deleted', _l('log')) : _l('something_went_wrong'));
        }
        redirect(admin_url('whatsbot/activity_log'));
    }

    public function delete_chat()
    {
        if (!$this->input->is_ajax_request()) {
            ajax_access_denied();
        }
        $id = $this->input->post('interaction_id');
        $res = $this->whatsbot_model->delete_chat($id);
        echo json_encode($res);
    }

    public function assign_staff()
    {
        $post_data = $this->input->post();
        $res = $this->interaction_model->add_assign_staff($post_data);
        echo json_encode($res);
    }

    public function remove_staff()
    {
        $post_data = $this->input->post();
        $res = $this->interaction_model->remove_staff($post_data);
        echo json_encode($res);
    }
}
