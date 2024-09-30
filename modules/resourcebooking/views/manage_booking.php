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
		                  <h4 class="no-margin font-bold"><i class="fa fa-edit" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
		                  <hr />
		                 </div>
		              	</div>
		              	<div class="row">    
	                        <div class="_buttons col-md-3">
		                        <a href="<?php echo admin_url('resourcebooking/add_edit_booking'); ?>"class="btn btn-info pull-left mright10 display-block">
		                            <?php echo _l('new_booking'); ?>
		                        </a>
		                        <a href="<?php echo admin_url('resourcebooking/calendar_booking'); ?>"class="btn btn-default pull-left display-block">
		                            <?php echo _l('calendar_view'); ?>
		                        </a>
		                    </div>

                    	</div>
                    <br><br>
                    <?php render_datatable(array(
                        _l('purpose'),
                        _l('orderer'),
                        _l('follower'),
                        _l('resource_group'),
                        _l('resource'),
                        _l('start_time'),
                        _l('end_time'),
                        _l('status'),
                        _l('options')
                        ),'table_booking'); ?>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script language="JavaScript" type="text/javascript" src="<?php echo module_dir_url('resourcebooking','assets/js/manage_booking.js'); ?>"></script>
<style>
.table.dataTable>thead:first-child>tr:first-child>th:last-child { 
    padding: 13px; 
}
th.sorting_disabled.sorting_asc:after {
    display: none !important;
}
</style>