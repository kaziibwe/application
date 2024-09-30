<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">
    <?php $this->load->view('admin/estimates/estimates_top_stats'); ?>
    <?php if (staff_can('create',  'estimates')) { ?>
    <a href="<?php echo admin_url('estimates/estimate'); ?>" class="btn btn-primary pull-left new new-estimate-btn">
        <i class="fa-regular fa-plus tw-mr-1"></i>
        <?php echo _l('create_new_estimate'); ?>
    </a>
    <?php } ?>
    <a href="<?php echo admin_url('estimates/pipeline/' . $switch_pipeline); ?>"
        class="btn btn-default mleft5 pull-left switch-pipeline hidden-xs" data-toggle="tooltip" data-placement="top"
        data-title="<?php echo _l('switch_to_pipeline'); ?>">
        <i class="fa-solid fa-grip-vertical"></i>
    </a>
    <div class="display-block pull-right tw-space-x-0 sm:tw-space-x-1.5">
        <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs"
            onclick="toggle_small_view('.table-estimates','#estimate'); return false;" data-toggle="tooltip"
            title="<?php echo _l('estimates_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
        <a href="#" class="btn btn-default btn-with-tooltip estimates-total"
            onclick="slideToggle('#stats-top'); init_estimates_total(true); return false;" data-toggle="tooltip"
            title="<?php echo _l('view_stats_tooltip'); ?>"><i class="fa fa-bar-chart"></i></a>
     
          <app-filters
                id="<?php echo $estimates_table->id(); ?>"
                view="<?php echo $estimates_table->viewName(); ?>"
                :rules="extra.estimatesRules || <?php echo app\services\utilities\Js::from($this->input->get('status') ? $estimates_table->findRule('status')->setValue([$this->input->get('status')]) : ($this->input->get('not_sent') ?  $estimates_table->findRule('sent')->setValue("0") : [])); ?>"
                :saved-filters="<?php echo $estimates_table->filtersJs(); ?>"
                :available-rules="<?php echo $estimates_table->rulesJs(); ?>">
            </app-filters>
    </div>
    <div class="clearfix"></div>
    <div class="row tw-mt-2 sm:tw-mt-4">
        <div class="col-md-12" id="small-table">
            <div class="panel_s">
                <div class="panel-body">
                    <!-- if estimateid found in url -->
                    <?php echo form_hidden('estimateid', $estimateid); ?>
                    <?php $this->load->view('admin/estimates/table_html'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-7 small-table-right-col">
            <div id="estimate" class="hide">
            </div>
        </div>
    </div>
</div>