<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="<?php echo module_dir_url('si_todo','assets/css/si_todo_style.css'); ?>" rel="stylesheet">
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s panel-full">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-7">
							<h4><?php echo _l('si_todo_dashboard_menu'); ?></h4>
							</div>
							<div class="col-md-5  _buttons text-right">
								<a href="#si_todo_modal" data-toggle="modal" class="btn btn-info">
									<?php echo _l('new_todo'); ?>
								</a>
								<a href="<?php echo admin_url('si_todo/category_list')?>" class="btn btn-primary">
									<?php echo _l('si_todo_category_menu'); ?>
								</a>
							</div>
						</div>	
					</div>
				</div>
			</div>
		</div>			
		<?php $this->load->view('todo_tabs'); ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<div class="panel_s events animated fadeIn">
									<div class="panel-body todo-body">
										<h4 class="todo-title warning-bg"><i class="fa fa-warning"></i>
											<?php echo _l('unfinished_todos_title'); ?></h4>
											<ul class="list-unstyled todo unfinished-todos si-todos-sortable">
												<li class="padding no-todos hide ui-state-disabled">
													<?php echo _l('no_unfinished_todos_found'); ?>
												</li>
											</ul>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 text-center padding">
											<a href="#" class="btn btn-default text-center unfinished-loader"><?php echo _l('load_more'); ?></a>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="panel_s animated fadeIn">
										<div class="panel-body todo-body">
											<h4 class="todo-title info-bg"><i class="fa fa-check"></i>
												<?php echo _l('finished_todos_title'); ?></h4>
												<ul class="list-unstyled todo finished-todos si-todos-sortable">
													<li class="padding no-todos hide ui-state-disabled">
														<?php echo _l('no_finished_todos_found'); ?>
													</li>
												</ul>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12 text-center padding">
												<a href="#" class="btn btn-default text-center finished-loader">
													<?php echo _l('load_more'); ?>
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('add_todo'); ?>
<?php $this->load->view('add_category'); ?>
<?php init_tail(); ?>
<script>
	var total_pages_unfinished = '<?php echo ($total_pages_unfinished); ?>';
	var total_pages_finished = '<?php echo ($total_pages_finished); ?>';
	var si_todo_alphanumeric_validation = '<?php echo _l('si_todo_alphanumeric_validation'); ?>';
</script>
<script src="<?php echo module_dir_url('si_todo','assets/js/si_todo_loader.js'); ?>"></script>
<script src="<?php echo module_dir_url('si_todo','assets/js/si_todo.js'); ?>"></script>
<script src="<?php echo module_dir_url('si_todo','assets/js/si_todo_category.js'); ?>"></script>
</body>
</html>
