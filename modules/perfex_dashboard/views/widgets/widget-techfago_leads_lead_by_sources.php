<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Lead by sources
  Description: Lead by sources

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
      TBLSources.id AS source_id,
      TBLSources.name AS source_name,
      COUNT(*) AS total_leads
    FROM
    " . db_prefix() . "leads TBLLeads
          INNER JOIN
      " . db_prefix() . "leads_sources TBLSources ON TBLLeads.source = TBLSources.id
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      WHERE
          TBLLeads.dateadded >= '" . $widget_req_from . " 00:00:00'
          AND TBLLeads.dateadded <= '" . $widget_req_to . " 23:59:59'
    ";
  }
  $sql .= "
    GROUP BY source_id , source_name
    ORDER BY total_leads DESC
    LIMIT 0 , 10
  ";
  $data_rows = $this->db->query($sql)->result_array();

  $colors = get_system_favourite_colors();

  $chart = [
    'labels' => [],
    'datasets' => [],
  ];

  $_data = [];
  $_data['data'] = [];
  $_data['backgroundColor'] = [];
  $_data['hoverBackgroundColor'] = [];
  $_data['statusLink'] = [];

  foreach ($data_rows as $data_index => $data_row) {
    array_push($_data['statusLink'], 'javascript:void(0);');
    array_push($chart['labels'], $data_row['source_name']);
    array_push($_data['backgroundColor'], $colors[$data_index % count($colors)]);
    array_push($_data['hoverBackgroundColor'], adjust_color_brightness($colors[$data_index % count($colors)], -20));
    array_push($_data['data'], intval($data_row['total_leads']));
  }

  $chart['datasets'][]           = $_data;

  return $chart;
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-leads-lead-by-sources" data-widget-id="<?= $widget['id'] ?>">
  <div class="panel_s">
    <div class="panel-body">
      <div class="widget-dragger"></div>

      <h4 class="pull-left mtop5"><?php echo _l('lead_by_sources'); ?></h4>
      <div class="clearfix"></div>
      <div class="row mtop5">
        <hr class="hr-panel-heading-dashboard">
      </div>

      <div class="row">
        <div class="col-md-12">
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