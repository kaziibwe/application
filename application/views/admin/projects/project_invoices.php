<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="vueApp">
    <?php include_once(APPPATH . 'views/admin/invoices/invoices_top_stats.php'); ?>
    <div class="panel_s">
        <div class="panel-body">
            <div class="project_invoices">
                <?php include_once(APPPATH.'views/admin/invoices/filter_params.php'); ?>
                <?php $this->load->view('admin/invoices/list_template', [
                    'table'=>$invoices_table,
                    'table_id'=> $invoices_table->id(),
                ]); ?>
            </div>
        </div>
    </div>
</div>