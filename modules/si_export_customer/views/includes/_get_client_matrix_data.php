<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){
	$customer_tabs = filter_client_visible_tabs(get_customer_profile_tabs());
	$counter = 0;
?>
<div class="col-md-4 border-left">
<?php if (array_key_exists("statement",$customer_tabs)){?>
	<h4 class="no-margin bold"><?php echo _l('account_summary'); ?></h4>
	<div class="text-right">
		<table class="table statement-account-summary">
			<tbody>
				<tr>
					<td class="text-left"><?php echo _l('statement_beginning_balance'); ?>:</td>
					<td><?php echo app_format_money($statement['beginning_balance'], $statement['currency']); ?></td>
				</tr>
				<tr>
					<td class="text-left"><?php echo _l('invoiced_amount'); ?>:</td>
					<td><?php echo app_format_money($statement['invoiced_amount'], $statement['currency']); ?></td>
				</tr>
				<tr>
					<td class="text-left"><?php echo _l('amount_paid'); ?>:</td>
					<td><?php echo app_format_money($statement['amount_paid'], $statement['currency']); ?></td>
				</tr>
				</tbody>
				<tfoot>
				<tr>
					<td class="text-left"><b><?php echo _l('balance_due'); ?></b>:</td>
					<td><?php echo app_format_money($statement['balance_due'], $statement['currency']); ?></td>
				</tr>
			</tfoot>
		</table>
  	</div>
	<?php } ?>
</div>
<div class="clearfix"></div>
<hr/>
<p class="text-muted text-center"><?php echo _l('statement_from_to',array($from,$to)); ?></p>
<?php if(array_key_exists("projects",$customer_tabs)){ $counter++; ?>
<div class="col-md-4  border-right">
	<h4 class="no-margin bold"><?php echo _l('projects_summary'); ?></h4>
	<table class="table statement-account-summary">
		<tbody>
	<?php
	foreach($project_statuses as $status){
	?>
		<tr>
			<td class="text-left"><span style="color:<?php echo htmlspecialchars($status['color']); ?>">
	<?php echo htmlspecialchars($status['name']); ?>
	</span></td><td>:</td><td><?php echo htmlspecialchars($status['total']); ?></td>
		</tr>
	<?php } ?>
		</tbody>
	</table>
</div>
<?php } ?>
<?php if (array_key_exists("tasks",$customer_tabs)){ $counter++; ?>
<div class="col-md-4  border-right">
	<h4 class="no-margin bold"><?php echo _l('tasks_summary'); ?></h4>
	<table class="table statement-account-summary">
		<tbody>
			<?php foreach($tasks as $summary){?>
			<tr>
				<td><span style="color:<?php echo htmlspecialchars($summary['color']); ?>"><?php echo htmlspecialchars($summary['name']); ?></span></td>
				<td>:</td>
				<td><?php echo htmlspecialchars($summary['total_tasks']); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php } ?>
<?php if(array_key_exists("tickets",$customer_tabs)){ $counter++; ?>
<div class="col-md-4  border-right">
	<h4 class="no-margin bold"><?php echo _l('tickets_summary'); ?></h4>
	<table class="table statement-account-summary">
		<tbody>
			<?php foreach($tickets as $summary){?>
			<tr>
				<td><span style="color:<?php echo htmlspecialchars($summary['color']); ?>"><?php echo htmlspecialchars($summary['name']); ?></span></td>
				<td>:</td>
				<td><?php echo htmlspecialchars($summary['total_tickets']); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php if($counter > 0 && $counter%3==0){?>
<div class="clearfix"></div>
<hr/>
<?php } ?>
<?php } ?>
<?php if(array_key_exists("estimates",$customer_tabs)){ $counter++; ?>
<div class="col-md-4  border-right">
	<h4 class="no-margin bold"><?php echo _l('estimates'); ?></h4>
	<table class="table statement-account-summary">
		<tbody>
			<?php foreach($estimates as $key => $data){
			$class = estimate_status_color_class($data['status']);
			$name = estimate_status_by_id($data['status']);
			?>
			<tr>
				<td><span class="text-<?php echo htmlspecialchars($class); ?>"><?php echo htmlspecialchars($name); ?></span></td>
				<td>:</td>
				<td><?php echo app_format_money($data['total'], $data['currency_name']); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php if($counter > 0 && $counter%3==0){?>
<div class="clearfix"></div>
<hr/>

<?php } ?>
<?php } ?>
<?php if(array_key_exists("invoices",$customer_tabs)){ $counter++; ?>
<div class="col-md-4  border-right">
	<h4 class="no-margin bold"><?php echo _l('invoices'); ?></h4>
	<table class="table statement-account-summary">
		<tbody>
			<tr>
				<td><span class="text-warning"><?php echo _l('outstanding_invoices'); ?></span></td>
				<td>:</td>
				<td><?php echo app_format_money($invoices['due'], $invoices['currency']); ?></td>
			</tr>
			<tr>
				<td><span class="text-danger"><?php echo _l('past_due_invoices'); ?></span></td>
				<td>:</td>
				<td><?php echo app_format_money($invoices['overdue'], $invoices['currency']); ?></td>
			</tr>
			<tr>
				<td><span class="text-success"><?php echo _l('paid_invoices'); ?></span></td>
				<td>:</td>
				<td><?php echo app_format_money($invoices['paid'], $invoices['currency']); ?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php if($counter > 0 && $counter%3==0){?>
<div class="clearfix"></div>
<hr/>
<?php } ?>
<?php } ?>
<?php if(array_key_exists("expenses",$customer_tabs)){ $counter++; ?>
<div class="col-md-4  border-right">
	<h4 class="no-margin bold"><?php echo _l('expenses'); ?></h4>
	<table class="table statement-account-summary">
		<tbody>
			<tr>
				<td><span class="text-warning"><?php echo _l('expenses_total'); ?></span></td>
				<td>:</td>
				<td><?php echo htmlspecialchars($expenses['all']['total']); ?></td>
			</tr>
			<tr>
				<td><span class="text-success"><?php echo _l('expenses_list_billable'); ?></span></td>
				<td>:</td>
				<td><?php echo htmlspecialchars($expenses['billable']['total']); ?></td>
			</tr>
			<tr>
				<td><span class="text-warning"><?php echo _l('expenses_list_non_billable'); ?></span></td>
				<td>:</td>
				<td><?php echo htmlspecialchars($expenses['non_billable']['total']); ?></td>
			</tr>
			<tr>
				<td><span class="text-danger"><?php echo _l('expenses_list_unbilled'); ?></span></td>
				<td>:</td>
				<td><?php echo htmlspecialchars($expenses['unbilled']['total']); ?></td>
			</tr>
			<tr>
				<td><span class="text-success"><?php echo _l('expense_billed'); ?></span></td>
				<td>:</td>
				<td> <?php echo htmlspecialchars($expenses['billed']['total']); ?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php if($counter > 0 && $counter%3==0){?>
<div class="clearfix"></div>
<hr/>
<?php } ?>
<?php } ?>

<?php }?>