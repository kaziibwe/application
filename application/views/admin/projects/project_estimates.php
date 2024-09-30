<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="vueApp">
    <?php include_once(APPPATH . 'views/admin/estimates/estimates_top_stats.php'); ?>
    <div class="panel_s panel-table-full ">
        <div class="panel-body">
            <div class="project_estimates">
                <?php $this->load->view('admin/estimates/list_template', [
                    'table'=>$estimates_table,
                    'table_id'=> $estimates_table->id(),
                ]); ?>
            </div>
        </div>
    </div>
</div>