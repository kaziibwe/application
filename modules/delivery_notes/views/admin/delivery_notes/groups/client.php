<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (isset($client)) { ?>
<h4 class="customer-profile-group-heading"><?php echo _l(DELIVERY_NOTE_MODULE_NAME); ?></h4>

<?php if (staff_can('create',  DELIVERY_NOTE_MODULE_NAME)) { ?>
<a href="<?php echo admin_url('delivery_notes/delivery_note?customer_id=' . $client->userid); ?>"
    class="btn btn-primary mbot15<?php echo $client->active == 0 ? ' disabled' : ''; ?>">
    <i class="fa-regular fa-plus tw-mr-1"></i>
    <?php echo _l('create_new_delivery_note'); ?>
</a>
<?php } ?>

<?php if (staff_can('view',  DELIVERY_NOTE_MODULE_NAME) || staff_can('view_own',  DELIVERY_NOTE_MODULE_NAME) || get_option('allow_staff_view_delivery_notes_assigned') == '1') { ?>
<a href="#" class="btn btn-primary mbot15" data-toggle="modal" data-target="#client_zip_delivery_notes">
    <i class="fa-regular fa-file-zipper tw-mr-1"></i>
    <?php echo _l('zip_delivery_notes'); ?>
</a>
<?php } ?>
<div id="delivery_notes_total" class="tw-mb-5"></div>
<?php
    $this->load->view('delivery_notes/admin/delivery_notes/table_html', ['class' => 'delivery-notes-single-client']);
    $this->load->view('delivery_notes/admin/delivery_notes/groups/client_zip', ['delivery_note_statuses'=>$this->delivery_notes_model->get_statuses()]);
    ?>
<?php } ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDataTable('.table-delivery-notes-single-client',
        admin_url + "delivery_notes/table/" + customer_id,
        'undefined',
        'undefined',
        'undefined', [
            [3, 'desc'],
            [0, 'desc']
        ]);
});
</script>