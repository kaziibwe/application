<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <div class="_buttons">
                        <?php if (staff_can('create',  'credit_notes')) { ?>
                        <a href="<?php echo admin_url('credit_notes/credit_note'); ?>"
                            class="btn btn-primary pull-left display-block">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('new_credit_note'); ?>
                        </a>
                        <?php } ?>
                        <div class="display-block pull-right">
                        <div id="vueApp" class="tw-inline pull-right tw-ml-0 sm:tw-ml-1.5">
                            <app-filters 
                                id="<?php echo $table->id(); ?>" 
                                view="<?php echo $table->viewName(); ?>"
                                :saved-filters="<?php echo $table->filtersJs(); ?>"
                                :available-rules="<?php echo $table->rulesJs(); ?>">
                            </app-filters>
                        </div>
                            <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs"
                                onclick="toggle_small_view('.table-credit-notes','#credit_note'); return false;"
                                data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i
                                    class="fa fa-angle-double-left"></i></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" id="small-table">
                        <div class="panel_s">
                            <div class="panel-body panel-table-full">
                                <!-- if credit not id found in url -->
                                <?php echo form_hidden('credit_note_id', $credit_note_id); ?>
                                <?php $this->load->view('admin/credit_notes/table_html'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 small-table-right-col">
                        <div id="credit_note" class="hide">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>
var hidden_columns = [4, 5, 6, 7];
</script>
<?php init_tail(); ?>
<script>
$(function() {
    initDataTable('.table-credit-notes', admin_url + 'credit_notes/table', ['undefined'], ['undefined'],
        {}, [
            [1, 'desc'],
            [0, 'desc']
        ]);
    init_credit_note();
});
</script>
</body>

</html>