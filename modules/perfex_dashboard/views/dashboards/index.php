<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->app_css->add('perfex_dashboard_styles', PERFEX_DASHBOARD_ASSETS_PATH . '/css/perfex_dashboard_styles.css');
$this->app_scripts->add('perfex_dashboard_scripts', PERFEX_DASHBOARD_ASSETS_PATH . '/js/perfex_dashboard_scripts.js');
?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="panel_s">
      <div class="panel-body">
        <h4 class="no-margin"><?= _l('all_dashboards') ?></h4>
        <div class="perfexdashboard-button-wrap">
          <?php if (has_permission('perfex_dashboard', '', 'dashboard_create')) { ?>
            <a href="javascript:void(0);" class="btn btn-primary btn-create-dashboard"><?= _l('create') ?></a>
          <?php } ?>

        </div>
        <table class="table table-striped perfexdashboard-table">
          <thead>
            <tr>
              <th><?= _l('name') ?></th>
              <th><?= _l('note') ?></th>
              <th><?= _l('created_at') ?></th>
              <th><?php echo _l('actions'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($dashboards) > 0) { ?>
              <?php foreach ($dashboards as $dashboard) { ?>
                <?php $link_builder = admin_url('perfex_dashboard/dashboards/edit_dashboard?dashboard_id=' . $dashboard['id']); ?>
                <tr>
                  <td><a href="<?php echo $link_builder; ?>" class="data-name" data-dashboard-id="<?= $dashboard['id'] ?>"><?php echo $dashboard['name']; ?></a></td>
                  <td class="data-note" data-dashboard-id="<?= $dashboard['id'] ?>"><?php echo $dashboard['note']; ?></td>
                  <td><?php echo $dashboard['created_at']; ?></td>
                  <td>
                    <?php if (has_permission('perfex_dashboard', '', 'dashboard_edit')) { ?>
                      <a class="btn btn-primary btn-xs" href="<?php echo $link_builder; ?>"><?php echo _l('builder'); ?></a>
                    <?php } ?>
                    <?php if (has_permission('perfex_dashboard', '', 'dashboard_delete')) { ?>
                      <a class="btn btn-danger btn-xs btn-delete" data-id="<?php echo $dashboard['id']; ?>" href="javascript:void(0);"><?php echo _l('delete'); ?></a>
                    <?php } ?>
                    <?php if (has_permission('perfex_dashboard', '', 'dashboard_clone')) { ?>
                      <a class="btn btn-warning btn-xs btn-clone" data-id="<?php echo $dashboard['id']; ?>" href="javascript:void(0);"><?php echo _l('copy'); ?></a>
                    <?php } ?>
                  </td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <th colspan="4">
                  <p><?= _l('not_found') ?></p>
                </th>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <!-- form create -->
        <form action="<?php echo base_url('admin/perfex_dashboard/dashboards/store_dashboard'); ?>" method="post" class="form-horizontal">
          <div id="modalCreateDashboard" class="modal">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title"><?= _l('new_dashboard') ?></h5>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label class="control-label"><?= _l('name') ?></label>
                    </div>
                    <div class="col-md-9">
                      <input type="text" class="form-control" name="name" value="" required />
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label class="control-label"><?= _l('note') ?></label>
                    </div>
                    <div class="col-md-9">
                      <textarea class="form-control" rows="4" name="note" required></textarea>
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
          <input type="hidden" name="dashboard_id" value="" />
        </form>

        <!-- form clone -->
        <form action="<?php echo base_url('admin/perfex_dashboard/dashboards/clone_dashboard'); ?>" method="post" class="form-horizontal">
          <div id="modalCloneDashboard" class="modal">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title"><?= _l('clone_dashboard') ?></h5>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                  <input type="hidden" name="clone_id" value="" />
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label class="control-label"><?= _l('name') ?></label>
                    </div>
                    <div class="col-md-9">
                      <input type="text" class="form-control" name="name" value="" required />
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label class="control-label"><?= _l('note') ?></label>
                    </div>
                    <div class="col-md-9">
                      <textarea class="form-control" rows="4" name="note" required></textarea>
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

      </div>
    </div>

  </div>
</div>
<script>
  const APP_PATH = "/dashboards";
</script>
<?php init_tail(); ?>
</body>

</html>