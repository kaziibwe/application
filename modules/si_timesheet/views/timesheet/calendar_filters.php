<?php defined('BASEPATH') or exit('No direct script access allowed'); 
$report_heading = '';
?>
<div id="si_ts_calendar_filters" style="<?php if(!$this->input->post('calendar_filters')){ echo 'display:none;'; } ?>">
	<?php echo form_open($this->uri->uri_string(). ($this->input->get('filter_id') ? '?filter_id='.$this->input->get('filter_id') : ''),array('id'=>'si_ts_calendar_filters_form')); ?>
	<?php echo form_hidden('calendar_filters',true); ?>
    <div class="row">
		<?php if(has_permission('si_timesheet','','view')){ ?>
		<div class="col-md-2 border-right">
			<label for="rel_type" class="control-label"><?php echo _l('staff'); ?></label>
			<?php echo render_select('member',$members,array('staffid',array('firstname','lastname')),'',$staff_id,array('data-none-selected-text'=>_l('all_staff_members')),array(),'no-margin'); ?>
		</div>
		<?php } ?>
		<!--start status-->
		<div class="col-md-2 border-right">
			<label for="status" class="control-label"><?php echo _l('task_status'); ?></label>		
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
		<!--end status-->
		<!--start tags -->
		<div class="col-md-2 border-right">
			<label for="tags" class="control-label"><?php echo _l('tags'); ?></label>		
				<?php 
				echo render_select('tags[]',get_tags(),array('id','name'),'',$tags,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>false),array(),'no-mbot','',false);?>
		</div>
		<!--end tags-->
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
		<!--start save filter-->
		<div class="col-md-8">
			<div class="checklist relative">
				<div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="<?php echo _l('si_ts_save_filter_template'); ?>">
					<input type="checkbox" id="si_save_filter" name="save_filter" value="1" title="<?php echo _l('si_ts_save_filter_template'); ?>" <?php echo ($this->input->get('filter_id')?'checked':'')?>>
					<label for=""><span class="hide"><?php echo _l('si_ts_save_filter_template'); ?></span></label>
					<textarea id="si_ts_filter_name" name="filter_name" rows="1" placeholder="<?php echo _l('si_ts_filter_template_name'); ?>" <?php echo ($this->input->get('filter_id')?'':'disabled="disabled"')?> maxlength='100'><?php echo ($this->input->get('filter_id')?$saved_filter_name:'');?></textarea>
				</div>
			</div>
		</div>
		<!--end save filter-->
    <div class="col-md-4 text-right">
		<a class="btn btn-default" href="<?php echo site_url($this->uri->uri_string()); ?>"><?php echo _l('clear'); ?></a>
		<button class="btn btn-success" type="submit"><?php echo _l('apply'); ?></button>
		<div class="btn-group pull-right mleft4 btn-with-tooltip-group" data-toggle="tooltip" data-title="<?php echo _l('si_ts_filter_templates'); ?>" data-original-title="" title="">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-list"></i>
			</button>
			<ul class="row dropdown-menu notifications width400">
			<?php
			if(!empty($filter_templates))
			{
				foreach($filter_templates as $row)
				{
					echo "<li><a href='?filter_id=$row[id]'>$row[filter_name]</a></li>";
				}
			}
			else
				echo '<li><a >'._l('si_ts_no_filter_template').'</a></li>';
			?>
			</ul>
		</div>
	</div>

</div>
<hr class="mbot15" />
<div class="clearfix"></div>
<?php echo form_close(); ?>
</div>
