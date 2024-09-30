<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <?php echo $this->load->view('partials/search-list') ?>
</div>
<div class="modal fade" id="leadfinder_new_search-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(flexibleleadfinder_admin_url('new_search')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo flexibleleadfinder_lang('new-search'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?php echo render_input('name', flexibleleadfinder_lang('title')); ?>
                <span class="tw-block tw-text-sm tw-text-gray-500 tw-mb-2">
                    <?php echo flexibleleadfinder_lang('title-desc'); ?>
                </span>
                <?php echo render_input('keyword', flexibleleadfinder_lang('keyword')); ?>
                <span class="tw-block tw-text-sm tw-text-gray-500 tw-mb-2">
                    <?php echo flexibleleadfinder_lang('keyword-desc'); ?>
                </span>
                <?php echo render_input('address', flexibleleadfinder_lang('address')); ?>
                <span class="tw-block tw-text-sm tw-text-gray-500 tw-mb-2">
                    <?php echo flexibleleadfinder_lang('location-desc'); ?>
                </span>

                <input type="hidden" name="id" id="sprint_id" value="0">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i>
                    <?php echo flexibleleadfinder_lang('search'); ?>
                </button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="leadfinder_settings_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(flexibleleadfinder_admin_url('settings')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo flexibleleadfinder_lang('settings'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?php echo render_input('settings[' . FLEXIBLELEADFINDER_RECORDS_LIMIT_SETTING . ']', flexibleleadfinder_lang('records-limit'), option_exists(FLEXIBLELEADFINDER_RECORDS_LIMIT_SETTING) ? get_option(FLEXIBLELEADFINDER_RECORDS_LIMIT_SETTING) : FLEXIBLELEADFINDER_MAX_LEADS, 'number', [
                    'min' => 0,
                ]); ?>
                <span class="tw-block tw-text-sm tw-text-gray-500 tw-mb-2">
                    <?php echo flexibleleadfinder_lang('records-limit-desc'); ?>
                </span>
                
                <?php echo render_select('settings[' . FLEXIBLELEADFINDER_ASSIGNEE_SETTING . ']', $staff_members, ['staffid', 'firstname', 'lastname'], flexibleleadfinder_lang('assignee'), option_exists(FLEXIBLELEADFINDER_ASSIGNEE_SETTING) ? get_option(FLEXIBLELEADFINDER_ASSIGNEE_SETTING) : ''); ?>
                <span class="tw-block tw-text-sm tw-text-gray-500 tw-mb-2">
                    <?php echo flexibleleadfinder_lang('assignee-desc'); ?>
                </span>

                <?php echo render_input('settings[flexibleleadfinder_radius]', flexibleleadfinder_lang('radius'), option_exists('flexibleleadfinder_radius') ? get_option('flexibleleadfinder_radius') : '5000','number'); ?>
                <span class="tw-block tw-text-sm tw-text-gray-500 tw-mb-4">
                    <?php echo flexibleleadfinder_lang('radius-desc'); ?>
                </span>

                <?php echo render_yes_no_option(FLEXIBLELEADFINDER_IMPORT_RESULTS_WITHOUT_EMAIL_SETTING, flexibleleadfinder_lang('import-results-without-email')); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?php echo _l('save'); ?>
                </button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>
    'use strict';
    $(document).ready(function () {
        function reloadPage() {
            let url = `<?php echo flexibleleadfinder_admin_url(); ?>`;

            $.get(url, {},
                function (response, textStatus, jqXHR) {
                    if (response.success) {
                        $('#wrapper').html(response.html)
                    } else {
                        alert_float('danger', response.message)
                    }
                },
                "json"
            );
        }

        $(document).on('click', '.flexlf-delete-search', function (e) {
            e.preventDefault();

            if (confirm_delete()) {
                let id = $(this).data('id');
                let url = `<?php echo flexibleleadfinder_admin_url('delete'); ?>/${id}`;

                $.post(url, {},
                    function (response, textStatus, jqXHR) {
                        if (response.success) {
                            alert_float('success', response.message)
                            reloadPage()
                        } else {
                            alert_float('danger', response.message)
                        }
                    },
                    "json"
                );
            }
            return false;
        })
    });
</script>
</body>

</html>