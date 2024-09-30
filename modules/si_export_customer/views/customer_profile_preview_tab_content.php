<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ 
$main_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_heading_text');
$custom_fields_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_custom_fields_text');
?>
<div role="tabpanel" class="tab-pane" id="customer_preview">
	<div class="col-md-12">
		<a href="<?php echo base_url(SI_EXPORT_CUSTOMER_MODULE_NAME.'/export_customer_profile/'.$client->userid);?>" target="_blank" class="btn btn-default btn-with-tooltip mright5 pull-right" data-toggle="tooltip" title="<?php echo _l('print'); ?>" data-placement="bottom">
			<i class="fa fa-print"></i>
		</a>
		<a href="<?php echo base_url(SI_EXPORT_CUSTOMER_MODULE_NAME.'/export_customer_profile/'.$client->userid.'?download=1');?>"  class="btn btn-default btn-with-tooltip mright5 pull-right" data-toggle="tooltip" title="<?php echo _l('view_pdf'); ?>" data-placement="bottom">
			<i class="fa fa-file-pdf-o"></i>
		</a>
	</div>
	<h4 class="hr-panel-heading text-center"><?php echo htmlspecialchars($main_heading);?></h4>
	<hr/>
	<div class="col-md-12">
		<div class="col-md-6">
			<p class="control-label"><b><?php echo _l('name');?> :</b> <?php echo htmlspecialchars($client->company);?></p>
			<p class="control-label"><b><?php echo _l('client_address');?> :</b>
			<br /> <?php echo htmlspecialchars_decode(htmlspecialchars($client->address));?>
			<br /><?php echo htmlspecialchars($client->city);?> - <?php echo htmlspecialchars($client->zip);?>
			<br /><?php echo htmlspecialchars($client->state);?>, <?php echo get_country_name($client->country);?></p>
			<?php if(get_option('company_requires_vat_number_field') == 1){?>
			<p class="control-label"><b><?php echo _l('client_vat_number');?> :</b> <?php echo htmlspecialchars($client->vat);?></p>	 
			<?php } ?>
			<?php if($client->phonenumber !== ''){?>
			<p class="control-label"><b><?php echo _l('client_phonenumber');?> :</b><?php echo htmlspecialchars($client->phonenumber);?></p>
			<?php } ?>
			<p class="control-label"><b><?php echo _l('client_website');?> :</b> <span><a href="<?php echo maybe_add_http($client->website); ?>" target="_blank" tabindex="-1"><?php echo htmlspecialchars($client->website);?></a></span></p>
		</div>
		<div class="col-md-6 text-right">
			<?php $selected = array();
			$customer_groups = $this->clients_model->get_customer_groups($client->userid);
			if(isset($customer_groups)){
				foreach($customer_groups as $group){
					array_push($selected,$group['groupid']);
				}
			}
			$customer_groups_name = array();
			if(!empty($groups))
			{
				foreach($groups as $group)
				{
					if(in_array($group['id'],$selected))
						$customer_groups_name[] = $group['name'];
				}
			}
			?>
			<p class="control-label"><?php echo (!empty($customer_groups_name)?implode(', ',$customer_groups_name):'');?> : <b><?php echo _l('customer_groups');?></b></p>
			<?php if(!empty($contact))
			{?>
				<p class="control-label"><b><?php echo ucwords(_l('si_customer_primary_contact'))?> :</b></p>
				<p class="control-label">
				<?php 
					echo  htmlspecialchars($contact->firstname.' '.$contact->lastname);
					if(!empty($contact->phonenumber)) echo '<br/>'.$contact->phonenumber;
					if(!empty($contact->email)) echo '<br/>'.$contact->email;
			}?>	
			</p><br/>
		</div>
	</div>	
	<div class="col-md-12">
		<div class="col-md-12">
			<p class="control-label"><b><?php echo _l('billing_address'); ?> : </b>
			<?php echo htmlspecialchars($client->billing_street.", "); ?>
			<?php echo htmlspecialchars($client->billing_city." - ".$client->billing_zip.", "); ?>
			<?php echo htmlspecialchars($client->billing_state.", ".get_country_name($client->billing_country)); ?>
			</p>
			<p class="control-label"><b><?php echo _l('shipping_address'); ?> : </b>
			<?php echo htmlspecialchars($client->shipping_street.", "); ?>
			<?php echo htmlspecialchars($client->shipping_city." - ".$client->shipping_zip.", "); ?>
			<?php echo htmlspecialchars($client->shipping_state.", ".get_country_name($client->shipping_country)); ?>
			</p>
		</div>
	</div>
	<div class="clearfix"></div>
	<hr />
	<div class="col-md-12">
		<h5 class="hr-panel-heading"><?php echo htmlspecialchars($custom_fields_heading);?></h5>
		<?php
		if(total_rows(db_prefix().'customfields',array('fieldto'=>'customers','active'=>1)) > 0 ){  
			$custom_fields = get_custom_fields('customers');
			foreach($custom_fields as $field)
			{
				if(is_admin() || !$field['only_admin'])
				{
					$current_value = get_custom_field_value($client->userid, $field['id'], 'customers', false);
					echo "<div class='col-md-".$field['bs_column']."'><p> <b>".$field['name']."</b><br>".$current_value."</p></div>";
				}
			}
		}?>
	</div>
	<div class="clearfix"></div>
	<hr />
	
	<div class="col-md-12">
		<h4 class="hr-panel-heading"><?php echo _l('si_customer_file_attachment_info')." <a href='".admin_url('clients/client/'.$client->userid.'?group=attachments')."' target='_blank'>". _l('customer_attachments')."</a>";?>
		<br/><small><?php echo _l('si_customer_file_attachment_note');?></small></h4>
		<div class="col-md-6">
			<?php
			$client_kyc_details = get_client_kyc_details($client->userid);
			$selected_profile_file = (!empty($client_kyc_details)?$client_kyc_details['profile_file_id']:'');
			echo render_select('si_export_customer_profile_file', $files, array('id','file_name'),'si_customer_profile_logo', $selected_profile_file); ?>
		</div>
		<div class="col-md-6">
			<?php
			$selected_files = (!empty($client_kyc_details)?unserialize($client_kyc_details['files_id']):array());
			echo render_select('si_export_customer_files[]',$files,array('id','file_name'),'si_customer_file_attachment',$selected_files,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<hr />
	<div class="col-md-12">
		<div class="col-md-12">
			<?php
			$selected_items = get_client_services_list($client->userid);
			echo render_select('si_export_customer_items[]',$items,array('itemid','description'),'si_customer_items',$selected_items,array('multiple'=>true,'data-actions-box'=>true),array('group_id'),'','',false); ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<hr />
</div>
<?php } ?>