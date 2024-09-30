<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="perfexdashboard-widget-info-wrap">
  <?php if(isset($widget['name'])) { ?>
    <div class="widget-name">
      <span><?= $widget['name'] ?></span>
    </div>
  <?php } ?>

  <?php if(isset($widget['category_name'])) { ?>
    <div class="widget-category">
      <span><?= $widget['category_name'] ?></span>
    </div>
  <?php } ?>

  <?php if(isset($widget['note'])) { ?>
    <p class="widget-note"><?= $widget['note'] ?></p>
  <?php } ?>
</div>