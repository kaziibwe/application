<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<h4 class="pull-left"><?php echo _l('si_timesheet')." - "._l('si_ts_filter_templates'); ?></h4>
						<div class="clearfix"></div>
						<hr />
						<table class="table dt-table scroll-responsive">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th><?php echo _l('si_ts_filter_name'); ?></th>
									<th><?php echo _l('si_ts_filter_type'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php
							if(!empty($filter_templates)){
								$i=1;
								foreach($filter_templates as $row){
									$url = 'index';
									if($row['filter_type']==1)
										$url = 'timesheet_summary';
								?>
								<tr class="has-row-options">
									<td><?php echo htmlspecialchars($i++);?></td>
									<td data-order="<?php echo htmlspecialchars($row['filter_name']); ?>">
										<a href="<?php echo ($url.'/?filter_id='.$row['id']);?>"><?php echo htmlspecialchars($row['filter_name']); ?></a>
										<div class="row-options">
										<a href="<?php echo ($url.'/?filter_id='.$row['id']);?>"><?php echo _l('edit');?></a> | <a href="del_timesheet_filter/<?php echo htmlspecialchars($row['id']);?>" class="confirm text-danger"><?php echo _l('delete');?></a>
										</div>
									</td>
									<td>
										<?php echo _l('si_ts_filter_type_option_'.$row['filter_type']); ?>
									</td>
								</tr>
								<?php } }?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script>
(function($) {
"use strict";
	$('.confirm').on('click',function(){
		return confirm("<?php echo _l('si_ts_delete_confirm');?>");
	});
})(jQuery);	 	
</script>

