<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/Clients.php');

class Client extends ClientsController
{
    public function dn($id, $hash)
    {
        check_delivery_note_restrictions($id, $hash);
        $delivery_note = $this->delivery_notes_model->get($id);

        if (!is_client_logged_in()) {
            load_client_language($delivery_note->clientid);
        }

        $identity_confirmation_enabled = get_option('delivery_note_accept_identity_confirmation');

        // Handle Delivery note PDF generator
        if ($this->input->post('delivery_notepdf')) {
            try {
                $pdf = delivery_note_pdf($delivery_note);
            } catch (Exception $e) {
                echo $e->getMessage();
                die;
            }

            $delivery_note_number = format_delivery_note_number($delivery_note->id);
            $companyname     = get_option('invoice_company_name');
            if ($companyname != '') {
                $delivery_note_number .= '-' . mb_strtoupper(slug_it($companyname), 'UTF-8');
            }

            $filename = hooks()->apply_filters('customers_area_download_delivery_note_filename', mb_strtoupper(slug_it($delivery_note_number), 'UTF-8') . '.pdf', $delivery_note);

            $pdf->Output($filename, 'D');
            die();
        }
        $this->load->library('app_number_to_word', [
            'clientid' => $delivery_note->clientid,
        ], 'numberword');

        $this->app_scripts->theme('sticky-js', 'assets/plugins/sticky/sticky.js');

        $data['title'] = format_delivery_note_number($delivery_note->id);
        $this->disableNavigation();
        $this->disableSubMenu();
        $data['hash']                          = $hash;
        $data['can_be_confirmed']               = in_array($delivery_note->status, [2, 4]) && empty($delivery_note->signature);
        $data['delivery_note']                      = hooks()->apply_filters('delivery_note_html_pdf_data', $delivery_note);
        $data['bodyclass']                     = 'viewdelivery_note';
        $data['identity_confirmation_enabled'] = $identity_confirmation_enabled;
        if ($identity_confirmation_enabled == '1') {
            $data['bodyclass'] .= ' identity-confirmation';
        }
        $this->data($data);
        $this->view('client/delivery_notehtml', $data, true);
        add_views_tracking('delivery_note', $id);
        hooks()->do_action('delivery_note_html_viewed', $id);
        no_index_customers_area();
        $this->layout();
    }

    public function index($status = '')
    {
        if (!has_contact_permission('delivery_notes')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $where = [
            'clientid' => get_client_user_id(),
        ];
        if (is_numeric($status)) {
            $where['status'] = $status;
        }
        if (isset($where['status'])) {
            if ($where['status'] == 1 && get_option('exclude_delivery_note_from_client_area_with_waiting_status') == 1) {
                unset($where['status']);
                $where['status !='] = 1;
            }
        } else {
            if (get_option('exclude_delivery_note_from_client_area_with_waiting_status') == 1) {
                $where['status !='] = 1;
            }
        }
        $data['delivery_notes'] = $this->delivery_notes_model->get('', $where);
        $data['title']     = _l('clients_my_delivery_notes');
        $this->data($data);
        $this->view('client/delivery_notes');
        $this->layout();
    }

    /**
     * @inheritDoc
     */
    public function view($view)
    {
        $project_group = $this->input->get('group');
        if ($project_group === DELIVERY_NOTE_MODULE_NAME && $view === 'project') {

            $view = 'client/project_delivery_notes';

            // Inject project purchase orders
            $data['delivery_notes'] = [];
            if (has_contact_permission(DELIVERY_NOTE_MODULE_NAME)) {
                $where_delivery_notes = [
                    'clientid'   => get_client_user_id(),
                    'project_id' => $this->data['project']->id,
                ];

                if (get_option('exclude_delivery_note_from_client_area_with_waiting_status') == 1) {
                    $where_delivery_notes['status !='] = 1;
                }

                $data['delivery_notes'] = $this->delivery_notes_model->get('', $where_delivery_notes);
                $this->data($data);
            }
        }

        $this->view = $view;

        return $this;
    }

    public function sign_delivery($id)
    {
        $delivery_note = $this->delivery_notes_model->get($id);
        check_delivery_note_restrictions($id, $delivery_note->hash);
        if (is_client_logged_in()) {
            load_client_language($delivery_note->clientid);
        }

        if ($this->input->post() && !empty($id)) {
            process_digital_signature_image($this->input->post('signature', false), get_upload_path_by_type(DELIVERY_NOTE_MODULE_NAME) . $id);
            if ($this->delivery_notes_model->add_client_signature($id))
                set_alert('success', _l('document_signed_successfully'));
        }

        $redURL = site_url('delivery_notes/client/dn/' . $delivery_note->id . '/' . $delivery_note->hash);
        return redirect($redURL);
    }
}