<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
  // declaration
  $tmp_base_url = $pagination['base_url'];
  $tmp_current_page = $pagination['current_page'];
  $tmp_total_pages = $pagination['total_pages'];
  $tmp_printed_dots_before = false;
  $tmp_printed_dots_after = false;
  
?>

<nav class="perfexdashboard-pagination-wrapper">
  <ul class="pagination">

    <?php for($tmp_i = 1; $tmp_i <= $tmp_total_pages; $tmp_i++) { ?>

      <?php if($tmp_i != 1 && $tmp_i != $tmp_total_pages && ($tmp_i < $tmp_current_page - 1 || $tmp_i > $tmp_current_page + 1)) { ?>

        <?php if($tmp_i < $tmp_current_page - 1 && !$tmp_printed_dots_before) { ?>
          <?php $tmp_printed_dots_before = true; ?>
          <li class="page-item disabled"><a class="page-link" href="javascript:void(0);">...</a></li>
        <?php } ?>

        <?php if($tmp_i > $tmp_current_page + 1 && !$tmp_printed_dots_after) { ?>
          <?php $tmp_printed_dots_after = true; ?>
          <li class="page-item disabled"><a class="page-link" href="javascript:void(0);">...</a></li>
        <?php } ?>

        <?php continue; ?>

      <?php } ?>

      <?php 
        if($tmp_i == $tmp_current_page) {
          $tmp_button_disable = true;
          $tmp_button_link = 'javascript:void(0);';
        } else {
          $tmp_button_disable = false;
          $tmp_button_link = $tmp_base_url . '?page=' . $tmp_i;
        }
      ?>
      
      <li class="page-item <?php echo $tmp_button_disable ? 'disabled' : ''; ?>"><a class="page-link" href="<?php echo $tmp_button_link; ?>"><?php echo $tmp_i; ?></a></li>

    <?php } ?>
  </ul>
</nav