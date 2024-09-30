<?php

use app\modules\delivery_notes\services\DeliveryNotePipeline;

defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_notes extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('delivery_notes_model');
    }

    /* Get all delivery_notes in case user go on index page */
    public function index($id = '')
    {
        $this->list_delivery_notes($id);
    }

    /* List all delivery_notes datatables */
    public function list_delivery_notes($id = '')
    {
        if (staff_cant('view', 'delivery_notes') && staff_cant('view_own', 'delivery_notes') && get_option('allow_staff_view_delivery_notes_assigned') == '0') {
            access_denied('delivery_notes');
        }

        $isPipeline = $this->session->userdata('delivery_note_pipeline') == 'true';

        $data['delivery_note_statuses'] = $this->delivery_notes_model->get_statuses();

        if ($isPipeline && !$this->input->get('status') && !$this->input->get('filter')) {
            $data['title']           = _l('delivery_notes_pipeline');
            $data['bodyclass']       = 'delivery_notes-pipeline delivery_notes-total-manual identity-confirmation';
            $data['switch_pipeline'] = false;

            if (is_numeric($id)) {
                $data['delivery_noteid'] = $id;
            } else {
                $data['delivery_noteid'] = $this->session->flashdata('delivery_noteid');
            }

            $this->load->view('admin/delivery_notes/pipeline/manage', $data);
        } else {

            // Pipeline was initiated but user click from home page and need to show table only to filter
            if ($this->input->get('status') || $this->input->get('filter') && $isPipeline) {
                $this->pipeline(0, true);
            }

            $data['delivery_noteid']            = $id;
            $data['switch_pipeline']       = true;
            $data['title']                 = _l('delivery_notes');
            $data['bodyclass']             = 'delivery_notes-total-manual identity-confirmation';
            $data['delivery_notes_years']       = $this->delivery_notes_model->get_delivery_notes_years();
            $data['delivery_notes_sale_agents'] = $this->delivery_notes_model->get_sale_agents();

            $this->load->view('admin/delivery_notes/manage', $data);
        }
    }

    public function table($clientid = '')
    {
        if (!has_permission('delivery_notes', '', 'view') && !has_permission('delivery_notes', '', 'view_own') && get_option('allow_staff_view_delivery_notes_assigned') == '0') {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path(DELIVERY_NOTE_MODULE_NAME, 'admin/tables/delivery_notes'), [
            'clientid' => $clientid,
        ]);
    }

    /* Add new delivery_note or update existing */
    public function delivery_note($id = '')
    {
        if ($this->input->post()) {
            $delivery_note_data = $this->input->post();

            $save_and_send_later = false;
            if (isset($delivery_note_data['save_and_send_later'])) {
                unset($delivery_note_data['save_and_send_later']);
                $save_and_send_later = true;
            }

            if ($id == '') {
                if (staff_cant('create', 'delivery_notes')) {
                    access_denied('delivery_notes');
                }
                $id = $this->delivery_notes_model->add($delivery_note_data);

                if ($id) {
                    set_alert('success', _l('added_successfully', _l('delivery_note')));

                    $redUrl = admin_url('delivery_notes/list_delivery_notes/' . $id);

                    if ($save_and_send_later) {
                        $this->session->set_userdata('send_later', true);
                        // die(redirect($redUrl));
                    }

                    redirect(
                        !$this->set_delivery_note_pipeline_autoload($id) ? $redUrl : admin_url('delivery_notes/list_delivery_notes/')
                    );
                }
            } else {
                if (staff_cant('edit', 'delivery_notes')) {
                    access_denied('delivery_notes');
                }

                $delivery_note = $this->delivery_notes_model->get($id);
                if (!empty($delivery_note->signature) || !empty($delivery_note->staff_signatures)) {
                    set_alert('danger',  _l('delivery_note_signed_not_all_fields_editable'));
                    redirect(admin_url('delivery_notes/list_delivery_notes/' . $id));
                }

                if (!empty($delivery_note->signature) || !empty($delivery_note->staff_signatures)) {
                    set_alert('success', _l('updated_successfully', _l('delivery_note')));
                    redirect(admin_url('delivery_notes/list_delivery_notes/' . $id));
                }

                $success = $this->delivery_notes_model->update($delivery_note_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('delivery_note')));
                }
                if ($this->set_delivery_note_pipeline_autoload($id)) {
                    redirect(admin_url('delivery_notes/list_delivery_notes/'));
                } else {
                    redirect(admin_url('delivery_notes/list_delivery_notes/' . $id));
                }
            }
        }
        if ($id == '') {
            $title = _l('create_new_delivery_note');
        } else {
            $delivery_note = $this->delivery_notes_model->get($id);

            if (!$delivery_note || !user_can_view_delivery_note($id)) {
                blank_page(_l('delivery_note_not_found'));
            }

            $data['delivery_note'] = $delivery_note;
            $data['edit']     = true;
            $title            = _l('edit', _l('delivery_note_lowercase'));
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        if ($this->input->get('delivery_note_request_id')) {
            $data['delivery_note_request_id'] = $this->input->get('delivery_note_request_id');
        }

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $this->load->model('invoice_items_model');

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['delivery_note_statuses'] = $this->delivery_notes_model->get_statuses();
        $data['title']             = $title;

        $this->load->view('admin/delivery_notes/delivery_note', $data);
    }

    public function update_number_settings($id)
    {
        $response = [
            'success' => false,
            'message' => '',
        ];
        if (staff_can('edit',  'delivery_notes')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'delivery_notes', [
                'prefix' => $this->input->post('prefix'),
            ]);
            if ($this->db->affected_rows() > 0) {
                $response['success'] = true;
                $response['message'] = _l('updated_successfully', _l('delivery_note'));
            }
        }

        echo json_encode($response);
        die;
    }

    public function validate_delivery_note_number()
    {
        $isedit          = $this->input->post('isedit');
        $number          = $this->input->post('number');
        $date            = $this->input->post('date');
        $original_number = $this->input->post('original_number');
        $number          = trim($number);
        $number          = ltrim($number, '0');

        if ($isedit == 'true') {
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }

        if (total_rows(db_prefix() . 'delivery_notes', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->delivery_notes_model->delete_attachment($id);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Get all delivery_note data used when user click on delivery_note number in a datatable left side*/
    public function get_delivery_note_data_ajax($id, $to_return = false)
    {
        if (staff_cant('view', 'delivery_notes') && staff_cant('view_own', 'delivery_notes') && get_option('allow_staff_view_delivery_notes_assigned') == '0') {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die('No delivery_note found');
        }

        $delivery_note = $this->delivery_notes_model->get($id);

        if (!$delivery_note || !user_can_view_delivery_note($id)) {
            echo _l('delivery_note_not_found');
            die;
        }

        $delivery_note->date       = _d($delivery_note->date);
        if ($delivery_note->invoiceid !== null) {
            $this->load->model('invoices_model');
            $delivery_note->invoice = $this->invoices_model->get($delivery_note->invoiceid);
        }

        $template_name = 'delivery_note_send_to_customer';

        $data = my_prepare_mail_preview_data($template_name, $delivery_note->clientid, [DELIVERY_NOTE_MODULE_NAME]);

        $data['activity']          = $this->delivery_notes_model->get_delivery_note_activity($id);
        $data['delivery_note']          = $delivery_note;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['delivery_note_statuses'] = $this->delivery_notes_model->get_statuses();
        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'delivery_note']);
        $data['has_signature'] = !empty($delivery_note->signature) || !empty($delivery_note->staff_signatures);
        $data['staff_signature'] = $this->delivery_notes_model->get_staff_signatures($id, get_staff_user_id())[0] ?? '';

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }

        if ($to_return == false) {
            $this->load->view('admin/delivery_notes/delivery_note_preview_template', $data);
        } else {
            return $this->load->view('admin/delivery_notes/delivery_note_preview_template', $data, true);
        }
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && user_can_view_delivery_note($rel_id)) {
            $this->misc_model->add_note($this->input->post(), 'delivery_note', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if (user_can_view_delivery_note($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'delivery_note');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function mark_action_status($status, $id)
    {
        if (staff_cant('edit', 'delivery_notes')) {
            access_denied('delivery_notes');
        }
        $success = $this->delivery_notes_model->mark_action_status($status, $id);

        if ($success) {
            set_alert('success', _l('delivery_note_status_changed_success'));
        } else {
            set_alert('danger', _l('delivery_note_status_changed_fail'));
        }
        if ($this->set_delivery_note_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('delivery_notes/list_delivery_notes/' . $id));
        }
    }

    /* Send delivery_note to email */
    public function send_to_email($id)
    {
        $canView = user_can_view_delivery_note($id);
        if (!$canView) {
            access_denied('delivery_notes');
        } else {
            if (staff_cant('view', 'delivery_notes') && staff_cant('view_own', 'delivery_notes') && $canView == false) {
                access_denied('delivery_notes');
            }
        }

        try {
            $success = $this->delivery_notes_model->send_delivery_note_to_client($id, '', $this->input->post('attach_pdf'), $this->input->post('cc'));
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('delivery_note_sent_to_client_success'));
        } else {
            set_alert('danger', _l('delivery_note_sent_to_client_fail'));
        }
        if ($this->set_delivery_note_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('delivery_notes/list_delivery_notes/' . $id));
        }
    }

    /* Convert delivery_note to invoice */
    public function convert_to_invoice($id)
    {
        if (staff_cant('create', 'invoices')) {
            access_denied('invoices');
        }
        if (!$id) {
            die('No delivery_note found');
        }
        $draft_invoice = false;
        if ($this->input->get('save_as_draft')) {
            $draft_invoice = true;
        }
        $invoiceid = $this->delivery_notes_model->convert_to_invoice($id, false, $draft_invoice);
        if ($invoiceid) {
            set_alert('success', _l('delivery_note_convert_to_invoice_successfully'));
            redirect(admin_url('invoices/list_invoices/' . $invoiceid));
        } else {
            if ($this->session->has_userdata('delivery_note_pipeline') && $this->session->userdata('delivery_note_pipeline') == 'true') {
                $this->session->set_flashdata('delivery_noteid', $id);
            }
            if ($this->set_delivery_note_pipeline_autoload($id)) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('delivery_notes/list_delivery_notes/' . $id));
            }
        }
    }

    /** convert estimate to delivery note */
    public function convert_from_estimate($estimateid)
    {
        if (staff_cant('create', 'delivery_notes')) {
            access_denied('delivery_notes');
        }

        if (!$estimateid) {
            die('No estimate found');
        }

        $status = 4; //confirmed
        if ($this->input->get('save_as_new')) {
            $status = 1; // new
        }
        $new_id = $this->delivery_notes_model->convert_from_estimate($estimateid, $status);
        if ($new_id) {
            set_alert('success', _l('estimate_convert_to_delivery_note_successfully'));
            redirect(admin_url('delivery_notes/delivery_note/' . $new_id));
        }

        set_alert('danger', _l('estimate_convert_to_delivery_note_fail'));
        if ($this->set_delivery_note_pipeline_autoload($estimateid)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('estimates/estimate/' . $estimateid));
        }
    }

    /** convert purchase order to delivery note */
    public function convert_from_purchase_order($purchase_orderid)
    {
        if (staff_cant('create', 'delivery_notes')) {
            access_denied('delivery_notes');
        }

        if (!$purchase_orderid) {
            die('No purchase order found');
        }

        $status = 4; //confirmed
        if ($this->input->get('save_as_new')) {
            $status = 1; // new
        }
        $new_id = $this->delivery_notes_model->convert_from_purchase_order($purchase_orderid, $status);
        if ($new_id) {
            set_alert('success', _l('purchase_order_convert_to_delivery_note_successfully'));
            redirect(admin_url('delivery_notes/delivery_note/' . $new_id));
        }

        set_alert('danger', _l('purchase_order_convert_to_delivery_note_fail'));
        if ($this->set_delivery_note_pipeline_autoload($purchase_orderid)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('purchase_orders/purchase_order/' . $purchase_orderid));
        }
    }

    /** convert invoice to delivery note */
    public function convert_from_invoice($invoiceid)
    {
        if (staff_cant('create', 'delivery_notes')) {
            access_denied('delivery_notes');
        }

        if (!$invoiceid) {
            die('No invoice found');
        }

        $status = 4; //confirmed
        if ($this->input->get('save_as_new')) {
            $status = 1; // new
        }
        $new_id = $this->delivery_notes_model->convert_from_invoice($invoiceid, $status);
        if ($new_id) {
            set_alert('success', _l('invoice_convert_to_delivery_note_successfully'));
            redirect(admin_url('delivery_notes/delivery_note/' . $new_id));
        }

        set_alert('danger', _l('invoice_convert_to_delivery_note_fail'));
        if ($this->set_delivery_note_pipeline_autoload($invoiceid)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('invoices/invoice/' . $invoiceid));
        }
    }


    public function copy($id)
    {
        if (staff_cant('create', 'delivery_notes')) {
            access_denied('delivery_notes');
        }
        if (!$id) {
            die('No delivery_note found');
        }
        $new_id = $this->delivery_notes_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('delivery_note_copied_successfully'));
            if ($this->set_delivery_note_pipeline_autoload($new_id)) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('delivery_notes/delivery_note/' . $new_id));
            }
        }
        set_alert('danger', _l('delivery_note_copied_fail'));
        if ($this->set_delivery_note_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('delivery_notes/delivery_note/' . $id));
        }
    }

    /* Delete delivery_note */
    public function delete($id)
    {
        if (staff_cant('delete', 'delivery_notes')) {
            access_denied('delivery_notes');
        }
        if (!$id) {
            redirect(admin_url('delivery_notes/list_delivery_notes'));
        }
        $success = $this->delivery_notes_model->delete($id);
        if (is_array($success)) {
            set_alert('warning', _l('is_invoiced_delivery_note_delete_error'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('delivery_note')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('delivery_note_lowercase')));
        }
        redirect(admin_url('delivery_notes/list_delivery_notes'));
    }

    public function clear_acceptance_info($id)
    {
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'delivery_notes', get_acceptance_info_array(true));
        }

        redirect(admin_url('delivery_notes/list_delivery_notes/' . $id));
    }

    /* Generates delivery_note PDF and senting to email  */
    public function pdf($id)
    {
        $canView = user_can_view_delivery_note($id);
        if (!$canView) {
            access_denied('Delivery_note');
        } else {
            if (staff_cant('view', 'delivery_notes') && staff_cant('view_own', 'delivery_notes') && $canView == false) {
                access_denied('Delivery_note');
            }
        }
        if (!$id) {
            redirect(admin_url('delivery_notes/list_delivery_notes'));
        }
        $delivery_note        = $this->delivery_notes_model->get($id);
        $delivery_note_number = format_delivery_note_number($delivery_note->id);

        try {
            $pdf = delivery_note_pdf($delivery_note);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $fileNameHookData = hooks()->apply_filters('delivery_note_file_name_admin_area', [
            'file_name' => mb_strtoupper(slug_it($delivery_note_number)) . '.pdf',
            'delivery_note'  => $delivery_note,
        ]);

        $pdf->Output($fileNameHookData['file_name'], $type);
    }

    // Pipeline
    public function get_pipeline()
    {
        if (staff_can('view',  'delivery_notes') || staff_can('view_own',  'delivery_notes') || get_option('allow_staff_view_delivery_notes_assigned') == '1') {
            $data['delivery_note_statuses'] = $this->delivery_notes_model->get_statuses();
            $this->load->view('admin/delivery_notes/pipeline/pipeline', $data);
        }
    }

    public function pipeline_open($id)
    {
        $canView = user_can_view_delivery_note($id);
        if (!$canView) {
            access_denied('Delivery_note');
        } else {
            if (staff_cant('view', 'delivery_notes') && staff_cant('view_own', 'delivery_notes') && $canView == false) {
                access_denied('Delivery_note');
            }
        }

        $data['id']       = $id;
        $data['delivery_note'] = $this->get_delivery_note_data_ajax($id, true);
        $this->load->view('admin/delivery_notes/pipeline/delivery_note', $data);
    }

    public function update_pipeline()
    {
        if (staff_can('edit',  'delivery_notes')) {
            $this->delivery_notes_model->update_pipeline($this->input->post());
        }
    }

    public function pipeline($set = 0, $manual = false)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
            'delivery_note_pipeline' => $set,
        ]);
        if ($manual == false) {
            redirect(admin_url('delivery_notes/list_delivery_notes'));
        }
    }

    public function pipeline_load_more()
    {
        $status = $this->input->get('status');
        $page   = $this->input->get('page');

        $delivery_notes = (new DeliveryNotePipeline($status))
            ->search($this->input->get('search'))
            ->sortBy(
                $this->input->get('sort_by'),
                $this->input->get('sort')
            )
            ->page($page)->get();

        foreach ($delivery_notes as $delivery_note) {
            $this->load->view('admin/delivery_notes/pipeline/_kanban_card', [
                'delivery_note' => $delivery_note,
                'status'   => $status,
            ]);
        }
    }

    public function set_delivery_note_pipeline_autoload($id)
    {
        if ($id == '') {
            return false;
        }

        if (
            $this->session->has_userdata('delivery_note_pipeline')
            && $this->session->userdata('delivery_note_pipeline') == 'true'
        ) {
            $this->session->set_flashdata('delivery_noteid', $id);

            return true;
        }

        return false;
    }

    public function zip_delivery_notes($id)
    {
        $has_permission_view = staff_can('view',  'delivery_notes');
        if (
            !$has_permission_view && staff_cant('view_own', 'delivery_notes')
            && get_option('allow_staff_view_delivery_notes_assigned') == '0'
        ) {
            access_denied('Zip Customer delivery notes');
        }

        if ($this->input->post()) {
            $this->load->library('delivery_notes_bulk_pdf_export', [
                'export_type'       => 'delivery_notes',
                'status'            => $this->input->post('delivery_note_zip_status'),
                'date_from'         => $this->input->post('zip-from'),
                'date_to'           => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=delivery_notes'),
            ]);

            $this->delivery_notes_bulk_pdf_export->set_client_id($id);
            $this->delivery_notes_bulk_pdf_export->in_folder($this->input->post('file_name'));
            $this->delivery_notes_bulk_pdf_export->export();
        }
    }

    /**
     * Add signature
     *
     * @param mixed $id
     * @return void
     */
    public function append_signature($id)
    {
        $has_permission_view = staff_can('sign',  'delivery_notes');
        if (
            !$has_permission_view
        ) {
            access_denied(_l('delivery_note_append_signature'));
        }

        if ($this->input->post() && !empty($id)) {
            process_digital_signature_image($this->input->post('signature', false), get_upload_path_by_type(DELIVERY_NOTE_MODULE_NAME) . $id);

            $staffid = get_staff_user_id();

            if ($this->delivery_notes_model->add_staff_signature($id, $staffid))
                set_alert('success', _l('document_signed_successfully'));
        }
        return redirect(admin_url('delivery_notes/list_delivery_notes/' . $id));
    }

    /**
     * Clear signatures
     *
     * @param mixed $id
     * @return void
     */
    public function clear_signature($id)
    {

        if (!empty($id) && staff_can('delete',  'delivery_notes')) {
            $this->delivery_notes_model->clear_signatures($id);
        }

        return redirect(admin_url('delivery_notes/list_delivery_notes/' . $id));
    }
}
