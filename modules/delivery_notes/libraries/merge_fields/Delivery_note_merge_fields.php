<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_note_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Delivery Note Link',
                'key'       => '{delivery_note_link}',
                'available' => [
                    'delivery_note',
                ]
            ],
            [
                'name'      => 'Delivery Note Number',
                'key'       => '{delivery_note_number}',
                'available' => [
                    'delivery_note',
                ],
            ],
            [
                'name'      => 'Reference no.',
                'key'       => '{delivery_note_reference_no}',
                'available' => [
                    'delivery_note',
                ],
            ],
            [
                'name'      => 'Delivery Note Date',
                'key'       => '{delivery_note_date}',
                'available' => [
                    'delivery_note',
                ],
            ],
            [
                'name'      => 'Delivery Note Status',
                'key'       => '{delivery_note_status}',
                'available' => [
                    'delivery_note',
                ],
            ],
            [
                'name'      => 'Delivery Note Sale Agent',
                'key'       => '{delivery_note_sale_agent}',
                'available' => [
                    'delivery_note',
                ],
            ],
            [
                'name'      => 'Delivery Note Total',
                'key'       => '{delivery_note_total}',
                'available' => [
                    'delivery_note',
                ],
            ],
            [
                'name'      => 'Delivery Note Subtotal',
                'key'       => '{delivery_note_subtotal}',
                'available' => [
                    'delivery_note',
                ],
            ]
        ];
    }

    /**
     * Merge fields for delivery_notes
     * @param  mixed $delivery_noteid delivery_note id
     * @return array
     */
    public function format($delivery_noteid)
    {
        $fields = [];
        $this->ci->db->where('id', $delivery_noteid);
        $delivery_note = $this->ci->db->get(db_prefix() . 'delivery_notes')->row();

        if (!$delivery_note) {
            return $fields;
        }

        $currency = get_currency($delivery_note->currency);

        $fields['{delivery_note_sale_agent}']   = get_staff_full_name($delivery_note->sale_agent);
        $fields['{delivery_note_total}']        = app_format_money($delivery_note->total, $currency);
        $fields['{delivery_note_subtotal}']     = app_format_money($delivery_note->subtotal, $currency);
        $fields['{delivery_note_link}']         = site_url('delivery_notes/client/dn/' . $delivery_noteid . '/' . $delivery_note->hash);
        $fields['{delivery_note_number}']       = format_delivery_note_number($delivery_noteid);
        $fields['{delivery_note_reference_no}'] = $delivery_note->reference_no;
        $fields['{delivery_note_date}']         = _d($delivery_note->date);
        $fields['{delivery_note_status}']       = format_delivery_note_status($delivery_note->status, '', false);
        $fields['{project_name}']          = get_project_name_by_id($delivery_note->project_id);
        $fields['{delivery_note_short_url}']    = get_delivery_note_shortlink($delivery_note);

        $custom_fields = get_custom_fields('delivery_note');
        foreach ($custom_fields as $field) {
            $fields['{' . $field['slug'] . '}'] = get_custom_field_value($delivery_noteid, $field['id'], 'delivery_note');
        }

        return hooks()->apply_filters('delivery_note_merge_fields', $fields, [
            'id'       => $delivery_noteid,
            'delivery_note' => $delivery_note,
        ]);
    }
}
