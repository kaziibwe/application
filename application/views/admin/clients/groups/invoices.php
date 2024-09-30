<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (isset($client)) { ?>
<h4 class="customer-profile-group-heading"><?php echo _l('client_invoices_tab'); ?></h4>
<?php if (staff_can('create',  'invoices')) { ?>
<a href="<?php echo admin_url('invoices/invoice?customer_id=' . $client->userid); ?>"
    class="btn btn-primary mbot15<?php echo $client->active == 0 ? ' disabled' : ''; ?>">
    <i class="fa-regular fa-plus tw-mr-1"></i>
    <?php echo _l('create_new_invoice'); ?>
</a>
<?php } ?>
<?php if (staff_can('view',  'invoices') || staff_can('view_own',  'invoices') || get_option('allow_staff_view_invoices_assigned') == '1') { ?>
<a href="#" class="btn btn-primary mbot15" data-toggle="modal" data-target="#client_zip_invoices">
    <i class="fa-regular fa-file-zipper tw-mr-1"></i>
    <?php echo _l('zip_invoices'); ?>
</a>
<div id="invoices_total" class="tw-mb-5"></div>
<?php
        $this->load->view('admin/invoices/table_html', ['class' => 'invoices-single-client']);
        $this->load->view('admin/clients/modals/zip_invoices');
?>
<?php } ?>
<?php } ?>