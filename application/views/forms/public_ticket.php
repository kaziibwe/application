<div class="public-ticket mtop40">
    <?php hooks()->do_action('public_ticket_start', $ticket); ?>
    <?php if (is_staff_logged_in()) { ?>
    <div class="alert alert-warning">
        <?= _l('staff_logged_in_public_ticket_warning') ?>
    </div>
    <?php } ?>
    <?php echo $single_ticket_view; ?>
    <?php hooks()->do_action('public_ticket_end', $ticket); ?>
</div>
