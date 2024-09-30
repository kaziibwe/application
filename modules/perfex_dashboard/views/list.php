<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->app_css->add('perfex_dashboard_styles', PERFEX_DASHBOARD_ASSETS_PATH . '/css/perfex_dashboard_styles.css');
$this->app_scripts->add('perfex_dashboard_scripts', PERFEX_DASHBOARD_ASSETS_PATH . '/js/perfex_dashboard_scripts.js');
$this->app_scripts->add('perfex_dashboard_widget_scripts', PERFEX_DASHBOARD_ASSETS_PATH . '/js/widgets/widget.js');
$this->app_css->add('perfex_dashboard_widget_styles', PERFEX_DASHBOARD_ASSETS_PATH . '/css/widget.css');

?>
<?php init_head(); ?>
<div id="wrapper" class="perfex-dashboard-widget-list">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <form method="get" action="<?php echo admin_url('perfex_dashboard/widgets'); ?>">
          <div class="perfexdashboard-actions-wrap perfexdashboard-actions-wrap-between">
            <div class="perfexdashboard-actions-section-left">
              <?php if (has_permission('perfex_dashboard', '', 'widget_create')) { ?>
                <a href="" data-toggle="modal" data-target="#modalCreateWidget" class="btn btn-primary"><?= _l('create_new_widget') ?></a>
              <?php } ?>
            </div>
            <div class="perfexdashboard-actions-section-right">
              <select name="category" class="form-control perfexdashboard-categories-select">
                <option value="" <?php if ('' == $active_category) {
                                    echo 'selected';
                                  } ?>><?= _l('all_widgets') ?></option>
                <?php foreach ($categories as $category) { ?>
                  <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $active_category) {
                                                                      echo 'selected';
                                                                    } ?>><?php echo $category['name']; ?></option>
                <?php } ?>
              </select>

              <input type="text" name="search" class="form-control perfexdashboard-search-input" placeholder="<?= _l('search_input_placeholder') ?>" value="<?php echo $active_search; ?>" />
              <div>
                <button type="submit" class="btn btn-primary"><?= _l('search') ?></button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <?php if (count($widgets) > 0) { ?>
        <div class="col-md-12 mtop30">
          <div class="row">
            <?php perfex_dashboard_render_widgets($widgets); ?>
          </div>
        </div>
      <?php } else { ?>
        <div class="col-md-12 mtop30">
          <h3 class="text-center wiki-empty-results"><?= _l('not_found_and_filter') ?></h3>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<!-- create widget -->
<form action="<?= admin_url('perfex_dashboard/widgets/store_widget')  ?>" method="post" class="form-horizontal">
  <div id="modalCreateWidget" class="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?= _l('create_widget') ?></h5>
        </div>
        <div class="modal-body">
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

          <div class="form-group row">
            <label class="control-label col-md-3"><?= _l('name') ?></label>
            <div class="col-md-6">
              <input type="text" name="name" class="form-control" required />
            </div>
          </div>

          <div class="form-group row">
            <label class="control-label col-md-3"><?= _l('note') ?></label>
            <div class="col-md-6">
              <textarea name="note" class="form-control" rows="3"></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label class="control-label col-md-3"><?= _l('widget_category') ?></label>
            <div class="col-md-6">
              <div class="input-group">
                <select name="category" class="form-control" required>
                  <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                  <?php } ?>
                </select>
                <div class="input-group-addon">
                  <span><a href="<?php echo admin_url('perfex_dashboard/categories'); ?>" tabindex="-1"><i class="fa fa-plus"></i></a></span>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="control-label col-md-3"><?= _l('widget_file') ?></label>
            <div class="col-md-6">
              <select name="widget_name" class="selectpicker" data-live-search="true" required>
                <?php foreach ($scan_all_widgets as $scan_widget) { ?>
                  <option value="<?= $scan_widget['name'] ?>"><?= $scan_widget['name'] ?></option>
                <?php } ?>
              </select>

            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="alert alert-warning">
                <?php echo _l('help_widget_name');  ?><br>
                <ul>
                  <li><?php echo _l('help_widget_name_note_step_1');  ?></li>
                  <li><?php echo _l('help_widget_name_note_step_2');  ?><strong><?php echo get_widgest_folder_path(); ?></strong></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l('close') ?></button>
          <button type="submit" class="btn btn-primary"><?= _l('create') ?></button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- edit widget -->
<form action="<?= admin_url('perfex_dashboard/widgets/update_widget')  ?>" method="post" class="form-horizontal">
  <div id="modalEditWidget" class="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?= _l('update_widget') ?></h5>
        </div>
        <div class="modal-body">
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <input type="hidden" name="id" value="" />

          <div class="form-group row">
            <label class="control-label col-md-3"><?= _l('name') ?></label>
            <div class="col-md-6">
              <input type="text" name="name" class="form-control" required />
            </div>
          </div>

          <div class="form-group row">
            <label class="control-label col-md-3"><?= _l('note') ?></label>
            <div class="col-md-6">
              <textarea name="note" class="form-control" rows="3"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label class="control-label col-md-3"><?= _l('widget_category') ?></label>
            <div class="col-md-6">
              <div class="input-group">
                <select name="category" class="form-control" required>
                  <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                  <?php } ?>
                </select>
                <div class="input-group-addon">
                  <span><a href="<?php echo admin_url('perfex_dashboard/categories'); ?>" tabindex="-1"><i class="fa fa-plus"></i></a></span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="control-label col-md-3"><?= _l('widget_file') ?></label>
            <div class="col-md-6">
              <select name="widget_name" class="form-control" required>
                <?php foreach ($scan_all_widgets as $scan_widget) { ?>
                  <option value="<?= $scan_widget['name'] ?>"><?= $scan_widget['name'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="alert alert-warning">
                <?php echo _l('help_widget_name');  ?><br>
                <ul>
                  <li><?php echo _l('help_widget_name_note_step_1');  ?></li>
                  <li><?php echo _l('help_widget_name_note_step_2');  ?><strong><?php echo get_widgest_folder_path(); ?></strong></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l('close') ?></button>
          <button type="submit" class="btn btn-primary"><?= _l('update') ?></button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- form delete widget -->
<form id="formDeleteWidget" action="<?php echo base_url('admin/perfex_dashboard/widgets/destroy_widget'); ?>" method="post" class="d-none">
  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
  <input type="hidden" name="id" value="" />
</form>
<script>
  const APP_PATH = "/widgets";
  var widget_edit_permission = false;
  var widget_delete_permission = false;
  var symbol_base_currency= "<?php echo get_base_currency()->symbol; ?>";
  <?php if (has_permission('perfex_dashboard', '', 'widget_create')) { ?>
    widget_edit_permission = true;
  <?php } ?>
  <?php if (has_permission('perfex_dashboard', '', 'widget_delete')) { ?>
    widget_delete_permission = true;
  <?php } ?>
</script>
<?php init_tail(); ?>
</body>

</html>