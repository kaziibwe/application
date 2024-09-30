<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Email template class for all mail sent through the affiliate management.
 * The class is inherited by all affiliate management email templates.
 */
trait AffiliatemanagementMailTemplate
{

    /**
     * Client email address
     *
     * @var string
     */
    protected $contact_email;

    /**
     * The ID of the client.
     *
     * @var mixed
     */
    protected $client_id;

    /**
     * The ID of the client contact.
     *
     * @var mixed
     */
    protected $contact_id;

    /**
     * Instance
     *
     * @var mixed
     */
    protected $template_data;


    /**
     * The constructor.
     * This is called when perfex is creating instance of this template
     *
     * @param string $contact_email
     * @param int $client_id
     * @param int $contact_id
     * @param mixed $template_data
     */
    public function __construct($contact_email, $client_id, $contact_id, $template_data)
    {
        parent::__construct();

        $this->contact_email = $contact_email;
        $this->contact_id    = $contact_id;
        $this->client_id     = $client_id;
        $this->template_data = $template_data;
    }

    /**
     * Build the email message.
     */
    public function build()
    {
        // Load required libraries
        $this->ci->load->library('merge_fields/client_merge_fields');
        $this->ci->load->library(AFFILIATE_MANAGEMENT_MODULE_NAME . '/merge_fields/' . AFFILIATE_MANAGEMENT_MODULE_NAME . '_merge_fields');

        // Set email properties
        $this->to($this->contact_email)                                // Set the recipient email address
            ->set_rel_id($this->contact_id)                            // Set the relationship ID
            ->set_merge_fields('client_merge_fields', $this->client_id, $this->contact_id)   // Set merge fields for client
            ->set_merge_fields(AFFILIATE_MANAGEMENT_MODULE_NAME . '_merge_fields', $this->template_data);   // Set merge fields for affiliate management
    }
}