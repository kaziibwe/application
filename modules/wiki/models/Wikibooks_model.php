<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wikibooks_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $dataDB = [];

        $dataDB['name'] = isset($data['name']) ? $data['name'] : '';
        $dataDB['short_description'] = isset($data['short_description']) ? $data['short_description'] : '';

        if(isset($data['assign_type'])){
            switch ($data['assign_type']) {
                case 'specific_staff':
                    $dataDB['assign_type'] = $data['assign_type'];
                    $dataDB['assign_ids'] = wiki_serialize($data['assign_ids_staff'], 'staff_');
                    break;

                case 'roles':
                    $dataDB['assign_type'] = $data['assign_type'];
                    $dataDB['assign_ids'] = wiki_serialize($data['assign_ids_roles'], 'role_');
                    break;
                
                default:
                    $dataDB['assign_type'] = $data['assign_type'];
                    $dataDB['assign_ids'] = wiki_serialize([], 'default_');
                    break;
            }
        } else {
            $dataDB['assign_type'] = $data['assign_type'];
            $dataDB['assign_ids'] = wiki_serialize([], 'default_');
        }
        
        $user = get_staff($this->session->userdata('tfa_staffid'));
        $dataDB['author_id'] = $user->staffid;
        
        $this->db->insert(db_prefix() . 'wiki_books', $dataDB);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Book Added [ID:' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'wiki_books')->row();
        }

        return $this->db->get(db_prefix() . 'wiki_books')->result_array();
    }

    public function getOwnBooks()
    {
        $user = get_staff($this->session->userdata('tfa_staffid'));
        $this->db->where('author_id', $user->staffid);
        return $this->db->get(db_prefix() . 'wiki_books')->result_array();
    }

    public function update($data, $id)
    {
        $dataDB = [];

        $dataDB['name'] = isset($data['name']) ? $data['name'] : '';
        $dataDB['short_description'] = isset($data['short_description']) ? $data['short_description'] : '';

        if(isset($data['assign_type'])){
            switch ($data['assign_type']) {
                case 'specific_staff':
                    $dataDB['assign_type'] = $data['assign_type'];
                    $dataDB['assign_ids'] = wiki_serialize($data['assign_ids_staff'], 'staff_');
                    break;

                case 'roles':
                    $dataDB['assign_type'] = $data['assign_type'];
                    $dataDB['assign_ids'] = wiki_serialize($data['assign_ids_roles'], 'role_');
                    break;
                
                default:
                    $dataDB['assign_type'] = $data['assign_type'];
                    $dataDB['assign_ids'] = wiki_serialize([], 'default_');
                    break;
            }
        } else {
            $dataDB['assign_type'] = $data['assign_type'];
            $dataDB['assign_ids'] = wiki_serialize([], 'default_');
        }
        
        $user = get_staff($this->session->userdata('tfa_staffid'));
        $dataDB['author_id'] = $user->staffid;

        $this->db->set('updated_at', 'NOW()', FALSE);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'wiki_books', $dataDB);
        if ($this->db->affected_rows() > 0) {
            log_activity('Book Updated [ID:' . $id . ']');

            return true;
        }

        return false;
    }

    public function delete($id)
    {
        if(!isset($this->wikiarticles_model)){
            $this->load->model('wikiarticles_model');
        }
        $this->wikiarticles_model->delete_by_book($id);
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'wiki_books');
        if ($this->db->affected_rows() > 0) {
            log_activity('Book Deleted [ID:' . $id . ']');

            return true;
        }

        return false;
    }

    public function get_all_books($query = ""){
        $tblBooks = db_prefix() . 'wiki_books';
        $tblArticles = db_prefix() . 'wiki_articles';
        
        $sqlFilterName = " ";
        if($query != ""){
            $sqlFilterName = " and ( TBLBooks.name LIKE '%" . $query . "%' OR TBLBooks.short_description LIKE '%" . $query . "%' ) ";
        }

        $sqlPermissionBook = '1 = 1';
        // haven't permssion view global
        if (!has_permission('wiki_articles', '', 'view')) {
            $sqlPermissionBook = $this->getPermissionClause('TBLBooks');
        }

        $sql = "
            SELECT 
                TBLBooks.*,
                IFNULL(TWKCounters.total, 0) AS articles_total
            FROM " . $tblBooks . " TBLBooks
                LEFT JOIN (
                    SELECT TBLArticles.book_id AS book_id, COUNT(*) as total
                    FROM " . $tblArticles . " TBLArticles
                    GROUP BY TBLArticles.book_id
                ) TWKCounters ON TBLBooks.id = TWKCounters.book_id
            WHERE 1 = 1 " . $sqlFilterName . ' and ' . $sqlPermissionBook . " 
            ORDER BY TBLBooks.updated_at DESC
        ";

        $rs = $this->db->query($sql);
        $data = $rs->result_array();
        return array_values($data);
    }

    public function getPermissionClause($tableName, $user = null){
        if(!isset($user)){
            $user = get_staff($this->session->userdata('tfa_staffid'));
        }

        $user_id = $user->staffid;
        $role_pattern = 'role_' . $user->role;
        $staff_pattern = 'staff_' . $user->staffid;

        $sqlFilterPermission = " (" . $tableName . ".author_id = ".$user_id." OR " . $tableName . ".assign_ids LIKE '%".$role_pattern."%' OR " . $tableName . ".assign_ids LIKE '%".$staff_pattern."%') ";
        
        return $sqlFilterPermission;
    }

}
