<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Bots Controller
 * 
 * Handles the functionality related to bots management.
 */
class Bots extends AdminController
{
    /**
     * Constructor
     * 
     * Loads necessary models.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['bots_model', 'campaigns_model']);
    }

    /**
     * Index method
     * 
     * Loads the main view for bots management.
     */
    public function index()
    {
        $data['type'] = !$this->input->get('group') ? 'text' : $this->input->get('group');

        if (!staff_can('view', ('text' == $data['type']) ? 'wtc_message_bot' : 'wtc_template_bot')) {
            access_denied();
        }
        $data['title'] = ('text' == $data['type']) ? _l('message_bot') : _l('template_bot');
        $this->load->view('bots/manage', $data);
    }

    /**
     * Bot method
     * 
     * Loads the view for creating or editing a bot.
     * 
     * @param string $type The type of the bot ('template' or 'text').
     * @param string $id The ID of the bot (optional).
     */
    public function bot($type, $id = '')
    {
        $permission = (empty($id)) ? 'create' : 'edit';
        if (!staff_can($permission, ('template' == $type) ? 'wtc_template_bot' : 'wtc_message_bot') && ($this->session->has_userdata('is_bot_clone') == false)) {
            access_denied();
        }

        $data['title'] = ('template' == $type) ? _l('create_template_base_bot') : _l('create_message_bot');
        if (!empty($id)) {
            $data['bot'] = ('template' == $type) ? $this->bots_model->getTemplateBot($id) : $this->bots_model->getMessageBot($id);
        }
        $data['templates']  = wb_get_whatsapp_template();
        ('template' == $type) ? $this->load->view('bots/template_bot', $data) : $this->load->view('bots/message_bot', $data);
    }

    /**
     * Table method
     * 
     * Loads the data for the specified table.
     * 
     * @param string $table The table name.
     */
    public function table($table)
    {
        if (!$this->input->is_ajax_request()) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path(WHATSBOT_MODULE, 'tables/'.$table));
    }

    /**
     * Save Bots method
     * 
     * Saves bot data from POST request.
     */
    public function saveBots()
    {
        if ($this->input->post()) {
            $permission = empty($this->input->post('id')) ? 'create' : 'edit';
            if (!staff_can($permission, 'wtc_message_bot') && ($this->session->has_userdata('is_bot_clone') == false)) {
                access_denied();
            }

            $res = $this->bots_model->saveBots($this->input->post());
            $this->session->set_userdata('is_bot_clone', false);
            wb_handle_whatsbot_upload($res['id']);
            set_alert($res['type'], $res['message']);
        }
        redirect(admin_url('whatsbot/bots'));
    }

    /**
     * Delete Message Bot method
     * 
     * Deletes a message bot based on type and ID.
     * 
     * @param string $type The type of the bot.
     * @param string $id The ID of the bot.
     */
    public function deleteMessageBot($type, $id)
    {
        if (!$this->input->is_ajax_request()) {
            ajax_access_denied();
        }

        $res = $this->bots_model->deleteMessageBot($type, $id);
        echo json_encode($res);
    }

    /**
     * Save Template Bot method
     * 
     * Saves template bot data from POST request.
     */
    public function saveTemplateBot()
    {
        if ($this->input->post()) {
            $permission = empty($this->input->post('id')) ? 'create' : 'edit';
            if (!staff_can($permission, 'wtc_template_bot') && ($this->session->has_userdata('is_bot_clone') == false)) {
                access_denied();
            }

            $res = $this->bots_model->saveTemplateBot($this->input->post());
            $this->session->set_userdata('is_bot_clone', false);
            wb_handle_campaign_upload($res['temp_id'], 'template');
            set_alert($res['type'], $res['message']);
        }
        redirect(admin_url('whatsbot/bots?group=template'));
    }

    /**
     * Change Active Status method
     * 
     * Changes the active status of a bot.
     * 
     * @param string $type The type of the bot.
     * @param string $id The ID of the bot.
     * @param string $status The new status of the bot.
     */
    public function change_active_status($type, $id, $status)
    {
        if (!$this->input->is_ajax_request()) {
            ajax_access_denied();
        }
        echo json_encode($this->bots_model->change_active_status($type, $id, $status));
    }

    /**
     * Delete bot files
     * @param  string $id The ID of the bot
     */
    public function delete_bot_files($id)
    {
        $res = $this->bots_model->delete_bot_files($id);
        set_alert('danger', $res['message']);
        redirect(admin_url('whatsbot/bots/bot/text/' . $id));
    }

    /**
     * Bot method
     * 
     * create clone of bots.
     * 
     * @param string $type The type of the bot ('template' or 'text').
     * @param string $id The ID of the bot.
     */
    public function clone_bot($type, $id)
    {
        if (!staff_can('clone_bot', ('template' == $type) ? 'wtc_template_bot' : 'wtc_message_bot')) {
            access_denied();
        }

        $data['title'] = ('template' == $type) ? _l('edit_template_base_bot') : _l('edit_message_bot');
        $res = $this->bots_model->clone_bot($type, $id);
        $this->session->set_userdata('is_bot_clone', $res);
        set_alert($res['type'], $res['message']);
        redirect(admin_url('whatsbot/bots/bot/' . $type . '/' . $res['id']));
    }
}
