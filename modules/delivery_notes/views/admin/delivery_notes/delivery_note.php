<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), ['id' => 'delivery_note-form', 'class' => '_transaction_form delivery_note-form']);
            if (isset($delivery_note)) {
                echo form_hidden('isedit');
            }
            ?>
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo isset($delivery_note) ? format_delivery_note_number($delivery_note) : _l('create_new_delivery_note'); ?>
                    </span>
                    <?php echo isset($delivery_note) ? format_delivery_note_status($delivery_note->status) : ''; ?>
                </h4>
                <?php $this->load->view('admin/delivery_notes/delivery_note_template'); ?>
            </div>
            <?php echo form_close(); ?>
            <?php $this->load->view('admin/invoice_items/item'); ?>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        validate_delivery_note_form();
        // Init accountacy currency symbol
        init_currency();
        // Project ajax search
        init_ajax_project_search_by_customer_id();
        // Maybe items ajax search
        init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
    });
</script>

</body>

</html>