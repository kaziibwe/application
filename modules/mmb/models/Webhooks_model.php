<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Webhooks_model extends App_Model
{
    protected $webhook_table;
    protected $webhook_log_table;

    public function __construct()
    {
        parent::__construct();
        $this->webhook_table      = db_prefix() . 'webhooks_master';
        $this->webhook_log_table = db_prefix() . 'webhooks_debug_log';
    }

    public function change_webhook_status($data, $where)
    {
        if ($this->db->update($this->webhook_table, $data, $where)) {
            return true;
        }

        return false;
    }

    public function delete_webhook($where)
    {
        if ($this->db->delete($this->webhook_table, $where)) {
            return true;
        }

        return false;
    }

    public function get($id = null)
    {
        if (!empty($id)) {
            $this->db->where('id', $id);
        }
        $this->db->from($this->webhook_table);

        return $this->db->get()->row();
    }

    public function getAll($webhook_for = null)
    {
        if (!empty($webhook_for)) {
            $this->db->where('webhook_for', $webhook_for);
        }
        $this->db->where('active', 1);

        return $this->db->get($this->webhook_table)->result();
    }

    public function add($data)
    {
        if ($this->db->insert($this->webhook_table, $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    public function update($data, $where)
    {
        if ($this->db->update($this->webhook_table, $data, $where)) {
            return true;
        }

        return false;
    }

    public function clear_webhook_log()
    {
        if ($this->db->truncate($this->webhook_log_table)) {
            return true;
        }

        return false;
    }

    public function add_log($data)
    {
        if ($this->db->insert($this->webhook_log_table, $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    public function get_log_info($id)
    {
        return $this->db->get_where($this->webhook_log_table, ["id" => $id])->row();
    }
    public function copy_webhook($data)
    {
        $_data = $data;
        $copy_data = [
            'name'           => $_data['name'] . ' - copy',
            'webhook_for'    => $_data['webhook_for'],
            'webhook_action' => $_data['webhook_action'],
            'request_url'    => $_data['request_url'],
            'request_method' => $_data['request_method'],
            'request_format' => $_data['request_format'],
            'request_header' => $_data['request_header'],
            'request_body'   => $_data['request_body'],
            'debug_mode'     => $_data['debug_mode'],
            'created_at'     => $_data['created_at'],
        ];
        return $this->add($copy_data);
    }
}
