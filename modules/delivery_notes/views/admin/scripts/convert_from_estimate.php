<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="estimate_to_delivery_note pull-right">
    <?php if (empty($estimate->delivery_noteid) && empty($estimate->invoiceid) && empty($estimate->purchase_orderid)) { ?>
        <?php if ((int)get_option('delivery_note_allow_creating_from_estimate') && staff_can('create', 'delivery_notes') && !empty($estimate->clientid)) { ?>
            <div class="btn-group pull-right mleft5" data-toggle="tooltip" data-title="<?= _l('estimate_convert_to_delivery_note_full'); ?>">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo _l('estimate_convert_to_delivery_note'); ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo admin_url('delivery_notes/convert_from_estimate/' . $estimate->id); ?>"><?php echo _l('convert_and_save_as_delivered'); ?></a>
                    </li>
                    <li class="divider"></li>

                    <li><a href="<?php echo admin_url('delivery_notes/convert_from_estimate/' . $estimate->id . '?save_as_new=true'); ?>"><?php echo _l('convert'); ?></a>
                    </li>

                </ul>
            </div>
        <?php } ?>
        <?php } else if (!empty($estimate->delivery_noteid)) {
        $formated_dnid = format_delivery_note_number($estimate->delivery_noteid);
        if (!empty($formated_dnid)) { ?>
            <a href="<?php echo admin_url('delivery_notes/list_delivery_notes/' . $estimate->delivery_noteid); ?>" class="btn btn-primary mleft10 pull-right"><?php echo $formated_dnid; ?></a>
    <?php }
    } ?>
</div>

<script>
    document.querySelector('.pull-right._buttons').appendChild(document.querySelector('.estimate_to_delivery_note'));
    // Remove convert to invoice button
    <?php if (!empty($estimate->delivery_noteid) && !empty($formated_dnid)) : ?>
        var buttons = document.querySelectorAll('.pull-right._buttons button');
        if (buttons.length)
            buttons.forEach(function(button) {
                // Check if the button's text content contains the desired text
                if (button.textContent.toLocaleLowerCase().trim().includes("<?= _l('estimate_convert_to_invoice'); ?>"
                        .trim().toLocaleLowerCase())) {
                    button.remove();
                }
            });
    <?php endif; ?>
</script>