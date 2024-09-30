<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Templates Controller
 * 
 * Handles operations related to WhatsApp templates.
 */
class Templates extends AdminController
{
    use modules\whatsbot\traits\Whatsapp; // Uses a trait for WhatsApp related methods

    /**
     * Constructor
     * 
     * Initializes the controller and checks module activation status.
     */
    public function __construct()
    {
        parent::__construct();

        // Check if the whatsbot module is inactive; deny access if so
        if ($this->app_modules->is_inactive('whatsbot')) {
            access_denied();
        }

        $this->load->model('whatsbot_model'); // Load the WhatsApp bot model
    }

    /**
     * Index method
     * 
     * Loads the main view for WhatsApp templates management.
     */
    public function index()
    {
        // Check if user has permission to view WhatsApp templates
        if (!staff_can('view', 'wtc_template')) {
            access_denied();
        }

        $viewData['title'] = _l('templates'); // Set view title

        $this->load->view('templates', $viewData); // Load templates view
    }

    /**
     * Get Table Data method
     * 
     * Retrieves data for the templates table via AJAX.
     * 
     * @return bool Returns false if the request is not an AJAX request.
     */
    public function get_table_data()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $this->app->get_table_data(module_views_path(WHATSBOT_MODULE, 'tables/templates')); // Get table data
    }

    /**
     * Load Templates method
     * 
     * Loads WhatsApp templates asynchronously.
     * 
     * @return bool Returns false if the request is not an AJAX request or if the user lacks permission.
     */
    public function load_templates()
    {
        if (!$this->input->is_ajax_request() && !staff_can('load_template', 'wtc_template')) {
            return false;
        }

        $response = $this->whatsbot_model->load_templates(); // Call model method to load templates

        if (false == $response['success']) {
            // If loading templates fails, return error response
            echo json_encode([
                'success' => $response['success'],
                'type'    => $response['type'],
                'message' => $response['message'],
            ]);
            exit();
        }

        // If templates are loaded successfully, return success response
        echo json_encode([
            'success' => true,
            'type'    => 'success',
            'message' => _l('template_data_loaded'),
        ]);
    }
}
