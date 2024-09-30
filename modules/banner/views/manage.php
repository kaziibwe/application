<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (has_permission('banner', get_staff_user_id(), 'create')) { ?>
                    <div class="_buttons">
                        <a href="<?php echo admin_url('banner/manage'); ?>"
                            class="btn btn-primary mright5 test pull-left display-block">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('add_banner'); ?>
                        </a>
                    </div>
                    <div class="clearfix"></div>
                <?php } ?>
                <div class="panel_s tw-mt-2 sm:tw-mt-4">
                    <div class="panel-body">
                        <?php
                        $tableColumns = [
                            _l('#'),
                            _l('title'),
                            _l('section'),
                            _l('start_date'),
                            _l('end_date'),
                            _l('status'),
                        ];

if (has_permission('banner', get_staff_user_id(), 'edit') || has_permission('banner', get_staff_user_id(), 'delete')) {
    $tableColumns[] = _l('actions');
}

echo render_datatable($tableColumns, 'banner-details');
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>