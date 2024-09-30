<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Articles extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('wikiarticles_model');
        $this->load->model('wikibooks_model');
    }

    public function index()
    {
        $user = get_staff($this->session->userdata('tfa_staffid'));
        $data['title'] = _l('wiki_articles_list');

        $filter_query = $this->input->get('filter_query');
        if(!isset($filter_query)){
            $filter_query = "";
        }
        $filter_book_id = $this->input->get('filter_book_id');
        $filter_book = null;
        if(!isset($filter_book_id) || $filter_book_id == ""){
            $filter_book_id = null;
        }else{
            $filter_book = $this->wikibooks_model->get($filter_book_id);
            if(!isset($filter_book)){
                show_404();
            }
        }
        $filter_is_owner = $this->input->get('filter_is_owner');
        if(!isset($filter_is_owner)){
            $filter_is_owner = null;
            $filter_owner_id = null;
        }else{
            $filter_owner_id = $user->staffid;
        }

        $filter_is_bookmark = $this->input->get('filter_is_bookmark');

        $data['filter_query'] = $filter_query;
        $data['filter_book_id'] = $filter_book_id;
        $data['filter_is_owner'] = $filter_is_owner;
        $data['filter_is_bookmark'] = $filter_is_bookmark;
        $data['articles'] = $this->wikiarticles_model->get_all_articles([
            'book_id' => $filter_book_id,
            'query' => $filter_query,
            'is_owner' => $filter_is_owner,
            'owner_id' => $filter_owner_id,
            'is_bookmark' => $filter_is_bookmark,
        ]);
        $data['user_id'] = $user->staffid;
        $data['books']    = $this->wikibooks_model->get_all_books();
        $this->load->view('articles_manage', $data);
    }

    public function article($id = '')
    {
        if (!has_permission('wiki_articles', '', 'view')) {
            access_denied('wiki_articles');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('wiki_articles', '', 'create')) {
                    access_denied('wiki_articles');
                }
                $data = $this->input->post();
                $data['content'] = $this->input->post('content', false);

                $id = $this->wikiarticles_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('wiki_article')));
                    $submit_value = $this->input->post('submit');
                    if(isset($submit_value) && $submit_value == 'SAVE_AND_BUILD'){
                        redirect(admin_url('wiki/articles/mindmap?article_id=' . $id));
                    }else{
                        redirect(admin_url('wiki/articles/article/' . $id));
                    }
                }
            } else {
                if (!has_permission('wiki_articles', '', 'edit')) {
                    access_denied('wiki_articles');
                }
                $data = $this->input->post();
                $data['content'] = $this->input->post('content', false);

                $success = $this->wikiarticles_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('wiki_article')));
                }
                $submit_value = $this->input->post('submit');
                if(isset($submit_value) && $submit_value == 'SAVE_AND_BUILD'){
                    redirect(admin_url('wiki/articles/mindmap?article_id=' . $id));
                }else{
                    redirect(admin_url('wiki/articles/article/' . $id));
                }
            }
        }
        $back_url = $this->input->get('back_url');
        if(isset($back_url)){
            $data['back_url'] = $back_url;
        }
        if ($id == '') {
            $title = _l('wiki_create_article');
            $clone_id = $this->input->get('clone_id');
            if(isset($clone_id)){
                $data['article'] = $this->wikiarticles_model->get($clone_id);
                $data['clone_id'] = isset($data['article']) ? $data['article']->id : null;
            }
        } else {
            $data['article']        = $this->wikiarticles_model->get($id);
            $title = _l('edit', _l('wiki_article_lowercase'));
        }
        $data['title']                 = $title;
        $data['books']    = $this->wikibooks_model->get_all_books();
        $this->load->view('article', $data);
    }

    public function show($id)
    {
        $data['articles'] = $this->wikiarticles_model->get_all_articles([
            'article_id' => $id,
        ]);
        if (isset($data['articles'][0])) {
            $article = $data['articles'][0];
            $data['article'] = $article;
            $data['title']                 = $article['title'];
            // counter
            $this->add_count($id);
            switch ($article['type']) {
                case 'document':
                    $this->load->view('article_show', $data);
                    break;
                case 'mindmap':
                    $this->load->view('article_mindmap_show', $data);
                    break;
                default:
                    show_404();
                    break;
            }
        } else {
            show_404();
        }
    }
    // This is the counter function.. 
    function add_count($id)
    {
        $cookie_name = 'wiki_article_counter_'.$id;
        // load cookie helper
        $this->load->helper('cookie');
        // this line will return the cookie which has slug name
        $check_visitor = $this->input->cookie($cookie_name, TRUE);
        // this line will return the visitor ip address
        $ip = $this->input->ip_address();
        
        if ($check_visitor == false) {

            $cookie = array(
                "name"   => $cookie_name,
                "value"  => true,
                "expire" =>  time() + 7200,
                "secure" => false
            );

            $this->input->set_cookie($cookie);
            
            $this->wikiarticles_model->count_view($id);
        }

    
    }

    public function countView()
    {
        if ($this->input->post()) {
            if ($this->input->is_ajax_request()) {

                $articleId = $this->input->post('article_id');

                if(isset($articleId)){
                    $this->wikiarticles_model->count_view($articleId);
                }

                echo json_encode(["done" => 1]);
                die();
            }
        }
    }

    public function delete($id)
    {
        if (!has_permission('wiki_articles', '', 'delete')) {
            access_denied('wiki_articles');
        }
        if (!$id) {
            redirect(admin_url('wiki/articles'));
        }
        $response = $this->wikiarticles_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('wiki_article')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('wiki_article_lowercase')));
        }
        redirect(admin_url('wiki/articles'));
    }

    public function validateslug()
    {
        $rs = false;

        if ($this->input->post()) {
            if ($this->input->is_ajax_request()) {

                $slug = $this->input->post('slug');
                $except_id = $this->input->post('except_id');

                if(!isset($except_id)){
                    $except_id = null;
                }

                if(isset($slug)){
                    $exist = $this->wikiarticles_model->exist_slug($slug, $except_id);
                }

                $rs = !$exist;

            }
        }

        echo json_encode(["result" => $rs]);
        die();
    }

    public function bookmark_switch()
    {
        $rs = true;

        if ($this->input->post()) {
            if ($this->input->is_ajax_request()) {

                $is_on = $this->input->post('is_on');
                $article_id = $this->input->post('article_id');

                $user = get_staff($this->session->userdata('tfa_staffid'));

                if(isset($is_on) && isset($article_id)){
                    $article = $this->wikiarticles_model->get($article_id);
                    if(!isset($article)){
                        $rs = false;
                    }else{
                        $this->wikiarticles_model->switch_bookmark($user->staffid, $article_id, $is_on);
                        $rs = true;
                    }
                }

            }
        }

        echo json_encode(["result" => $rs]);
        die();
    }

    public function mindmap()
    {
        $data['title'] = _l('wiki_design_mindmap');
        $article_id = $this->input->get('article_id');

        if(!isset($article_id) || $article_id == ''){
            show_404();
        }

        $article = $this->wikiarticles_model->get($article_id);

        if(!isset($article)){
            show_404();
        }

        $data['article'] = $article;
        $data['back_url'] = admin_url('wiki/articles/article/' . $article->id);
        $this->load->view('mindmap', $data);
    }

    public function mindmap_save()
    {
        $rs = false;

        if ($this->input->post()) {
            if ($this->input->is_ajax_request()) {
                $data = $this->input->post();

                $rs_update = $this->wikiarticles_model->update_mindmap($data);
                if($rs_update){
                    $rs = true;
                }else{
                    $rs = false;
                }
            }
        }

        echo json_encode(["result" => $rs]);
        die();
    }

}
