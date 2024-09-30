<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Project to deadline this month.
  Description: Project to deadline this month.
  
*/
?>

<?php
$fn_get_statuses = function () {
  $statuses = hooks()->apply_filters('before_get_project_statuses', [
    [
      'id'             => 1,
      'color'          => '#989898',
      'name'           => _l('project_status_1'),
      'order'          => 1,
      'filter_default' => true,
    ],
    [
      'id'             => 2,
      'color'          => '#03a9f4',
      'name'           => _l('project_status_2'),
      'order'          => 2,
      'filter_default' => true,
    ],
    [
      'id'             => 3,
      'color'          => '#ff6f00',
      'name'           => _l('project_status_3'),
      'order'          => 3,
      'filter_default' => true,
    ],
    [
      'id'             => 4,
      'color'          => '#84c529',
      'name'           => _l('project_status_4'),
      'order'          => 100,
      'filter_default' => false,
    ],
    [
      'id'             => 5,
      'color'          => '#989898',
      'name'           => _l('project_status_5'),
      'order'          => 4,
      'filter_default' => false,
    ],
  ]);

  usort($statuses, function ($a, $b) {
    return $a['order'] - $b['order'];
  });

  return $statuses;
};

$fn_get_data = function () use ($fn_get_statuses) {
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

  $statuses = $fn_get_statuses();
  $sql  = "
    SELECT 
      TBLProjects.id,
      TBLProjects.name,
      TBLProjects.deadline,
      CASE
  ";
  foreach ($statuses as $status) {
    $sql  .= "
      WHEN TBLProjects.status = " . $status['id'] . " THEN '" . $status['name'] . "'
    ";
  }
  $sql  .= " 
        ELSE '" . _l('unknown') . "'
      END AS status
    FROM " . db_prefix() . "projects TBLProjects
    WHERE TBLProjects.status NOT IN (5)
  ";
  if (isset($widget_req_from) && isset($widget_req_to)) {
    $sql .= "
      AND TBLProjects.deadline >= '" . $widget_req_from . "' AND TBLProjects.deadline <= '" . $widget_req_to . "'
    ";
  }
  $sql .= "
    ORDER BY TBLProjects.deadline ASC
    LIMIT 0 , 10
  ";

  return $this->db->query($sql )->result_array();
};

$widget_data = $fn_get_data();

?>

  <div class="widget widget-monthly-deadline-projects widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
    <div class="">
      <div class="panel_s user-data">
        <div class="panel-body perfex-dashboard-panel">
          <div class="widget-dragger"></div>

          <h4 class="pull-left mtop5"><?php echo _l('project_to_deadline_this_month'); ?></h4>
          <div class="clearfix"></div>
          <div class="row mtop5">
            <hr class="hr-panel-heading-dashboard">
          </div>

          <table class="table dataTable no-footer dtr-inline">
            <thead>
              <th><?= _l('name') ?></th>
              <th><?= _l('status') ?></th>
              <th><?= _l('deadline') ?></th>
            </thead>
            <tbody>
              <?php if (count($widget_data) > 0) { ?>
                <?php foreach ($widget_data as $row_data) { ?>
                  <tr>
                    <td><a href="<?= admin_url('projects/view/' . $row_data['id']) ?>"><?= $row_data['name'] ?></a></td>
                    <td><?= $row_data['status'] ?></td>
                    <td><?= $row_data['deadline'] ?></td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="3"><?= _l('not_found') ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>