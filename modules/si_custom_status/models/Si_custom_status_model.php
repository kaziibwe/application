<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_custom_status_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_status($id = '', $where = [])
	{
		$this->db->where($where);
		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'si_custom_status')->row();
		}

		$statuses = $this->app_object_cache->get('si-custom-all-statuses');

		if (!$statuses) {
			$this->db->order_by('order', 'asc');

			$statuses = $this->db->get(db_prefix() . 'si_custom_status')->result_array();
			$this->app_object_cache->add('si-custom-all-statuses', $statuses);
		}

		return $statuses;
	}

	public function add_status($data)
	{
		if (isset($data['color']) && $data['color'] == '') {
			$data['color'] = hooks()->apply_filters('si_default_custom_status_color', '#757575');
		}

		if (!isset($data['order'])) {
			$data['order'] = total_rows(db_prefix() . 'si_custom_status',['relto'=>$data['relto']]) + 1;
		}

		$this->db->insert(db_prefix() . 'si_custom_status', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			log_activity('New Custom Status Added [StatusID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
			return $insert_id;
		}
		return false;
	}
	
	public function update_status($data, $id)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'si_custom_status', $data);
		if ($this->db->affected_rows() > 0) {
			log_activity('Custom Status Updated [StatusID: ' . $id . ', Name: ' . $data['name'] . ']');
			return true;
		}
		return false;
	}

	public function delete_status($id)
	{
		$current = $this->get_status($id);
		if (is_reference_in_table('status', db_prefix() . 'projects', $id) || is_reference_in_table('status', db_prefix() . 'tasks', $id)) {
			return [
				'referenced' => true,
			];
		}
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'si_custom_status');
		if ($this->db->affected_rows() > 0) {
			log_activity('Custom Status Deleted [StatusID: ' . $id . ']');
			return true;
		}
		return false;
	}
	
	public function get_default_status($id = '', $where = [])
	{
		$this->db->where($where);
		if (is_numeric($id)) {
			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'si_custom_status_default')->row();
		}

		$statuses = $this->app_object_cache->get('si-custom-all-default-statuses');

		if (!$statuses) {
			$this->db->order_by('order', 'asc');

			$statuses = $this->db->get(db_prefix() . 'si_custom_status_default')->result_array();
			$this->app_object_cache->add('si-custom-all-default-statuses', $statuses);
		}

		return $statuses;
	}
	public function update_default_status($data, $id)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'si_custom_status_default', $data);
		if ($this->db->affected_rows() > 0) {
			log_activity('Custom Status Updated [StatusID: ' . $id . ', Name: ' . $data['name'] . ']');
			return true;
		}
		return false;
	}
}
