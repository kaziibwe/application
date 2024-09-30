<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="panel_s">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <h4 class="no-margin"><?php echo _l('whatsapp_template_details'); ?></h4>
          </div>
          <div class="col-md-6 text-right">
            <button class="btn btn-info load_data">Load Data</button>
          </div>
        </div>
        <div class="clearfix"></div>
        <hr class="hr-panel-heading" />
        <div class="row">
          <div class="col-md-12">
            <?php render_datatable([
              _l('the_number_sign'),
              _l('template_name'),
              _l('language'),
              _l('category'),
              _l('status'),
              _l('body_data'),
            ], 'template_list', ['table-condensed']); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>

<script type="text/javascript">
  initDataTable('.table-template_list', admin_url + 'whatsapp_api/datatable', undefined, undefined, undefined, undefined);
  $(document).on('click', '.load_data', function(event) {
    event.preventDefault();
    $.ajax({
      url: admin_url + "whatsapp_api/get_business_information",
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        alert_float(response.type, response.message, 5000);
        $('.table-template_list').DataTable().ajax.reload();
      }
    });
  });
</script>