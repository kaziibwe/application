<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Total income
  Description: Total income
  
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
      IFNULL(SUM(TBLPayments.amount), 0) AS total_amount
    FROM
      " . db_prefix() . "invoicepaymentrecords TBLPayments
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      WHERE
        TBLPayments.date >= '" . $widget_req_from . "'
            AND TBLPayments.date <= '" . $widget_req_to . "'
    ";
  }

  return $this->db->query($sql)->result_array();
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-finance-total-income" data-widget-id="<?= $widget['id'] ?>">
  <div class="widget-dragger"></div>
  <div class="card-counter primary">
    <i class="fa fa-money"></i>
    <span class="count-numbers priceable"><?= $widget_data[0]['total_amount'] ?></span>
    <span class="count-name"><?= _l('total_income') ?></span>
  </div>
</div>