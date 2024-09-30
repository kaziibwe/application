<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
* Function that format todo category for the final user
* @param  string  $id    category id
* @param  boolean $text
* @param  boolean $clean
* @return string
*/
function si_todo_format_category($category, $text = false, $clean = false)
{
	$categoty_name = $category['category_name'];
	if ($clean == true) {
		return $categoty_name;
	}
	$style = '';
	$class = '';
	if ($text == false) {
		$style = 'border: 1px solid ' . $category['color'] . ';color:' . $category['color'] . ';';
		$class = 'label inline-block';
	} else {
		$style = 'color:' . $category['color'] . ';';
	}
	
	return '<span class="' . $class . '" style="' . $style . '">' . $categoty_name . '</span>';
}

/**
* Return predefined todos priorities
* @return array
*/
function si_todo_get_priorities()
{
	return hooks()->apply_filters('si_todos_priorities', [
		[
			'id'     => 1,
			'name'   => _l('task_priority_low'),
			'color' => '#777',
		],
		[
			'id'     => 2,
			'name'   => _l('task_priority_medium'),
			'color' => '#03a9f4',
		],
		[
			'id'    => 3,
			'name'  => _l('task_priority_high'),
			'color' => '#ff6f00',
		],
		[
			'id'    => 4,
			'name'  => _l('task_priority_urgent'),
			'color' => '#fc2d42',
		],
	]);
}
/**
* Get and return task priority color
* @param  mixed $id priority id
* @return string
*/
function si_todo_priority_color($id)
{
	foreach (si_todo_get_priorities() as $priority) {
		if ($priority['id'] == $id) {
			return $priority['color'];
		}
	}
	return '#333';
}
