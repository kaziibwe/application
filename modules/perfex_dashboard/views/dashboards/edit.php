<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->app_css->add('perfex_dashboard_styles', PERFEX_DASHBOARD_ASSETS_PATH . '/css/perfex_dashboard_styles.css');
$this->app_scripts->add('perfex_dashboard_scripts', PERFEX_DASHBOARD_ASSETS_PATH . '/js/perfex_dashboard_scripts.js');
$this->app_scripts->add('perfex_dashboard_widget_scripts', PERFEX_DASHBOARD_ASSETS_PATH . '/js/widgets/widget.js');
$this->app_css->add('perfex_dashboard_widget_styles', PERFEX_DASHBOARD_ASSETS_PATH . '/css/widget.css');
$categories = perfex_dashboard_get_categories();

$fn_generate_period = function ($period, $param1 = null) {
  $format = 'Y-m-d';
  $now = new Datetime();

  switch (strtoupper($period)) {
    case 'NOW':
      return [$now->format($format), $now->format($format)];
      break;

    case 'CURRENT_WEEK':
      $start_current_week = (clone $now)->sub(new DateInterval('P' . $now->format('w') . 'D'));
      $end_current_week = (clone $start_current_week)->add(new DateInterval('P6D'));
      return [$start_current_week->format($format), $end_current_week->format($format)];
      break;

    case 'PREVIOUS_WEEK':
      $end_previous_week = (clone $now)->sub(new DateInterval('P' . $now->format('w') . 'D'))->sub(new DateInterval('P1D'));
      $start_previous_week = (clone $end_previous_week)->sub((new DateInterval('P6D')));
      return [$start_previous_week->format($format), $end_previous_week->format($format)];
      break;

    case 'CURRENT_MONTH':
      $start_current_month = (clone $now)->setDate($now->format('Y'), $now->format('m'), 1);
      $end_current_month = (clone $start_current_month)->add(new DateInterval('P' . (intval($now->format('t')) - 1) . 'D'));
      return [$start_current_month->format($format), $end_current_month->format($format)];
      break;

    case 'PREVIOUS_MONTH':
      $end_previous_month = (clone $now)->setDate($now->format('Y'), $now->format('m'), 1)->sub(new DateInterval('P1D'));
      $start_previous_month = (clone $now)->setDate($end_previous_month->format('Y'), $end_previous_month->format('m'), 1);
      return [$start_previous_month->format($format), $end_previous_month->format($format)];
      break;

    case 'CURRENT_YEAR':
      $start_current_year = (clone $now)->setDate($now->format('Y'), 1, 1);
      $end_current_year = (clone $now)->setDate($now->format('Y'), 12, 31);
      return [$start_current_year->format($format), $end_current_year->format($format)];
      break;

    case 'PREVIOUS_YEAR':
      $start_previous_year = (clone $now)->setDate((intval($now->format('Y')) - 1), 1, 1);
      $end_previous_year = (clone $now)->setDate((intval($now->format('Y')) - 1), 12, 31);
      return [$start_previous_year->format($format), $end_previous_year->format($format)];
      break;

    case 'LAST_MONTHS':
      if (!isset($param1)) {
        return null;
      }
      return [date('Y-m-01', strtotime("-" . ($param1 - 1) . " MONTH")), date('Y-m-t')];
      break;

    default:
      return null;
      break;
  }
};

