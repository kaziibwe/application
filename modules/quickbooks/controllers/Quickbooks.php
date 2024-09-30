<?php
	
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APP_MODULES_PATH. 'quickbooks/libraries/Quickbook_config.php');

class Quickbooks extends AdminController

{
    private $quickbooks_obj;

    public function __construct()

    {
        parent::__construct();

        if (!is_admin()) {

            access_denied('Quickbooks');

        }

    $this->quickbooks_obj=new Quickbook_config;

    }
    public function quickbooks_process()

    {	

        $this->db->select('*');

        $this->db->from('tbloptions');

        $this->db->where('name','quickbook_refresh_token');    

        $db_data =  $this->db->get()->row();

        if($db_data){

           $data['is_qb_connected']= 'yes';

        }else{

           $data['is_qb_connected']= 'no'; 

        }

    	$data['title'] = _l('settings_group_quickbooks');

    	$this->load->view('quickbooks',$data);

    }
    /*  quickbook functions */

    public function check_auth_quickbook(){

      	$accessToken=$this->quickbooks_obj->generate_accessToken($_GET);

        $accessToken=$_GET;

        $this->session->set_userdata('quickbook_auth',$accessToken);

        if($accessToken){
            update_option('realmId',$_GET['realmId'],1);
            $company_country = $this->quickbooks_obj->get_company_country();
            if($company_country)
            {
                update_option('xero_company_country',$company_country,1);
                echo htmlspecialchars("Conneced Successfully...");

                echo "<script>opener.location.reload();</script>";
    
                echo "<script>setTimeout(function(){ window.close(); },1000);</script>";
            }

           

        }

    } 
    public function regenerate_token(){

        $this->quickbooks_obj->refresh_token();

        redirect('/admin/quickbooks/quickbooks_process');

    }
    public function disconnect_qb(){

        $this->db->where('name', 'quickbook_refresh_token');

        $this->db->delete('tbloptions');



        $this->db->where('name', 'quickbook_access_token');

        $this->db->delete('tbloptions');



        $this->db->where('name', 'quickbook_realmId');

        $this->db->delete('tbloptions');



        $this->session->unset_userdata('quickbook_access_token');

        $this->session->unset_userdata('quickbook_refresh_token');

        $this->session->unset_userdata('realmId');

        

        redirect('/admin/quickbooks/quickbooks_process');
    }
    public function store(){

        $data=$this->input->post();
      
        if(!empty($data)){

			\modules\quickbooks\core\Apiinit::ease_of_mind('quickbooks');
			\modules\quickbooks\core\Apiinit::the_da_vinci_code('quickbooks');
			
            if($data['settings']['is_quickbooks_app_in_production_mode'] == 'production')
            {
                update_option('is_quickbooks_app_in_production_mode','production',1);
            }
            else{
                update_option('is_quickbooks_app_in_production_mode','development',1);
            }
            $this->db->select('*');

            $this->db->from('tbloptions');

            $this->db->where('name','quickbook_client_id');

            $this->db->or_where('name','quickbook_client_secret');

            $this->db->or_where('name','quickbook_redirect_uri');

            $this->db->or_where('name','quickbook_scope');

            $this->db->or_where('name','quickbook_response_type');

            $this->db->or_where('name','quickbook_state');
            $check_quickbook_data =  $this->db->get()->result_array();
            $form_data=array(
                        array(

                            'name'  => 'quickbook_client_id',

                            'value' => $data['quickbook_client_id']

                        ),
                        array(

                            'name'  => 'quickbook_client_secret',

                            'value' => $data['quickbook_client_secret']

                        ),
                        array(

                            'name'  => 'quickbook_redirect_uri',

                            'value' => site_url('/admin/quickbooks/check_auth_quickbook')

                        )      

                    );
            if(empty($check_quickbook_data)){

                    $this->db->insert_batch('tbloptions', $form_data);

                    $this->db->affected_rows();

            } else {

                $this->db->update_batch('tbloptions', $form_data,'name');

                $this->db->affected_rows();

            }

            redirect('/admin/quickbooks/quickbooks_process');

        }

    }
    public function qb_expenses_cron_job(){

        $CI = &get_instance();

        $CI->load->model('expenses_model');       

        $CI->load->library(QUICKBOOKS_MODULE . '/quickbook_expenses');

        \modules\quickbooks\core\Apiinit::ease_of_mind('quickbooks');
		\modules\quickbooks\core\Apiinit::the_da_vinci_code('quickbooks');

        $db_data=$this->expenses_model->get();

        foreach ($db_data as $key => $db_values) {

            $this->quickbook_expenses->create_expense_data($db_values);

        }

    }



}