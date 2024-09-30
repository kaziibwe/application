<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 section-heading section-heading-delivery_notes">
    <?php echo _l('clients_my_delivery_notes'); ?>
</h4>
<div class="panel_s">
    <div class="panel-body">

        <?php
        $where_total = ['clientid' => get_client_user_id()];
        $exclude_waiting = get_option('exclude_delivery_note_from_client_area_with_waiting_status') == 1;
        if ($exclude_waiting) {
            $where_total['status !='] = 1;
        }
        $total_delivery_notes = total_rows(db_prefix() . 'delivery_notes', $where_total);
        $delivery_note_statuses = $this->delivery_notes_model->get_statuses();
        $col_class = $exclude_waiting ? 'col-md-4' : 'col-md-3';
        ?>
        <div class="row text-left delivery_notes-stats">

            <?php foreach ($delivery_note_statuses as $status) {

                if ($exclude_waiting) continue;

                $total = total_rows(db_prefix() . 'delivery_notes', ['status' => $status, 'clientid' => get_client_user_id()]);
                $percent     = ($total_delivery_notes > 0 ? number_format(($total * 100) / $total_delivery_notes, 2) : 0);
            ?>
                <div class="<?php echo e($col_class); ?>">
                    <div class="row">
                        <div class="col-md-8 stats-status">
                            <a href="<?php echo site_url('clients/delivery_notes/' . $status); ?>" class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                                <?php echo format_delivery_note_status($status, '', false); ?>
                            </a>
                        </div>
                        <div class="col-md-4 text-right bold stats-numbers">
                            <?php echo e($total); ?> / <?php echo e($total_delivery_notes); ?>
                        </div>
                        <div class="col-md-12 tw-mt-1.5">
                            <div class="progress">
                                <div class="progress-bar progress-bar-<?php echo delivery_note_status_color_class($status); ?>" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo e($percent); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>


        <hr />

        <?php require_once(__DIR__ . '/delivery_notes_table.php'); ?>

    </div>
</div>