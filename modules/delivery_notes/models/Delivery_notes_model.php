<?php

use app\services\AbstractKanban;

defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_notes_model extends App_Model
{
    private $statuses;

    private $shipping_fields = ['shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];

    public function __construct()
    {
        parent::__construct();

        $this->statuses = hooks()->apply_filters('before_set_delivery_note_statuses', [
            1,
            2,
            3,
            5,
            4,
        ]);
    }

    /**
     * Get unique sale agent for delivery_notes / Used for filters
     * @return array
     */
    public function get_sale_agents()
    {
        return $this->db->query("SELECT DISTINCT(sale_agent) as sale_agent, CONCAT(firstname, ' ', lastname) as full_name FROM " . db_prefix() . 'delivery_notes JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'delivery_notes.sale_agent WHERE sale_agent != 0')->result_array();
    }

    /**
     * Get delivery_note/s
     * @param mixed $id delivery_note id
     * @param array $where perform where
     * @return mixed
     */
    public function get($id = '', $where = [])
    {
        $this->db->select('*,' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'delivery_notes.id as id, ' . db_prefix() . 'currencies.name as currency_name');
        $this->db->from(db_prefix() . 'delivery_notes');
        $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'delivery_notes.currency', 'left');
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'delivery_notes.id', $id);
            $delivery_note = $this->db->get()->row();
            if ($delivery_note) {
                $delivery_note->attachments                           = $this->get_attachments($id);
                $delivery_note->visible_attachments_to_customer_found = false;

                foreach ($delivery_note->attachments as $attachment) {
                    if ($attachment['visible_to_customer'] == 1) {
                        $delivery_note->visible_attachments_to_customer_found = true;

                        break;
                    }
                }

                $delivery_note->items = get_items_by_type('delivery_note', $id);

                if ($delivery_note->project_id) {
                    $this->load->model('projects_model');
                    $delivery_note->project_data = $this->projects_model->get($delivery_note->project_id);
                }

                $delivery_note->client = $this->clients_model->get($delivery_note->clientid);

                if (!$delivery_note->client) {
                    $delivery_note->client          = new stdClass();
                    $delivery_note->client->company = $delivery_note->deleted_customer_name;
                }

                $this->load->model('email_schedule_model');
                $delivery_note->scheduled_email = $this->email_schedule_model->get($id, 'delivery_note');

                $delivery_note->staff_signatures = $this->get_staff_signatures($id);

                // Add invoice
                $invoice = $this->invoices_model->db->where('delivery_noteid', $id)->from(db_prefix() . 'invoices')->get()->row();
                if ($invoice) {
                    $delivery_note->invoice = $invoice;
                    $delivery_note->invoiceid = $invoice->id;
                }

                // Add estimate
                $estimate = $this->estimates_model->db->where('delivery_noteid', $id)->from(db_prefix() . 'estimates')->get()->row();
                if ($estimate) {
                    $delivery_note->estimate = $estimate;
                }

                if (defined('PURCHASE_ORDER_MODULE_NAME') && $this->db->table_exists(db_prefix() . 'purchase_orders')) {
                    // Add purchase order
                    $purchase_order = $this->purchase_orders_model->db->where('delivery_noteid', $id)->from(db_prefix() . 'purchase_orders')->get()->row();
                    if ($purchase_order) {
                        $delivery_note->purchase_order = $purchase_order;
                    }
                }
            }

            return $delivery_note;
        }
        $this->db->order_by('number,YEAR(date)', 'desc');

        return $this->db->get()->result_array();
    }

    /**
     * Get delivery_note statuses
     * @return array
     */
    public function get_statuses()
    {
        return $this->statuses;
    }

    /**
     * Convert delivery_note to invoice
     * @param mixed $id delivery_note id
     * @return mixed     New invoice ID
     */
    public function convert_to_invoice($id, $client = false, $draft_invoice = false)
    {
        // Recurring invoice date is okey lets convert it to new invoice
        $_delivery_note = $this->get($id);
        if (!empty($_delivery_note->invoiceid)) {
            if (!empty($this->invoices_model->get($_delivery_note->invoiceid)))
                return $_delivery_note->invoiceid;
        }

        $new_invoice_data = [];
        if ($draft_invoice == true) {
            $new_invoice_data['save_as_draft'] = true;
        }
        $new_invoice_data['clientid']   = $_delivery_note->clientid;
        $new_invoice_data['project_id'] = $_delivery_note->project_id;
        $new_invoice_data['number']     = get_option('next_invoice_number');
        $new_invoice_data['date']       = _d(date('Y-m-d'));
        $new_invoice_data['duedate']    = _d(date('Y-m-d'));
        if (get_option('invoice_due_after') != 0) {
            $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
        }
        $new_invoice_data['show_quantity_as'] = $_delivery_note->show_quantity_as;
        $new_invoice_data['currency']         = $_delivery_note->currency;
        $new_invoice_data['subtotal']         = $_delivery_note->subtotal;
        $new_invoice_data['total']            = $_delivery_note->total;
        $new_invoice_data['adjustment']       = $_delivery_note->adjustment;
        $new_invoice_data['discount_percent'] = $_delivery_note->discount_percent;
        $new_invoice_data['discount_total']   = $_delivery_note->discount_total;
        $new_invoice_data['discount_type']    = $_delivery_note->discount_type;
        $new_invoice_data['sale_agent']       = $_delivery_note->sale_agent;
        // Since version 1.0.6
        $new_invoice_data['billing_street']   = clear_textarea_breaks($_delivery_note->billing_street);
        $new_invoice_data['billing_city']     = $_delivery_note->billing_city;
        $new_invoice_data['billing_state']    = $_delivery_note->billing_state;
        $new_invoice_data['billing_zip']      = $_delivery_note->billing_zip;
        $new_invoice_data['billing_country']  = $_delivery_note->billing_country;
        $new_invoice_data['shipping_street']  = clear_textarea_breaks($_delivery_note->shipping_street);
        $new_invoice_data['shipping_city']    = $_delivery_note->shipping_city;
        $new_invoice_data['shipping_state']   = $_delivery_note->shipping_state;
        $new_invoice_data['shipping_zip']     = $_delivery_note->shipping_zip;
        $new_invoice_data['shipping_country'] = $_delivery_note->shipping_country;

        if ($_delivery_note->include_shipping == 1) {
            $new_invoice_data['include_shipping'] = 1;
        }

        $new_invoice_data['show_shipping_on_invoice'] = $_delivery_note->show_shipping_on_delivery_note;
        $new_invoice_data['terms']                    = get_option('predefined_terms_invoice');
        $new_invoice_data['clientnote']               = get_option('predefined_clientnote_invoice');
        // Set to unpaid status automatically
        $new_invoice_data['status']    = 1;
        $new_invoice_data['adminnote'] = '';

        $this->load->model('payment_modes_model');
        $modes = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
        $temp_modes = [];
        foreach ($modes as $mode) {
            if ($mode['selected_by_default'] == 0) {
                continue;
            }
            $temp_modes[] = $mode['id'];
        }
        $new_invoice_data['allowed_payment_modes'] = $temp_modes;
        $new_invoice_data['newitems']              = [];
        $custom_fields_items                       = get_custom_fields('items');
        $key                                       = 1;
        foreach ($_delivery_note->items as $item) {
            $new_invoice_data['newitems'][$key]['description']      = $item['description'];
            $new_invoice_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
            $new_invoice_data['newitems'][$key]['unit']             = $item['unit'];
            $new_invoice_data['newitems'][$key]['taxname']          = [];
            $taxes                                                  = get_delivery_note_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_invoice_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_invoice_data['newitems'][$key]['rate']  = $item['rate'];
            $new_invoice_data['newitems'][$key]['order'] = $item['item_order'];
            foreach ($custom_fields_items as $cf) {
                $new_invoice_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                    define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                }
            }
            $key++;
        }

        $new_invoice_data['delivery_noteid'] = $_delivery_note->id;

        $this->load->model('invoices_model');
        $id = $this->invoices_model->add($new_invoice_data);
        if ($id) {
            // Customer accepted the delivery_note and is auto converted to invoice
            if (!is_staff_logged_in()) {
                $this->db->where('rel_type', 'invoice');
                $this->db->where('rel_id', $id);
                $this->db->delete(db_prefix() . 'sales_activity');
                $this->invoices_model->log_invoice_activity($id, 'invoice_activity_auto_converted_from_delivery_note', true, serialize([
                    '<a href="' . admin_url('delivery_notes/list_delivery_notes/' . $_delivery_note->id) . '">' . format_delivery_note_number($_delivery_note->id) . '</a>',
                ]));
            }
            // For all cases update addefrom and sale agent from the invoice
            // May happen staff is not logged in and these values to be 0
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', [
                'addedfrom'  => $_delivery_note->addedfrom,
                'sale_agent' => $_delivery_note->sale_agent,
            ]);

            // Update delivery_note with the new invoice data and set to status accepted
            $this->db->where('id', $_delivery_note->id);
            $this->db->update(db_prefix() . 'delivery_notes', [
                'invoiced_date' => date('Y-m-d H:i:s'),
                'invoiceid'     => $id,
                'status'        => 4,
            ]);


            $this->transfer_custom_fields('delivery_note', 'invoice', $_delivery_note->id, $id);

            if ($client == false) {
                $this->log_delivery_note_activity($_delivery_note->id, 'delivery_note_activity_converted', false, serialize([
                    '<a href="' . admin_url('invoices/list_invoices/' . $id) . '">' . format_invoice_number($id) . '</a>',
                ]));
            }

            hooks()->do_action('delivery_note_converted_to_invoice', ['invoice_id' => $id, 'delivery_noteid' => $_delivery_note->id]);
        }

        return $id;
    }

    public function convert_from_invoice($invoiceid, $status)
    {
        $this->load->model('invoices_model');

        $_invoice = $this->invoices_model->get($invoiceid);
        if (!empty($_invoice->delivery_noteid)) {
            if (!empty($this->get($_invoice->delivery_noteid)))
                return $_invoice->delivery_noteid;
        }

        $new_delivery_note_data               = [];
        $new_delivery_note_data['clientid']   = $_invoice->clientid;
        $new_delivery_note_data['project_id'] = $_invoice->project_id;
        $new_delivery_note_data['number']     = get_option('next_delivery_note_number');
        $new_delivery_note_data['date']       = _d(date('Y-m-d'));

        $new_delivery_note_data['show_quantity_as'] = $_invoice->show_quantity_as;
        $new_delivery_note_data['currency']         = $_invoice->currency;
        $new_delivery_note_data['subtotal']         = $_invoice->subtotal;
        $new_delivery_note_data['total']            = $_invoice->total;
        $new_delivery_note_data['adminnote']        = $_invoice->adminnote;
        $new_delivery_note_data['adjustment']       = $_invoice->adjustment;
        $new_delivery_note_data['discount_percent'] = $_invoice->discount_percent;
        $new_delivery_note_data['discount_total']   = $_invoice->discount_total;
        $new_delivery_note_data['discount_type']    = $_invoice->discount_type;
        $new_delivery_note_data['terms']            = $_invoice->terms;
        $new_delivery_note_data['sale_agent']       = $_invoice->sale_agent;
        $new_delivery_note_data['reference_no']     = $_invoice->reference_no;
        // Since version 1.0.6
        $new_delivery_note_data['billing_street']   = clear_textarea_breaks($_invoice->billing_street);
        $new_delivery_note_data['billing_city']     = $_invoice->billing_city;
        $new_delivery_note_data['billing_state']    = $_invoice->billing_state;
        $new_delivery_note_data['billing_zip']      = $_invoice->billing_zip;
        $new_delivery_note_data['billing_country']  = $_invoice->billing_country;
        $new_delivery_note_data['shipping_street']  = clear_textarea_breaks($_invoice->shipping_street);
        $new_delivery_note_data['shipping_city']    = $_invoice->shipping_city;
        $new_delivery_note_data['shipping_state']   = $_invoice->shipping_state;
        $new_delivery_note_data['shipping_zip']     = $_invoice->shipping_zip;
        $new_delivery_note_data['shipping_country'] = $_invoice->shipping_country;
        if ($_invoice->include_shipping == 1) {
            $new_delivery_note_data['include_shipping'] = $_invoice->include_shipping;
        }
        $new_delivery_note_data['show_shipping_on_delivery_note'] = $_invoice->show_shipping_on_invoice;
        // Set to unpaid status automatically
        $new_delivery_note_data['status']     = $status;
        $new_delivery_note_data['clientnote'] = $_invoice->clientnote;
        $new_delivery_note_data['adminnote']  = '';
        $new_delivery_note_data['newitems']   = [];
        $custom_fields_items             = get_custom_fields('items');
        $key                             = 1;
        foreach ($_invoice->items as $item) {
            $new_delivery_note_data['newitems'][$key]['description']      = $item['description'];
            $new_delivery_note_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $new_delivery_note_data['newitems'][$key]['qty']              = $item['qty'];
            $new_delivery_note_data['newitems'][$key]['unit']             = $item['unit'];
            $new_delivery_note_data['newitems'][$key]['taxname']          = [];
            $taxes                                                   = get_invoice_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_delivery_note_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_delivery_note_data['newitems'][$key]['rate']  = $item['rate'];
            $new_delivery_note_data['newitems'][$key]['order'] = $item['item_order'];
            foreach ($custom_fields_items as $cf) {
                $new_delivery_note_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                    define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                }
            }
            $key++;
        }

        $new_delivery_note_data['invoiced_date'] = date('Y-m-d H:i:s');
        $new_delivery_note_data['invoiceid'] = $_invoice->id;

        $id = $this->add($new_delivery_note_data);

        if ($id) {

            // Update invoice with the new delivery note data
            $this->db->where('id', $_invoice->id);
            $this->db->update(db_prefix() . 'invoices', [
                'delivery_noteid'     => $id,
            ]);

            $tags = get_tags_in($_invoice->id, 'invoice');
            handle_tags_save($tags, $id, 'delivery_note');

            $this->transfer_custom_fields('invoice', 'delivery_note', $_invoice->id, $id);

            $this->invoices_model->log_invoice_activity($_invoice->id, 'invoice_activity_converted_to_delivery_note', false, serialize([
                '<a href="' . admin_url('delivery_notes/delivery_note/' . $id) . '">' . format_delivery_note_number($id) . '</a>',
            ]));


            hooks()->do_action('invoice_converted_to_delivery_note', ['delivery_noteid' => $id, 'invoice_id' => $_invoice->id]);

            return $id;
        }

        return false;
    }

    public function convert_from_estimate($estimateid, $status)
    {
        $this->load->model('estimates_model');

        $_estimate = $this->estimates_model->get($estimateid);
        if (!empty($_estimate->delivery_noteid)) {
            if (!empty($this->get($_estimate->delivery_noteid)))
                return $_estimate->delivery_noteid;
        }

        $new_delivery_note_data               = [];
        $new_delivery_note_data['clientid']   = $_estimate->clientid;
        $new_delivery_note_data['project_id'] = $_estimate->project_id;
        $new_delivery_note_data['number']     = get_option('next_delivery_note_number');
        $new_delivery_note_data['date']       = _d(date('Y-m-d'));

        $new_delivery_note_data['show_quantity_as'] = $_estimate->show_quantity_as;
        $new_delivery_note_data['currency']         = $_estimate->currency;
        $new_delivery_note_data['subtotal']         = $_estimate->subtotal;
        $new_delivery_note_data['total']            = $_estimate->total;
        $new_delivery_note_data['adminnote']        = $_estimate->adminnote;
        $new_delivery_note_data['adjustment']       = $_estimate->adjustment;
        $new_delivery_note_data['discount_percent'] = $_estimate->discount_percent;
        $new_delivery_note_data['discount_total']   = $_estimate->discount_total;
        $new_delivery_note_data['discount_type']    = $_estimate->discount_type;
        $new_delivery_note_data['terms']            = $_estimate->terms;
        $new_delivery_note_data['sale_agent']       = $_estimate->sale_agent;
        $new_delivery_note_data['reference_no']     = $_estimate->reference_no;
        // Since version 1.0.6
        $new_delivery_note_data['billing_street']   = clear_textarea_breaks($_estimate->billing_street);
        $new_delivery_note_data['billing_city']     = $_estimate->billing_city;
        $new_delivery_note_data['billing_state']    = $_estimate->billing_state;
        $new_delivery_note_data['billing_zip']      = $_estimate->billing_zip;
        $new_delivery_note_data['billing_country']  = $_estimate->billing_country;
        $new_delivery_note_data['shipping_street']  = clear_textarea_breaks($_estimate->shipping_street);
        $new_delivery_note_data['shipping_city']    = $_estimate->shipping_city;
        $new_delivery_note_data['shipping_state']   = $_estimate->shipping_state;
        $new_delivery_note_data['shipping_zip']     = $_estimate->shipping_zip;
        $new_delivery_note_data['shipping_country'] = $_estimate->shipping_country;
        if ($_estimate->include_shipping == 1) {
            $new_delivery_note_data['include_shipping'] = $_estimate->include_shipping;
        }
        $new_delivery_note_data['show_shipping_on_delivery_note'] = $_estimate->show_shipping_on_estimate;
        // Set to unpaid status automatically
        $new_delivery_note_data['status']     = $status;
        $new_delivery_note_data['clientnote'] = $_estimate->clientnote;
        $new_delivery_note_data['adminnote']  = '';
        $new_delivery_note_data['newitems']   = [];
        $custom_fields_items             = get_custom_fields('items');
        $key                             = 1;
        foreach ($_estimate->items as $item) {
            $new_delivery_note_data['newitems'][$key]['description']      = $item['description'];
            $new_delivery_note_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $new_delivery_note_data['newitems'][$key]['qty']              = $item['qty'];
            $new_delivery_note_data['newitems'][$key]['unit']             = $item['unit'];
            $new_delivery_note_data['newitems'][$key]['taxname']          = [];
            $taxes                                                   = get_estimate_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_delivery_note_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_delivery_note_data['newitems'][$key]['rate']  = $item['rate'];
            $new_delivery_note_data['newitems'][$key]['order'] = $item['item_order'];
            foreach ($custom_fields_items as $cf) {
                $new_delivery_note_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                    define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                }
            }
            $key++;
        }
        $id = $this->add($new_delivery_note_data);

        if ($id) {

            // Update estimate with the new delivery note data
            $this->db->where('id', $_estimate->id);
            $this->db->update(db_prefix() . 'estimates', [
                'delivery_noteid'     => $id,
            ]);

            $tags = get_tags_in($_estimate->id, 'estimate');
            handle_tags_save($tags, $id, 'delivery_note');

            $this->transfer_custom_fields('estimate', 'delivery_note', $_estimate->id, $id);

            $this->estimates_model->log_estimate_activity($_estimate->id, 'estimate_activity_converted_to_delivery_note', false, serialize([
                '<a href="' . admin_url('delivery_notes/delivery_note/' . $id) . '">' . format_delivery_note_number($id) . '</a>',
            ]));


            hooks()->do_action('estimate_converted_to_delivery_note', ['delivery_noteid' => $id, 'estimate_id' => $_estimate->id]);

            return $id;
        }

        return false;
    }

    public function convert_from_purchase_order($purchase_orderid, $status)
    {

        $this->load->model('purchase_orders_model');

        $_purchase_order = $this->purchase_orders_model->get($purchase_orderid);
        if (!empty($_purchase_order->delivery_noteid)) {
            if (!empty($this->get($_purchase_order->delivery_noteid)))
                return $_purchase_order->delivery_noteid;
        }

        $new_delivery_note_data               = [];
        $new_delivery_note_data['clientid']   = $_purchase_order->clientid;
        $new_delivery_note_data['project_id'] = $_purchase_order->project_id;
        $new_delivery_note_data['number']     = get_option('next_delivery_note_number');
        $new_delivery_note_data['date']       = _d(date('Y-m-d'));

        $new_delivery_note_data['show_quantity_as'] = $_purchase_order->show_quantity_as;
        $new_delivery_note_data['currency']         = $_purchase_order->currency;
        $new_delivery_note_data['subtotal']         = $_purchase_order->subtotal;
        $new_delivery_note_data['total']            = $_purchase_order->total;
        $new_delivery_note_data['adminnote']        = $_purchase_order->adminnote;
        $new_delivery_note_data['adjustment']       = $_purchase_order->adjustment;
        $new_delivery_note_data['discount_percent'] = $_purchase_order->discount_percent;
        $new_delivery_note_data['discount_total']   = $_purchase_order->discount_total;
        $new_delivery_note_data['discount_type']    = $_purchase_order->discount_type;
        $new_delivery_note_data['terms']            = $_purchase_order->terms;
        $new_delivery_note_data['sale_agent']       = $_purchase_order->sale_agent;
        $new_delivery_note_data['reference_no']     = $_purchase_order->reference_no;
        // Since version 1.0.6
        $new_delivery_note_data['billing_street']   = clear_textarea_breaks($_purchase_order->billing_street);
        $new_delivery_note_data['billing_city']     = $_purchase_order->billing_city;
        $new_delivery_note_data['billing_state']    = $_purchase_order->billing_state;
        $new_delivery_note_data['billing_zip']      = $_purchase_order->billing_zip;
        $new_delivery_note_data['billing_country']  = $_purchase_order->billing_country;
        $new_delivery_note_data['shipping_street']  = clear_textarea_breaks($_purchase_order->shipping_street);
        $new_delivery_note_data['shipping_city']    = $_purchase_order->shipping_city;
        $new_delivery_note_data['shipping_state']   = $_purchase_order->shipping_state;
        $new_delivery_note_data['shipping_zip']     = $_purchase_order->shipping_zip;
        $new_delivery_note_data['shipping_country'] = $_purchase_order->shipping_country;
        if ($_purchase_order->include_shipping == 1) {
            $new_delivery_note_data['include_shipping'] = $_purchase_order->include_shipping;
        }
        $new_delivery_note_data['show_shipping_on_delivery_note'] = $_purchase_order->show_shipping_on_purchase_order;
        // Set to unpaid status automatically
        $new_delivery_note_data['status']     = $status;
        $new_delivery_note_data['clientnote'] = $_purchase_order->clientnote;
        $new_delivery_note_data['adminnote']  = '';
        $new_delivery_note_data['newitems']   = [];
        $custom_fields_items             = get_custom_fields('items');
        $key                             = 1;
        foreach ($_purchase_order->items as $item) {
            $new_delivery_note_data['newitems'][$key]['description']      = $item['description'];
            $new_delivery_note_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $new_delivery_note_data['newitems'][$key]['qty']              = $item['qty'];
            $new_delivery_note_data['newitems'][$key]['unit']             = $item['unit'];
            $new_delivery_note_data['newitems'][$key]['taxname']          = [];
            $taxes                                                   = get_purchase_order_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_delivery_note_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_delivery_note_data['newitems'][$key]['rate']  = $item['rate'];
            $new_delivery_note_data['newitems'][$key]['order'] = $item['item_order'];
            foreach ($custom_fields_items as $cf) {
                $new_delivery_note_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                    define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                }
            }
            $key++;
        }
        $id = $this->add($new_delivery_note_data);

        if ($id) {

            // Update purchase_order with the new delivery note data
            $this->db->where('id', $_purchase_order->id);
            $this->db->update(db_prefix() . 'purchase_orders', [
                'delivery_noteid'     => $id,
            ]);

            $tags = get_tags_in($_purchase_order->id, 'purchase_order');
            handle_tags_save($tags, $id, 'delivery_note');

            $this->transfer_custom_fields('purchase_order', 'delivery_note', $_purchase_order->id, $id);

            $this->purchase_orders_model->log_purchase_order_activity($_purchase_order->id, 'purchase_order_activity_converted_to_delivery_note', false, serialize([
                '<a href="' . admin_url('delivery_notes/delivery_note/' . $id) . '">' . format_delivery_note_number($id) . '</a>',
            ]));


            hooks()->do_action('purchase_order_converted_to_delivery_note', ['delivery_noteid' => $id, 'purchase_orderid' => $_purchase_order->id]);

            return $id;
        }

        return false;
    }

    /**
     * Copy a resources (i.e estiamte, invoice ) custom field to another custom field (i.e delivery note)
     * 
     * @param string $from_res i.e estimate
     * @param string $to_res i.e invoice
     * @param string|number $from_res_id The source resources id
     * @param string|number $to_res_id The target resources id
     * @return void
     */
    private function transfer_custom_fields($from_res, $to_res, $from_res_id, $to_res_id)
    {
        if (!is_custom_fields_smart_transfer_enabled()) return false;

        $this->db->where('fieldto', $from_res);
        $this->db->where('active', 1);
        $from_res_custom_feilds = $this->db->get(db_prefix() . 'customfields')->result_array();
        foreach ($from_res_custom_feilds as $field) {

            // Replace source resources i.e delivery_ntoe_sku_xxx becomes _sku_xxx
            $tmpSlug = str_ireplace($from_res, '', $field['slug']);
            // Split and get the slug i.e _sku_xxx
            $tmpSlug = explode('_', $tmpSlug, 2);

            if (isset($tmpSlug[1])) {
                $this->db->where('fieldto', $to_res);

                $this->db->group_start();
                $this->db->like('slug', $to_res . '_' . $tmpSlug[1], 'after');
                $this->db->where('type', $field['type']);
                $this->db->where('options', $field['options']);
                $this->db->where('active', 1);
                $this->db->group_end();

                $cfTransfer = $this->db->get(db_prefix() . 'customfields')->result_array();
                $value = get_custom_field_value($from_res_id, $field['id'], $from_res, false);

                // Don't make mistakes
                // Only valid if 1 result returned
                // + if field names similarity is equal or more then CUSTOM_FIELD_TRANSFER_SIMILARITY%
                if (count($cfTransfer) == 1 && ((similarity($field['name'], $cfTransfer[0]['name']) * 100) >= CUSTOM_FIELD_TRANSFER_SIMILARITY)) {
                    if ($value == '') {
                        continue;
                    }

                    $this->db->insert(db_prefix() . 'customfieldsvalues', [
                        'relid'   => $to_res_id,
                        'fieldid' => $cfTransfer[0]['id'],
                        'fieldto' => $to_res,
                        'value'   => $value,
                    ]);
                } else if ((int)get_option('delivery_note_allow_transfer_of_non_similar_custom_fields')) { // If no similar field found
                    // Create the custom field
                    $new_field = $field;
                    $new_field['id'] = null;
                    $new_field['fieldto'] = $to_res;
                    $new_field['slug'] = str_ireplace($from_res, $to_res, $field['slug']);

                    $this->db->insert(db_prefix() . 'customfields', $new_field);
                    $new_field_id = $this->db->insert_id();

                    $this->db->insert(db_prefix() . 'customfieldsvalues', [
                        'relid'   => $to_res_id,
                        'fieldid' => $new_field_id,
                        'fieldto' => $to_res,
                        'value'   => $value,
                    ]);
                }
            }
        }
    }

    /**
     * Copy delivery_note
     * @param mixed $id delivery_note id to copy
     * @return mixed
     */
    public function copy($id)
    {
        $_delivery_note                       = $this->get($id);
        $new_delivery_note_data               = [];
        $new_delivery_note_data['clientid']   = $_delivery_note->clientid;
        $new_delivery_note_data['project_id'] = $_delivery_note->project_id;
        $new_delivery_note_data['number']     = get_option('next_delivery_note_number');
        $new_delivery_note_data['date']       = _d(date('Y-m-d'));

        $new_delivery_note_data['show_quantity_as'] = $_delivery_note->show_quantity_as;
        $new_delivery_note_data['currency']         = $_delivery_note->currency;
        $new_delivery_note_data['subtotal']         = $_delivery_note->subtotal;
        $new_delivery_note_data['total']            = $_delivery_note->total;
        $new_delivery_note_data['adminnote']        = $_delivery_note->adminnote;
        $new_delivery_note_data['adjustment']       = $_delivery_note->adjustment;
        $new_delivery_note_data['discount_percent'] = $_delivery_note->discount_percent;
        $new_delivery_note_data['discount_total']   = $_delivery_note->discount_total;
        $new_delivery_note_data['discount_type']    = $_delivery_note->discount_type;
        $new_delivery_note_data['terms']            = $_delivery_note->terms;
        $new_delivery_note_data['sale_agent']       = $_delivery_note->sale_agent;
        $new_delivery_note_data['reference_no']     = $_delivery_note->reference_no;
        // Since version 1.0.6
        $new_delivery_note_data['billing_street']   = clear_textarea_breaks($_delivery_note->billing_street);
        $new_delivery_note_data['billing_city']     = $_delivery_note->billing_city;
        $new_delivery_note_data['billing_state']    = $_delivery_note->billing_state;
        $new_delivery_note_data['billing_zip']      = $_delivery_note->billing_zip;
        $new_delivery_note_data['billing_country']  = $_delivery_note->billing_country;
        $new_delivery_note_data['shipping_street']  = clear_textarea_breaks($_delivery_note->shipping_street);
        $new_delivery_note_data['shipping_city']    = $_delivery_note->shipping_city;
        $new_delivery_note_data['shipping_state']   = $_delivery_note->shipping_state;
        $new_delivery_note_data['shipping_zip']     = $_delivery_note->shipping_zip;
        $new_delivery_note_data['shipping_country'] = $_delivery_note->shipping_country;
        if ($_delivery_note->include_shipping == 1) {
            $new_delivery_note_data['include_shipping'] = $_delivery_note->include_shipping;
        }
        $new_delivery_note_data['show_shipping_on_delivery_note'] = $_delivery_note->show_shipping_on_delivery_note;
        // Set to unpaid status automatically
        $new_delivery_note_data['status']     = 1;
        $new_delivery_note_data['clientnote'] = $_delivery_note->clientnote;
        $new_delivery_note_data['adminnote']  = '';
        $new_delivery_note_data['newitems']   = [];
        $custom_fields_items             = get_custom_fields('items');
        $key                             = 1;
        foreach ($_delivery_note->items as $item) {
            $new_delivery_note_data['newitems'][$key]['description']      = $item['description'];
            $new_delivery_note_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $new_delivery_note_data['newitems'][$key]['qty']              = $item['qty'];
            $new_delivery_note_data['newitems'][$key]['unit']             = $item['unit'];
            $new_delivery_note_data['newitems'][$key]['taxname']          = [];
            $taxes                                                   = get_delivery_note_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_delivery_note_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_delivery_note_data['newitems'][$key]['rate']  = $item['rate'];
            $new_delivery_note_data['newitems'][$key]['order'] = $item['item_order'];
            foreach ($custom_fields_items as $cf) {
                $new_delivery_note_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                    define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                }
            }
            $key++;
        }
        $id = $this->add($new_delivery_note_data);
        if ($id) {
            $custom_fields = get_custom_fields('delivery_note');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($_delivery_note->id, $field['id'], 'delivery_note', false);
                if ($value == '') {
                    continue;
                }

                $this->db->insert(db_prefix() . 'customfieldsvalues', [
                    'relid'   => $id,
                    'fieldid' => $field['id'],
                    'fieldto' => 'delivery_note',
                    'value'   => $value,
                ]);
            }

            $tags = get_tags_in($_delivery_note->id, 'delivery_note');
            handle_tags_save($tags, $id, 'delivery_note');

            log_activity('Copied delivery_note ' . format_delivery_note_number($_delivery_note->id));

            return $id;
        }

        return false;
    }

    /**
     * Insert new delivery_note to database
     * @param array $data invoiec data
     * @return mixed - false if not insert, delivery_note ID if succes
     */
    public function add($data)
    {
        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        $data['prefix'] = get_option('delivery_note_prefix');

        $data['number_format'] = get_option('delivery_note_number_format');

        $save_and_send = isset($data['save_and_send']);

        $delivery_noteRequestID = false;
        if (isset($data['delivery_note_request_id'])) {
            $delivery_noteRequestID = $data['delivery_note_request_id'];
            unset($data['delivery_note_request_id']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $data['hash'] = app_generate_hash();
        $tags         = isset($data['tags']) ? $data['tags'] : '';

        $items = [];
        if (isset($data['newitems'])) {
            $items = $data['newitems'];
            unset($data['newitems']);
        }

        $data = $this->map_shipping_columns($data);

        $data['billing_street'] = trim($data['billing_street']);
        $data['billing_street'] = nl2br($data['billing_street']);

        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        if (!isset($data['created_by']) && !is_client_logged_in()) {
            $data['created_by'] = get_staff_user_id();
        }

        $hook = hooks()->apply_filters('before_delivery_note_added', [
            'data'  => $data,
            'items' => $items,
        ]);

        $data  = $hook['data'];
        $items = $hook['items'];

        $this->db->insert(db_prefix() . 'delivery_notes', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Update next delivery_note number in settings
            $this->db->where('name', 'next_delivery_note_number');
            $this->db->set('value', 'value+1', false);
            $this->db->update(db_prefix() . 'options');

            if ($delivery_noteRequestID !== false && $delivery_noteRequestID != '') {
                $this->load->model('delivery_note_request_model');
                $completedStatus = $this->delivery_note_request_model->get_status_by_flag('completed');
                $this->delivery_note_request_model->update_request_status([
                    'requestid' => $delivery_noteRequestID,
                    'status'    => $completedStatus->id,
                ]);
            }

            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            handle_tags_save($tags, $insert_id, 'delivery_note');

            foreach ($items as $key => $item) {
                if ($itemid = add_new_sales_item_post($item, $insert_id, 'delivery_note')) {
                    _maybe_insert_post_item_tax($itemid, $item, $insert_id, 'delivery_note');
                }
            }

            update_sales_total_tax_column($insert_id, 'delivery_note', db_prefix() . 'delivery_notes');
            $this->log_delivery_note_activity($insert_id, 'delivery_note_activity_created', false);

            hooks()->do_action('after_delivery_note_added', $insert_id);

            if ($save_and_send === true) {
                $this->send_delivery_note_to_client($insert_id, '', true, '', true);
            }

            return $insert_id;
        }

        return false;
    }

    /**
     * Get item by id
     * @param mixed $id item id
     * @return object
     */
    public function get_delivery_note_item($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'itemable')->row();
    }

    /**
     * Update delivery_note data
     * @param array $data delivery_note data
     * @param mixed $id delivery_noteid
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;

        $data['number'] = trim($data['number']);

        $original_delivery_note = $this->get($id);

        $original_status = $original_delivery_note->status;

        $original_number = $original_delivery_note->number;

        $original_number_formatted = format_delivery_note_number($id);

        $save_and_send = isset($data['save_and_send']);

        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        $newitems = [];
        if (isset($data['newitems'])) {
            $newitems = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'delivery_note')) {
                $affectedRows++;
            }
        }

        $data['billing_street'] = trim($data['billing_street']);
        $data['billing_street'] = nl2br($data['billing_street']);

        $data['shipping_street'] = trim($data['shipping_street']);
        $data['shipping_street'] = nl2br($data['shipping_street']);

        $data = $this->map_shipping_columns($data);

        $hook = hooks()->apply_filters('before_delivery_note_updated', [
            'data'          => $data,
            'items'         => $items,
            'newitems'      => $newitems,
            'removed_items' => isset($data['removed_items']) ? $data['removed_items'] : [],
        ], $id);

        $data                  = $hook['data'];
        $items                 = $hook['items'];
        $newitems              = $hook['newitems'];
        $data['removed_items'] = $hook['removed_items'];

        // Delete items checked to be removed from database
        foreach ($data['removed_items'] as $remove_item_id) {
            $original_item = $this->get_delivery_note_item($remove_item_id);
            if (handle_removed_sales_item_post($remove_item_id, 'delivery_note')) {
                $affectedRows++;
                $this->log_delivery_note_activity($id, 'invoice_delivery_note_activity_removed_item', false, serialize([
                    $original_item->description,
                ]));
            }
        }

        unset($data['removed_items']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'delivery_notes', $data);

        if ($this->db->affected_rows() > 0) {
            // Check for status change
            if ($original_status != $data['status']) {
                $this->log_delivery_note_activity($original_delivery_note->id, 'not_delivery_note_status_updated', false, serialize([
                    '<original_status>' . $original_status . '</original_status>',
                    '<new_status>' . $data['status'] . '</new_status>',
                ]));
                if ($data['status'] == 2) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'delivery_notes', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);
                }
            }
            if ($original_number != $data['number']) {
                $this->log_delivery_note_activity($original_delivery_note->id, 'delivery_note_activity_number_changed', false, serialize([
                    $original_number_formatted,
                    format_delivery_note_number($original_delivery_note->id),
                ]));
            }
            $affectedRows++;
        }

        foreach ($items as $key => $item) {
            $original_item = $this->get_delivery_note_item($item['itemid']);

            if (update_sales_item_post($item['itemid'], $item, 'item_order')) {
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'unit')) {
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'rate')) {
                $this->log_delivery_note_activity($id, 'invoice_delivery_note_activity_updated_item_rate', false, serialize([
                    $original_item->rate,
                    $item['rate'],
                ]));
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'qty')) {
                $this->log_delivery_note_activity($id, 'invoice_delivery_note_activity_updated_qty_item', false, serialize([
                    $item['description'],
                    $original_item->qty,
                    $item['qty'],
                ]));
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'description')) {
                $this->log_delivery_note_activity($id, 'invoice_delivery_note_activity_updated_item_short_description', false, serialize([
                    $original_item->description,
                    $item['description'],
                ]));
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'long_description')) {
                $this->log_delivery_note_activity($id, 'invoice_delivery_note_activity_updated_item_long_description', false, serialize([
                    $original_item->long_description,
                    $item['long_description'],
                ]));
                $affectedRows++;
            }

            if (isset($item['custom_fields'])) {
                if (handle_custom_fields_post($item['itemid'], $item['custom_fields'])) {
                    $affectedRows++;
                }
            }

            if (!isset($item['taxname']) || (isset($item['taxname']) && count($item['taxname']) == 0)) {
                if (delete_taxes_from_item($item['itemid'], 'delivery_note')) {
                    $affectedRows++;
                }
            } else {
                $item_taxes        = get_delivery_note_item_taxes($item['itemid']);
                $_item_taxes_names = [];
                foreach ($item_taxes as $_item_tax) {
                    array_push($_item_taxes_names, $_item_tax['taxname']);
                }

                $i = 0;
                foreach ($_item_taxes_names as $_item_tax) {
                    if (!in_array($_item_tax, $item['taxname'])) {
                        $this->db->where('id', $item_taxes[$i]['id'])
                            ->delete(db_prefix() . 'item_tax');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                    $i++;
                }
                if (_maybe_insert_post_item_tax($item['itemid'], $item, $id, 'delivery_note')) {
                    $affectedRows++;
                }
            }
        }

        foreach ($newitems as $key => $item) {
            if ($new_item_added = add_new_sales_item_post($item, $id, 'delivery_note')) {
                _maybe_insert_post_item_tax($new_item_added, $item, $id, 'delivery_note');
                $this->log_delivery_note_activity($id, 'invoice_delivery_note_activity_added_item', false, serialize([
                    $item['description'],
                ]));
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            update_sales_total_tax_column($id, 'delivery_note', db_prefix() . 'delivery_notes');
        }

        if ($save_and_send === true) {
            $this->send_delivery_note_to_client($id, '', true, '', true);
        }

        if ($affectedRows > 0) {
            hooks()->do_action('after_delivery_note_updated', $id);

            return true;
        }

        return false;
    }

    public function mark_action_status($action, $id, $client = false)
    {
        $delivery_note = $this->get($id);
        if ($client) {
            // Client can only confirm delivery
            if ($action !== 4 || empty($delivery_note->signature) || empty($delivery_note->acceptance_firstname))
                return false;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'delivery_notes', [
            'status' => $action,
        ]);

        $notifiedUsers = [];

        if ($this->db->affected_rows() > 0) {
            $delivery_note = $this->get($id);

            $editor =  new stdClass();
            if ($client) {
                $editor->firstname = $delivery_note->acceptance_firstname;
                $editor->lastname = $delivery_note->acceptance_lastname;
                $editor->email = $delivery_note->acceptance_email;
            } else $editor = get_staff();

            $this->db->where('staffid', $delivery_note->addedfrom);
            $this->db->or_where('staffid', $delivery_note->sale_agent);
            $staff_delivery_note = $this->db->get(db_prefix() . 'staff')->result_array();

            $contact_id = get_primary_contact_user_id($delivery_note->clientid);
            if ($action == 4) {

                $additional_data = '';
                if ($client)
                    $additional_data = serialize([$delivery_note->acceptance_firstname . ' ' . $delivery_note->acceptance_lastname . ' - ' . $delivery_note->acceptance_ip]);
                $this->log_delivery_note_activity($id, 'delivery_note_activity_confirmed', $client, $additional_data);

                foreach ($staff_delivery_note as $member) {
                    $notified = add_notification([
                        'fromcompany'     => true,
                        'touserid'        => $member['staffid'],
                        'description'     => 'not_delivery_note_confirmed',
                        'link'            => 'delivery_notes/list_delivery_notes/' . $id,
                        'additional_data' => serialize([
                            format_delivery_note_number($delivery_note->id)
                        ]),
                    ]);

                    if ($notified) {
                        array_push($notifiedUsers, $member['staffid']);
                    }

                    send_mail_template('delivery_note_delivered_to_staff', DELIVERY_NOTE_MODULE_NAME, $delivery_note, $member, $contact_id, $editor);
                }

                pusher_trigger_notification($notifiedUsers);
                hooks()->do_action('delivery_note_confirmed', $id);
            } elseif ($action == 3) {
                foreach ($staff_delivery_note as $member) {
                    $notified = add_notification([
                        'fromcompany'     => true,
                        'touserid'        => $member['staffid'],
                        'description'     => 'not_delivery_note_cancelled',
                        'link'            => 'delivery_notes/list_delivery_notes/' . $id,
                        'additional_data' => serialize([
                            format_delivery_note_number($delivery_note->id)
                        ]),
                    ]);

                    if ($notified) {
                        array_push($notifiedUsers, $member['staffid']);
                    }
                    // Send staff email notification that delivery note is cancelled
                    send_mail_template('delivery_note_cancelled_to_staff', DELIVERY_NOTE_MODULE_NAME, $delivery_note, $member, $contact_id, $editor);
                }

                pusher_trigger_notification($notifiedUsers);
                $this->log_delivery_note_activity($id, 'delivery_note_activity_cancelled', false);
                hooks()->do_action('delivery_note_cancelled', $id);
            } else {

                // Other status update
                $this->log_delivery_note_activity($id, 'delivery_note_activity_marked', false, serialize([
                    '<status>' . $action . '</status>',
                ]));

                foreach ($staff_delivery_note as $member) {
                    $notified = add_notification([
                        'fromcompany'     => true,
                        'touserid'        => $member['staffid'],
                        'description'     => 'not_delivery_note_status_updated',
                        'link'            => 'delivery_notes/list_delivery_notes/' . $id,
                        'additional_data' => serialize([
                            format_delivery_note_number($delivery_note->id),
                            format_delivery_note_status($action)
                        ]),
                    ]);

                    if ($notified) {
                        array_push($notifiedUsers, $member['staffid']);
                    }
                    // Send staff email notification that delivery note is cancelled
                    send_mail_template('delivery_note_status_updated_to_staff', DELIVERY_NOTE_MODULE_NAME, $delivery_note, $member, $contact_id, $editor);
                }

                pusher_trigger_notification($notifiedUsers);
            }

            if ($action == 2) {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'delivery_notes', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);
            }

            return true;
        }

        return false;
    }

    /**
     * Get delivery_note attachments
     * @param mixed $delivery_noteid
     * @param string $id attachment id
     * @return mixed
     */
    public function get_attachments($delivery_noteid, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $delivery_noteid);
        }
        $this->db->where('rel_type', 'delivery_note');
        $result = $this->db->get(db_prefix() . 'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     *  Delete delivery_note attachment
     * @param mixed $id attachmentid
     * @return  boolean
     */
    public function delete_attachment($id)
    {
        $attachment = $this->get_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('delivery_note') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('delivery_note Attachment Deleted [delivery_noteID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('delivery_note') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('delivery_note') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('delivery_note') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Delete delivery_note items and all connections
     * @param mixed $id delivery_noteid
     * @return boolean
     */
    public function delete($id, $simpleDelete = false)
    {
        if (get_option('delete_only_on_last_delivery_note') == 1 && $simpleDelete == false) {
            if (!is_last_delivery_note($id)) {
                return false;
            }
        }
        $delivery_note = $this->get($id);
        if (!empty($delivery_note->invoice->id) && $simpleDelete == false) {
            return [
                'is_invoiced_delivery_note_delete_error' => true,
            ];
        }
        hooks()->do_action('before_delivery_note_deleted', $id);

        $number = format_delivery_note_number($id);

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'delivery_notes');

        if ($this->db->affected_rows() > 0) {
            if (!is_null($delivery_note->short_link)) {
                app_archive_short_link($delivery_note->short_link);
            }

            if (get_option('delivery_note_number_decrement_on_delete') == 1 && $simpleDelete == false) {
                $current_next_delivery_note_number = get_option('next_delivery_note_number');
                if ($current_next_delivery_note_number > 1) {
                    // Decrement next delivery_note number to
                    $this->db->where('name', 'next_delivery_note_number');
                    $this->db->set('value', 'value-1', false);
                    $this->db->update(db_prefix() . 'options');
                }
            }

            if (total_rows(db_prefix() . 'estimates', [
                'delivery_noteid' => $id,
            ]) > 0) {
                $this->db->where('delivery_noteid', $delivery_note->id);
                $this->db->update(db_prefix() . 'estimates', [
                    'delivery_noteid'    => null,
                ]);
            }

            if ($this->db->table_exists(db_prefix() . 'purchase_orders')) {
                if (total_rows(db_prefix() . 'purchase_orders', [
                    'delivery_noteid' => $id,
                ]) > 0) {
                    $this->db->where('delivery_noteid', $delivery_note->id);
                    $this->db->update(db_prefix() . 'purchase_orders', [
                        'delivery_noteid'    => null,
                    ]);
                }
            }

            delete_tracked_emails($id, 'delivery_note');

            $this->db->where('relid IN (SELECT id from ' . db_prefix() . 'itemable WHERE rel_type="delivery_note" AND rel_id="' . $this->db->escape_str($id) . '")');
            $this->db->where('fieldto', 'items');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'delivery_note');
            $this->db->delete(db_prefix() . 'notes');

            $this->db->where('rel_type', 'delivery_note');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'views_tracking');

            $this->db->where('rel_type', 'delivery_note');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'taggables');

            $this->db->where('rel_type', 'delivery_note');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'reminders');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'delivery_note');
            $this->db->delete(db_prefix() . 'itemable');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'delivery_note');
            $this->db->delete(db_prefix() . 'item_tax');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'delivery_note');
            $this->db->delete(db_prefix() . 'sales_activity');

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'delivery_note');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $attachments = $this->get_attachments($id);
            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'delivery_note');
            $this->db->delete('scheduled_emails');

            // Get related tasks
            $this->db->where('rel_type', 'delivery_note');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }
            if ($simpleDelete == false) {
                log_activity('Delivery_note Deleted [Number: ' . $number . ']');
            }

            // Clear signatures 
            $this->clear_signatures($id);

            hooks()->do_action('after_delivery_note_deleted', $id);

            return true;
        }

        return false;
    }

    /**
     * Set delivery_note to sent when email is successfuly sended to client
     * @param mixed $id delivery_noteid
     */
    public function set_delivery_note_sent($id, $emails_sent = [])
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'delivery_notes', [
            'sent'     => 1,
            'datesend' => date('Y-m-d H:i:s'),
        ]);

        $this->log_delivery_note_activity($id, 'invoice_delivery_note_activity_sent_to_client', false, serialize([
            '<custom_data>' . implode(', ', $emails_sent) . '</custom_data>',
        ]));

        // Update delivery_note status to sent
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'delivery_notes', [
            'status' => 2,
        ]);

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'delivery_note');
        $this->db->delete('scheduled_emails');
    }

    /**
     * Send delivery_note to client
     * @param mixed $id delivery_noteid
     * @param string $template email template to sent
     * @param boolean $attachpdf attach delivery_note pdf or not
     * @return boolean
     */
    public function send_delivery_note_to_client($id, $template_name = '', $attachpdf = true, $cc = '', $manually = false)
    {
        $delivery_note = $this->get($id);

        if ($template_name == '') {
            $template_name = 'delivery_note_send_to_customer';
        }

        $delivery_note_number = format_delivery_note_number($delivery_note->id);

        $emails_sent = [];
        $send_to     = [];

        // Manually is used when sending the delivery_note via add/edit area button Save & Send
        if (!DEFINED('CRON') && $manually === false) {
            $send_to = $this->input->post('sent_to');
        } elseif (isset($GLOBALS['scheduled_email_contacts'])) {
            $send_to = $GLOBALS['scheduled_email_contacts'];
        } else {
            $contacts = $this->clients_model->get_contacts(
                $delivery_note->clientid,
                ['active' => 1, 'delivery_note_emails' => 1]
            );

            foreach ($contacts as $contact) {
                array_push($send_to, $contact['id']);
            }
        }

        $status_auto_updated = false;
        $status_now          = $delivery_note->status;

        if (is_array($send_to) && count($send_to) > 0) {
            $i = 0;

            // Auto update status to sent in case when user sends the delivery_note is with status draft
            if ($status_now == 1) {
                $this->db->where('id', $delivery_note->id);
                $this->db->update(db_prefix() . 'delivery_notes', [
                    'status' => 4,
                ]);
                $status_auto_updated = true;
            }

            if ($attachpdf) {
                $_pdf_delivery_note = $this->get($delivery_note->id);
                set_mailing_constant();
                $pdf = delivery_note_pdf($_pdf_delivery_note);

                $attach = $pdf->Output($delivery_note_number . '.pdf', 'S');
            }

            foreach ($send_to as $contact_id) {
                if ($contact_id != '') {
                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
                    }

                    $contact = $this->clients_model->get_contact($contact_id);

                    if (!$contact) {
                        continue;
                    }

                    $template = mail_template($template_name, DELIVERY_NOTE_MODULE_NAME, $delivery_note, $contact, $cc);

                    if ($attachpdf) {
                        $hook = hooks()->apply_filters('send_delivery_note_to_customer_file_name', [
                            'file_name' => str_replace('/', '-', $delivery_note_number . '.pdf'),
                            'delivery_note'  => $_pdf_delivery_note,
                        ]);

                        $template->add_attachment([
                            'attachment' => $attach,
                            'filename'   => $hook['file_name'],
                            'type'       => 'application/pdf',
                        ]);
                    }

                    if ($template->send()) {
                        array_push($emails_sent, $contact->email);
                    }
                }
                $i++;
            }
        } else {
            return false;
        }

        if (count($emails_sent) > 0) {
            $this->set_delivery_note_sent($id, $emails_sent);
            hooks()->do_action('delivery_note_sent', $id);

            return true;
        }

        if ($status_auto_updated) {
            // delivery_note not send to customer but the status was previously updated to sent now we need to revert back to new
            $this->db->where('id', $delivery_note->id);
            $this->db->update(db_prefix() . 'delivery_notes', [
                'status' => $status_now,
            ]);
        }

        return false;
    }

    /**
     * All delivery_note activity
     * @param mixed $id delivery_noteid
     * @return array
     */
    public function get_delivery_note_activity($id)
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'delivery_note');
        $this->db->order_by('date', 'asc');

        return $this->db->get(db_prefix() . 'sales_activity')->result_array();
    }

    /**
     * Log delivery_note activity to database
     * @param mixed $id delivery_noteid
     * @param string $description activity description
     */
    public function log_delivery_note_activity($id, $description = '', $client = false, $additional_data = '')
    {
        $staffid   = get_staff_user_id();
        $full_name = get_staff_full_name(get_staff_user_id());
        if (DEFINED('CRON')) {
            $staffid   = '[CRON]';
            $full_name = '[CRON]';
        } elseif ($client == true) {
            $staffid   = null;
            $full_name = '';
        }

        $this->db->insert(db_prefix() . 'sales_activity', [
            'description'     => $description,
            'date'            => date('Y-m-d H:i:s'),
            'rel_id'          => $id,
            'rel_type'        => 'delivery_note',
            'staffid'         => $staffid,
            'full_name'       => $full_name,
            'additional_data' => $additional_data,
        ]);
    }

    /**
     * Updates pipeline order when drag and drop
     * @param mixe $data $_POST data
     * @return void
     */
    public function update_pipeline($data)
    {
        $this->mark_action_status($data['status'], $data['delivery_noteid']);
        AbstractKanban::updateOrder($data['order'], 'pipeline_order', 'delivery_notes', $data['status']);
    }

    /**
     * Get delivery_note unique year for filtering
     * @return array
     */
    public function get_delivery_notes_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM ' . db_prefix() . 'delivery_notes ORDER BY year DESC')->result_array();
    }

    private function map_shipping_columns($data)
    {
        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = null;
                }
            }
            $data['show_shipping_on_delivery_note'] = 1;
            $data['include_shipping']          = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_delivery_note']) && ($data['show_shipping_on_delivery_note'] == 1 || $data['show_shipping_on_delivery_note'] == 'on')) {
                $data['show_shipping_on_delivery_note'] = 1;
            } else {
                $data['show_shipping_on_delivery_note'] = 0;
            }
        }

        return $data;
    }

    /**
     * Get list of all staff signatures for the delivery note
     *
     * @param mixed $id The delivery note ID
     * @param mixed $staffid Optional. When provided only return for the specified staff
     * @return array 
     */
    public function get_staff_signatures($id, $staffid = '')
    {
        $table = db_prefix() . 'delivery_note_staff_signatures';
        $this->db->select('*');
        $this->db->where('delivery_noteid', $id);
        if (!empty($staffid)) {
            $this->db->where('staff_id', $staffid);
        }
        $signatures = $this->db->get($table)->result();
        return $signatures;
    }

    /**
     * Add a staff signature to delivery note
     *
     * @param mixed $id The delivery note id
     * @param mixed $staffid
     * @return bool
     */
    public function add_staff_signature($id, $staffid)
    {
        // Remove existing signatures for the staff for this delivery note
        $this->clear_signatures($id, $staffid);

        // Add new signature
        $table = db_prefix() . 'delivery_note_staff_signatures';
        $signature_data = get_acceptance_info_array();
        $signature_data['staff_id'] = $staffid;
        $signature_data['delivery_noteid'] = $id;
        $signature_data['signature_title'] = $this->input->post('signature_title', true);
        $signature_data['datecreated']           = date('Y-m-d H:i:s');

        // Rename to unique file
        $this->load->helper('string');
        $basepath =  get_upload_path_by_type(DELIVERY_NOTE_MODULE_NAME) . $id . '/';
        $original_name = $signature_data['signature'];
        $new_name = time() . '_' . random_string() . '_' . $original_name;
        if (rename($basepath . $original_name, $basepath . $new_name)) {

            $signature_data['signature'] = $new_name;
            if ($this->db->insert($table, $signature_data)) {
                $this->load->helper('file');
                $path = $basepath . $signature_data['signature'];
                $mime_type = get_mime_by_extension($path);
                $signature_url = 'data:' . $mime_type . ';base64,' . base64_encode(file_get_contents($path));
                $this->log_delivery_note_activity($id, 'delivery_note_activity_signed', false, serialize([
                    '<img src="' . $signature_url . '" alt="">',
                ]));

                return true;
            }
        }
        return false;
    }

    /**
     * Add a client signature to delivery note
     *
     * @param mixed $id The delivery note id
     * @return bool
     */
    public function add_client_signature($id)
    {
        // Add new signature
        $table = db_prefix() . 'delivery_notes';
        $signature_data = get_acceptance_info_array();

        // Rename to unique file
        $this->load->helper('string');
        $basepath =  get_upload_path_by_type(DELIVERY_NOTE_MODULE_NAME) . $id . '/';
        $original_name = $signature_data['signature'];
        $new_name = time() . '_' . random_string() . '_' . $original_name;
        if (rename($basepath . $original_name, $basepath . $new_name)) {

            $signature_data['signature'] = $new_name;
            $this->db->where('id', $id);
            if ($this->db->update($table, $signature_data)) {

                // Mark as delivered
                $this->mark_action_status(4, $id, true);

                $this->load->helper('file');
                $path = $basepath . $signature_data['signature'];
                $mime_type = get_mime_by_extension($path);
                $signature_url = 'data:' . $mime_type . ';base64,' . base64_encode(file_get_contents($path));
                $name = is_client_logged_in() ? get_contact_full_name() : '';
                $name = empty($name) ? $signature_data['acceptance_firstname'] . ' ' . $signature_data['acceptance_lastname'] . ' - ' . $signature_data['acceptance_ip'] : $name;
                $this->log_delivery_note_activity($id, 'delivery_note_activity_signed_by_client', true, serialize([
                    $name,
                    '<img src="' . $signature_url . '" alt="">',
                ]));

                return true;
            }
        }
        return false;
    }

    /**
     * Clear all signature attached to a delivery note
     *
     * @param mixed $id The delivery note ID
     * @param mixed $staffid The staff id - optional. Will only clear for the staff when provided
     * @return bool
     */
    public function clear_signatures($id, $staffid = '')
    {
        $upload_path = get_upload_path_by_type(DELIVERY_NOTE_MODULE_NAME);

        $signatures = $this->get_staff_signatures($id, $staffid);
        foreach ($signatures as $signature) {

            if (!empty($signature->signature)) {
                $path = $upload_path . $id . '/' . $signature->signature;
                if (file_exists($path) && !is_dir($path))
                    unlink($path);
            }
        }

        // Remove alls signatures for the staff for this delivery note
        $table = db_prefix() . 'delivery_note_staff_signatures';
        $this->db->where('delivery_noteid', $id);
        if (!empty($staffid)) {
            $this->db->where('staff_id', $staffid);
        }
        $this->db->delete($table);

        // Remove user signature
        if (!empty($staffid)) {
            $this->log_delivery_note_activity($id, 'delivery_note_activity_staff_signatures_cleared', false, serialize([get_staff_full_name($staffid)]));
            return true;
        }

        // Clear client signature
        $delivery_note = $this->get($id);
        if ($delivery_note) {

            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'delivery_notes', get_acceptance_info_array(true));

            if (!empty($delivery_note->signature)) {
                $path = $upload_path . $id . '/' . $delivery_note->signature;
                if (file_exists($path) && !is_dir($path))
                    unlink($path);
            }

            $this->log_delivery_note_activity($id, 'delivery_note_activity_signatures_cleared', false);

            return true;
        }

        return false;
    }
}
