<div class="modal fade" id="flexiblewa_action_sequence" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('flexiblewa_order_action'); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo render_select('section_id', $statuses, ['id', 'name'], 'flexiblewa_select_section', '', [
                    'required' => 'required',
                    'id' => 'flexiblewa_action_sequence_section_id'
                ]); ?>
                <div class="flexiblewa_action_sequence_container"></div>
            </div>
        </div>
    </div>
</div>