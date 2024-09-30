<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form'));
			if(isset($invoice)){
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<?php $this->load->view('bills/invoice_template'); ?>
			</div>

			<input type="hidden" name="bill" value="1">
			<?php echo form_close(); ?>
			<?php $this->load->view('admin/invoice_items/item'); ?>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
$(function(){
	validate_invoice_form();
	// Init accountacy currency symbol
	init_currency();
	// Project ajax search
	init_ajax_project_search_by_customer_id();
	// Maybe items ajax search
	init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');

	$('body').off('change', '.f_client_id select[name="clientid"]');
	$("body").on('change', '.select-bills-customer', customer_change_event);


});


function customer_change_event() {

	var val = $(this).val();
	var projectAjax = $('select[name="project_id"]');
	var clonedProjectsAjaxSearchSelect = projectAjax.html('').clone();
	var projectsWrapper = $('.projects-wrapper');
	projectAjax.selectpicker('destroy').remove();
	projectAjax = clonedProjectsAjaxSearchSelect;

	var currentInvoiceID = $("body").find('input[name="merge_current_invoice"]').val();
	currentInvoiceID = typeof(currentInvoiceID) == 'undefined' ? '' : currentInvoiceID;

	var projectAjax = $('select[name="project_id"]');

	if (!val) {
		$('#merge').empty();
		$('#expenses_to_bill').empty();
		$('#invoice_top_info').addClass('hide');
		projectsWrapper.addClass('hide');
		return false;
	}

	requestGetJSON('bills/client_change_data/' + val + '/' + currentInvoiceID).done(function(response) {
		$('#merge').html(response.merge_info);
		var $billExpenses = $('#expenses_to_bill');
		// Invoice from project, in invoice_template this is not shown
		$billExpenses.length === 0 ? response.expenses_bill_info = '' : $billExpenses.html(response.expenses_bill_info);
		((response.merge_info !== '' || response.expenses_bill_info !== '') ? $('#invoice_top_info').removeClass('hide') : $('#invoice_top_info').addClass('hide'));

		for (var f in billingAndShippingFields) {
			if (billingAndShippingFields[f].indexOf('billing') > -1) {
				if (billingAndShippingFields[f].indexOf('country') > -1) {
					$('select[name="' + billingAndShippingFields[f] + '"]').selectpicker('val', response['billing_shipping'][0][billingAndShippingFields[f]]);
				} else {
					if (billingAndShippingFields[f].indexOf('billing_street') > -1) {
						$('textarea[name="' + billingAndShippingFields[f] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[f]]);
					} else {
						$('input[name="' + billingAndShippingFields[f] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[f]]);
					}
				}
			}
		}

		if (!empty(response['billing_shipping'][0]['shipping_street'])) {
			$('input[name="include_shipping"]').prop("checked", true).change();
		}

		for (var fsd in billingAndShippingFields) {
			if (billingAndShippingFields[fsd].indexOf('shipping') > -1) {
				if (billingAndShippingFields[fsd].indexOf('country') > -1) {
					$('select[name="' + billingAndShippingFields[fsd] + '"]').selectpicker('val', response['billing_shipping'][0][billingAndShippingFields[fsd]]);
				} else {
					if (billingAndShippingFields[fsd].indexOf('shipping_street') > -1) {
						$('textarea[name="' + billingAndShippingFields[fsd] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[fsd]]);
					} else {
						$('input[name="' + billingAndShippingFields[fsd] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[fsd]]);
					}
				}
			}
		}

		init_billing_and_shipping_details();

		var client_currency = response['client_currency'];
		var s_currency = $("body").find('.accounting-template select[name="currency"]');
		client_currency = parseInt(client_currency);
		client_currency != 0 ? s_currency.val(client_currency) : s_currency.val(s_currency.data('base'));
		_init_tasks_billable_select(response['billable_tasks'], projectAjax.selectpicker('val'));
		response.customer_has_projects === true ? projectsWrapper.removeClass('hide') : projectsWrapper.addClass('hide');
		s_currency.selectpicker('refresh');
		init_currency();
	});
}


</script>
</body>
</html>
