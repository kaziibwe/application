<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $category = $this->input->get('group');?>
<div class="row">
<div class="col-md-12">
<div class="panel_s">
<div class="panel-body">
<div class="horizontal-scrollable-tabs">
	<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
		<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
		<div class="horizontal-tabs">
		<ul class="nav nav-tabs no-margin project-tabs1 nav-tabs-horizontal" role="tablist">
			<li class="<?php if($category==''){echo 'active';} ?>">
				<a class="si_todo_tabs_a"
				data-group="0"
				role="tab"
				href="<?php echo admin_url('si_todo'); ?>">
					<span class="label label-info inline-block"><?php echo _l('all')?></span>
					<span class="label si-total-todos bg-info"><?php echo ($total_pending);?></span> 
				</a>
			</li>
			<?php
			if(!empty($categories)){
			foreach($categories as $key => $tab){
				?>
				<li class="<?php if($category==$tab['id']){echo 'active';} ?>">
					<a class="si_todo_tabs_a"
					data-group="<?php echo ($tab['id']); ?>"
					role="tab"
					href="<?php echo admin_url('si_todo?group='.$tab['id']); ?>">
						<?php echo si_todo_format_category($tab); ?>
						<span class="label si-total-todos" style="background-color:<?php echo ($tab['color'])?>"><?php echo ($tab['total']-$tab['finished'])?></span>
					</a>
				</li>
			<?php } } ?>
		</ul>
	</div>
</div>
</div>
</div>
</div>
</div>