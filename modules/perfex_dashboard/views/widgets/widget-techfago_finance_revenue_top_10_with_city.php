<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Revenue top 10 with city
  Description: Revenue top 10 with city
  
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
    SELECT 
      COUNT(id) AS orders, SUM(TBLInvoices.subtotal) AS total, TBLInvoices.billing_city AS city
    FROM
      " . db_prefix() . "invoices TBLInvoices
    WHERE TBLInvoices.status = 2 
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      AND TBLInvoices.date >= '" . $widget_req_from . "' AND TBLInvoices.date <= '" . $widget_req_to . "' 
    ";
  }
  $sql .= "
    GROUP BY TBLInvoices.billing_city
    ORDER BY total DESC
    LIMIT 0 , 10
  ";

  return $this->db->query($sql )->result_array();
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-revenue-top-10-with-city widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="">
    <div class="panel_s user-data">
      <div class="panel-body perfex-dashboard-panel">
        <div class="widget-dragger"></div>

        <h4 class="pull-left mtop5"><?php echo _l('sale_by_cities'); ?></h4>
        <div class="clearfix"></div>
        <div class="row mtop5">
          <hr class="hr-panel-heading-dashboard">
        </div>

        <table class="table dataTable no-footer dtr-inline">
          <thead>
            <th><?= _l('city') ?></th>
            <th><?= _l('order') ?></th>
            <th><?= _l('revenue') ?></th>
          </thead>
          <tbody>
            <?php if (count($widget_data) > 0) { ?>
              <?php foreach ($widget_data as $row_data) { ?>
                <tr>
                  <td><?= $row_data['city'] ?></td>
                  <td class="order-num"><?= $row_data['orders'] ?></td>
                  <td class="priceable"><?= $row_data['total'] ?></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="3"><?= _l('not_found') ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>