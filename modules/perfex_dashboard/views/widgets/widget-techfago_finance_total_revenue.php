<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Total Revenue
  Description: Total Revenue
  
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
    SELECT IFNULL(SUM(subtotal), 0) AS total_revenue FROM " . db_prefix() . "invoices WHERE status = 2 
  ";

  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      AND date >='" . $widget_req_from . "' AND date <= '" . $widget_req_to . "' 
    ";
  }

  return $this->db->query($sql )->result_array();
};

$widget_data = $fn_get_data();
?>

  <div class="widget widget-total-revenue widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
    <div class="">
      <div class="panel_s user-data">
        <div class="panel-body perfex-dashboard-panel">
          <div class="widget-dragger"></div>

          <h4 class="pull-left mtop5"><?php echo _l('total_revenue'); ?></h4>
          <div class="clearfix"></div>
          <div class="row mtop5">
            <hr class="hr-panel-heading-dashboard">
          </div>

          <div class="revenue-labels">
            <div>
              <strong class="text-info priceable"><?= $widget_data[0]['total_revenue'] ?></strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>