<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Monthly income and expense
  Description: Monthly income and expense
  
*/
?>

<?php

$fn_get_data = function () {
  $widget_req_from = DateTime::createFromFormat('Y-m-d', $this->input->get('period_from'));
  if ($widget_req_from !== false) {
    $widget_req_from = $widget_req_from->format('Y');
  } else {
    $widget_req_from = null;
  }

  $widget_req_to = DateTime::createFromFormat('Y-m-d', $this->input->get('period_to'));
  if ($widget_req_to !== false) {
    $widget_req_to = $widget_req_to->format('Y');
  } else {
    $widget_req_to = null;
  }

  if (isset($widget_req_from) && isset($widget_req_to) && $widget_req_from == $widget_req_to) {
    $current_year = $widget_req_from;
  } else {
    $current_year = (new DateTime())->format('Y');
  }

  $months_text = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December',
  ];

  $data_labels = [];
  $data_income = [];
  $data_expense = [];

  $sql_income = "
    SELECT 
        MONTH(TBLPayments.date) AS month,
        SUM(TBLPayments.amount) AS total_amount
    FROM
        " . db_prefix() . "invoicepaymentrecords TBLPayments
    WHERE
        YEAR(TBLPayments.date) = '" . $current_year . "'
    GROUP BY month
    ORDER BY month ASC
  ";

  $sql_expense = "
    SELECT 
      MONTH(TBLExpenses.date) AS month,
        SUM((TBLExpenses.amount + ((IFNULL(TBLTax.taxrate, 0) / 100) * TBLExpenses.amount) + ((IFNULL(TBLTax2.taxrate, 0) / 100) * TBLExpenses.amount))) AS total_amount
    FROM
        " . db_prefix() . "expenses TBLExpenses
            LEFT JOIN
        " . db_prefix() . "taxes TBLTax ON TBLExpenses.tax = TBLTax.id
            LEFT JOIN
        " . db_prefix() . "taxes TBLTax2 ON TBLExpenses.tax2 = TBLTax2.id
    WHERE YEAR(TBLExpenses.date) = '" . $current_year . "'
    GROUP BY month
    ORDER BY month ASC
  ";

  $data_income_rows = $this->db->query($sql_income)->result_array();
  $data_expense_rows = $this->db->query($sql_expense)->result_array();

  $index_income = 0;
  $index_expense = 0;
  for ($i = 1; $i <= 12; $i++) {
    array_push($data_labels, _l($months_text[$i - 1]));

    if ($index_income < count($data_income_rows) && $data_income_rows[$index_income]['month'] == $i) {
      array_push($data_income, floatval($data_income_rows[$index_income]['total_amount']));
      $index_income++;
    } else {
      array_push($data_income, 0);
    }

    if ($index_expense < count($data_expense_rows) && $data_expense_rows[$index_expense]['month'] == $i) {
      array_push($data_expense, floatval($data_expense_rows[$index_expense]['total_amount']));
      $index_expense++;
    } else {
      array_push($data_expense, 0);
    }
  }

  $chart = [
    'labels'   => $data_labels,
    'datasets' => [
      [
        'label' => _l('income'),
        'backgroundColor' => 'rgba(37,155,35,0.2)',
        'borderColor' => '#84c529',
        'borderWidth' => 1,
        'tension' => false,
        'data' => $data_income,
      ], [
        'label' => _l('expense'),
        'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
        'borderColor' => '#c53da9',
        'borderWidth' => 1,
        'tension' => false,
        'data' => $data_expense,
      ],
    ],
  ];;

  return $chart;
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-finance-monthly-income-and-expense" data-widget-id="<?= $widget['id'] ?>">
  <div class="panel_s">
    <div class="panel-body">
      <div class="widget-dragger"></div>

      <h4 class="pull-left mtop5"><?php echo _l('monthly_income_and_expense'); ?></h4>
      <div class="clearfix"></div>
      <div class="row mtop5">
        <hr class="hr-panel-heading-dashboard">
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="relative max-height-400">
            <canvas id="perfex_dashboard_widget_<?= $widget['id'] ?>_chart_data" class="animated fadeIn" height="400"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  window['perfex_dashboard_widget_<?= $widget['id'] ?>_chart_data'] = <?= json_encode($widget_data); ?>;
</script>