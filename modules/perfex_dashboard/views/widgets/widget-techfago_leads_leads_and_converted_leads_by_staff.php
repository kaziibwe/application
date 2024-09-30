<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Leads and converted leads by staff
  Description: Leads and converted leads by staff
  
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
  $data_converted_leads = [];

  $sql  = "
    SELECT 
      TBLTotalLeads.staff_id AS staff_id,
      TBLTotalLeads.staff_fullname AS staff_fullname,
      TBLTotalLeads.total_leads AS total_leads,
      TBLTotalConvertedLeads.total_converted_leads AS total_converted_leads,
      (TBLTotalLeads.total_leads + TBLTotalConvertedLeads.total_converted_leads) AS total_rows
    FROM
      (SELECT 
          TBLStaff.staffid AS staff_id,
          CONCAT(TBLStaff.firstname, ' ', TBLStaff.lastname) AS staff_fullname,
              SUM(CASE
                  WHEN
                      (TBLLeads.id IS NOT NULL ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
        AND TBLLeads.dateadded >= '" . $widget_req_from . " 00:00:00'
        AND TBLLeads.dateadded <= '" . $widget_req_to . " 23:59:59'
    ";
  }
  $sql .= " )
                 THEN
                      1
                  ELSE 0
              END) AS total_leads
      FROM
      " . db_prefix() . "staff TBLStaff
      LEFT JOIN " . db_prefix() . "leads TBLLeads ON TBLStaff.staffid = TBLLeads.assigned
      GROUP BY staff_id, staff_fullname) TBLTotalLeads
          INNER JOIN
      (SELECT 
          TBLStaff.staffid AS staff_id,
              SUM(CASE
                  WHEN
                      (TBLLeads.status = 1 ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
        AND TBLLeads.date_converted >= '" . $widget_req_from . " 00:00:00'
        AND TBLLeads.date_converted <= '" . $widget_req_to . " 23:59:59'
    ";
  }
  $sql .= "          )
                  THEN
                      1
                  ELSE 0
              END) AS total_converted_leads
      FROM
      " . db_prefix() . "staff TBLStaff
      LEFT JOIN " . db_prefix() . "leads TBLLeads ON TBLLeads.assigned = TBLStaff.staffid
      GROUP BY staff_id) TBLTotalConvertedLeads ON TBLTotalLeads.staff_id = TBLTotalConvertedLeads.staff_id
      WHERE (TBLTotalLeads.total_leads + TBLTotalConvertedLeads.total_converted_leads) > 0
    ORDER BY total_rows DESC
    LIMIT 0 , 10
  ";

  $data_rows = $this->db->query($sql)->result_array();

  foreach ($data_rows as $data_rows) {
    array_push($data_labels, $data_rows['staff_fullname']);
    array_push($data_leads, intval($data_rows['total_leads']));
    array_push($data_converted_leads, intval($data_rows['total_converted_leads']));
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
      ], [
        'label' => _l('converted_leads'),
        'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
        'borderColor' => '#c53da9',
        'borderWidth' => 1,
        'tension' => false,
        'data' => $data_converted_leads,
      ],
    ],
  ];;

  return $chart;
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-leads-and-converted-leads-by-staff" data-widget-id="<?= $widget['id'] ?>">
  <div class="panel_s">
    <div class="panel-body">
      <div class="widget-dragger"></div>

      <h4 class="pull-left mtop5"><?php echo _l('leads_and_converted_leads_by_staff'); ?></h4>
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