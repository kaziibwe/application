<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once(module_dir_path(DELIVERY_NOTE_MODULE_NAME, 'services/delivery_notes/DeliveryNotesPipeline.php'));

$i                   = 0;
$has_permission_edit = staff_can('edit',  'delivery_notes');
foreach ($delivery_note_statuses as $status) {
    $kanBan = new \app\modules\delivery_notes\services\DeliveryNotePipeline($status);
    $kanBan->search($this->input->get('search'))
        ->sortBy($this->input->get('sort_by'), $this->input->get('sort'));
    if ($this->input->get('refresh')) {
        $kanBan->refresh($this->input->get('refresh')[$status] ?? null);
    }
    $delivery_notes       = $kanBan->get();
    $total_delivery_notes = count($delivery_notes);
    $total_pages     = $kanBan->totalPages(); ?>
    <ul class="kan-ban-col" data-col-status-id="<?php echo $status; ?>" data-total-pages="<?php echo $total_pages; ?>" data-total="<?php echo $total_delivery_notes; ?>">
        <li class="kan-ban-col-wrapper">
            <div class="panel_s panel-<?php echo delivery_note_status_color_class($status); ?> no-mbot">
                <div class="panel-heading">
                    <?php echo delivery_note_status_by_id($status); ?> -
                    <span class="tw-text-sm">
                        <?php echo $kanBan->countAll() . ' ' . _l('delivery_notes') ?>
                    </span>
                </div>
                <div class="kan-ban-content-wrapper">
                    <div class="kan-ban-content">
                        <ul class="sortable<?php if ($has_permission_edit) {
                                                echo ' status pipeline-status';
                                            } ?>" data-status-id="<?php echo $status; ?>">
                            <?php
                            foreach ($delivery_notes as $delivery_note) {
                                $this->load->view('admin/delivery_notes/pipeline/_kanban_card', ['delivery_note' => $delivery_note, 'status' => $status]);
                            } ?>
                            <?php if ($total_delivery_notes > 0) { ?>
                                <li class="text-center not-sortable kanban-load-more" data-load-status="<?php echo $status; ?>">
                                    <a href="#" class="btn btn-default btn-block<?php if ($total_pages <= 1 || $kanBan->getPage() === $total_pages) {
                                                                                    echo ' disabled';
                                                                                } ?>" data-page="<?php echo $kanBan->getPage(); ?>" onclick="kanban_load_more(<?php echo $status; ?>,this,'delivery_notes/pipeline_load_more',310,360); return false;" ;><?php echo _l('load_more'); ?></a>
                                </li>
                            <?php } ?>
                            <li class="text-center not-sortable mtop30 kanban-empty<?php if ($total_delivery_notes > 0) {
                                                                                        echo ' hide';
                                                                                    } ?>">
                                <h4>
                                    <i class="fa-solid fa-circle-notch" aria-hidden="true"></i><br /><br />
                                    <?php echo _l('no_delivery_notes_found'); ?>
                                </h4>
                            </li>
                        </ul>
                    </div>
                </div>
        </li>
    </ul>
<?php $i++;
} ?>