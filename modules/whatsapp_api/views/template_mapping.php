<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="panel_s">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <h4 class="no-margin"><?php echo _l('whatsapp_template_mapping'); ?></h4>
          </div>
          <?php
          if (has_permission('whatsapp_api', '', 'template_mapping_add')) {
          ?>
            <div class="col-md-6 text-right">
              <a href="<?php echo admin_url('whatsapp_api/template_mapping/add'); ?>" class="btn btn-info load_data"><?php echo _l('add'); ?></a>
            </div>
          <?php
          }
          ?>

        </div>
        <div class="clearfix"></div>
        <hr class="hr-panel-heading" />
        <div class="row">
          <div class="col-md-12">
            <?php render_datatable([
              _l('template_name'),
              _l('category'),
              _l('send_to'),
              _l('active'),
              _l('run_in_debug_mode'),
              _l('options'),
            ], 'template_mapping_table', ['table-condensed']); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
  initDataTable('.table-template_mapping_table', admin_url + "whatsapp_api/template_mapping/template_mapping_table", undefined, undefined);
</script>