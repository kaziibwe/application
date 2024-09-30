<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">


			<div class="alert alert-warning" font-medium="">
				<h4><b><?php echo  htmlspecialchars(_l('warning')); ?></b></h4>
				<hr class="hr-10">
				<p>
					<b><?php echo htmlspecialchars(_l('quickBooks_expense_warning')); ?>
				</p>
				<?php
				if (get_option('xero_company_country') !== substr(get_base_currency()->name, 0, 2) && $is_qb_connected != 'no') { ?>
					<hr class="hr-10">
					<p>
						<b><?php echo htmlspecialchars(_l('quickBooks_currency_warning')); ?>
					</p>
				<?php } ?>
			</div>

		</div>
		<div class="row">
			<div class="panel_s">
				<div class="panel-body">
					<div class="col-md-12">
						<h2><?php echo htmlspecialchars(_l('quickbook_heading')); ?></h2>
						<?php

						if (get_option('quickbook_client_id') != '' && get_option('quickbook_client_secret')) {
							if ($is_qb_connected == 'no') {
								echo '<code> <ipp:connectToIntuit onclick="connect_quickbook()"></ipp:connectToIntuit></code>';
							} else {
								echo '<p style="color: #006600">Sucessfully connected...</p>';
								echo '<button class="" onclick="disconnect_qb();">Disconnect</button>';
							}
						}

						?>
						<hr />
						<?php echo form_open('admin/quickbooks/store'); ?>
						<?php echo render_input('quickbook_client_id', 'quickbook_client_id', get_option('quickbook_client_id')); ?>
						<hr />
						<?php echo render_input('quickbook_client_secret', 'quickbook_client_secret', get_option('quickbook_client_secret')); ?>
						<hr />

						<?php echo render_input('quickbook_redirect_uri', 'quickbook_redirect_uri', site_url('/admin/quickbooks/check_auth_quickbook')); ?>
						<hr />
						<?php echo render_input('SalesTaxRate0', 'SalesTaxRate0_instructions', 'SalesTaxRate0'); ?>
						<hr />
						<?php echo render_input('ExpensestaxRate0', 'ExpensesTaxRate0_instructions', 'ExpensesTaxRate0'); ?>
						<hr />
						<?php echo render_yes_no_option('is_quickbooks_app_in_production_mode', 'is_quickbooks_app_in_production_mode', '', 'Production', 'Development', 'production', 'development'); ?>
						<hr />
						<?php echo render_input('quickbook_scope', '', 'com.intuit.quickbooks.accounting openid profile email phone address', 'hidden'); ?>
						<?php echo render_input('quickbook_response_type', '', 'code', 'hidden'); ?>

						<?php echo render_input('quickbook_state', '', uniqid(), 'hidden'); ?>

						<input type="submit" value="Submit" onclick="submit_data()" />

						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere-1.3.3.js"></script>
<script type="text/javascript">
	function submit_data() {
		var client_id = document.getElementById('quickbook_client_id').value;

		var client_secret = document.getElementById('quickbook_client_secret').value;

		var scope = document.getElementById('quickbook_scope').value;

		var redirect_uri = document.getElementById('quickbook_redirect_uri').value;

		var response_type = document.getElementById('quickbook_response_type').value;

		var state = document.getElementById('quickbook_state').value;

		if (client_secret == '') {

			alert('Please enter client secret id of your quickbooks app');

			return false;

		} else if (client_id == '') {

			alert('Please enter client id of your quickbooks app');

			return false;

		}

	}

	function connect_quickbook() {

		var client_id = document.getElementById('quickbook_client_id').value;

		var client_secret = document.getElementById('quickbook_client_secret').value;

		var scope = document.getElementById('quickbook_scope').value;

		var redirect_uri = document.getElementById('quickbook_redirect_uri').value;

		var response_type = document.getElementById('quickbook_response_type').value;

		var state = document.getElementById('quickbook_state').value;



		var url = 'https://appcenter.intuit.com/connect/oauth2' + '?' + 'client_id=' + client_id + '&' + 'cliend_secret=' + client_secret + '&' + 'scope=' + scope + '&' + 'redirect_uri=' + redirect_uri + '&' + 'response_type=' + response_type + '&' + 'state=' + state;

		console.log(url);

		window.open(url, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=600,height=600");



	}

	function reconnect() {
		window.location.href = "<?php echo site_url('admin/quickbooks/regenerate_token') ?>";
	}

	function disconnect_qb() {
		window.location.href = "<?php echo site_url('admin/quickbooks/disconnect_qb') ?>";
	}
</script>
<style>
	#quickbook_redirect_uri {

		pointer-events: none;

		cursor: not-allowed;

	}

	#SalesTaxRate0 {

		pointer-events: none;

		cursor: not-allowed;

	}

	#ExpensestaxRate0 {

		pointer-events: none;

		cursor: not-allowed;

	}
</style>



<?php init_tail(); ?>