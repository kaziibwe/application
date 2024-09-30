<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_todo extends AdminController 
{
	public function __construct()
	{
		parent::__construct(); 
	}
	
	# Get all staff todo items
	public function index()
	{
		$category_id = $this->input->get('group');
		$settings = $this->si_todo_model->get_settings();
		$this->si_todo_model->setTodosLimit(isset($settings['todos_load_limit'])?$settings['todos_load_limit']:20);
		if ($this->input->is_ajax_request()) {
			echo json_encode($this->si_todo_model->get_todo_items($this->input->post('finished'), $this->input->post('todo_page'),$category_id));
			exit;
		}
		$data['bodyclass']            = 'si-todo-page';
		$where =  array('staffid'  => get_staff_user_id(),
						'finished' => 1,
				);
		if(is_numeric($category_id) && $category_id > 0)
			$where['category'] = $category_id;
		$data['total_pages_finished'] = ceil(total_rows(db_prefix().'si_todos', $where) / $this->si_todo_model->getTodosLimit());
		$where['finished'] = 0;
		$data['total_pages_unfinished'] = ceil(total_rows(db_prefix().'si_todos', $where) / $this->si_todo_model->getTodosLimit());
		$data['categories'] = $this->si_todo_model->get_category();
		$data['total_pending'] = $this->si_todo_model->get_total_pending_todo();
		$data['title'] = _l('si_todo');
		$this->load->view('todo_list', $data);
	}
	
	# Add new todo item 
	public function todo()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if ($data['todoid'] == '') {
				unset($data['todoid']);
				$id = $this->si_todo_model->add($data);
				if ($id) {
					set_alert('success', _l('added_successfully', _l('todo')));
				}
			} else {
				$id = $data['todoid'];
				unset($data['todoid']);
				$success = $this->si_todo_model->update($id, $data);
				if ($success) {
					set_alert('success', _l('updated_successfully', _l('todo')));
				}
			}
	
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	public function get_by_id($id)
	{
		$todo              = $this->si_todo_model->get($id);
		$todo->description = clear_textarea_breaks($todo->description);
		echo json_encode($todo);
	}
	
	# Change todo status
	public function change_todo_status($id, $status)
	{
		$success = $this->si_todo_model->change_todo_status($id, $status);
		if ($success) {
			set_alert('success', _l('todo_status_changed'));
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	# Update todo order / ajax 
	public function update_todo_items_order()
	{
		if ($this->input->post()) {
			$this->si_todo_model->update_todo_items_order($this->input->post());
		}
	}
	
	#Delete todo item from databse
	public function delete_todo_item($id)
	{
		if ($this->input->is_ajax_request()) {
			echo json_encode([
				'success' => $this->si_todo_model->delete_todo_item($id),
			]);
		}
		die();
	}
	# Get all staff todo items
	public function category_list()
	{
		if ($this->input->is_ajax_request()) {
			echo json_encode($this->si_todo_model->get_category());
			exit;
		}
		$data['bodyclass'] = 'si-todo-category-page';
		$data['title'] = _l('si_todo_category');
		$this->load->view('category_list', $data);
	}
	# Add new todo category 
	public function category()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if ($data['id'] == '') {
				unset($data['id']);
				#Add category called from two way, from todos and from category
				$id = $this->si_todo_model->add_category($data);
				if ($id) {
					echo json_encode([
						'success' => $id ? true : false,
						'message' => $id ? _l('added_successfully', _l('si_todo_category')) : '',
						'id'      => $id,
						'name'    => $this->input->post('category_name'),
					]);
				}
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->si_todo_model->update_category($id, $data);
				if ($success) {
					set_alert('success', _l('updated_successfully', _l('si_todo_category')));
				}
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}
	# get category
	public function get_category_by_id($id)
	{
		$todo              = $this->si_todo_model->get_category($id);
		$todo->category_name = clear_textarea_breaks($todo->category_name);
		echo json_encode($todo);
	}
	# Update todo category order / ajax 
	public function update_todo_categories_order()
	{
		if ($this->input->post()) {
			$this->si_todo_model->update_todo_categories_order($this->input->post());
		}
	}
	#Delete todo category from databse
	public function delete_todo_category($id)
	{
		if ($this->input->is_ajax_request()) {
			echo json_encode([
				'success' => $this->si_todo_model->delete_todo_category($id),
			]);
		}
		die();
	}
	public function settings()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$success = $this->si_todo_model->save_settings($data);
			if ($success) {
				set_alert('success', _l('updated_successfully', _l('settings')));
			}
			redirect(admin_url('si_todo/settings'));
		}
		$data['settings'] = $this->si_todo_model->get_settings();
		$data['title'] = _l('si_todo_settings');
		$this->load->view('settings', $data);
	}
}