<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			include_once(APPPATH.'views/admin/invoices/filter_params.php');
			$this->load->view('list_template');
			?>
		</div>
	</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>var hidden_columns = [2,6,7,8];</script>
<?php init_tail(); ?>
<script>
	$(function(){


		table_bills = $('table.table-bills');
		if (table_bills.length) {

			var Invoices_Estimates_ServerParams = {};

			var Invoices_Estimates_Filter = $('._hidden_inputs._filters input');

			$.each(Invoices_Estimates_Filter, function() {
				Invoices_Estimates_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
			});

			// Invoices tables
			initDataTable(table_bills, (admin_url + 'bills/table' + ($('body').hasClass('recurring') ? '?recurring=1' : '')), 'undefined', 'undefined', Invoices_Estimates_ServerParams, !$('body').hasClass('recurring') ? [
				[3, 'desc'],
				[0, 'desc']
			] : [table_bills.find('th.next-recurring-date').index(), 'asc']);
		}


		$("body").on('click', '.invoices-total', function(e) {
			init_bills_total()
		});


	init_bill();

	init_bills_total()
	});

	function init_bill(id) {
	    load_small_table_item(id, '#invoice', 'invoiceid', 'bills/get_invoice_data_ajax', '.table-bills');
	}

	function init_bills_total(manual) {

		if ($('#invoices_total').length === 0) { return; }
		var _inv_total_inline = $('.invoices-total-inline');
		var _inv_total_href_manual = $('.invoices-total');

		if ($("body").hasClass('invoices-total-manual') && typeof(manual) == 'undefined' &&
			!_inv_total_href_manual.hasClass('initialized')) {
			return;
		}

		if (_inv_total_inline.length > 0 && _inv_total_href_manual.hasClass('initialized')) {
			// On the next request won't be inline in case of currency change
			// Used on dashboard
			_inv_total_inline.removeClass('invoices-total-inline');
			return;
		}

		_inv_total_href_manual.addClass('initialized');
		var _years = $("body").find('select[name="invoices_total_years"]').selectpicker('val');
		var years = [];
		$.each(_years, function(i, _y) {
			if (_y !== '') { years.push(_y); }
		});

		var currency = $("body").find('select[name="total_currency"]').val();
		var data = {
			currency: currency,
			years: years,
			init_total: true,
		};

		var project_id = $('input[name="project_id"]').val();
		var customer_id = $('.customer_profile input[name="userid"]').val();
		if (typeof(project_id) != 'undefined') {
			data.project_id = project_id;
		} else if (typeof(customer_id) != 'undefined') {
			data.customer_id = customer_id;
		}
		$.post(admin_url + 'bills/get_invoices_total', data).done(function(response) {
			$('#invoices_total').html(response);
		});
	}
</script>


</body>
</html>
