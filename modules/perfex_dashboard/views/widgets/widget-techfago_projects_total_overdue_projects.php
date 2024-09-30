<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Total overdue projects
  Description: Total overdue projects
  
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

  $now = (new Datetime())->format('Y-m-d');

  $sql = "
    SELECT 
      COUNT(*) AS total_rows
    FROM
      " . db_prefix() . "projects TBLProjects
    WHERE
      TBLProjects.deadline < '" . $now . "'
          AND TBLProjects.status NOT IN (4 , 5)
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      AND TBLProjects.project_created >= '" . $widget_req_from . " 00:00:00' AND TBLProjects.project_created <= '" . $widget_req_to . " 23:59:59'
    ";
  }

  return $this->db->query($sql)->result_array();
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-projects-total-overdue-projects" data-widget-id="<?= $widget['id'] ?>">
  <div class="widget-dragger"></div>
  <div class="card-counter danger">
    <i class="fa fa-file-powerpoint-o"></i>
    <span class="count-numbers"><?= $widget_data[0]['total_rows'] ?></span>
    <span class="count-name"><?= _l('overdue_projects') ?></span>
  </div>
</div>