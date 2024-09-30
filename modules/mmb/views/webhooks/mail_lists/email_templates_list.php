<div class="col-md-12">
	<h4 class="bold well email-template-heading">
		<?php echo _l('webhook_email_templates'); ?>
		<?php if ($hasPermissionEdit) { ?>
			<a href="<?php echo admin_url('emails/disable_by_type/webhooks'); ?>" class="pull-right mleft5 mright25"><small><?php echo _l('disable_all'); ?></small></a>
			<a href="<?php echo admin_url('emails/enable_by_type/webhooks'); ?>" class="pull-right"><small><?php echo _l('enable_all'); ?></small></a>
		<?php } ?>

	</h4>
	<div class="table-responsive">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th><?php echo _l('email_templates_table_heading_name'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($webhooks as $webhooks) { ?>
					<tr>
						<td class="<?php if (0 == $webhooks['active']) {echo 'text-throught';} ?>">
							<a href="<?php echo admin_url('emails/email_template/'.$webhooks['emailtemplateid']); ?>"><?php echo $webhooks['name']; ?></a>
							<?php if (ENVIRONMENT !== 'production') { ?>
								<br/><small><?php echo $webhooks['slug']; ?></small>
							<?php } ?>
							<?php if ($hasPermissionEdit) { ?>
								<a href="<?php echo admin_url('emails/'.('1' == $webhooks['active'] ? 'disable/' : 'enable/').$webhooks['emailtemplateid']); ?>" class="pull-right"><small><?php echo _l(1 == $webhooks['active'] ? 'disable' : 'enable'); ?></small></a>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>