<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Proposals_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
                [
                    'name'      => 'Proposal ID',
                    'key'       => '{proposal_id}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Proposal Number',
                    'key'       => '{proposal_number}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Subject',
                    'key'       => '{proposal_subject}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Proposal Total',
                    'key'       => '{proposal_total}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Proposal Subtotal',
                    'key'       => '{proposal_subtotal}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Open Till',
                    'key'       => '{proposal_open_till}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Proposal Assigned',
                    'key'       => '{proposal_assigned}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Proposal To',
                    'key'       => '{proposal_proposal_to}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Address',
                    'key'       => '{proposal_address}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'City',
                    'key'       => '{proposal_city}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'State',
                    'key'       => '{proposal_state}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Zip Code',
                    'key'       => '{proposal_zip}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Country',
                    'key'       => '{proposal_country}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Email',
                    'key'       => '{proposal_email}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Phone',
                    'key'       => '{proposal_phone}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Proposal Link',
                    'key'       => '{proposal_link}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Created At',
                    'key'       => '{proposal_created_at}',
                    'available' => [
                        'proposals',
                    ],
                ],
                [
                    'name'      => 'Proposal Date',
                    'key'       => '{proposal_date}',
                    'available' => [
                        'proposals',
                    ],
                ],
            ];
    }

    /**
 * Merge fields for proposals
 * @param  mixed $proposal_id proposal id
 * @return array
 */
    public function format($proposal_id)
    {
        $fields = [];
        $this->ci->db->where('id', $proposal_id);
        $this->ci->db->join(db_prefix() . 'countries', db_prefix() . 'countries.country_id=' . db_prefix() . 'proposals.country', 'left');
        $proposal = $this->ci->db->get(db_prefix() . 'proposals')->row();


        if (!$proposal) {
            return $fields;
        }

        if ($proposal->currency != 0) {
            $currency = get_currency($proposal->currency);
        } else {
            $currency = get_base_currency();
        }

        $fields['{proposal_id}']          = $proposal_id;
        $fields['{proposal_number}']      = e(format_proposal_number($proposal_id));
        $fields['{proposal_link}']        = site_url('proposal/' . $proposal_id . '/' . $proposal->hash);
        $fields['{proposal_subject}']     = e($proposal->subject);
        $fields['{proposal_total}']       = e(app_format_money($proposal->total, $currency));
        $fields['{proposal_subtotal}']    = e(app_format_money($proposal->subtotal, $currency));
        $fields['{proposal_open_till}']   = e(_d($proposal->open_till));
        $fields['{proposal_proposal_to}'] = e($proposal->proposal_to);
        $fields['{proposal_address}']     = e($proposal->address);
        $fields['{proposal_email}']       = e($proposal->email);
        $fields['{proposal_phone}']       = e($proposal->phone);

        $fields['{proposal_city}']       = e($proposal->city);
        $fields['{proposal_state}']      = e($proposal->state);
        $fields['{proposal_zip}']        = e($proposal->zip);
        $fields['{proposal_country}']    = e($proposal->short_name);
        $fields['{proposal_assigned}']   = e(get_staff_full_name($proposal->assigned));
        $fields['{proposal_short_url}']  = get_proposal_shortlink($proposal);
        $fields['{proposal_created_at}'] = e(_dt($proposal->datecreated));
        $fields['{proposal_date}']       = e(_d($proposal->date));

        $custom_fields = get_custom_fields('proposal');
        foreach ($custom_fields as $field) {
            $fields['{' . $field['slug'] . '}'] = get_custom_field_value($proposal_id, $field['id'], 'proposal');
        }

        return hooks()->apply_filters('proposal_merge_fields', $fields, [
        'id'       => $proposal_id,
        'proposal' => $proposal,
     ]);
    }
}
