<?php

namespace app\modules\delivery_notes\services;

use app\services\AbstractKanban;

class DeliveryNotePipeline extends AbstractKanban
{
    protected function table(): string
    {
        return 'delivery_notes';
    }

    public function defaultSortDirection()
    {
        return get_option('default_delivery_notes_pipeline_sort_type');
    }

    public function defaultSortColumn()
    {
        return get_option('default_delivery_notes_pipeline_sort');
    }

    public function limit()
    {
        return get_option('delivery_notes_pipeline_limit');
    }

    protected function applySearchQuery($q): self
    {
        if (!startsWith($q, '#')) {
            $fields_client    = $this->ci->db->list_fields(db_prefix() . 'clients');
            $fields_delivery_notes = $this->ci->db->list_fields(db_prefix() . 'delivery_notes');

            $q = $this->ci->db->escape_like_str($q);

            $where = '(';
            $i     = 0;
            foreach ($fields_client as $f) {
                $where .= db_prefix() . 'clients.' . $f . ' LIKE "%' . $q . '%" ESCAPE \'!\'';
                $where .= ' OR ';
                $i++;
            }
            $i = 0;
            foreach ($fields_delivery_notes as $f) {
                $where .= db_prefix() . 'delivery_notes.' . $f . ' LIKE "%' . $q . '%" ESCAPE \'!\'';
                $where .= ' OR ';

                $i++;
            }
            $where = substr($where, 0, -4);
            $where .= ')';
            $this->ci->db->where($where);
        } else {
            $this->ci->db->where(db_prefix() . 'delivery_notes.id IN
                (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                (SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . $this->ci->db->escape_str(strafter($search, '#')) . '")
                AND ' . db_prefix() . 'taggables.rel_type=\'delivery_note\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
        }

        return $this;
    }

    protected function initiateQuery(): self
    {
        $has_permission_view = staff_can('view',  'delivery_notes');
        $noPermissionQuery   = get_delivery_notes_where_sql_for_staff(get_staff_user_id());

        $this->ci->db->select(db_prefix() . 'delivery_notes.id,status,invoiceid,' . get_sql_select_client_company() . ',total,currency,symbol,' . db_prefix() . 'currencies.name as currency_name,date,clientid');
        $this->ci->db->from('delivery_notes');
        $this->ci->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'delivery_notes.clientid', 'left');
        $this->ci->db->join(db_prefix() . 'currencies', db_prefix() . 'delivery_notes.currency = ' . db_prefix() . 'currencies.id');
        $this->ci->db->where('status', $this->status);

        if (!$has_permission_view) {
            $this->ci->db->where($noPermissionQuery);
        }

        return $this;
    }
}
