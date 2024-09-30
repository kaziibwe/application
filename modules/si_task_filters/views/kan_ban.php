<?php defined('BASEPATH') or exit('No direct script access allowed'); 

foreach ($task_statuses as $status) {
	if(!isset($overview[$status['id']]))
		$overview[$status['id']]=[];	
	if(!in_array('',$statuses) && !in_array($status['id'],$statuses))
		continue;		
	$total_pages = 1;//ceil(count($overview[$status['id']])/get_option('tasks_kanban_limit'));
  ?>
  <ul class="kan-ban-col tasks-kanban" data-col-status-id="<?php echo $status['id']; ?>" data-total-pages="<?php echo $total_pages; ?>">
    <li class="kan-ban-col-wrapper">
      <div class="border-right panel_s">
        <div class="panel-heading-bg" style="background:<?php echo $status['color']; ?>;border-color:<?php echo $status['color']; ?>;color:#fff; ?>" data-status-id="<?php echo $status['id']; ?>">
          <div class="kan-ban-step-indicator<?php if($status['id'] == Tasks_model::STATUS_COMPLETE){ echo ' kan-ban-step-indicator-full'; } ?>"></div>
          <span class="heading"><?php echo format_task_status($status['id'],false,true); ?> - <?php echo count($overview[$status['id']]) . ' ' . _l('tasks') ?>
          </span>
          <a href="#" onclick="return false;" class="pull-right color-white">
          </a>
        </div>
        <div class="kan-ban-content-wrapper">
          <div class="kan-ban-content">
            <ul class="status tasks-status sortable relative" data-task-status-id="<?php echo $status['id']; ?>">
              <?php
			  $total_tasks = count($overview[$status['id']]);
              foreach ($overview[$status['id']] as $task) {
                if ($task['status'] == $status['id']) {
                  $this->load->view('_kan_ban_card',array('task'=>$task,'status'=>$status['id']));
                } } ?>
                <?php if($total_tasks > 0 ){ ?>
                <li class="text-center not-sortable kanban-load-more" data-load-status="<?php echo $status['id']; ?>">
                 <a href="#" class="btn btn-default btn-block<?php if($total_pages <= 1){echo ' disabled';} ?>" data-page="1" onclick="kanban_load_more(<?php echo $status['id']; ?>,this,'tasks/tasks_kanban_load_more',265,360); return false;";><?php echo _l('load_more'); ?></a>
               </li>
               <?php } ?>
               <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_tasks > 0){echo ' hide';} ?>">
                <h4>
                  <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                  <?php echo _l('no_tasks_found'); ?></h4>
                </li>
              </ul>
            </div>
          </div>
        </li>
      </ul>
      <?php } ?>