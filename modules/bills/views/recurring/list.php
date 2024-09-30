<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
				include_once(APPPATH.'modules/bills/views/recurring/filter_params.php');
				$this->load->view('recurring/list_template');
			?>
		</div>
	</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>var hidden_columns = [5, 7, 8, 9];</script>
<?php init_tail(); ?>
<script>
	$(function(){
		init_invoice();

		table_bills = $('table.table-bills');
		if (table_bills.length) {

			var Invoices_Estimates_ServerParams = {};
			// Invoices tables
			initDataTable(table_bills, admin_url + 'bills/table?recurring=1', 'undefined', 'undefined', Invoices_Estimates_ServerParams, !$('body').hasClass('recurring') ? [
				[3, 'desc'],
				[0, 'desc']
			] : [table_bills.find('th.next-recurring-date').index(), 'asc']);
		}
	});
</script>

</body>
</html>
