<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="<?php echo module_dir_url('si_timesheet','assets/css/si_timesheet_style.css'); ?>" rel="stylesheet" />
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body si_ts_overfolw_x">
						<div class="dt-loader hide"></div>
						<?php $this->load->view('timesheet/calendar_filters'); ?>
						<div id="si_calendar"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('timesheet/calendar_template'); ?>
<?php init_tail(); ?>
<script>
	var si_ts_confirm_edit = "<?php echo _l('si_ts_confirm_edit')?>";
	var si_ts_date_alert = "<?php echo _l('si_ts_date_alert')?>";
</script>
<script src="<?php echo module_dir_url('si_timesheet','assets/js/si_timesheet_calendar_filters.js'); ?>"></script>
<script src="<?php echo module_dir_url('si_timesheet','assets/js/si_timesheet_add_task_filters.js'); ?>"></script>
<?php if(si_timesheet_get_perfex_version()  <= 282){?>
<script src="<?php echo module_dir_url('si_timesheet','assets/js/si_timesheet_calendar.js'); ?>"></script>
<?php }else{?>
<script src="<?php echo module_dir_url('si_timesheet','assets/js/si_timesheet_calendar_283.js'); ?>"></script>
<?php } ?>
</body>
</html>
