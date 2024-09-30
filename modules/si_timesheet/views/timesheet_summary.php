<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$report_heading = '';
?>
<link href="<?php echo module_dir_url('si_timesheet','assets/css/si_timesheet_style.css'); ?>" rel="stylesheet" />
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_open($this->uri->uri_string() . ($this->input->get('filter_id') ? '?filter_id='.$this->input->get('filter_id') : ''),"id=si_form_timesheet_filter"); ?>
						<h4 class="pull-left"><?php echo _l('si_timesheet')." - "._l('si_ts_timesheet_summary_menu'); ?> <small class="text-success"><?php echo htmlspecialchars($saved_filter_name);?></small></h4>
						<div class="btn-group pull-right mleft4 btn-with-tooltip-group" data-toggle="tooltip" data-title="<?php echo _l('si_ts_filter_templates'); ?>" data-original-title="" title="">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-list"></i>
							</button>
							<ul class="row dropdown-menu notifications width400">
							<?php
							if(!empty($filter_templates))
							{
								foreach($filter_templates as $row)
								{
									echo "<li><a href='timesheet_summary?filter_id=$row[id]'>$row[filter_name]</a></li>";
								}
							}
							else
								echo '<li><a >'._l('si_ts_no_filter_template').'</a></li>';
							?>
							</ul>
						</div>
						<button type="submit" data-toggle="tooltip" data-title="<?php echo _l('si_ts_apply_filter'); ?>" class=" pull-right btn btn-info mleft4"><?php echo _l('filter'); ?></button>
						<a href="timesheet_summary" class=" pull-right btn btn-info mleft4"><?php echo _l('si_ts_new'); ?></a>
						<div class="clearfix"></div>
						<hr />
						<div class="row">
							<?php if(has_permission('si_timesheet','','view') && has_permission('tasks','','view')){ ?>
							<div class="col-md-2 border-right">
								<label for="rel_type" class="control-label"><?php echo _l('staff_members'); ?></label>
								<?php echo render_select('member',$members,array('staffid',array('firstname','lastname')),'',$staff_id,array('data-none-selected-text'=>_l('all_staff_members')),array(),'no-margin'); ?>
							</div>
							<?php } ?>
							<div class="col-md-2 text-center1 border-right">
								<label for="rel_type" class="control-label"><?php echo _l('task_status'); ?></label>		
								<div class="form-group no-margin select-placeholder">
									<select name="status[]" id="status" class="selectpicker no-margin" data-width="100%" data-title="<?php echo _l('task_status'); ?>" multiple>
										<option value="" <?php if(in_array('',$statuses)){echo 'selected'; } ?>><?php echo _l('task_list_all'); ?></option>
										<?php foreach($task_statuses as $status){ ?>
										<option value="<?php echo htmlspecialchars($status['id']); ?>" <?php if(in_array($status['id'],$statuses)){echo 'selected'; } ?>>
										<?php echo htmlspecialchars($status['name']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<!--start billable select -->
							<div class="col-md-2 border-right form-group">
								<label for="billable" class="control-label"><span class="control-label"><?php echo _l('task_billable'); ?></span></label>
								<select name="billable" id="billable" class="selectpicker no-margin" data-width="100%" >
									<option value=""><?php echo _l('task_list_all'); ?></option>
									<option value="1" <?php echo ($billable!='' && $billable=="1"?'selected':'')?>><?php echo _l('si_ts_yes'); ?></option>
									<option value="0" <?php echo ($billable!='' && $billable=="0"?'selected':'')?>><?php echo _l('si_ts_no'); ?></option>
								</select>
							</div>
							<!--end billable select-->
							<!--start rel type-->
							<div class="col-md-2 border-right">
								<label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
								<select name="rel_type" class="selectpicker" id="si_ts_rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<option value=""></option>
									<option value="project" <?php if(isset($rel_type)){if($rel_type == 'project'){echo 'selected';}} ?>><?php echo _l('project'); ?></option>
									<option value="invoice" <?php if(isset($rel_type)){if($rel_type == 'invoice'){echo 'selected';}} ?>><?php echo _l('invoice'); ?></option>
									<option value="customer" <?php if(isset($rel_type)){if($rel_type == 'customer'){echo 'selected';}} ?>><?php echo _l('client'); ?></option>
									<option value="estimate" <?php if(isset($rel_type)){if($rel_type == 'estimate'){echo 'selected';}} ?>><?php echo _l('estimate'); ?></option>
									<option value="contract" <?php if(isset($rel_type)){if($rel_type == 'contract'){echo 'selected';}} ?>><?php echo _l('contract'); ?></option>
									<option value="ticket" <?php if(isset($rel_type)){if($rel_type == 'ticket'){echo 'selected';}} ?>><?php echo _l('ticket'); ?></option>
									<option value="expense" <?php if(isset($rel_type)){if($rel_type == 'expense'){echo 'selected';}} ?>><?php echo _l('expense'); ?></option>
									<option value="lead" <?php if(isset($rel_type)){if($rel_type == 'lead'){echo 'selected';}} ?>><?php echo _l('lead'); ?></option>
									<option value="proposal" <?php if(isset($rel_type)){if($rel_type == 'proposal'){echo 'selected';}} ?>><?php echo _l('proposal'); ?></option>
								</select>
							</div>
							<!--end of list of rel type-->
							<!--start rel_id select from rel_type-->
							<div class="col-md-2 border-right form-group<?php if($rel_id == '' && $rel_type==''){echo ' hide';} ?>" id="si_ts_rel_id_wrapper">
								<label for="rel_id" class="control-label"><span class="si_ts_rel_id_label"></span></label>
								<div id="si_ts_rel_id_select">
									<select name="rel_id" id="si_ts_rel_id" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<?php if($rel_id != '' && $rel_type != ''){
									$rel_data = get_relation_data($rel_type,$rel_id);
									$rel_val = get_relation_values($rel_data,$rel_type);
									echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
									if($group_by=='')
									$report_heading.=" - ".$rel_val['name'];
									} ?>
									</select>
								</div>
							</div>
							<!--end rel_id select-->
							<!--start group_id select from rel_id if rel_type is customer-->
							<div class="col-md-2 border-right form-group<?php if($rel_type !== 'customer'){echo ' hide';} ?>" id="si_ts_group_id_wrapper">
								<label for="group_id" class="control-label"><span class="control-label"><?php echo _l('customer_groups'); ?></span></label>
								<div id="group_id_select">
									<select name="group_id" id="group_id" class="selectpicker no-margin" data-width="100%" >
										<option value="" selected><?php echo _l('dropdown_non_selected_tex'); ?></option>
										<?php if(!empty($groups)){
											foreach($groups as $group)
											{
												echo '<option value="'.$group['id'].'" '.($group_id!='' && $group_id==$group['id']?'selected':'').'>'.$group['name'].'</option>';
												if($group_id==$group['id'])
													$report_heading.=" (Group:".$group['name'].")";
											}
											} 
										?>
									</select>
								</div>
							</div>
							<!--end group_id select-->
						</div>
						<div class="row">
							<!--start group_by select -->
							<div class="col-md-2 border-right form-group">
								<label for="group_id" class="control-label"><span class="control-label"><?php echo _l('group_by_task'); ?></span></label>
								<select name="group_by" id="group_by" class="selectpicker no-margin" data-width="100%">
									<option value="" selected><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="rel_name" <?php echo ($group_by!='' && $group_by=='rel_name'?'selected':'')?>><?php echo _l('task_related_to'); ?></option>
									<option value="rel_name_and_name" <?php echo ($group_by!='' && $group_by=='rel_name_and_name'?'selected':'')?>><?php echo _l('si_ts_task_related_to_and_name'); ?></option>
									<option value="name_and_rel_name" <?php echo ($group_by!='' && $group_by=='name_and_rel_name'?'selected':'')?>><?php echo _l('si_ts_task_name_and_related_to'); ?></option>
									<option value="task_name" <?php echo ($group_by!='' && $group_by=='task_name'?'selected':'')?>><?php echo _l('si_ts_filter_task_name'); ?></option>
									<option value="staff" <?php echo ($group_by!='' && $group_by=='staff'?'selected':'')?>><?php echo _l('staff'); ?></option>
									<option value="status" <?php echo ($group_by!='' && $group_by=='status'?'selected':'')?>><?php echo _l('task_status'); ?></option>
								</select>
							</div>
							<!--end group_by select-->
							<!--start hide_export_columns select -->
							<div class="col-md-2 border-right form-group">
								<label for="hide_columns" class="control-label">
									<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('si_ts_hide_export_columns_info');?>"></i>
									<span class="control-label"><?php echo _l('si_ts_hide_export_columns'); ?>
								</span></label>
								<select name="hide_columns[]" id="hide_columns" class="selectpicker no-margin" data-width="100%" multiple>
									<option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="name" <?php echo (in_array('name',$hide_columns)?'selected':'')?>><?php echo _l('tasks_dt_name'); ?></option>
									<option value="assigned" <?php echo (in_array('assigned',$hide_columns)?'selected':'')?>><?php echo _l('staff'); ?></option>
									<option value="staff_rate" <?php echo (in_array('staff_rate',$hide_columns)?'selected':'')?>><?php echo _l('staff_hourly_rate'); ?></option>
									<option value="status" <?php echo (in_array('status',$hide_columns)?'selected':'')?>><?php echo _l('task_status'); ?></option>
									<?php
									if($show_custom_fields){
									$custom_fields = get_custom_fields('tasks', ['show_on_table' => 1,]);
									foreach($custom_fields as $field)
										echo "<option value='$field[slug]' ".(in_array($field['slug'],$hide_columns)?'selected':'').">$field[name]</option>";
									}
									?>
									<option value="logged_time" <?php echo (in_array('logged_time',$hide_columns)?'selected':'')?>><?php echo _l('staff_stats_total_logged_time'); ?></option>
									
									
								</select>
							</div>
							<!--end hide_export_columns select-->
							<div class="col-md-2 form-group border-right" id="report-time">
								<label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
								<select class="selectpicker" name="report_months" id="report_months" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
									<option value="today"><?php echo _l('today'); ?></option>
									<option value="this_week"><?php echo _l('this_week'); ?></option>
									<option value="last_week"><?php echo _l('last_week'); ?></option>
									<option value="this_month"><?php echo _l('this_month'); ?></option>
									<option value="1"><?php echo _l('last_month'); ?></option>
									<option value="this_year"><?php echo _l('this_year'); ?></option>
									<option value="last_year"><?php echo _l('last_year'); ?></option>
									<option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
									<option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
									<option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
									<option value="custom"><?php echo _l('period_datepicker'); ?></option>
								</select>
								<?php
									if($report_months !== '')
									{
										$report_heading.=' for '._l('period_datepicker')." ";
										switch($report_months)
										{
											case 'today':$report_heading.=_d(date('d-m-Y'))." To "._d(date('d-m-Y'));break;
											case 'this_week':$report_heading.=_d(date('d-m-Y', strtotime('monday this week')))." To "._d(date('d-m-Y', strtotime('sunday this week')));break;
											case 'last_week':$report_heading.=_d(date('d-m-Y', strtotime('monday last week')))." To "._d(date('d-m-Y', strtotime('sunday last week')));break;
											case 'this_month':$report_heading.=_d(date('01-m-Y'))." To "._d(date('t-m-Y'));break;
											case '1'         :$report_heading.=_d(date('01-m-Y',strtotime('-1 month')))." To "._d(date('t-m-Y',strtotime('-1 month')));break;
											case 'this_year' :$report_heading.=_d(date('01-01-Y'))." To "._d(date('31-12-Y'));break;
											case 'last_year' :$report_heading.=_d(date('01-01-Y',strtotime('-1 year')))." To "._d(date('31-12-Y',strtotime('-1 year')));break;
											case '3'         :$report_heading.=_d(date('01-m-Y',strtotime('-2 month')))." To "._d(date('t-m-Y'));break;
											case '6'         :$report_heading.=_d(date('01-m-Y',strtotime('-5 month')))." To "._d(date('t-m-Y'));break;
											case '12'        :$report_heading.=_d(date('01-m-Y',strtotime('-11 month')))." To "._d(date('t-m-Y'));break;
											case 'custom'    :$report_heading.=$report_from." To ".$report_to;break;
											default          :$report_heading.='All Time';
										}
									}
								?>
							</div>
							<div id="date-range" class="col-md-4 hide mbot15">
								<div class="row">
									<div class="col-md-6">
										<label for="report_from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report_from" name="report_from" value="<?php echo htmlspecialchars($report_from ?? '');?>" autocomplete="off">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
									<div class="col-md-6 border-right">
										<label for="report_to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report_to" name="report_to" autocomplete="off">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!--end date time div-->
							<!--start tags -->
							<div class="col-md-2 border-right mbot15">
								<label for="rel_type" class="control-label"><?php echo _l('tags'); ?></label>		
									<?php echo render_select('tags[]',get_tags(),array('id','name'),'',$tags,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>false),array(),'no-mbot','',false);?>
							</div>
							<!--end tags-->
							<!--start hourly rate by select -->
							<div class="col-md-2 border-right form-group">
								<label for="hourly_rate_by" class="control-label"><span class="control-label"><?php echo _l('si_ts_hourly_date_by'); ?></span></label>
								<select name="hourly_rate_by" id="hourly_rate_by" class="selectpicker no-margin" data-width="100%" >
									<option value="staff" <?php echo ($hourly_rate_by=="staff"?'selected':'')?>><?php echo _l('staff'); ?></option>
									<option value="tasks" <?php echo ($hourly_rate_by=="tasks"?'selected':'')?>><?php echo _l('tasks'); ?></option>
								</select>
							</div>
							<!--end hourly rate by select-->
							<!--start save filter-->
							<div class="col-md-10">
								<div class="checklist relative">
									<div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="<?php echo _l('si_ts_save_filter_template'); ?>">
										<input type="checkbox" id="si_save_filter" name="save_filter" value="1" title="<?php echo _l('si_ts_save_filter_template'); ?>" <?php echo ($this->input->get('filter_id')?'checked':'')?>>
										<label for=""><span class="hide"><?php echo _l('si_ts_save_filter_template'); ?></span></label>
										<textarea id="si_ts_filter_name" name="filter_name" rows="1" placeholder="<?php echo _l('si_ts_filter_template_name'); ?>" <?php echo ($this->input->get('filter_id')?'':'disabled="disabled"')?> maxlength='100'><?php echo ($this->input->get('filter_id')?$saved_filter_name:'');?></textarea>
									</div>
								</div>
							</div>
							<!--end save filter-->
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
					<?php
					foreach($overview as $month =>$data){ if(count($data) == 0){continue;} 
					$months_total_hours_cols = array();
					$months_total_cost_cols = array();
					?>
						<h4 class="bold text-success"><?php if($group_by!=='staff') echo ($month); ?>
						<?php if(is_numeric($staff_id) && has_permission('tasks','','view')) { 
								echo get_staff_full_name($staff_id);
								echo ' ('._l('staff_hourly_rate').": ".app_format_money($this->staff_model->get($staff_id)->hourly_rate, $base_currency).')';
							}elseif($group_by!='' && $group_by=='staff'){
								echo get_staff_full_name($month);
								echo ' ('._l('staff_hourly_rate').": ".app_format_money($this->staff_model->get($month)->hourly_rate, $base_currency).')';
							}
						?>
						</h4>
						<table class="table tasks-overview dt-table scroll-responsive">
							<caption class="si_caption"><?php if($group_by!=='staff') echo ($month);echo ($report_heading);?></caption>
							<thead>
								<tr>
									<?php if (($group_by!=='rel_name_and_name' && $group_by!=='name_and_rel_name') || $month==''){?>
									<th class="<?php echo (in_array('name',$hide_columns)?'not-export':'')?>"><?php echo _l('tasks_dt_name'); ?></th>
									<?php }?>
									<?php if((is_admin() || has_permission('si_timesheet','','view')) && $staff_id==''){?>
									<th class="<?php echo (in_array('assigned',$hide_columns)?'not-export':'')?>"><?php echo _l('staff'); ?></th>
									<th class="<?php echo (in_array('staff_rate',$hide_columns)?'not-export':'')?>"><?php echo _l('staff_hourly_rate').' ('._l($hourly_rate_by).')'; ?></th>
									<?php }?>
									<th class="<?php echo (in_array('status',$hide_columns)?'not-export':'')?>"><?php echo _l('task_status'); ?></th>
									<?php
									if($show_custom_fields){
										$custom_fields = get_custom_fields('tasks', ['show_on_table' => 1,]);
										foreach($custom_fields as $field)
										{
											echo '<th class="'.(in_array($field['slug'],$hide_columns)?'not-export':'').'">'.$field['name'].'</th>';	
										}
									}	
									?>
									<th class="<?php echo (in_array('logged_time',$hide_columns)?'not-export':'')?>"><?php echo _l('staff_stats_total_logged_time') ; ?> </th>
									<?php foreach($months_cols as $key=>$cols)
											echo "<th>$cols</th>";
									?>
								</tr>
							</thead>
						<tbody>
							<?php
								foreach($data as $task){ ?>
								<tr>
								<?php if (($group_by!=='rel_name_and_name' && $group_by!=='name_and_rel_name') || $month==''){?>
									<td data-order="<?php echo htmlentities($task['name']); ?>"><a href="<?php echo admin_url('tasks/view/'.$task['task_id']); ?>" onclick="init_task_modal(<?php echo htmlspecialchars($task['task_id']); ?>); return false;"><?php echo htmlspecialchars($task['name']); ?></a>
									<?php
										if (!empty($task['rel_id']) && $group_by!='rel_name')
											echo '<br />'. _l('task_related_to').': <a class="text-muted" href="' . task_rel_link($task['rel_id'],$task['rel_type']) . '">' . task_rel_name($task['rel_name'],$task['rel_id'],$task['rel_type']) . '</a>';
									?>
									</td>
								<?php }?>
								<?php if((is_admin() || has_permission('si_timesheet','','view')) && $staff_id=='')//display staff only if multi staff task is showing
								{
									$staff_full_name = $task['staff_full_name'];
						
									$staffOutput	 = "<a data-toggle='tooltip' data-placement='bottom' data-title='" . $staff_full_name . "' href='" . admin_url('profile/' . $task['staff_id']) . "'>" . staff_profile_image($task['staff_id'], [
									"staff-profile-image-small",
								   ]) . "</a>";
						
									$staffOutput .= "<br/><span>" . $staff_full_name . "</span>";
									echo "<td>$staffOutput</td>";
									echo "<td>".app_format_money($task['hourly_rate'], $base_currency)."</td>";
								}?>
									<td><?php echo format_task_status($task['status']); ?></td>
									<?php
									if($show_custom_fields){
										foreach($custom_fields as $field)
										{
											$current_value = get_custom_field_value($task['id'], $field['id'], 'tasks', false);
											echo '<td>'.(($field['type']=='date_picker' || $field['type']=='date_picker_time') && $current_value!='' ? date('d-m-Y',strtotime($current_value)):$current_value).'</td>';
										}
									}	
									?>
									<td data-order="<?php echo htmlspecialchars($task['total_logged_time']); ?>">
										<span class="label label-default pull-left mright5" data-toggle="tooltip" data-title="<?php echo _l('staff_stats_total_logged_time'); ?>">
											<i class="fa fa-clock-o"></i> <?php echo seconds_to_time_format($task['total_logged_time']); ?>
										</span>
									</td>
									<?php foreach($months_cols as $month_key=>$cols){
											$col_val=(isset($task[$month_key])?"<span class='label label-default pull-left mright5' data-toggle='tooltip' data-title='".$cols."'><i class='fa fa-clock-o'></i> ".seconds_to_time_format($task[$month_key])."</span>":'');
											echo "<td>$col_val</td>";
											if(isset($months_total_hours_cols[$month_key])){
												$months_total_hours_cols[$month_key] += isset($task[$month_key])?$task[$month_key]:0;
												$months_total_cost_cols[$month_key] += isset($task[$month_key])?sec2qty($task[$month_key])*$task['hourly_rate']:0;
											}else{
												$months_total_hours_cols[$month_key] = isset($task[$month_key])?$task[$month_key]:0;
												$months_total_cost_cols[$month_key] = isset($task[$month_key])?sec2qty($task[$month_key])*$task['hourly_rate']:0;
											}	
										}
											
									?>
								</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<?php if (($group_by!=='rel_name_and_name' && $group_by!=='name_and_rel_name') || $month==''){?>
									<th></th>
									<?php }?>
									<?php if((is_admin() || has_permission('si_timesheet','','view')) && $staff_id==''){?>
									<th></th>
									<th></th>
									<?php }?>
									<th>
									<?php
										echo _l('si_ts_total_hours')."<br/><br/>";
										echo " ("._l('si_ts_total_cost').")" ;
											
									 ?>
									</th>
									<?php
									if($show_custom_fields){
										$custom_fields = get_custom_fields('tasks', ['show_on_table' => 1,]);
										foreach($custom_fields as $field)
										{
											echo '<th></th>';	
										}
									}	
									?>
									<th><?php  echo "<span class='label label-success pull-left mright5' data-toggle='tooltip' data-title='Total'><i class='fa fa-clock-o'></i> ".seconds_to_time_format(array_sum($months_total_hours_cols))."</span><br/><br/> (".app_format_money(array_sum($months_total_cost_cols), $base_currency).")"; ?></th>
									<?php foreach($months_total_hours_cols as $key=>$cols)
											echo "<th><span class='label label-primary pull-left mright5' data-toggle='tooltip' data-title='".$months_cols[$key]."'><i class='fa fa-clock-o'></i> ".seconds_to_time_format($cols)."</span><br/><br/> (".app_format_money($months_total_cost_cols[$key], $base_currency).")</th>";
									?>
								</tr>
							</tfoot>
						</table>
						<hr />
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script src="<?php echo module_dir_url('si_timesheet','assets/js/si_timesheet_calendar_filters.js'); ?>"></script>
<script>
(function($) {
"use strict";
<?php  if($report_months !== ''){ ?>
	$('#report_months').val("<?php echo htmlspecialchars($report_months ?? '');?>");
	$('#report_months').change();
<?php }
	if($report_from !== ''){ 
?>
	$('#report_from').val("<?php echo htmlspecialchars($report_from ?? '');?>");
<?php
	}
	if($report_to !== ''){ 
?>
	$('#report_to').val("<?php echo htmlspecialchars($report_to ?? '');?>");
<?php
	}
?>
})(jQuery);
</script>

