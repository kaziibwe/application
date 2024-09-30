<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('si_timesheet'); ?>">
	<link href="<?php echo module_dir_url('si_timesheet','assets/css/si_timesheet_style.css'); ?>" rel="stylesheet"/>
	<div class="clearfix"></div>
	<div class="panel_s">
		<div class="panel-body">
			<div class="col-md-12 text-stats-wrapper">
			   <p class="text-dark text-uppercase"><?php echo _l('si_timesheet')?></p>
			   <hr class="mtop15">
			</div>
			<div class="clearfix"></div>
			<div class="widget-dragger"></div>
			<div class="dt-loader hide"></div>
			<?php $this->load->view('si_timesheet/timesheet/calendar_template'); ?>
			<div id="si_calendar"></div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>


<?php hooks()->add_action('app_admin_footer','parse_calendar_timesheet_html');
function parse_calendar_timesheet_html(){?>
	<script>
		var si_ts_confirm_edit = "<?php echo _l('si_ts_confirm_edit')?>";
		var si_ts_date_alert = "<?php echo _l('si_ts_date_alert')?>";
	</script>
	<script src="<?php echo module_dir_url('si_timesheet','assets/js/si_timesheet_add_task_filters.js'); ?>"></script>
	<?php if(si_timesheet_get_perfex_version()  <= 282){?>
	<script src="<?php echo module_dir_url('si_timesheet','assets/js/si_timesheet_calendar.js'); ?>"></script>
	<?php }else{?>
	<script src="<?php echo module_dir_url('si_timesheet','assets/js/si_timesheet_calendar_283.js'); ?>"></script>
	<?php } ?>
<?php } ?>
