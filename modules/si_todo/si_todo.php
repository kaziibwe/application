<?php
defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Advanced To Do Manager
Description: Module will allow to manage To Do list with category.
Author: Sejal Infotech
Version: 1.0.1
Requires at least: 2.3.*
Author URI: http://www.sejalinfotech.com
*/
define('SI_TODO_MODULE_NAME', 'si_todo');
$CI = &get_instance();
hooks()->add_action('admin_init', 'si_todo_hook_admin_init');
hooks()->add_filter('get_dashboard_widgets','si_todo_hook_get_dashboard_widgets');
hooks()->add_filter('before_dashboard_render','si_todo_hook_before_dashboard_render');
/**
* Load the module helper
*/
$CI->load->helper(SI_TODO_MODULE_NAME . '/si_todo');
/**
* Load the module model
*/
$CI->load->model(SI_TODO_MODULE_NAME . '/si_todo_model');
/**
* Register activation module hook
*/
register_activation_hook(SI_TODO_MODULE_NAME, 'si_todo_activation_hook');
function si_todo_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}
/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SI_TODO_MODULE_NAME, [SI_TODO_MODULE_NAME]);
/**
*	Admin Init Hook for module
*/
function si_todo_hook_admin_init()
{
	$CI = &get_instance();
	/** Add Menu for Todo**/
	$CI->app_menu->add_sidebar_menu_item('si_todo_menu', [
		'collapse' => true,
		'icon'     => 'fa fa-check-square',
		'name'     => _l('si_todo_main_menu'),
		'position' => 15,
	]);
	$CI->app_menu->add_sidebar_children_item('si_todo_menu', [
		'slug'     => 'si-todo-list-menu',
		'name'     => _l('si_todo_list_menu'),
		'href'     => admin_url('si_todo'),
		'position' => 1,
	]);
	$CI->app_menu->add_sidebar_children_item('si_todo_menu', [
		'slug'     => 'si-todo-category-menu',
		'name'     => _l('si_todo_category_menu'),
		'href'     => admin_url('si_todo/category_list'),
		'position' => 2,
	]);
	$CI->app_menu->add_sidebar_children_item('si_todo_menu', [
		'slug'     => 'si-todo-settings-menu',
		'name'     => _l('si_todo_settings_menu'),
		'href'     => admin_url('si_todo/settings'),
		'position' => 3,
	]);
}
/**Hook to add calendar widget to dashboard**/
function si_todo_hook_get_dashboard_widgets($widgets)
{
	$widgets[] = array(	'path' => 'si_todo/dashboard/widgets/si_todo_widget',
					'container' => 'right-4',
				);
	return $widgets;
}
/** Hook to set data for todo before dashboard rander*/
function si_todo_hook_before_dashboard_render($data)
{
	$CI = &get_instance();
	$data['bodyclass']            = $data['bodyclass'].' si-todo-page';
	$settings = $CI->si_todo_model->get_settings();
	$CI->si_todo_model->setTodosLimit(isset($settings['dashboard_unfinished_limit'])?$settings['dashboard_unfinished_limit']:20);
	$data['si_todos'] = $CI->si_todo_model->get_todo_items(0);
	# Only show last 5 or defined finished todo items
	$CI->si_todo_model->setTodosLimit(isset($settings['dashboard_finished_limit'])?$settings['dashboard_finished_limit']:5);
	$data['si_todos_finished'] = $CI->si_todo_model->get_todo_items(1);
	$data['categories'] = $CI->si_todo_model->get_category();
	return $data;
}