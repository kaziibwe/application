<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'libraries/App_bulk_pdf_export.php');

class Delivery_notes_bulk_pdf_export extends App_bulk_pdf_export
{
    /**
     * Create delivery_notes export
     * @return object
     */
    protected function delivery_notes()
    {
        $noPermissionQuery = get_delivery_notes_where_sql_for_staff(get_staff_user_id());

        $this->ci->db->select('id');
        $this->ci->db->from(db_prefix() . 'delivery_notes');

        if ($this->status != 'all') {
            $this->ci->db->where('status', $this->status);
        }

        if (!$this->can_view) {
            $this->ci->db->where($noPermissionQuery);
        }

        $this->ci->db->order_by($this->get_date_column(), 'desc');

        $data = $this->finalize();
        $this->ci->load->model('delivery_notes_model');
        foreach ($data as $delivery_note) {
            $delivery_note = $this->ci->delivery_notes_model->get($delivery_note['id']);
            $pdf      = delivery_note_pdf($delivery_note, $this->pdf_tag);
            $this->save_to_dir($delivery_note, $pdf, strtoupper(slug_it(format_delivery_note_number($delivery_note->id))) . '.pdf');
        }

        return $this;
    }
}
