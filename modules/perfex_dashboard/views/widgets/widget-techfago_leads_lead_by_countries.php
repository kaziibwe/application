<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Lead by countries
  Description: Lead by countries
  
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

  $data_labels = [];
  $data_leads = [];

  $sql  = "
    SELECT 
      TBLCountries.country_id AS country_id,
      TBLCountries.short_name AS country_name,
      COUNT(*) AS total_leads
    FROM
      tblleads TBLLeads
          INNER JOIN
      tblcountries TBLCountries ON TBLLeads.country = TBLCountries.country_id
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
    WHERE
        TBLLeads.dateadded >= '" . $widget_req_from . " 00:00:00'
        AND TBLLeads.dateadded <= '" . $widget_req_to . " 23:59:59'
    ";
  }
  $sql .= "
    GROUP BY country_id , country_name
    ORDER BY total_leads DESC
    LIMIT 0 , 10
  ";


  $data_rows = $this->db->query($sql)->result_array();

  foreach ($data_rows as $data_rows) {
    array_push($data_labels, $data_rows['country_name']);
    array_push($data_leads, intval($data_rows['total_leads']));
  }

  $chart = [
    'labels'   => $data_labels,
    'datasets' => [
      [
        'label' => _l('leads'),
        'backgroundColor' => 'rgba(37,155,35,0.2)',
        'borderColor' => '#84c529',
        'borderWidth' => 1,
        'tension' => false,
        'data' => $data_leads,
      ],
    ],
  ];;

  return $chart;
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-lead-by-countries" data-widget-id="<?= $widget['id'] ?>">
  <div class="panel_s">
    <div class="panel-body">
      <div class="widget-dragger"></div>

      <h4 class="pull-left mtop5"><?php echo _l('lead_by_countries'); ?></h4>
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