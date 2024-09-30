<?php
defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
	table {
		table-layout: fixed;
	}
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin"><?php echo _l('request_details'); ?></h4>
								<hr class="hr-panel-heading" />
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 box">
								<?php if (null !== $log_data) { ?>
									<table class="table table-striped table-condensed table-hover">
										<tr>
											<td width="25%"><?php echo _l('action'); ?></td>
											<td width="75%"><?php echo $log_data->message_category; ?></td>
										</tr>
										<tr>
											<td><?php echo strtoupper(_l('invoice_dt_table_heading_date')); ?></td>
											<td><?php echo _d($log_data->recorded_at) . ' (' . time_ago($log_data->recorded_at) . ')'; ?></td>
										</tr>
										<tr>
											<td><?php echo _l('total_parameters'); ?></td>
											<td><?php echo $log_data->category_params; ?></td>
										</tr>
										<?php if($log_data->message_category == "Broadcast Message"){ ?>
											<tr>
												<td><?php echo _l('params'); ?></td>
												<td>
													<pre><code class="language-json"><?php echo json_encode(json_decode(html_entity_decode($log_data->merge_field_data)), \JSON_PRETTY_PRINT); ?></code></pre>
												</td>
											</tr>
										<?php } ?>
									</table>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin"><?php echo _l('headers'); ?></h4>
								<hr class="hr-panel-heading" />
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 box">
								<?php if (null !== $log_data) { ?>
									<table class="table table-striped table-condensed table-hover">
										<tr>
											<td><?php echo _l('phone_number_id'); ?></td>
											<td><?php echo $log_data->phone_number_id; ?></td>
										</tr>
										<tr>
											<td><?php echo _l('whatsapp_business_account_id'); ?></td>
											<td><?php echo $log_data->business_account_id; ?></td>
										</tr>
										<tr>
											<td><?php echo _l('whatsapp_access_token'); ?></td>
											<td><?php echo $log_data->access_token; ?></td>
										</tr>

									<?php } ?>
									</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<h4 class="no-margin"><?php echo _l('raw_content'); ?></h4>
							</div>
							<div class="col-md-6">
								<?php if (null !== $log_data) { ?>
									<span class="no-margin pull-right label label-info"><?php echo _l('format_type'); ?>: JSON</span>
								<?php } ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<div class="row">
							<div class="col-md-12">
								<?php if (null !== $log_data) { ?>
									<p>

									<pre><code class="language-json"><?php echo json_encode(json_decode(html_entity_decode($log_data->send_json)), \JSON_PRETTY_PRINT); ?></code></pre>

									</p>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<h4 class="no-margin"><?php echo _l('Response'); ?></h4>
							</div>
							<div class="col-md-6 ">
								<?php if (null !== $log_data) { ?>
									<span class="no-margin pull-right label label-info"><?php echo _l('response_code'); ?> : <?php echo $log_data->response_code; ?></span>
								<?php } ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<div class="row">
							<div class="col-md-12 box">
								<?php if (null !== $log_data) { ?>
									<p>
										<?php if ((isset($log_data->response_data)) && (isJson(html_entity_decode($log_data->response_data)))) { ?>
									<pre><code class="language-json"><?php echo json_encode(json_decode(html_entity_decode($log_data->response_data)), \JSON_PRETTY_PRINT); ?></code></pre>
								<?php } ?>
								</p>
							<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>