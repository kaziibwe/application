<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ 
$main_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_heading_text');
$services_heading = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_print_services_text');
$show_services = get_option(SI_EXPORT_CUSTOMER_MODULE_NAME.'_matrix_print_services');
?>
<h4 class="customer-profile-group-heading"><?php echo _l('si_customer_matrix');?></h4>
<div class="row">
	<div class="col-md-9">
	<?php $this->load->view(SI_EXPORT_CUSTOMER_MODULE_NAME.'/includes/_statement_period_select', ['onChange'=>'render_customer_matrix()']); ?>
	</div>
	<div class="col-md-3">
		<a id="btn_print" href="<?php echo base_url(SI_EXPORT_CUSTOMER_MODULE_NAME.'/export_customer_profile/'.$client->userid);?>" target="_blank" class="btn btn-default btn-with-tooltip mright5 pull-right" data-toggle="tooltip" title="<?php echo _l('print'); ?>" data-placement="bottom">
			<i class="fa fa-print"></i>
		</a>
		<a id="btn_pdf" href="<?php echo base_url(SI_EXPORT_CUSTOMER_MODULE_NAME.'/export_customer_profile/'.$client->userid.'?download=1');?>"  class="btn btn-default btn-with-tooltip mright5 pull-right" data-toggle="tooltip" title="<?php echo _l('view_pdf'); ?>" data-placement="bottom">
			<i class="fa fa-file-pdf-o"></i>
		</a>
	</div>
</div>
<h4 class="hr-panel-heading text-center"><?php echo htmlspecialchars($main_heading);?></h4>
<hr/>
<div class="col-md-8">
	<p class="control-label"><b><?php echo _l('name');?> :</b> <?php echo htmlspecialchars($client->company);?></p>
	<p class="control-label"><b><?php echo _l('client_address');?> :</b>
	<br /><?php echo htmlspecialchars($client->address);?>
	<br /><?php echo htmlspecialchars($client->city.($client->city!=''?'-':''). $client->zip);?>
	<br /><?php echo htmlspecialchars($client->state.($client->state!=''?',':''). get_country_name($client->country));?></p>
	<?php if(get_option('company_requires_vat_number_field') == 1 && $client->vat !== ''){?>
	<p class="control-label"><b><?php echo _l('client_vat_number');?> :</b> <?php echo htmlspecialchars($client->vat);?></p>	 
	<?php } ?>
	<?php if($client->phonenumber !== ''){?>
	<p class="control-label"><b><?php echo _l('client_phonenumber');?> :</b><?php echo htmlspecialchars($client->phonenumber);?></p>
	<?php } ?>
	<?php if($client->website !== ''){?>
	<p class="control-label"><b><?php echo _l('client_website');?> :</b> <span><a href="<?php echo maybe_add_http($client->website); ?>" target="_blank" tabindex="-1"><?php echo htmlspecialchars($client->website);?></a></span></p>
	<?php } ?>
	<?php if($show_services){
		$selected_items = get_client_services_details($client->userid);
		if(!empty($selected_items))
		{
			$services = array();
			foreach($selected_items as $row)
			{
				$services[] = $row['description'];
			}
	?>
	<p class="control-label"><b><?php echo htmlspecialchars($services_heading);?> :</b></p>
	<p><?php echo implode(', ',$services);?></p>
	<?php } }?>
</div>
<div id="matrix_html"></div>
<?php } ?>
<?php hooks()->add_action('app_admin_footer','parse_customer_matrix_html');
function parse_customer_matrix_html(){ global $client;?>
<script>
(function($) {
"use strict";
	render_customer_matrix();
})(jQuery);

	function render_customer_matrix(){
		var $statementPeriod = $('#range');
		var value = $statementPeriod.selectpicker('val');
		var period = new Array();
		var customer_id = '<?php echo htmlspecialchars($client->userid);?>';
		
		if(value != 'period'){
			period = JSON.parse(value);
		} else {
			period[0] = $('input[name="period-from"]').val();
			period[1] = $('input[name="period-to"]').val();

			if(period[0] == '' || period[1] == ''){
			return false;
			}
		}
		var statementUrl = '<?php echo base_url(SI_EXPORT_CUSTOMER_MODULE_NAME.'/get_customer_matrix');?>/'+customer_id;
		var statementUrlParams = new Array();
		statementUrlParams['from'] = period[0];
		statementUrlParams['to'] = period[1];
		statementUrl = buildUrl(statementUrl,statementUrlParams);

	$.get(statementUrl,function(response){
		$('#matrix_html').html(response.html);
		$('#btn_print').attr('href',buildUrl('<?php echo base_url(SI_EXPORT_CUSTOMER_MODULE_NAME.'/export_customer_matrix/');?>'+customer_id,statementUrlParams));
		statementUrlParams['download'] = true;
		$('#btn_pdf').attr('href',buildUrl('<?php echo base_url(SI_EXPORT_CUSTOMER_MODULE_NAME.'/export_customer_matrix/');?>'+customer_id,statementUrlParams));
	},'json').fail(function(response){
		alert_float('danger',response.responseText);
	});
}
</script>
<?php } ?>

