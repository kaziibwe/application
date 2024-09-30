<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wikiarticles_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $dataDB = [];

        $user = get_staff($this->session->userdata('tfa_staffid'));
        $dataDB['author_id'] = $user->staffid;

        $dataDB['title'] = isset($data['title']) ? $data['title'] : '';
        $dataDB['description'] = isset($data['description']) ? $data['description'] : '';
        $dataDB['type'] = isset($data['type']) ? $data['type'] : '';
        if($dataDB['type'] == 'document'){
            $dataDB['content'] = isset($data['content']) ? $data['content'] : '';
        }else if($dataDB['type'] == 'mindmap'){
            $clone = null;
            if(isset($data['clone_id']) && $data['clone_id'] != ''){
                $clone = $this->get($data['clone_id']);
            }

            if(isset($clone)){
                $dataDB['mindmap_content'] = $clone->mindmap_content;
                $new_thumb = wiki_copy_thumb_mindmap($clone->mindmap_thumb, 'mindmap' . $user->staffid . '_');
                if($new_thumb){
                    $dataDB['mindmap_thumb'] = $new_thumb;
                }
            }else{
                $dataDB['mindmap_content'] = wiki_get_mindmap_content();
                $new_thumb = wiki_copy_default_mindmap_thumb('mindmap' . $user->staffid . '_');
                if($new_thumb){
                    $dataDB['mindmap_thumb'] = $new_thumb;
                }
            }

        }

        $bookId = isset($data['book_id']) ? $data['book_id'] : null;
        
        if(!isset($bookId)){
            return false;
        }
        if(!isset($this->wikibooks_model)){
            $this->load->model('wikibooks_model');
        }
        $book = $this->wikibooks_model->get($bookId);
        if(!isset($book)){
            return false;
        }

        $dataDB['book_id'] = $book->id;

        $this->db->set('slug', 'UUID()', FALSE);
        
        $this->db->insert(db_prefix() . 'wiki_articles', $dataDB);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Article Added [ID:' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'wiki_articles')->row();
        }

        return $this->db->get(db_prefix() . 'wiki_articles')->result_array();
    }

    public function update($data, $id)
    {
        $article = $this->get($id);
        $dataDB = [];
        $user = get_staff($this->session->userdata('tfa_staffid'));
        $dataDB['author_id'] = $user->staffid;

        $dataDB['title'] = isset($data['title']) ? $data['title'] : '';
        $dataDB['description'] = isset($data['description']) ? $data['description'] : '';
        $dataDB['type'] = isset($data['type']) ? $data['type'] : '';
        if($dataDB['type'] == 'document'){
            $dataDB['content'] = isset($data['content']) ? $data['content'] : '';
        }

        $dataDB['is_publish'] = isset($data['is_publish']) ? 1 : 0;
        
        $bookId = isset($data['book_id']) ? $data['book_id'] : null;
        
        if(!isset($bookId)){
            return false;
        }
        if(!isset($this->wikibooks_model)){
            $this->load->model('wikibooks_model');
        }
        $book = $this->wikibooks_model->get($bookId);
        if(!isset($book)){
            return false;
        }

        $dataDB['book_id'] = $book->id;
        
        $this->db->set('updated_at', 'NOW()', FALSE);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'wiki_articles', $dataDB);
        if ($this->db->affected_rows() > 0) {
            log_activity('Article Updated [ID:' . $id . ']');

            return true;
        }

        return false;
    }

    public function delete($id)
    {

        $article = $this->get($id);

        if(!isset($article)){
            return false;
        }

        wiki_remove_thumb_mindmap($article->mindmap_thumb);

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'wiki_articles');
        if ($this->db->affected_rows() > 0) {
            log_activity('Article Deleted [ID:' . $id . ']');

            return true;
        }

        return false;
    }

    public function delete_by_book($book_id)
    {
        $this->db->where('book_id', $book_id);
        $this->db->delete(db_prefix() . 'wiki_articles');
        if ($this->db->affected_rows() > 0) {
            log_activity('Articles Deleted by Book ID ['.$book_id.']');

            return true;
        }

        return false;
    }

    public function get_all_articles($filters = [])
    {
        $tbl_articles = db_prefix() . 'wiki_articles';
        $tbl_books = db_prefix() . 'wiki_books';
        $tbl_authors = db_prefix() . 'staff';
        $tbl_staff_article = db_prefix() . 'wiki_staff_article';

        if(!isset($this->wikibooks_model)){
            $this->load->model('wikibooks_model');
        }

        $sqlPermissionBook = '1 = 1';
        // haven't permssion view global
        if (!has_permission('wiki_articles', '', 'view')) {
            $sqlPermissionBook = $this->wikibooks_model->getPermissionClause('TBLBooks');
        }

        $user = get_staff($this->session->userdata('tfa_staffid'));

        $sql = " 
            SELECT 
                TBLArticles.* , 
                CONCAT(TBLAuthors.firstname, ' ', TBLAuthors.lastname) AS author_fullname, 
                TBLBooks.name AS book_name,
                TBLStaffArticle.id AS bookmark_id
            FROM " . $tbl_articles . " TBLArticles 
                INNER JOIN ".$tbl_authors." TBLAuthors ON TBLAuthors.staffid = TBLArticles.author_id 
                INNER JOIN ".$tbl_books." TBLBooks ON TBLBooks.id = TBLArticles.book_id 
                LEFT JOIN " . $tbl_staff_article . " TBLStaffArticle ON TBLArticles.id = TBLStaffArticle.article_id AND TBLStaffArticle.staff_id = " . $user->staffid . "
            WHERE 1 = 1 and " . $sqlPermissionBook . " ";
        
        if(isset($filters['article_id'])){
            $sql .= " and TBLArticles.id = " . $filters['article_id'] . " ";
        }
        if(isset($filters['book_id'])){
            $sql .= " and TBLArticles.book_id = " . $filters['book_id'] . " ";
        }
        if(isset($filters['query']) && $filters['query'] != ""){
            $sql .= " and ( TBLArticles.title LIKE  '%" . $filters['query'] . "%' OR TBLArticles.description LIKE  '%" . $filters['query'] . "%' ) ";
        }
        if(isset($filters['is_owner']) && isset($filters['owner_id']) && $filters['is_owner'] == '1'){
            $sql .= " and TBLArticles.author_id =  " . $filters['owner_id'] . " ";
        }
        if(isset($filters['slug'])){
            $sql .= " and TBLArticles.slug = '" . $filters['slug'] . "' ";
        }
        if(isset($filters['is_publish'])){
            $sql .= " and TBLArticles.is_publish = " . $filters['is_publish'] . " ";
        }
        if(isset($filters['is_bookmark']) && $filters['is_bookmark'] == 1){
            $sql .= " and TBLStaffArticle.id IS NOT NULL ";
        }
        $sql .= "
            ORDER BY 
                TBLArticles.updated_at DESC 
        ";
        $rs = $this->db->query($sql);
        $data = $rs->result_array();
        return array_values($data);
    }

    public function exist_slug($slug, $except_id)
    {
        $tblArticles = db_prefix() . 'wiki_articles';

        $sql = " 
            SELECT 
                TBLArticles.* 
            FROM " . $tblArticles . " TBLArticles 
            WHERE TBLArticles.slug = '" . $slug . "' ";
        
        if(isset($except_id)){
            $sql .= " and TBLArticles.id != " . $except_id . " ";
        }
        $rs = $this->db->query($sql);
        $data = $rs->result_array();
        $arr =  array_values($data);
        if(count($arr) > 0){
            return true;
        }else{
            return false;
        }
    }

    public function count_view($articleId)
    {
        if(!isset($articleId)){
            return;
        }
        $tblArticles = db_prefix() . 'wiki_articles';
        $sql = "
            UPDATE " . $tblArticles . "
                SET view_counter = view_counter + 1
            WHERE id = " . $articleId . "
        ";
        $rs = $this->db->query($sql);
    }

    public function get_published($slug)
    {
        $tbl_articles = db_prefix() . 'wiki_articles';
        $tbl_authors = db_prefix() . 'staff';
        $sql = "
            SELECT 
                TBLArticles.*, 
                CONCAT(IFNULL(TBLAuthors.firstname, ''), ' ', IFNULL(TBLAuthors.lastname, '')) AS author_fullname
            FROM " . $tbl_articles . " TBLArticles 
                INNER JOIN " . $tbl_authors . " TBLAuthors
                    ON TBLArticles.author_id = TBLAuthors.staffid
            WHERE TBLArticles.is_publish = 1
                AND TBLArticles.slug = '" . $slug . "' 
            LIMIT 1
        ";
        $rs = $this->db->query($sql);
        $data = $rs->result_array();
        return array_values($data);
    }

    public function switch_bookmark($staff_id, $article_id, $is_on)
    {
        $tbl_staff_article = db_prefix() . 'wiki_staff_article';
        $is_on = $is_on == 1;
        if($is_on){
            $this->switch_bookmark_on($staff_id, $article_id);
        }else{
            $this->switch_bookmark_off($staff_id, $article_id);
        }
    }

    public function switch_bookmark_on($staff_id, $article_id, $check = true)
    {
        if($check){
            $rows = $this->get_bookmark($staff_id, $article_id);
            if(isset($rows) && count($rows) > 0){
                return;
            }
        }
        $dataDB = [];
        $dataDB['staff_id'] = $staff_id;
        $dataDB['article_id'] = $article_id;
        $this->db->insert(db_prefix() . 'wiki_staff_article', $dataDB);
    }

    public function switch_bookmark_off($staff_id, $article_id, $check = true)
    {
        if($check){
            $rows = $this->get_bookmark($staff_id, $article_id);
            if(!isset($rows) || count($rows) == 0){
                return;
            }
        }
        $this->db->where('staff_id', $staff_id);
        $this->db->where('article_id', $article_id);
        $this->db->delete(db_prefix() . 'wiki_staff_article');
    }

    public function get_bookmark($staff_id, $article_id)
    {
        $tbl_staff_article = db_prefix() . 'wiki_staff_article';
        $tbl_articles = db_prefix() . 'wiki_articles';
        $tbl_staffs = db_prefix() . 'staff';
        $sql = "
            SELECT
                TBLStaffArticle.*
            FROM " . $tbl_staff_article . " TBLStaffArticle
                INNER JOIN " . $tbl_articles . " TBLArticles
                    ON TBLStaffArticle.article_id = TBLArticles.id
                INNER JOIN " . $tbl_staffs . " TBLStaffs
                    ON TBLStaffArticle.staff_id = TBLStaffs.staffid
            WHERE TBLStaffArticle.article_id = " . $article_id . "
                AND TBLStaffArticle.staff_id = " . $staff_id . "
            LIMIT 1
        ";
        $rs = $this->db->query($sql);
        $data = $rs->result_array();
        return array_values($data);
    }

    public function update_mindmap($data){
        $article_id = $this->input->post('article_id');
        $mindmap_content = $this->input->post('mindmap_content', false);
        $mindmap_thumb = $this->input->post('mindmap_thumb');

        if(!isset($article_id) || !isset($mindmap_content) || !isset($mindmap_thumb)){
            return false;
        }

        $article = $this->get($article_id);

        if(!isset($article)){
            return false;
        }

        $dataDB['mindmap_content'] = $mindmap_content;
        $user = get_staff($this->session->userdata('tfa_staffid'));

        $new_thumb = wiki_handle_thumb_mindmap_upload($data['mindmap_thumb'], 'mindmap' .$user->staffid . '_', $article->mindmap_thumb);
        if($new_thumb){
            $dataDB['mindmap_thumb'] = $new_thumb;
        }

        $this->db->set('updated_at', 'NOW()', FALSE);
        $this->db->where('id', $article->id);
        $this->db->update(db_prefix() . 'wiki_articles', $dataDB);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

}
