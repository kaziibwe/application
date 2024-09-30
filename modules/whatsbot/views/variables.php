<?php if (!empty($template)) { ?>
    <?php if (!empty($template['header_data_format']) && $template['header_params_count'] > 0) { ?>
        <h4 class="tw-mt-0 tw-font-semibold tw-text-neutral-700"><?php echo _l('header'); ?></h4>
        <?php if ('TEXT' === $template['header_data_format']) { ?>
            <?php for ($i = 1; $i <= $template['header_params_count']; ++$i) { ?>
                <?php echo render_input('header_params[' . $i . '][value]', _l('variable') . ' ' . $i, $header_params->$i->value ?? '', 'text', ['autocomplete' => 'off'], [], '', 'header_param_text header_input header[' . $i . '] mentionable'); ?>
            <?php } ?>
        <?php } else { ?>
            <div class="alert alert-danger"><?php echo _l('currently_type_not_supported', $template['header_data_format']); ?></div>
        <?php } ?>
        <hr>
    <?php } ?>
    <?php $allowd_extension = wb_get_allowed_extension(); ?>
    <?php if (!empty($template['header_data_format']) && 'IMAGE' === $template['header_data_format']) { ?>
        <h4 class="tw-mt-0 tw-font-semibold tw-text-neutral-700"><?php echo _l('image'); ?></h4>
        <input type="hidden" id="maxFileSize" value="<?= $allowd_extension['image']['size'] ?>">
        <div class="view_campaign_image <?= (isset($campaign) && empty($campaign['filename'])) ? 'hide' : '' ?>">
            <?php if (isset($campaign)) : ?>
                <div class="row">
                    <div class="col-md-9">
                        <input type="hidden" id="image_url" value="<?= (!empty($campaign['filename'])) ? base_url(get_upload_path_by_type($campaign['is_bot'] == '1' ? 'template' : 'campaign') . $campaign['filename']) : ''; ?>">
                        <img src="<?= base_url(get_upload_path_by_type($campaign['is_bot'] == '1' ? 'template' : 'campaign') . $campaign['filename']); ?>" class="img img-responsive" height="70%" width="70%">
                    </div>
                    <div class="col-md-3 text-right">
                        <a href="<?= admin_url(WHATSBOT_MODULE . '/campaigns/delete_campaign_files/' . $campaign['id']) ?>"><i class="fa fa-remove text-danger"></i></a>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <div class="campaign_image <?= (isset($campaign) && !empty($campaign['filename'])) ? 'hide' : '' ?>">
            <label for="image" class="control-label">
                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="<?= _l('maximum_file_size_should_be') . $allowd_extension['image']['size'] . ' MB' ?>"></i>
                <?= _l('select_image') ?>
                <small class="text-muted">( <?= _l('allowed_file_types') . $allowd_extension['image']['extension'] ?> )</small>
            </label>
            <input type="file" name="image" id="image" accept="<?= $allowd_extension['image']['extension'] ?>" class="form-control header_image">
        </div>
        <hr>
    <?php } ?>
    <?php if (!empty($template['body_params_count']) && $template['body_params_count'] > 0) { ?>
        <h4 class="tw-mt-0 tw-font-semibold tw-text-neutral-700"><?php echo _l('body'); ?></h4>
        <?php for ($i = 1; $i <= $template['body_params_count']; ++$i) { ?>
            <?php echo render_input('body_params[' . $i . '][value]', _l('variable') . ' ' . $i, $body_params->$i->value ?? '', 'text', ['autocomplete' => 'off'], [], '', 'body_param_text body_input body[' . $i . '] mentionable'); ?>
        <?php } ?>
        <hr>
    <?php } ?>
    <?php if (!empty($template['footer_params_count']) && $template['footer_params_count'] > 0) { ?>
        <h4 class="tw-mt-0 tw-font-semibold tw-text-neutral-700"><?php echo _l('footer'); ?></h4>
        <?php for ($i = 1; $i <= $template['footer_params_count']; ++$i) { ?>
            <?php echo render_input('footer_params[' . $i . '][value]', _l('variable') . ' ' . $i, $footer_params->$i->value ?? '', 'text', ['autocomplete' => 'off'], [], '', 'footer_param_text footer_input footer[' . $i . '] mentionable'); ?>
        <?php } ?>
        <hr>
    <?php } ?>
<?php } ?>
