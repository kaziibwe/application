<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Projects Activity
  Description: Projects Activity
*/
?>

<?php
$fn_get_data = function () {
  $id = '';
  $limit = hooks()->apply_filters('projects_activity_dashboard_limit', 20);
  $only_project_members_activity = false;
  if (!is_client_logged_in()) {
    $has_permission = has_permission('projects', '', 'view');
    if (!$has_permission) {
      $this->db->where('project_id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
    }
  }
  if (is_client_logged_in()) {
    $this->db->where('visible_to_customer', 1);
  }
  if (is_numeric($id)) {
    $this->db->where('project_id', $id);
  }
  if (is_numeric($limit)) {
    $this->db->limit($limit);
  }
  $this->db->order_by('dateadded', 'desc');
  $activities = $this->db->get(db_prefix() . 'project_activity')->result_array();
  $i          = 0;
  foreach ($activities as $activity) {
    $seconds          = get_string_between($activity['additional_data'], '<seconds>', '</seconds>');
    $other_lang_keys  = get_string_between($activity['additional_data'], '<lang>', '</lang>');
    $_additional_data = $activity['additional_data'];

    if ($seconds != '') {
      $_additional_data = str_replace('<seconds>' . $seconds . '</seconds>', seconds_to_time_format($seconds), $_additional_data);
    }

    if ($other_lang_keys != '') {
      $_additional_data = str_replace('<lang>' . $other_lang_keys . '</lang>', _l($other_lang_keys), $_additional_data);
    }

    if (strpos($_additional_data, 'project_status_') !== false) {
      $_additional_data = get_project_status_by_id(strafter($_additional_data, 'project_status_'));

      if (isset($_additional_data['name'])) {
        $_additional_data = $_additional_data['name'];
      }
    }

    $activities[$i]['description']     = _l($activities[$i]['description_key']);
    $activities[$i]['additional_data'] = $_additional_data;
    $activities[$i]['project_name']    = get_project_name_by_id($activity['project_id']);
    unset($activities[$i]['description_key']);
    $i++;
  }
  return $activities;
};
$widget_data = $fn_get_data();
?>

<div class="widget widget-projects-activity widget-<?= $widget['id'] ?> <?php if (count($widget_data) == 0) {
                                                                          echo ' hide';
                                                                        } ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="panel_s projects-activity">
    <div class="panel-body padding-10">
      <div class="widget-dragger"></div>
      <p class="padding-5"><?php echo _l('home_project_activity'); ?></p>
      <hr class="hr-panel-heading-dashboard">
      <div class="activity-feed">
        <?php
        foreach ($widget_data as $activity) {
          $name = $activity['fullname'];
          if ($activity['staff_id'] != 0) {
            $href = admin_url('profile/' . $activity['staff_id']);
          } else if ($activity['contact_id'] != 0) {
            $name = '<span class="label label-info inline-block mright5">' . _l('is_customer_indicator') . '</span> - ' . $name;
            $href = admin_url('clients/client/' . get_user_id_by_contact_id($activity['contact_id']) . '?contactid=' . $activity['contact_id']);
          } else {
            $href = '';
            $name = '[CRON]';
          }
          ?>
          <div class="feed-item">
            <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($activity['dateadded']); ?>">
                <?php echo time_ago($activity['dateadded']); ?>
              </span>
            </div>
            <div class="text">
              <p class="bold no-mbot">
                <?php if ($href != '') { ?>
                  <a href="<?php echo $href; ?>"><?php echo $name; ?></a> -
                <?php } else {
                    echo $name;
                  }; ?>
                <?php echo $activity['description']; ?></p>
              <?php echo _l('project_name'); ?>: <a href="<?php echo admin_url('projects/view/' . $activity['project_id']); ?>"><?php echo $activity['project_name']; ?></a>
            </div>
            <?php if (!empty($activity['additional_data'])) { ?>
              <p class="text-muted mtop5"><?php echo $activity['additional_data']; ?></p>
            <?php } ?>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>