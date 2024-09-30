<?php
defined('BASEPATH') or exit('No direct script access allowed');

function si_cs_get_custom_statuses($relto)
{
	$CI = &get_instance();
	if ($relto!='') {
		$CI->db->order_by('id', 'asc');
		$CI->db->where('relto',$relto);	
		$statuses = $CI->db->get(db_prefix() . 'si_custom_status')->result_array();
		return $statuses;
	}
	return array();
}
function si_cs_get_custom_default_statuses($relto)
{
	$CI = &get_instance();
	if ($relto!='') {
		$CI->db->select('*,status_id as id');
		$CI->db->order_by('status_id', 'asc');
		$CI->db->where('relto',$relto);	
		$CI->db->where('active',1);	
		$statuses = $CI->db->get(db_prefix() . 'si_custom_status_default')->result_array();
		return $statuses;
	}
	return array();
}

function si_cs_format_statuses($status, $text = false, $clean = false)
{
	if (!is_array($status)) {
		return '';
	}

	$status_name = htmlspecialchars($status['name']);
	if ($clean == true) {
		return $status_name;
	}
	$style_ = '';
	$class = '';
	if ($text == false) {
		$style_ = 'border: 1px solid ' . $status['color'] . ';color:' . $status['color'] . ';';
		$class = 'label';
	} else {
		$style_ = 'color:' . $status['color'] . ';';
	}

	return '<span class="' . $class . '" style = "' . $style_ . '">' . $status_name . '</span>';
}

