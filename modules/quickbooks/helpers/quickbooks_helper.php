<?php







defined('BASEPATH') or exit('No direct script access allowed');


function set_expense_created_in_quickBooks($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $CI->db->update(db_prefix() . 'expenses', array('is_expense_created_in_quickBook' => $id));
}
function get_client_primary_email($id)
{
    $CI = &get_instance();
    $where = array('userid' => $id, 'is_primary' => "1");
    $CI->db->select('email');
    $CI->db->where($where);
    return $CI->db->get(db_prefix() . 'contacts')->row()->email;
}
function set_quickBooks_company_realmId($id, $table)
{
    $CI = &get_instance();
    $CI->db->set('quickBooks_company_realmId', get_option('realmId'));
    $CI->db->where('id', $id);
    return $CI->db->update(db_prefix() . $table);
}
function get_quickBooks_company_realmId($id)
{
    $CI = &get_instance();
    $CI->db->select('quickBooks_company_realmId');
    $CI->db->where('id', $id);
    return $CI->db->get(db_prefix() . "invoices")->row()->quickBooks_company_realmId;
}
function get_tax_for_quickBooks($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $result = $CI->db->get(db_prefix() . "taxes")->row();
    if (isset($result->taxrate)) {
        return $result;
    } else {
        return 0;
    }
}
function get_paymentMethod_for_quickBooks($id)
{
    $CI = &get_instance();
    $CI->db->select('name');
    $CI->db->where('id', $id);
    $result = $CI->db->get(db_prefix() . "payment_modes")->row();

    if(isset($result->name))
    {
        return $result->name;
    }else{
        return $id;
    }
        
    
}
