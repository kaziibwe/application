<?php

class Webhooks extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model(WEBHOOKS_MODULE . '/webhooks_model');
        $this->load->helper(WEBHOOKS_MODULE. '/webhooks');
        $this->load->library('app_modules');

        if (!$this->app_modules->is_active(MMB_MODULE)) {
            access_denied();
        }
    }

    public function index(): void
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(MMB_MODULE, 'webhooks/tables/webhooks'));
        }
        $data['title'] = _l('webhooks');
        $this->load->view('webhooks/list', $data);
    }

    public function change_status_hook($webhook_id, $status)
    {
        $data['active'] = $status;
        $where['id']    = $webhook_id;
        if ($this->webhooks_model->change_webhook_status($data, $where)) {

            echo json_encode(['success' => true]);

            return true;
        }
        echo json_encode(['success' => false]);

        return false;
    }

    public function change_debug_status_hook($webhook_id, $status)
    {
        $data['debug_mode'] = $status;
        $where['id']        = $webhook_id;
        if ($this->webhooks_model->change_webhook_status($data, $where)) {
            echo json_encode(['success' => true]);

            return true;
        }
        echo json_encode(['success' => false]);

        return false;
    }

    public function delete_webhook($webhook_id): void
    {
        if ($this->webhooks_model->delete_webhook(['id' => $webhook_id])) {
            set_alert('success', _l('deleted', 'webhook'));
        } else {
            set_alert('danger', _l('problem_deleting', 'webhook'));
        }
        redirect(admin_url(WEBHOOKS_MODULE . '/webhooks'));
    }

    public function webhook($id = ''): void
    {
        if ($this->input->post()) {
            $posted_data  = $this->input->post();
            $is_valid_url = validate_request_url($posted_data['request_url']);
            if (!$is_valid_url) {
                set_alert('warning', 'URL is not valid');
            }
            if ($is_valid_url) {
                $data        = [
                    'name'           => $posted_data['webhook_name'],
                    'request_url'    => $is_valid_url,
                    'request_method' => $posted_data['request_method'],
                    'request_format' => $posted_data['request_format'],
                    'webhook_for'    => $posted_data['webhook_for'],
                    'webhook_action' => json_encode($posted_data['webhook_action'] ?? []),
                    'request_header' => json_encode(remove_blank_value($posted_data['header'], 'header_choice')),
                    'request_body'   => json_encode(remove_blank_value($posted_data['body'], 'key')),
                ];
                if (is_numeric($id)) {
                    if ($this->webhooks_model->update($data, ['id' => $id])) {
                        set_alert('success', _l('updated_successfully', _l('webhook')));
                    } else {
                        set_alert('danger', _l('something_went_wrong'));
                    }
                } else {
                    if ($this->webhooks_model->add($data)) {
                        set_alert('success', _l('added_successfully', _l('webhook')));
                    } else {
                        set_alert('danger', _l('something_went_wrong'));
                    }
                }
                redirect(admin_url(WEBHOOKS_MODULE . '/webhooks'));
            }
        }
        $data['title'] = _l('add_new', _l('webhook'));
        if (is_numeric($id)) {
            $data['title']   = _l('edit', _l('webhook'));
            $data['webhook'] = $this->get($id);
        }
        $this->load->view('webhooks/webhook', $data);
    }

    public function logs(): void
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(MMB_MODULE, '/webhooks/tables/webhooks_debug_log'));
        }
        $data['title'] = _l('webhook_log');
        $this->load->view('webhooks/logs', $data);
    }

    public function clear_webhook_log()
    {
        if ($this->webhooks_model->clear_webhook_log()) {
            set_alert('success', _l('deleted', _l('webhook_log')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('webhook_log')));
        }
        redirect(admin_url(WEBHOOKS_MODULE . '/webhooks/logs'));

        return true;
    }

    private function get($id)
    {
        $i       = $j       = 1;
        $webhook = $this->webhooks_model->get($id);
        if (!empty($webhook)) {
            $request_header_html = $request_body_html = '';
            foreach (json_decode($webhook->request_header) as $key => $value) {
                $header_key_select = '';
                if ('custom' === $value->header_choice) {
                    $header_key_select .= render_input('header[' . $i . '][header_custom_choice]', '', $value->header_custom_choice, '', [], [], 'header_custom_choice');
                    $header_key_select .= render_select('header[' . $i . '][header_choice]', get_header_choices(), ['label', 'value'], '', $value->header_choice, [], ['style' => 'display:none'], '', 'header_choice');
                } else {
                    $header_key_select .= render_select('header[' . $i . '][header_choice]', get_header_choices(), ['label', 'value'], '', $value->header_choice, [], [], '', 'header_choice');
                }
                $request_header_html .= "<div class='request_header_row' id='req_header_" . $i . "'>
                    <div class='row'>
                        <div class='col-md-4'>" . $header_key_select . "</div>
                        <div class='col-md-7'>" . render_input('header[' . $i . '][value]', '', $value->value, 'text', [], [], '', 'mentionable') . "
                        </div>
                        <div class='col-md-1'>
                            <button type='button' class='btn btn-sm btn-danger remove_row ' data-count='" . $i . "'>
                                <i class='fa fa-times'></i>
                            </button>
                        </div>
                    </div>
                </div>";
                $i++;
            }

            foreach (json_decode($webhook->request_body) as $key => $value) {
                $request_body_html .= "<div class='request_body_row' id='req_body_" . $j . "'>
                    <div class='row'>
                        <div class='col-md-4'>" . render_input('body[' . $j . '][key]', '', $value->key) . "</div>
                        <div class='col-md-7'>" . render_input('body[' . $j . '][value]', '', $value->value, 'text', [], [], '', 'mentionable') . "</div>
                        <div class='col-md-1'>
                            <button type='button' class='btn btn-sm btn-danger remove_body_row' data-count='" . $j . "'>
                                <i class='fa fa-times'></i>
                            </button>
                        </div>
                    </div>
                </div>";
                $j++;
            }
            $webhook->request_header_html = $request_header_html;
            $webhook->request_body_html   = $request_body_html;
            $webhook->webhook_action      = json_decode($webhook->webhook_action, true);
        }

        return $webhook;
    }

    public function get_webhook_log_info($log_id)
    {
        if ($log_id) {
            $data['title'] = _l('webhook_log');
            $data['log_data'] = $this->webhooks_model->get_log_info($log_id);
            $this->load->view('webhooks/webhook_log_detail', $data, FALSE);
        }
    }
    /* Copy webhook */
    public function copy_webhook($id)
    {
        $data = (array) $this->webhooks_model->get($id);

        $id = $this->webhooks_model->copy_webhook($data);

        if ($id) {
            set_alert('success', _l('webhook_copy_success'));
            redirect(admin_url(WEBHOOKS_MODULE . '/webhooks'));
        }

        set_alert('warning', _l('webhook_copy_fail'));
        redirect(admin_url(WEBHOOKS_MODULE . '/webhooks'));
    }
}
