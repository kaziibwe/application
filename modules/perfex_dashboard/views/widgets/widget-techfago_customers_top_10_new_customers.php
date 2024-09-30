<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: New Customers
  Description: New Customers
  
*/
?>

<?php
$fn_get_data = function () {
  $widget_req_from = DateTime::createFromFormat('Y-m-d', $this->input->get('period_from'));
  if ($widget_req_from !== false) {
    $widget_req_from = $widget_req_from->format('Y-m-d');
  } else {
    $widget_req_from = null;
  }

  $widget_req_to = DateTime::createFromFormat('Y-m-d', $this->input->get('period_to'));
  if ($widget_req_to !== false) {
    $widget_req_to = $widget_req_to->format('Y-m-d');
  } else {
    $widget_req_to = null;
  }

  $sql = "
    SELECT company, datecreated,userid FROM " . db_prefix() . "clients 
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      WHERE datecreated >= '" . $widget_req_from . " 00:00:00' AND datecreated <= '" . $widget_req_to . " 23:23:59' 
    ";
  }
  $sql .= "
    ORDER BY datecreated DESC LIMIT 0 , 10
  ";
  
  return $this->db->query($sql )->result_array();
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-new-customers widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="">
    <div class="panel_s">
      <div class="panel-body">
        <div class="widget-dragger"></div>

        <h4 class="pull-left mtop5"><?php echo _l('top_10_new_customers'); ?></h4>
        <a href="<?php echo admin_url('clients'); ?>" class="pull-right mtop5"><?php echo _l('home_stats_full_report'); ?></a>
        <div class="clearfix"></div>
        <div class="row mtop5">
          <hr class="hr-panel-heading-dashboard">
        </div>

        <table class="table dataTable no-footer dtr-inline">
          <thead>
            <th><?= _l('company') ?></th>
            <th><?= _l('created_at') ?></th>
          </thead>
          <tbody>
            <?php if (count($widget_data) > 0) { ?>
              <?php foreach ($widget_data as $widget_row) { ?>
                <tr>
                  <td><a href="<?= admin_url('clients/client/' . $widget_row['userid']) ?>"><?= $widget_row['company'] ?></a></td>
                  <td><?= time_ago($widget_row['datecreated']) ?></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="2"><?= _l('not_found') ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>