<?php

defined('BASEPATH') || exit('No direct script access allowed');

use WpOrg\Requests\Requests as Whatsapp_api_Requests;

class Log_details extends AdminController
{
    public function index()
    {
        $startTime = strtotime(date("Y-m-d 00:00:00"));
        $endTime = strtotime(date("Y-m-d 23:59:59"));

        $whatsapp_last_log_time = get_option('whatsapp_last_log_time');
        $seconds = 300;

        if ($whatsapp_last_log_time == '' || (time() > ($whatsapp_last_log_time + $seconds))) {
            $whatsapp_business_account_id = get_option('whatsapp_business_account_id');
            $whatsapp_access_token        = get_option('whatsapp_access_token');
            $request                      = Whatsapp_api_Requests::get(
                'https://graph.facebook.com/v16.0/' . $whatsapp_business_account_id . '?fields=id,name,analytics.start('.$startTime.').end('.$endTime.').granularity(DAY)&access_token=' . $whatsapp_access_token
            );

            $response = json_decode($request->body);

            $logData = serialize(json_encode($response->analytics->data_points ?? []));
            update_option("whatsapp_logs", $logData);
            update_option('whatsapp_last_log_time', time());

        }

        $logData = json_decode(unserialize(get_option("whatsapp_logs")));
        $data['sent'] = 0;
        if (!empty($logData)) {
            $data['sent'] = array_sum(array_column($logData, "sent"));
        }

        $data['title']                 = _l('whatsapp_log_details');
        if (!has_permission('whatsapp_api', '', 'whatsapp_log_details_view')) {
            access_denied('whatsapp_log_details_view');
        }
        $this->load->view('whatsapp/whatsapp_log', $data);
    }

    public function whatsapp_log_details_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(MMB_MODULE, 'whatsapp/tables/whatsapp_log_details_table'));
        }
    }

    public function clear_webhook_log()
    {
        if (!has_permission('whatsapp_api', '', 'whatsapp_log_details_clear')) {
            access_denied("whatsapp_log_details_clear");
        }
        $this->load->model(WHATSAPP_API_MODULE . '/whatsapp_api_model');
        if ($this->whatsapp_api_model->clear_webhook_log()) {
            set_alert('success', _l('deleted', _l('whatsapp_api_log')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('whatsapp_api_log')));
        }
        redirect(admin_url(WHATSAPP_API_MODULE . '/log_details'));

        return true;
    }

    public function get_whatsapp_api_log_info($log_id)
    {
        $this->load->model(WHATSAPP_API_MODULE . '/whatsapp_api_model');
        if ($log_id) {
            $data['title']    = _l('whatsapp_api_log');
            $data['log_data'] = $this->whatsapp_api_model->get_whatsapp_api_log_info($log_id);
            $this->load->view('whatsapp/whatsapp_api_log_details', $data, false);
        }
    }
}

/* End of file whatsapp_log_details.php */
/* Location: ./modules/whatsapp_api/controllers/whatsapp_log_details.php */
