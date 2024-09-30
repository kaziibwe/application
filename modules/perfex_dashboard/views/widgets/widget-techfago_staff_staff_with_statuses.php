<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Staff with statuses
  Description: Staff with statuses
  
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
      TBLStaff.staffid AS staff_id,
      CONCAT(TBLStaff.firstname,
              ' ',
              TBLStaff.lastname) AS staff_fullname,
      SUM(CASE
          WHEN (TBLTasks.id IS NOT NULL) THEN 1
          ELSE 0
      END) AS total_tasks,
      SUM(CASE
          WHEN
              (TBLTasks.id IS NOT NULL
                  AND TBLTasks.status = 1)
          THEN
              1
          ELSE 0
      END) AS not_started_tasks,
      SUM(CASE
          WHEN
              (TBLTasks.id IS NOT NULL
                  AND TBLTasks.status = 2)
          THEN
              1
          ELSE 0
      END) AS awaiting_feedback_tasks,
      SUM(CASE
          WHEN
              (TBLTasks.id IS NOT NULL
                  AND TBLTasks.status = 3)
          THEN
              1
          ELSE 0
      END) AS testing_tasks,
      SUM(CASE
          WHEN
              (TBLTasks.id IS NOT NULL
                  AND TBLTasks.status = 4)
          THEN
              1
          ELSE 0
      END) AS in_progress_tasks,
      SUM(CASE
          WHEN
              (TBLTasks.id IS NOT NULL
                  AND TBLTasks.status = 5)
          THEN
              1
          ELSE 0
      END) AS complete_tasks
    FROM
    " . db_prefix() . "staff TBLStaff
          LEFT JOIN
          " . db_prefix() . "task_assigned TBLAssigns ON TBLStaff.staffid = TBLAssigns.staffid
          LEFT JOIN
          " . db_prefix() . "tasks TBLTasks ON TBLAssigns.taskid = TBLTasks.id
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      WHERE
        TBLTasks.dateadded >= '" . $widget_req_from . " 00:00:00'
            AND TBLTasks.dateadded <= '" . $widget_req_to . " 23:59:59'
    ";
  }
  $sql .= "
    GROUP BY staff_id , staff_fullname
    ORDER BY total_tasks DESC
    LIMIT 0 , 10
  ";

  return $this->db->query($sql)->result_array();
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-staff-staff-with-statuses widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="">
    <div class="panel_s">
      <div class="panel-body">
        <div class="widget-dragger"></div>

        <h4 class="pull-left mtop5"><?php echo _l('staff_with_statuses'); ?></h4>
        <div class="clearfix"></div>
        <div class="row mtop5">
          <hr class="hr-panel-heading-dashboard">
        </div>

        <table class="table dataTable no-footer dtr-inline">
          <thead>
            <th><?= _l('staff') ?></th>
            <th><?= _l('total_tasks') ?></th>
            <th><?= _l('task_status_1') ?></th>
            <th><?= _l('task_status_2') ?></th>
            <th><?= _l('task_status_3') ?></th>
            <th><?= _l('task_status_4') ?></th>
            <th><?= _l('task_status_5') ?></th>
          </thead>
          <tbody>
            <?php if (count($widget_data) > 0) { ?>
              <?php foreach ($widget_data as $widget_row) { ?>
                <tr>
                  <td><a href="<?= admin_url('staff/member/' . $widget_row['staff_id']) ?>"><?= $widget_row['staff_fullname'] ?></a></td>
                  <td><?= $widget_row['total_tasks'] ?></td>
                  <td><?= $widget_row['not_started_tasks'] ?></td>
                  <td><?= $widget_row['awaiting_feedback_tasks'] ?></td>
                  <td><?= $widget_row['testing_tasks'] ?></td>
                  <td><?= $widget_row['in_progress_tasks'] ?></td>
                  <td><?= $widget_row['complete_tasks'] ?></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="7"><?= _l('not_found') ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>