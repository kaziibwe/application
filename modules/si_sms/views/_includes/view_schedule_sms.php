<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<?php 
$added_phone = '<i class="fa fa-check text-success"></i>';
$not_added_phone = '<i class="fa fa-close text-danger"></i>';
$send_type = get_option(SI_SMS_MODULE_NAME.'_send_to_customer');
?>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('si_sms_text'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php echo nl2br($schedule['content']);?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<p class="bold"><?php echo _l('si_sms_schedule_date'); ?> :</p>
					</div>
					<div class="col-md-9">
						<?php echo _d($schedule['schedule_date']);?>
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
				<hr />
				<div class="row">
					<div class="col-md-12">
						<p class="bold"><?php 
						$filter_by = _l('clients');
						if($schedule['filter_by'] == 'lead') $filter_by = _l('leads');
						if($schedule['filter_by'] == 'staff') $filter_by = _l('staff_members');
						echo $filter_by;
						?>
						<font class='text-danger pull-right'>(<?php echo _l('si_sms_schedule_pnone_info')?>)</font>
						</p>
					</div>
					<div class="col-md-12">
						<?php if(!empty($contacts)){?>
						<table class=" no-mtop table table-hover table-bordered">
							<thead>
								<tr>
									<th><?php echo _l('the_number_sign')?></th>
									<th><?php echo _l('name')?></th>
									<th><?php echo _l('client_phonenumber')?> ?</th>
								</tr>	
							</thead>
							<tbody>
							<?php foreach($contacts as $contact){?>
								<tr>
									<td><?php echo $contact['id']?></td>
									<td><?php echo $contact['name'].($schedule['filter_by']=='customer' && ($send_type=='all' || $send_type=='primary')? " <span class='text-info'>(".get_company_name($contact['userid']).")</span>":"");?></td>
									<td><?php echo ($contact['phonenumber'] !="" ? $added_phone : $not_added_phone);?></td>
								</tr>
							<?php }?>
							</tbody>
						</table>
						<?php }
							else
								echo _l('si_sms_sent_error_message');	
						?>
						
					</div>
				</div>

