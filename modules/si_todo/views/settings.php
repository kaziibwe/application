<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">		
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
							<h4><?php echo _l('si_todo_settings'); ?></h4>
							</div>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<?php echo form_open('admin/si_todo/settings',array('id'=>'si_todo_settings')); ?>
						<div class="row">
							<div class="col-md-4">
								<?php echo render_input('dashboard_unfinished_limit','si_todo_dashboard_unfinished_limit',$settings['dashboard_unfinished_limit'],'number',array('min'=>0,'max'=>100)); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('dashboard_finished_limit','si_todo_dashboard_finished_limit',$settings['dashboard_finished_limit'],'number',array('min'=>0,'max'=>100)); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('todos_load_limit','si_todo_todos_load_limit',$settings['todos_load_limit'],'number',array('min'=>0,'max'=>1000)); ?>
							</div>
						</div>
						<div class="btn-bottom-toolbar text-right">
							<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
						</div>
						<?php echo form_close(); ?>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
