<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $delivery_note_statuses = $this->delivery_notes_model->get_statuses(); ?>
<div id="vueApp">
    <div class="project_delivery_notes">


        <?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
        <div class="col-md-12">
            <?php $this->load->view('delivery_notes/admin/delivery_notes/delivery_notes_top_stats', ['delivery_note_statuses' => $delivery_note_statuses]);
                    ?>

            <div class="display-block text-right">
                <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip"
                    data-title="<?php echo _l('filter_by'); ?>">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu width300">
                        <li>
                            <a href="#" data-cview="all"
                                onclick="dt_custom_view('','.table-delivery_notes',''); return false;">
                                <?php echo _l('delivery_notes_list_all'); ?>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li class="<?php if ($this->input->get('filter') == 'not_sent') {
                                                echo 'active';
                                            } ?>">
                            <a href="#" data-cview="not_sent"
                                onclick="dt_custom_view('not_sent','.table-delivery_notes','not_sent'); return false;">
                                <?php echo _l('not_sent_indicator'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-cview="invoiced"
                                onclick="dt_custom_view('invoiced','.table-delivery_notes','invoiced'); return false;">
                                <?php echo _l('delivery_note_invoiced'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-cview="not_invoiced"
                                onclick="dt_custom_view('not_invoiced','.table-delivery_notes','not_invoiced'); return false;"><?php echo _l('delivery_notes_not_invoiced'); ?></a>
                        </li>
                        <li class="divider"></li>
                        <?php foreach ($delivery_note_statuses as $status) { ?>
                        <li class="<?php if ($this->input->get('status') == $status) {
                                                    echo 'active';
                                                } ?>">
                            <a href="#" data-cview="delivery_notes_<?php echo $status; ?>"
                                onclick="dt_custom_view('delivery_notes_<?php echo $status; ?>','.table-delivery_notes','delivery_notes_<?php echo $status; ?>'); return false;">
                                <?php echo format_delivery_note_status($status, '', false); ?>
                            </a>
                        </li>
                        <?php } ?>
                        <div class="clearfix"></div>

                    </ul>
                </div>
                <a href="#" class="btn btn-default btn-with-tooltip delivery_notes-total"
                    onclick="slideToggle('#stats-top'); return false;" data-toggle="tooltip"
                    title="<?php echo _l('view_stats_tooltip'); ?>"><i class="fa fa-bar-chart"></i></a>
            </div>
            <div class="row tw-mt-2 sm:tw-mt-4">
                <div class="col-md-12" id="small-table">
                    <div class="panel_s">
                        <div class="panel-body">
                            <?php $this->load->view('delivery_notes/admin/delivery_notes/table_html'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 small-table-right-col">
                    <div id="delivery_note" class="hide">
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>