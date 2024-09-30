<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_todo_model extends App_Model
{
	public $todo_limit;
	
	public function __construct()
	{
		parent::__construct();
		$this->todo_limit = 20;
	}
	
	public function setTodosLimit($limit)
	{
		$this->todo_limit = $limit;
	}
	
	public function getTodosLimit()
	{
		return $this->todo_limit;
	}
	
	public function get($id = '')
	{
		$this->db->where('staffid', get_staff_user_id());
		if (is_numeric($id)) {
			$this->db->where('todoid', $id);
			return $this->db->get(db_prefix().'si_todos')->row();
		}
		return $this->db->get(db_prefix().'si_todos')->result_array();
	}
	
	public function get_todo_items($finished, $page = '',$category_id = 0)
	{
		$this->db->select(db_prefix().'si_todos.*,'.db_prefix().'si_todos_category.category_name,'.db_prefix().'si_todos_category.color');
		$this->db->from(db_prefix().'si_todos');
		$this->db->where('finished', $finished);
		$this->db->where(db_prefix().'si_todos.staffid', get_staff_user_id());
		if(is_numeric($category_id) && $category_id > 0)
			$this->db->where('category', $category_id);
		$this->db->join(db_prefix().'si_todos_category',db_prefix().'si_todos_category.id='.db_prefix().'si_todos.category','left');	
		$this->db->order_by('item_order', 'asc');
		if ($page != '' && $this->input->post('todo_page')) {
			$position = ($page * $this->todo_limit);
			$this->db->limit($this->todo_limit, $position);
		} else {
			$this->db->limit($this->todo_limit);
		}
		$todos = $this->db->get()->result_array();
		# format date
		$i = 0;
		foreach ($todos as $todo) {
			$todos[$i]['dateadded']    = _dt($todo['dateadded']);
			$todos[$i]['datefinished'] = _dt($todo['datefinished']);
			$todos[$i]['description']  = check_for_links($todo['description']);
			$i++;
		}
		return $todos;
	}
	public function get_total_pending_todo()
	{
		$this->db->select('count(todoid) as total_pending',false);
		$this->db->where('finished', 0);
		$this->db->where('staffid', get_staff_user_id());
		$result = $this->db->get(db_prefix().'si_todos');
		if($result)
			return $result->row()->total_pending;
		else
			return 0;
	}
	
	public function add($data)
	{
		$data['dateadded']		= date('Y-m-d H:i:s');
		$data['description']	= nl2br($data['description']);
		$data['staffid']		= get_staff_user_id();
		$this->db->insert(db_prefix().'si_todos', $data);
		return $this->db->insert_id();
	}
	
	public function update($id, $data)
	{
		$data['description'] = nl2br($data['description']);
	
		$this->db->where('todoid', $id);
		$this->db->update(db_prefix().'si_todos', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	
	public function update_todo_items_order($data)
	{
		for ($i = 0; $i < count($data['data']); $i++) {
			$update = [
				'item_order' => $data['data'][$i][1],
				'finished'   => $data['data'][$i][2],
			];
			if ($data['data'][$i][2] == 1) {
				$update['datefinished'] = date('Y-m-d H:i:s');
			}
			$this->db->where('todoid', $data['data'][$i][0]);
			$this->db->update(db_prefix().'si_todos', $update);
		}
	}
	
	public function delete_todo_item($id)
	{
		$this->db->where('todoid', $id);
		$this->db->where('staffid', get_staff_user_id());
		$this->db->delete(db_prefix().'si_todos');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	
	public function change_todo_status($id, $status)
	{
		$this->db->where('todoid', $id);
		$this->db->where('staffid', get_staff_user_id());
		$date = date('Y-m-d H:i:s');
		$this->db->update(db_prefix().'si_todos', [
			'finished'     => $status,
			'datefinished' => $date,
		]);
		if ($this->db->affected_rows() > 0) {
			return [
				'success' => true,
			];
		}
		return [
			'success' => false,
		];
	}
	
	public function get_category($id = '')
	{
		$this->db->where('staffid', get_staff_user_id());
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix().'si_todos_category')->row();
		}
		$this->db->select(db_prefix().'si_todos_category.*,(select count(todoid) from '.db_prefix().'si_todos where category='.db_prefix().'si_todos_category.id) as total,(select count(todoid) from '.db_prefix().'si_todos where category='.db_prefix().'si_todos_category.id and finished=1) as finished',false);
		$this->db->order_by('cat_order', 'asc');
		return $this->db->get(db_prefix().'si_todos_category')->result_array();
	}
	
	public function add_category($data)
	{
		$data['dateadded']   = date('Y-m-d H:i:s');
		$data['staffid']     = get_staff_user_id();
		$this->db->insert(db_prefix().'si_todos_category', $data);
		return $this->db->insert_id();
	}
	
	public function update_category($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->where('staffid', get_staff_user_id());
		$this->db->update(db_prefix().'si_todos_category', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
	public function update_todo_categories_order($data)
	{
		for ($i = 0; $i < count($data['data']); $i++) {
			$update = [
				'cat_order' => $data['data'][$i][1],
			];
			$this->db->where('id', $data['data'][$i][0]);
			$this->db->update(db_prefix().'si_todos_category', $update);
		}
	}
	public function delete_todo_category($id)
	{
		$this->db->where('id', $id);
		$this->db->where('staffid', get_staff_user_id());
		$this->db->delete(db_prefix().'si_todos_category');
		if ($this->db->affected_rows() > 0) {
			$this->db->where('category', $id);
			$this->db->delete(db_prefix().'si_todos');
			return true;
		}
		return false;
	}
	public function get_settings()
	{
		$staffid = get_staff_user_id();
		$this->db->where('staffid', $staffid);
		$result = $this->db->get(db_prefix().'si_todos_settings')->row();
		if(!$result){
			$this->db->insert(db_prefix().'si_todos_settings',array('staffid'=>$staffid));
		}
		$this->db->where('staffid', $staffid);
		return (array)$this->db->get(db_prefix().'si_todos_settings')->row();
	}
	public function save_settings($data)
	{
		$data['dateadded']		= date('Y-m-d H:i:s');
		$this->db->where('staffid', get_staff_user_id());
		$this->db->update(db_prefix().'si_todos_settings',$data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}
}