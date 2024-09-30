<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Staff with roles
  Description: Staff with roles
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
      TBLRoles.roleid AS role_id,
      TBLRoles.name AS role_name,
      SUM(CASE
          WHEN (TBLStaff.staffid IS NOT NULL) THEN 1
          ELSE 0
      END) AS total_staff
    FROM
      " . db_prefix() . "roles TBLRoles
          LEFT JOIN
          " . db_prefix() . "staff TBLStaff ON TBLRoles.roleid = TBLStaff.role
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      WHERE
        TBLStaff.datecreated >= '" . $widget_req_from . " 00:00:00'
            AND TBLStaff.datecreated <= '" . $widget_req_to . " 23:59:59'      
    ";
  }
  $sql .= "
    GROUP BY role_id , role_name
    ORDER BY total_staff DESC
    LIMIT 0 , 10
  ";

  $resultSet = $this->db->query($sql);
  $resultArray = $resultSet->result_array();

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

  foreach ($resultArray as $result_num => $result_row) {
    if (intval($result_row['total_staff']) <= 0) {
      continue;
    }
    array_push($chart['labels'], $result_row['role_name']);
    array_push($_data['backgroundColor'], $colors[$result_num % count($colors)]);
    array_push($_data['hoverBackgroundColor'], adjust_color_brightness($colors[$result_num % count($colors)], -20));
    array_push($_data['data'], intval($result_row['total_staff']));
  }

  $chart['datasets'][] = $_data;

  return $chart;
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-staff-staff-with-roles widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="row">
    <div class="col-md-12">
      <div class="panel_s">
        <div class="panel-body padding-10">
          <div class="widget-dragger"></div>

          <h4 class="pull-left mtop5"><?php echo _l('staff_with_roles'); ?></h4>
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