<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Flexibleworkflow_model extends App_Model
{
    protected $table = 'flexibleworkflows';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $conditions
     * @return array|array[]
     * get all models
     */
    public function all($conditions = [])
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . $this->table);
        if (!empty($conditions)) {
            $this->db->where($conditions);
        }
        $this->db->order_by('order', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @param $conditions
     * @return array
     * get model by conditions
     */
    public function get($conditions)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . $this->table);
        $this->db->where($conditions);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * @param $data
     * @return bool
     * add model
     */
    public function add($data)
    {
        $this->db->insert(db_prefix() . $this->table, $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Workflow Added [ID:' . $insert_id . ', ' . $data['section_name'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * @param $id
     * @param $data
     * @return bool
     * update model
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . $this->table, $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Workflow Updated [ID:' . $id . ', ' . $data['section_name'] . ']');
            return true;
        }
        return false;
    }

    /**
     * @param $conditions
     * @return bool
     * delete model
     */
    public function delete($conditions)
    {
        $this->db->where($conditions);
        $this->db->delete(db_prefix() . $this->table);
        if ($this->db->affected_rows() > 0) {
            log_activity('Workflow Deleted');
            return true;
        }
        return false;
    }

    function update_actions_order($actions){
        $index = 1;
        foreach($actions as $id){
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . $this->table, ['order' => $index]);
            $index++;
        }
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
}
