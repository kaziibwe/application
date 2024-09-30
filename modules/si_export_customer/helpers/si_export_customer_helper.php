<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
* @param  integer
* @return array
* Get single client kyc files ids
*/
function get_client_kyc_details($client_id)
{
	$CI = &get_instance();
	if (is_numeric($client_id)) {
		$CI->db->where('client_id',$client_id);
		$result = $CI->db->get(db_prefix() . 'si_export_customer_kyc_files');
		if($result->num_rows()>0)
			return (array)$result->row();
	}
	return array();
}
/**
* @param  integer
* @return array
* Get single client items ids
*/
function get_client_services_list($client_id)
{
	$items_id = array();
	$CI = &get_instance();
	if (is_numeric($client_id)) {
		$CI->db->where('client_id',$client_id);
		$result = $CI->db->get(db_prefix() . 'si_export_customer_services');
		if($result->num_rows()>0)
		{
			foreach($result->result_array() as $row)
			$items_id[]=$row['item_id'];
		}	
	}
	return $items_id;
}
/**
* @param  integer
* @return array
* Get single client items details
*/
function get_client_services_details($client_id)
{
	$CI = &get_instance();
	if (is_numeric($client_id)) {
		$CI->db->select(db_prefix() . 'items.*');
		$CI->db->where('client_id',$client_id);
		$CI->db->join(db_prefix() . 'si_export_customer_services',db_prefix() . 'si_export_customer_services.item_id='.db_prefix() . 'items.id','left');
		$result = $CI->db->get(db_prefix() . 'items');
		if($result->num_rows()>0)
		{
			return $result->result_array();
		}	
	}
	return array();
}


