<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Calendar
  Description: Calendar Description
  
*/
?>

<div class="widget widget-calendar widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="clearfix"></div>
  <div class="panel_s">
    <div class="panel-body">
      <div class="widget-dragger"></div>
      <div class="dt-loader hide"></div>
      <?php $this->load->view('perfex_dashboard/partials/calendar_template', ['widget' => $widget]); ?>
      <?php $this->load->view('perfex_dashboard/partials/calendar_filters', ['widget' => $widget]); ?>
      <div id="calendar-<?= $widget['id'] ?>"></div>
    </div>
  </div>
  <div class="clearfix"></div>
</div>