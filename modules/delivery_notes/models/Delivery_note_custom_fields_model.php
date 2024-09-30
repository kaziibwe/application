<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'models/Custom_fields_model.php');

/**
 * Custom fields class for delivery note.
 */
class Delivery_note_custom_fields_model extends Custom_fields_model
{

    /**
     * @inheritDoc
     */
    public function add($data)
    {
        $insert_id = parent::add($data);

        if ($data['fieldto'] !== 'delivery_note' || !$insert_id)
            return $insert_id;

        return $this->custom_update($insert_id, $data);
    }

    /**
     * @inheritDoc
     */
    public function update($data, $id)
    {
        $updated = parent::update($data, $id);
        if ($data['fieldto'] !== 'delivery_note')
            return $updated;

        return $this->custom_update($id, $data);
    }

    /**
     * Custom update to the custom field for delivery note
     * @param mixed $data All $_POST data
     * @return  boolean
     */
    protected function custom_update($id, $data)
    {
        $new_data = [];
        // We need to always save this value if the box is checked
        if (isset($data['show_on_pdf'])) {
            $new_data['show_on_pdf'] = 1;
        }
        if (isset($data['show_on_client_portal'])) {
            $new_data['show_on_client_portal'] = 1;
        }
        $this->db->where('id', $id);
        if ($this->db->update(db_prefix() . 'customfields', $new_data))
            return $id;
        else
            return false;
    }
}
