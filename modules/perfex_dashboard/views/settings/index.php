<?php 
  defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="panel_s">
      <div class="panel-body">
        <h4 class=""><?= _l('settings_dashboard') ?></h4>
        <hr>
            <div class="">
                <h4>If you want to replace default dashboard of app with "perfex_dashboard"</h4>
                <p>Please add the line below at the end of the file <strong><?php echo APPPATH.'config/my_routes.php'; ?></strong><small> (Create a file my_routes.php if the file is not found)<small></p>
                <p>
                  <pre>$route['admin'] = 'admin/perfex_dashboard/dashboards/my_dashboard';</pre>
                </p>
            </div>
            <hr>
      </div>
    </div>

  </div>
</div>

<?php init_tail(); ?>
</body>

</html>