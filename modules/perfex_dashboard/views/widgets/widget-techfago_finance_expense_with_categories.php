<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Expense with categories
  Description: Expense with categories
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
      TBLCates.id AS category_id,
      TBLCates.name AS category_name,
      SUM(IFNULL(TBLExpenses.amount, 0) + (IFNULL(TBLExpenses.amount, 0) * (IFNULL(TBLTax.taxrate, 0) / 100)) + (IFNULL(TBLExpenses.amount, 0) * (IFNULL(TBLTax2.taxrate, 0) / 100))) AS total_amount
    FROM
      " . db_prefix() . "expenses_categories TBLCates
          LEFT JOIN
      " . db_prefix() . "expenses TBLExpenses ON TBLCates.id = TBLExpenses.category
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
  $sql .= "
    GROUP BY category_id , category_name
    HAVING total_amount > 0
    ORDER BY total_amount DESC
    LIMIT 0 , 10
  ";

  $result_rows = $this->db->query($sql)->result_array();

  $colors   = get_system_favourite_colors();

  $chart = [
    'labels' => [],
    'datasets' => [],
  ];

  $_data = [];
  $_data['data'] = [];
  $_data['backgroundColor'] = [];
  $_data['hoverBackgroundColor'] = [];
  $_data['statusLink'] = [];

  foreach ($result_rows as $result_num => $result_row) {
    array_push($_data['statusLink'], 'javascript:void(0);');
    array_push($chart['labels'], $result_row['category_name']);
    array_push($_data['backgroundColor'], $colors[$result_num % count($colors)]);
    array_push($_data['hoverBackgroundColor'], adjust_color_brightness($colors[$result_num % count($colors)], -20));
    array_push($_data['data'], intval($result_row['total_amount']));
  }

  $chart['datasets'][] = $_data;

  return $chart;
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-finance-expense-with-categories widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="row">
    <div class="col-md-12">
      <div class="panel_s">
        <div class="panel-body padding-10">
          <div class="widget-dragger"></div>

          <h4 class="pull-left mtop5"><?php echo _l('expense_with_categories'); ?></h4>
          <div class="clearfix"></div>
          <div class="row mtop5">
            <hr class="hr-panel-heading-dashboard">
          </div>

          <?php if (count($widget_data['datasets'][0]['data']) > 0) { ?>
            <div class="relative height-250">
              <canvas class="chart" height="250" id="chart-<?= $widget['id'] ?>"></canvas>
            </div>
          <?php } else { ?>
            <p class="text-center"><?= _l('not_found') ?></p>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  window['perfex_dashboard_widget_<?= $widget['id'] ?>_chart_data'] = <?= json_encode($widget_data) ?>;
</script>