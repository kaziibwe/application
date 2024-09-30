<?php
defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin"><?= _l('request_details'); ?></h4>
								<hr class="hr-panel-heading" />
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 box" >
								<?php if (!is_null($log_data)): ?>
									<table class="table table-striped table-condensed table-hover" style="overflow-x: hidden;width: auto;overflow: scroll;">
										<tr>
											<td><?= $log_data->request_method ?></td>
											<td><?= $log_data->request_url ?></td>
										</tr>
										<tr>
											<td><?= strtoupper(_l('invoice_dt_table_heading_date')); ?></td>
											<td><?= _d($log_data->recorded_at) . " (" . time_ago($log_data->recorded_at) . ")" ?></td>
										</tr>
										<tr>
											<td><?= _l('webhook_action'); ?></td>
											<td><?= $log_data->webhook_action ?></td>
										</tr>
										<tr>
											<td><?= _l('webhook_for'); ?></td>
											<td><?= $log_data->webhook_for ?></td>
										</tr>
									</table>
								<?php endif ?>
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
								<h4 class="no-margin"><?= _l('headers'); ?></h4>
								<hr class="hr-panel-heading" />
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 box">
								<?php if (!is_null($log_data)): ?>
									<table class="table table-striped table-condensed table-hover" style="overflow-x: hidden;width: auto;overflow: scroll;">
										<?php foreach (json_decode($log_data->request_header) as $key => $header) : ?>
											<tr>
												<td><?= $key ?></td>
												<td><?= $header ?></td>
											</tr>
										<?php endforeach ?>
									<?php endif ?>
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
								<h4 class="no-margin"><?= _l('raw_content'); ?></h4>
							</div>
							<div class="col-md-6">
								<?php if (!is_null($log_data)): ?>
									<span class="no-margin pull-right label label-info"><?= _l('format_type'); ?>: <?= $log_data->request_format; ?></span>
								<?php endif ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<div class="row">
							<div class="col-md-12" >
								<?php if (!is_null($log_data)): ?>
									<p>
										<?php if ((isset($log_data->request_body)) && ($log_data->request_format=="JSON")): ?>
										<pre><code class="language-json"><?php echo json_encode(json_decode($log_data->request_body), JSON_PRETTY_PRINT); ?></code></pre>
									<?php endif ?>

									<?php if ((isset($log_data->request_body)) && ($log_data->request_format=="FORM")): ?>
									<pre><code class="language-json"><?php print_r(json_decode($log_data->request_body)); ?></code></pre>
								<?php endif ?>
							</p>
						<?php endif ?>
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
						<h4 class="no-margin"><?= _l('Response'); ?></h4>
					</div>
					<div class="col-md-6 ">
						<?php if (!is_null($log_data)): ?>
							<span class="no-margin pull-right label label-info"><?= _l('response_code'); ?> : <?= $log_data->response_code ?></span>
						<?php endif ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<hr class="hr-panel-heading" />
				<div class="row">
					<div class="col-md-12 box">
						<?php if (!is_null($log_data)): ?>
							<p>
								<?php if ((isset($log_data->response_data)) && (isJson(html_entity_decode($log_data->response_data)))): ?>
								<pre><code class="language-json"><?php echo json_encode(json_decode(html_entity_decode($log_data->response_data)), JSON_PRETTY_PRINT); ?></code></pre>
							<?php endif ?>

							<?php if (isset($log_data->response_data) && isXml(html_entity_decode($log_data->response_data))) : ?>
							<pre><code class="language-xml"><?php  print_r($log_data->response_data); ?></code></pre>
						<?php endif ?>
					</p>
				<?php endif ?>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>