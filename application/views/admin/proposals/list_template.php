<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="_filters _hidden_inputs">
    <?php
        if (isset($project)) {
            echo form_hidden('project_id', $project->id);
        }
    ?>
</div>
<div class="col-md-12">
    <div class="tw-mb-2 sm:tw-mb-4">
        <div class="_buttons">
            <?php if (staff_can('create',  'proposals')) { ?>
            <a href="<?php echo admin_url('proposals/proposal'); ?>"
                class="btn btn-primary pull-left display-block new-proposal-btn">
                <i class="fa-regular fa-plus tw-mr-1"></i>
                <?php echo _l('new_proposal'); ?>
            </a>
            <?php } ?>
            <a href="<?php echo admin_url('proposals/pipeline/' . $switch_pipeline); ?>"
                class="btn btn-default mleft5 pull-left switch-pipeline hidden-xs" data-toggle="tooltip"
                data-placement="top" data-title="<?php echo _l('switch_to_pipeline'); ?>">
                <i class="fa-solid fa-grip-vertical"></i>
            </a>
            <div class="pull-right">
                <div id="vueApp" class="tw-inline">
                    <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs tw-mr-0 sm:tw-mr-1.5"
                    onclick="toggle_small_view('.table-proposals','#proposal'); return false;" data-toggle="tooltip"
                    title="<?php echo _l('invoices_toggle_table_tooltip'); ?>">
                        <i class="fa fa-angle-double-left"></i>
                    </a>
                    <app-filters
                        id="<?php echo $table->id(); ?>"
                        view="<?php echo $table->viewName(); ?>"
                        :rules="<?php echo app\services\utilities\Js::from($this->input->get('status') ? $table->findRule('status')->setValue([(int) $this->input->get('status')]) : []); ?>"
                        :saved-filters="<?php echo $table->filtersJs(); ?>"
                        :available-rules="<?php echo $table->rulesJs(); ?>">
                    </app-filters>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <div class="col-md-12" id="small-table">
            <div class="panel_s">
                <div class="panel-body">
                    <!-- if invoiceid found in url -->
                    <?php echo form_hidden('proposal_id', $proposal_id); ?>
                    <?php
                    $table_data = [
                        _l('proposal') . ' #',
                        _l('proposal_subject'),
                        _l('proposal_to'),
                        _l('proposal_total'),
                        _l('proposal_date'),
                        _l('proposal_open_till'),
                        ];
                    if (!isset($project)) {
                        $table_data[] = _l('project');
                    }
                    $table_data = array_merge($table_data, [
                        _l('tags'),
                        _l('proposal_date_created'),
                        _l('proposal_status'),
                    ]);

                    $custom_fields = get_custom_fields('proposal', ['show_on_table' => 1]);
                    foreach ($custom_fields as $field) {
                        array_push($table_data, [
                         'name'     => $field['name'],
                         'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
                     ]);
                    }

                    $table_data = hooks()->apply_filters('proposals_table_columns', $table_data);
                    render_datatable($table_data, isset($class) ? $class : 'proposals', [], [
                        'data-last-order-identifier' => 'proposals',
                        'data-default-order'         => get_table_last_order('proposals'),
                        'id'=>$table_id ?? 'proposals'
                    ]);
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-7 small-table-right-col">
            <div id="proposal" class="hide">
            </div>
        </div>
    </div>
</div>