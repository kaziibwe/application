<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="table dt-table table-delivery_notes" data-order-col="1" data-order-type="desc">
    <thead>
        <tr>
            <th class="th-delivery-note-number"><?php echo _l('clients_delivery_note_dt_number'); ?></th>
            <th class="th-delivery-note-date"><?php echo _l('clients_delivery_note_dt_date'); ?></th>
            <th class="th-delivery-note-amount"><?php echo _l('clients_delivery_note_dt_amount'); ?></th>
            <th class="th-delivery-note-reference-number"><?php echo _l('reference_no'); ?></th>
            <th class="th-delivery-note-status"><?php echo _l('clients_delivery_note_dt_status'); ?></th>
            <?php
                    $custom_fields = get_custom_fields('delivery_note', array('show_on_client_portal' => 1));
                    foreach ($custom_fields as $field) { ?>
            <th><?php echo e($field['name']); ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($delivery_notes as $delivery_note) { ?>
        <tr>
            <td data-order="<?php echo e($delivery_note['number']); ?>"><a
                    href="<?php echo site_url('delivery_notes/client/dn/' . $delivery_note['id'] . '/' . $delivery_note['hash']); ?>"
                    class="delivery-notenumber"><?php echo e(format_delivery_note_number($delivery_note['id'])); ?></a>
                <?php
                            if ($delivery_note['invoiceid']) {
                                echo '<br /><span class="text-success">' . _l('delivery_note_invoiced') . '</span>';
                            }
                            ?>
            </td>
            <td data-order="<?php echo e($delivery_note['date']); ?>">
                <?php echo e(_d($delivery_note['date'])); ?></td>
            <td data-order="<?php echo e($delivery_note['total']); ?>">
                <?php echo e(app_format_money($delivery_note['total'], $delivery_note['currency_name'])); ?>
            </td>
            <td><?php echo e($delivery_note['reference_no']); ?></td>
            <td><?php echo format_delivery_note_status($delivery_note['status'], 'inline-block', true); ?></td>
            <?php foreach ($custom_fields as $field) { ?>
            <td><?php echo get_custom_field_value($delivery_note['id'], $field['id'], 'delivery_note'); ?></td>
            <?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>