<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_note_send_to_customer extends App_mail_template
{
    protected $for = 'customer';

    protected $delivery_note;

    protected $contact;

    public $slug = 'delivery-note-send-to-client';

    public $rel_type = 'delivery_note';

    public function __construct($delivery_note, $contact, $cc = '')
    {
        parent::__construct();

        $this->delivery_note = $delivery_note;
        $this->contact = $contact;
        $this->cc      = $cc;
    }

    public function build()
    {
        if ($this->ci->input->post('email_attachments')) {
            $_other_attachments = $this->ci->input->post('email_attachments');
            foreach ($_other_attachments as $attachment) {
                $_attachment = $this->ci->delivery_notes_model->get_attachments($this->delivery_note->id, $attachment);
                $this->add_attachment([
                    'attachment' => get_upload_path_by_type('delivery_note') . $this->delivery_note->id . '/' . $_attachment->file_name,
                    'filename'   => $_attachment->file_name,
                    'type'       => $_attachment->filetype,
                    'read'       => true,
                ]);
            }
        }

        $this->to($this->contact->email)
            ->set_rel_id($this->delivery_note->id)
            ->set_merge_fields('client_merge_fields', $this->delivery_note->clientid, $this->contact->id)
            ->set_merge_fields('delivery_note_merge_fields', $this->delivery_note->id);
    }
}
