<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Flexibleleadfinder extends AdminController
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $this->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfinder_model');
        $data['title'] = flexibleleadfinder_lang('lead-finder');
        $data['searches'] = $this->flexibleleadfinder_model->all();
        $data['staff_members'] = flexibleleadfinder_get_staff_members();

        foreach ($data['searches'] as &$search) {
            $search['results_count'] = flexibleleadfinder_get_contacts_count($search['id']);
        }

        if ($this->input->is_ajax_request()) {
            $response = [
                'success' => true,
                'html' => $this->load->view('partials/search-list', $data, true)
            ];
            echo json_encode($response);
            die;
        }
        $this->load->view('index', $data);
    }

    public function view($id)
    {
        $this->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfinder_model');
        $data['search'] = $this->flexibleleadfinder_model->get(['id' => $id]);
        $this->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfindercontacts_model');
        $data['contacts'] = $this->flexibleleadfindercontacts_model->all(['leadfinder_id' => $id]);
        if ($this->input->is_ajax_request()) {
            $response = [
                'success' => true,
                'html' => $this->load->view('partials/search-detail', $data, true)
            ];
            echo json_encode($response);
            die;
        }
        $this->load->view('view', $data);
    }

    public function delete($id)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfinder_model');
            $this->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfindercontacts_model');

            $response = [
                'success' => false,
                'message' => flexibleleadfinder_lang('search-deletion-failed')
            ];

            $this->db->trans_begin();

            try {
                $this->flexibleleadfindercontacts_model->delete(['leadfinder_id' => $id]);
                $deleted = $this->flexibleleadfinder_model->delete(['id' => $id]);

                if ($deleted) {
                    $this->db->trans_commit();
                    $response = [
                        'success' => true,
                        'message' => flexibleleadfinder_lang('search-deleted-successfully')
                    ];
                } else {
                    $this->db->trans_rollback();
                }
            } catch (\Throwable $th) {
                $response['message'] = $th->getMessage();
                $this->db->trans_rollback();
            }

            echo json_encode($response);
        }
    }

    public function delete_contact($id)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfindercontacts_model');
            $deleted = $this->flexibleleadfindercontacts_model->delete(['id' => $id]);

            $response = [
                'success' => false,
                'message' => flexibleleadfinder_lang('contact-deletion-failed')
            ];

            if ($deleted) {
                $response = [
                    'success' => true,
                    'message' => flexibleleadfinder_lang('contact-deleted-successfully')
                ];
            }

            echo json_encode($response);
            die;
        }
    }

    public function sync_contact($contact_id)
    {
        if ($this->input->is_ajax_request()) {
            $this->db->trans_begin();

            try {
                //code...
                $response = [
                    'success' => false,
                    'message' => flexibleleadfinder_lang('contact-sync-failed')
                ];

                if (flexibleleadfinder_sync_lead($contact_id)) {
                    $response = [
                        'success' => true,
                        'message' => flexibleleadfinder_lang('contact-synced-successfully')
                    ];

                    $this->db->trans_commit();
                }
            } catch (\Throwable $th) {
                $this->db->trans_rollback();
                $response['message'] = $th->getMessage();
            }

            echo json_encode($response);
            die;
        }
    }

    public function sync_all($search_id)
    {
        if ($this->input->is_ajax_request()) {
            $this->db->trans_begin();

            try {
                $response = [
                    'success' => false,
                    'message' => flexibleleadfinder_lang('syncing-contacts-failed')
                ];

                if (flexibleleadfinder_sync_all_leads($search_id)) {
                    $response = [
                        'success' => true,
                        'message' => flexibleleadfinder_lang('all-contacts-synced-successfully')
                    ];

                    $this->db->trans_commit();
                }
            } catch (\Throwable $th) {
                $response['message'] = $th->getMessage();
            }

            echo json_encode($response);
            die;
        }
    }

    public function new_search()
    {
        $this->db->trans_begin();

        try {
            $location = $this->input->post('address');
            $keyword = $this->input->post('keyword');
            $name = $this->input->post('name');
            $this->load->library(FLEXIBLELEADFINDER_MODULE_NAME . '/leadfindersearch_module');
            $this->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfinder_model');

            $date = flexibleleadfinder_get_date();
            $data = [
                'name' => $name,
                'keyword' => $keyword,
                'location' => $location,
                'date_added' => $date,
                'date_updated' => $date
            ];

            $leadfinder_id = $this->flexibleleadfinder_model->add($data);

            if (
                $this->leadfindersearch_module->populate_businesses_near_location(
                    $leadfinder_id,
                    $location,
                    $keyword
                )
            ) {
                $this->db->trans_commit();
                set_alert('success', flexibleleadfinder_lang("search-successful"));
            } else {
                $this->db->trans_rollback();
                set_alert('warning', flexibleleadfinder_lang("no-results-found"));
            }
        } catch (\Throwable $th) {
            $this->db->trans_rollback();

            set_alert('danger', $th->getMessage());
        }
        redirect(flexibleleadfinder_admin_url());
    }

    public function settings()
    {
        if ($post = $this->input->post()) {
            foreach ($post['settings'] as $key => $value) {
                if (option_exists($key)) {
                    update_option($key, $value);
                } else {
                    add_option($key, $value);
                }
            }
            set_alert('success', flexibleleadfinder_lang("settings-updated-successfully"));
        }
        redirect(flexibleleadfinder_admin_url());
    }

    public function delete_all_searches()
    {
        if(!is_admin(get_staff_user_id())){
            redirect(admin_url('flexibleleadfinder'));
        }

        $this->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfinder_model');
        $this->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfindercontacts_model');

        $this->db->trans_begin();

        try {
            $searches = $this->flexibleleadfinder_model->all();
    
            foreach($searches as $search){
                $this->flexibleleadfindercontacts_model->delete([
                    'leadfinder_id' => $search['id']
                ]);
    
                $this->flexibleleadfinder_model->delete([
                    'id' => $search['id']
                ]);
            }

            $this->db->trans_commit();
            set_alert('success', flexibleleadfinder_lang('all-searches-deleted-successfully'));
            
        } catch (\Throwable $th) {
            set_alert('danger', $th->getMessage());
        }

        redirect(admin_url('flexibleleadfinder'));
    }
}

