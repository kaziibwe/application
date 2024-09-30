<?php if (!empty($template_info)) { ?>

	<pre class="header_data_text hidden" data-text="<?php echo $template_info->header_data_text; ?>"><?php echo $template_info->header_data_text; ?></pre>
	<pre class="body_data hidden" data-text="<?php echo $template_info->body_data; ?>"><?php echo $template_info->body_data; ?></pre>
	<pre class="footer_data hidden" data-text="<?php echo $template_info->footer_data; ?>"><?php echo $template_info->footer_data; ?></pre>

	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-12">
					<div class="panel_s">
						<div class="panel-body">
							<div class="row">
								<div class="col-md-6"><b><?php echo _l("template_category") ?>:</b> <?php echo $template_info->category; ?></div>
								<div class="col-md-6"><b><?php echo _l("template_type") ?>:</b> <?php echo (empty($template_info->header_data_format)) ? "TEXT" : $template_info->header_data_format; ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if ($template_info->header_params_count > 0) { ?>
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<label for="request_url" class="control-label">
											<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('tootltip_template_headers'); ?>" data-original-title="" title=""></i>
											<b><?php echo _l('template_header_params'); ?></b>
										</label>
									</div>
								</div>
								<div class="row">
									<?php
									if (isset($template_info->header_params)) {
										$header_params = (array) (json_decode($template_info->header_params));
									}
									?>
									<?php for ($i = 1; $i <= $template_info->header_params_count; $i++) { ?>
										<div class="col-md-4 col-xs-6">
											<?php echo render_input('header_params[' . $i . '][key]', _l('key'), '{{' . $i . '}}', 'text', ['readonly' => 'readonly']); ?>
										</div>
										<div class="col-md-8 col-xs-6">
											<?php echo render_input('header_params[' . $i . '][value]', _l('value'), $header_params[$i]->value ?? '', 'text', [], [], '', 'header_param_text mentionable'); ?>
											<span style="display: none;" class="header_custom_choice_span" id="header_custom_choice_span_0"><i class="fa fa-times"></i></span>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ($template_info->body_params_count > 0) { ?>
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<label for="request_url" class="control-label">
											<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('tootltip_template_body'); ?>" data-original-title="" title=""></i>
											<b><?php echo _l('template_body_params'); ?></b>
										</label>
									</div>
								</div>
								<?php
								if (isset($template_info->body_params)) {
									$body_params = (array) (json_decode($template_info->body_params));
								}
								?>
								<div class="row">
									<?php for ($i = 1; $i <= $template_info->body_params_count; $i++) { ?>
										<div class="col-md-4 col-xs-6">
											<?php echo render_input('body_params[' . $i . '][key]', _l('key'), '{{' . $i . '}}', 'text', ['readonly' => 'readonly']); ?>
										</div>
										<div class="col-md-8 col-xs-6">
											<?php echo render_input('body_params[' . $i . '][value]', _l('value'), $body_params[$i]->value ?? '', 'text', [], [], '', 'body_param_text mentionable'); ?>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ($template_info->footer_params_count > 0) { ?>
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<label for="request_url" class="control-label">
											<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('tootltip_template_footer'); ?>" data-original-title="" title=""></i>
											<b><?php echo _l('template_footer_params'); ?></b>
										</label>
									</div>
								</div>
								<div class="row">
									<?php
									if (isset($template_info->footer_params)) {
										$footer_params = (array) (json_decode($template_info->footer_params));
									}
									?>
									<?php for ($i = 1; $i <= $template_info->footer_params_count; $i++) { ?>
										<div class="col-md-4 col-xs-6">
											<?php echo render_input('footer_params[' . $i . '][key]', _l('key'), '{{' . $i . '}}', 'text', ['readonly' => 'readonly']); ?>
										</div>
										<div class="col-md-8 col-xs-6">
											<?php echo render_input('footer_params[' . $i . '][value]', _l('value'), $footer_params[$i]->value ?? '', 'text', [], [], '', 'footer_param_text mentionable'); ?>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>

<?php } ?>