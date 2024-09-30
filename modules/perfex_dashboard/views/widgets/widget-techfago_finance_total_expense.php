<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Total expense
  Description: Total expense
  
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
    IFNULL(SUM((TBLExpenses.amount + ((IFNULL(TBLTax.taxrate, 0) / 100) * TBLExpenses.amount) + ((IFNULL(TBLTax2.taxrate, 0) / 100) * TBLExpenses.amount))), 0) AS total_amount
    FROM
      " . db_prefix() . "expenses TBLExpenses
          LEFT JOIN
          " . db_prefix() . "taxes TBLTax ON TBLExpenses.tax = TBLTax.id
          LEFT JOIN
          " . db_prefix() . "taxes TBLTax2 ON TBLExpenses.tax2 = TBLTax2.id
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      WHERE
        TBLExpenses.date >= '" . $widget_req_from . "'
            AND TBLExpenses.date <= '" . $widget_req_to . "'
    ";
  }

  return $this->db->query($sql)->result_array();
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-finance-total-expense" data-widget-id="<?= $widget['id'] ?>">
  <div class="widget-dragger"></div>
  <div class="card-counter danger">
    <i class="fa fa-money"></i>
    <span class="count-numbers priceable"><?= $widget_data[0]['total_amount'] ?></span>
    <span class="count-name"><?= _l('total_expense') ?></span>
  </div>
</div>