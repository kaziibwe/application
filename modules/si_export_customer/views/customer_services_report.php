<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<link href="<?php echo module_dir_url('si_export_customer','assets/css/si_export_customer_style.css'); ?>" rel="stylesheet" />
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_open($this->uri->uri_string()); ?>
						<h4 class="pull-left"><?php echo _l('si_customer_services'); ?></h4>
						<button type="submit" data-toggle="tooltip" data-title="<?php echo _l('si_customer_apply_filter'); ?>" class=" pull-right btn btn-info mleft4"><?php echo _l('filter'); ?></button>
						<div class="clearfix"></div>
						<hr />
						<div class="row">
							<div class="col-md-3 border-right">
								<label><?php echo _l('si_customer_filter_by');?></label><br/>
								<div class="radio radio-inline radio-primary">
								  <input type="radio" id="filter_by2" name="filter_by" value="customer" <?php if($filter_by == 'customer'){echo 'checked';} ?>>
								  <label for="filter_by2"><?php echo _l('si_customer_filter_customer'); ?></label>
								</div>
								<div class="radio radio-inline radio-primary">
								  <input type="radio" id="filter_by1" name="filter_by" value="service" <?php if($filter_by == 'service'){echo 'checked';} ?>>
								  <label for="filter_by1"><?php echo _l('si_customer_filter_service'); ?></label>
								</div>
							</div>
							<?php if(has_permission('staff','','view')){ ?>
							<div class="col-md-2 border-right">
								<label for="member" class="control-label"><?php echo _l('staff_members'); ?></label>
								<?php echo render_select('member',$members,array('staffid',array('firstname','lastname')),'',$staff_id,array('data-none-selected-text'=>_l('all_staff_members')),array(),'no-margin'); ?>
							</div>
							<?php } ?>
							<div class="col-md-2 border-right">
								<label id='label_filter_by' class="control-label"><?php echo ($filter_by=='customer'?_l('si_customer_filter_customer'):_l('si_customer_filter_service')); ?></label>		
								<div class="form-group no-margin select-placeholder">
									<?php echo render_select('search_list',$search_list,array('id','name'),'',$filter_id,array('data-none-selected-text'=>_l('si_customer_select_all')),array(),'no-margin'); ?>
								</div>
							</div>
							<div class="col-md-2 border-right">
								<label id='label_filter_by' class="control-label"><?php echo _l('item_group_name'); ?></label>		
								<div class="form-group no-margin select-placeholder">
									<?php echo render_select('group_list',$group_list,array('id','name'),'',$group_id,array('data-none-selected-text'=>_l('si_customer_select_all')),array(),'no-margin'); ?>
								</div>
							</div>
							<div class="col-md-2 border-right form-group">
								<label for="group_by" class="control-label"><span class="control-label"><?php echo _l('si_customer_group_by'); ?></span></label>
								<select name="group_by" id="group_by" class="selectpicker no-margin" data-width="100%">
									<option value="" selected><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="customer" <?php echo ($group_by!='' && $group_by=='customer'?'selected':'')?>><?php echo _l('client'); ?></option>
									<option value="item" <?php echo ($group_by!='' && $group_by=='item'?'selected':'')?>><?php echo _l('si_customer_filter_service'); ?></option>
									<option value="item_group" <?php echo ($group_by!='' && $group_by=='item_group'?'selected':'')?>><?php echo _l('si_customer_filter_service_group'); ?></option>
								</select>
							</div>
						</div><!--end row-->
						<?php echo form_close(); ?>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
						<?php
						if(!empty($table_data)){
						foreach($table_data as $key_group =>$data){ if(count($data) == 0){continue;} $i=1; ?>
							<h4 class="bold text-success"><?php echo htmlspecialchars($key_group); ?></h4>
							<table class="table tasks-overview dt-table scroll-responsive">
								<caption class="si_export_customer_caption"><?php echo htmlspecialchars($key_group);?></caption>
								<thead>
									<tr>
										<th>#</th>
										<?php if($group_by!='customer'){?>
										<th><?php echo _l('client'); ?></th>
										<?php } ?>
										<th><?php echo _l('customer_group'); ?></th>
										<?php if($group_by!='item'){?>
										<th><?php echo _l('si_customer_filter_service'); ?></th>
										<?php } ?>
										<?php if($group_by!='item_group'){?>
										<th><?php echo _l('si_customer_filter_service_group'); ?></th>
										<?php } ?>
										<?php if(has_permission('staff','','view')){ ?>
										<th><?php echo _l('customer_admins'); ?></th>
										<?php }?>
									</tr>
								</thead>
							<tbody>
								<?php
									foreach($data as $row){ ?>
									<tr>
										<td><?php echo htmlspecialchars($i++);?></td>
										<?php if($group_by!='customer'){?>
										<td data-order="<?php echo htmlspecialchars($row['company']); ?>"><a href="<?php echo admin_url('clients/client/' . $row['client_id'])?>"><?php echo htmlspecialchars($row['company']); ?></a></td>
										<?php } ?>
										<td data-order="<?php echo htmlspecialchars($row['customer_groups']); ?>"><?php echo htmlspecialchars($row['customer_groups']); ?></td>
										<?php if($group_by!='item'){?>
										<td data-order="<?php echo htmlspecialchars($row['description']); ?>"><?php echo htmlspecialchars($row['description']); ?></td>
										<?php } ?>
										<?php if($group_by!='item_group'){?>
										<td data-order="<?php echo htmlspecialchars($row['item_group_name']); ?>"><?php echo htmlspecialchars($row['item_group_name']); ?></td>
										<?php } ?>
										<?php if(has_permission('staff','','view')){ ?>
										<td>
											<?php echo format_members_by_ids_and_names($row['staff_ids'],$row['staff_names'], false);?>
										</td>
										<?php } ?>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							<hr />
						<?php } } ?>
					</div>
				</div>
			</div>
		</div><!--end row-->
	</div><!--end content-->
</div><!--end wrapper-->
<?php init_tail(); ?>
</body>
</html>
<script src="<?php echo module_dir_url('si_export_customer','assets/js/si_export_customer_customer_service_report.js'); ?>"></script>
<script>
	var txt_customer = '<?php echo _l('si_customer_filter_customer');?>';
	var txt_service = '<?php echo _l('si_customer_filter_service'); ?>';
</script>