$tmp_current_month = $fn_generate_period('CURRENT_MONTH');
$tmp_previous_month = $fn_generate_period('PREVIOUS_MONTH');
$tmp_current_year = $fn_generate_period('CURRENT_YEAR');
$tmp_previous_year = $fn_generate_period('PREVIOUS_YEAR');
$tmp_last_3_months = $fn_generate_period('LAST_MONTHS', 3);
$tmp_last_6_months = $fn_generate_period('LAST_MONTHS', 6);
$tmp_last_12_months = $fn_generate_period('LAST_MONTHS', 12);
?>
  <?php init_head(); ?>
  <div id="wrapper">
    <div class="content">
      <div class="row">
        <div class="col-md-12">
          <form method="get" action="<?php echo admin_url('perfex_dashboard/dashboards/edit_dashboard'); ?>">
            <input type="hidden" name="dashboard_id" value="<?= $dashboard['id'] ?>" />
            <div class="perfexdashboard-actions-wrap perfexdashboard-actions-wrap-between">
              <div class="perfexdashboard-actions-section-left">
                <h4><?= _l('edit') ?> <?= $dashboard['name']; ?></h4>
              </div>
              <div class="perfexdashboard-actions-section-right"></div>
            </div>
          </form>
        </div>
      </div>
      <!-- widgets -->
      <?php if (count($dashboard['dashboard_widgets']) == 0) { ?>
        <div class="row mtop30">
          <div class="col-md-12">
            <p><?= _l('not_found') ?></p>
          </div>
        </div>
      <?php } else { ?>
        <div class="row mtop30">
          <div class="col-md-3" data-container="top-left-first-4">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'top-left-first-4'); ?>
          </div>
          <div class="col-md-3" data-container="top-left-last-4">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'top-left-last-4'); ?>
          </div>
          <div class="col-md-3" data-container="top-right-first-4">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'top-right-first-4'); ?>
          </div>
          <div class="col-md-3" data-container="top-right-last-4">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'top-right-last-4'); ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12" data-container="top-12">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'top-12'); ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6" data-container="middle-left-6">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'middle-left-6'); ?>
          </div>
          <div class="col-md-6" data-container="middle-right-6">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'middle-right-6'); ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-8" data-container="left-8">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'left-8'); ?>
          </div>
          <div class="col-md-4" data-container="right-4">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'right-4'); ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4" data-container="bottom-left-4">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'bottom-left-4'); ?>
          </div>
          <div class="col-md-4" data-container="bottom-middle-4">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'bottom-middle-4'); ?>
          </div>
          <div class="col-md-4" data-container="bottom-right-4">
            <?php perfex_dashboard_render_widgets_from_dashboard($dashboard, 'bottom-right-4'); ?>
          </div>
        </div>
      <?php } ?>
      <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
        <a id="viewWidgetableArea" href="javascript:void(0);" class="btn btn-default"><?= _l('widget_area') ?></a>
        <?php if (has_permission('perfex_dashboard', '', 'dashboard_edit')) { ?>
          <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalAddWidget"><?= _l('add_widgets') ?></button>
        <?php } ?>
        <?php if (has_permission('perfex_dashboard', '', 'dashboard_edit')) { ?>
          <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalUpdateDashboard"><?= _l('update_infomations') ?></button>
          <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalUpdateStaff"><?= _l('update_role') ?></button>
        <?php } ?>
        <?php if (has_permission('perfex_dashboard', '', 'dashboard_delete')) { ?>
          <button type="button" class="btn btn-danger btn-delete"><?= _l('delete') ?></button>
        <?php } ?>
      </div>
    </div>
  </div>

  <!-- modal update dashboard meta infomations -->
  <form action="<?= admin_url('perfex_dashboard/dashboards/update_dashboard')  ?>" method="post" class="form-horizontal">
    <div id="modalUpdateDashboard" class="modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?= _l('update_meta_infomations') ?></h5>
          </div>
          <div class="modal-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <input type="hidden" name="dashboard_id" value="<?php echo $dashboard['id']; ?>" />

            <div class="form-group row">
              <label class="control-label col-md-3"><?= _l('name') ?></label>
              <div class="col-md-6">
                <input type="text" class="form-control" name="name" value="<?php echo $dashboard['name'] ?>" required />
              </div>
            </div>

            <div class="form-group row">
              <label class="control-label col-md-3"><?= _l('note') ?></label>
              <div class="col-md-6">
                <textarea class="form-control" rows="4" name="note" required><?php echo $dashboard['note'] ?></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary"><?= _l('save') ?></button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- modal add widget -->
  <form id="formAddWidget" action="<?php echo base_url('admin/perfex_dashboard/dashboards/add_widget'); ?>" method="post" class="form-horizontal">
    <div id="modalAddWidget" class="modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?= _l('add_widgets') ?></h5>
          </div>
          <div class="modal-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="dashboard_id" value="<?php echo $dashboard['id']; ?>" />
            <input type="hidden" name="widget_id" value="" />
            <input type="hidden" name="widget_container" value="" />

            <div class="perfexdashboard-actions-wrap">
              <select id="changeCategory" class="form-control perfexdashboard-categories-select">
                <?php foreach ($categories as $category) { ?>
                  <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                <?php } ?>
              </select>
              &nbsp;
              <a href="<?php echo admin_url('perfex_dashboard/widgets'); ?>" class="btn btn-primary"><?= _l('view_all') ?></a>
            </div>

            <table class="table table-striped perfexdashboard-table">
              <thead>
                <th><?= _l('name') ?></th>
                <th></th>
                <th></th>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l('close') ?></button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- modal staff -->
  <form action="<?= admin_url('perfex_dashboard/dashboards/update_staff')  ?>" method="post" class="form-horizontal">
    <div id="modalUpdateStaff" class="modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?= _l('perfex_dashboard_staff_modal_title') ?></h5>
          </div>
          <div class="modal-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <input type="hidden" name="dashboard_id" value="<?php echo $dashboard['id']; ?>" />

            <div class="row">
              <label class="control-label col-md-2"><?= _l('staff') ?></label>
              <div class="col-md-8">
                <?php
                $selected = array();
                if (isset($dashboard_staff)) {
                  foreach ($dashboard_staff as $member) {
                    array_push($selected, $member['user_id']);
                  }
                }
                echo render_select('dashboard_staff[]', $staff, array('staffid', array('firstname', 'lastname')), '', $selected, array('multiple' => true, 'data-actions-box' => true), array(), '', '', false);
                ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary"><?= _l('save') ?></button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- form delete -->
  <form id="formDelete" action="<?php echo base_url('admin/perfex_dashboard/dashboards/delete_dashboard'); ?>" method="post" class="d-none">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="dashboard_id" value="<?= $dashboard['id'] ?>" />
  </form>
  <script>
    const APP_PATH = "/dashboards/edit_dashboard";
    const DASHBOARD_ID = "<?php echo $dashboard['id']; ?>";
  </script>
  <?php init_tail(); ?>

  </body>

  </html>