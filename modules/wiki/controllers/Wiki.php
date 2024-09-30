<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wiki extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('wikiarticles_model');
    }

    public function index($slug = '')
    {
        if($slug == ''){
            show_404();
        }
        $articles = $this->wikiarticles_model->get_published($slug);
        if(isset($articles) && count($articles) > 0){
            $data['article'] = $articles[0];
            $data['title'] = $articles[0]['title'];
            // $this->add_count($id);
            switch ($articles[0]['type']) {
                case 'document':
                    $this->load->view('publish_article_show', $data);
                    break;
                case 'mindmap':
                    $this->load->view('publish_article_mindmap_show', $data);
                    break;
                default:
                    show_404();
                    break;
            }
        }else{
            show_404();
        }
    }
    // protected function add_count($id)
    // {
    //     $cookie_name = 'wiki_article_counter_'.$id;
    //     $this->load->helper('cookie');
    //     $check_visitor = $this->input->cookie($cookie_name, TRUE);
    //     $ip = $this->input->ip_address();
    //     if ($check_visitor == false) {
    //         $cookie = array(
    //             "name"   => $cookie_name,
    //             "value"  => true,
    //             "expire" =>  time() + 7200,
    //             "secure" => false
    //         );

    //         $this->input->set_cookie($cookie);
            
    //         $this->wikiarticles_model->count_view($id);
    //     }
    // }
}
