<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade delivery_note-pipeline" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="close_modal_manually('.delivery_note-pipeline'); return false;" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo format_delivery_note_number($id); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $delivery_note; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="close_modal_manually('.delivery_note-pipeline'); return false;"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>