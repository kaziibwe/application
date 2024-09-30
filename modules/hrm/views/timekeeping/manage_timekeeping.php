<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
    <div class="row">
     <?php if($this->session->flashdata('debug')){ ?>
       <div class="col-lg-12">
        <div class="alert alert-warning">
         <?php echo $this->session->flashdata('debug'); ?>
       </div>
     </div>
   <?php } ?>
   <div class="col-md-2">
    <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
      <?php
      $i = 0;
      foreach($tab as $group){
        ?>
        <li<?php if($i == 0){echo " class='active'"; } ?>>
        <a href="<?php echo admin_url('hrm/timekeeping?group='.$group); ?>" data-group="<?php echo htmlspecialchars($group); ?>">
          <?php
          if ($group == 'manage_dayoff') {
            echo '<i class=" fa fa-hotel"></i> '._l($group);
          }elseif ($group == 'allocate_shiftwork') {
            echo '<i class=" fa fa-fax"></i> '._l($group);
          }elseif ($group == 'table_shiftwork') {
            echo '<i class=" fa fa-table"></i> '._l($group);
          }
          ?>
          </a>
        </li>
        <?php $i++; } ?>
      </ul>
  </div>

  <div class="col-md-10">
    <div class="panel_s">
     <div class="panel-body">
      <?php $this->load->view($tabs['view']); ?>
      
    </div>
  </div>
</div>
<div class="clearfix"></div>
</div>
<?php echo form_close(); ?>
<div class="btn-bottom-pusher"></div>
</div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>
<?php hooks()->do_action('settings_tab_footer', $tab); ?>
</body>
</html>
