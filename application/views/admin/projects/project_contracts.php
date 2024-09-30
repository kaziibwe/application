<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="p_buttons">
    <div id="vueApp" class="tw-inline pull-right tw-ml-0 sm:tw-ml-1.5">
        <app-filters 
                id="<?php echo $contracts_table->id(); ?>" 
                view="<?php echo $contracts_table->viewName(); ?>"
                :saved-filters="<?php echo $contracts_table->filtersJs(); ?>"
                :available-rules="<?php echo $contracts_table->rulesJs(); ?>">
        </app-filters>
    </div>
</div>
<div class="clearfix"></div>
<div class="panel_s panel-table-full tw-mt-4">
    <div class="panel-body">
        <div class="project_contracts">
            <?php 
                $this->load->view('admin/contracts/table_html', [
                    'table_id' => 'project_contracts'
                ]); 
            ?>
        </div>
    </div>
</div>