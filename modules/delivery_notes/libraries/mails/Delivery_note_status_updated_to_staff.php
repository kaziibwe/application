<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_note_status_updated_to_staff extends App_mail_template
{
    protected $for = 'staff';

    protected $delivery_note;

    protected $staff;

    // Staff editing the resources
    protected $editor;

    protected $contact_id;

    public $slug = 'delivery-note-status-updated-to-staff';

    public $rel_type = 'delivery_note';

    public function __construct($delivery_note, $staff, $contact_id, $editor)
    {
        parent::__construct();

        $this->delivery_note    = $delivery_note;
        $this->staff = (object)$staff;
        $this->contact_id  = $contact_id;
        $this->editor = (object)$editor;
    }

    public function build()
    {
        $this->to($this->staff->email)
            ->set_rel_id($this->delivery_note->id)
            ->set_merge_fields('client_merge_fields', $this->delivery_note->clientid, $this->contact_id)
            ->set_merge_fields('staff_merge_fields', $this->staff->staffid)
            ->set_merge_fields('editor_merge_fields', $this->editor)
            ->set_merge_fields('delivery_note_merge_fields', $this->delivery_note->id);
    }
}
