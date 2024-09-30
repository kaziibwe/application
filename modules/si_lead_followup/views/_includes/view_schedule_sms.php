<?php defined('BASEPATH') or exit('No direct script access allowed');?>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('si_lfs_name'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php echo htmlspecialchars($schedule['name']);?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('lead_status'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php echo ($this->leads_model->get_status($schedule['status'])->name);?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('lead_source'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php if($schedule['source'] > 0) echo ($this->leads_model->get_source($schedule['source'])->name);?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('contract_send_to'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php 
						$filter_by = _l('leads');
						if($schedule['filter_by'] == 'staff') $filter_by = _l('staff_members');
						echo $filter_by;?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('si_lfs_text'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php echo nl2br($schedule['content']);?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('si_lfs_schedule_days'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php echo htmlspecialchars($schedule['schedule_days']).' '._l('days');?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('si_lfs_schedule_hour'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php echo date('h A',strtotime('01-01-1970 '.$schedule['schedule_hour'].":00:00"));?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('task_created_at'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php echo _d($schedule['dateadded']);?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('task_created_by'); ?> :</p>
					</div>
					<div class="col-md-9">
						<a data-toggle="tooltip" data-title="<?php echo get_staff_full_name($schedule['staff_id']) ?>" href="<?php echo admin_url('profile/' . $schedule['staff_id'])?>">
						<?php echo staff_profile_image($schedule['staff_id'], ['staff-profile-image-small',])?>
						</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('si_lfs_schedule_last_executed'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php if(!is_null($schedule['last_executed'])) echo _d($schedule['last_executed']);?>
					</div>
				</div>
				<hr />
				<div class="row">
					<div class="col-md-12">
						<p class="bold"><?php 
						echo _l('si_lfs_schedule_executed_list_info',$filter_by);
						?>
						</p>
					</div>
					<div class="col-md-12">
						<?php if(!empty($contacts)){?>
						<table class=" no-mtop table table-hover table-bordered">
							<thead>
								<tr>
									<th><?php echo _l('the_number_sign')?></th>
									<th><?php echo _l('name')?></th>
									<?php if($schedule['filter_by']=='staff'){?>
									<th><?php echo _l('comment_string')?></th>
									<?php }?>
									<th><?php echo _l('view_date')?></th>
								</tr>	
							</thead>
							<tbody>
							<?php foreach($contacts as $contact){?>
								<tr>
									<td><?php echo $contact['rel_id'];?></td>
									<td><?php echo $contact['name'];?></td>
									<?php if($schedule['filter_by']=='staff'){?>
									<td><?php echo $contact['comment']?></td>
									<?php }?>
									<td><?php echo _d($contact['dateadded']);?></td>
								</tr>
							<?php }?>
							</tbody>
						</table>
						<?php }
							else
								echo _l('si_lfs_sent_error_message');	
						?>
						
					</div>
				</div>