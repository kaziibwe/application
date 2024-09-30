<!-- horizontal tabs: start -->
<div class="horizontal-scrollable-tabs panel-full-width-tabs">
    <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
    <div class="horizontal-tabs">
        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
            <li role="presentation" class="active">
                <a href="#cover_page" aria-controls="cover_page" role="tab"
                    data-toggle="tab"><?php echo _l('cover_page'); ?></a>
            </li>
            <li role="presentation">
                <a href="#head" aria-controls="head" role="tab" data-toggle="tab"><?php echo _l('header'); ?></a>
            </li>
            <li role="presentation">
                <a href="#footer" aria-controls="footer" role="tab" data-toggle="tab"><?php echo _l('footer'); ?></a>
            </li>
            <li role="presentation">
                <a href="#closing_page" aria-controls="closing_page" role="tab"
                    data-toggle="tab"><?php echo _l('closing_page'); ?></a>
            </li>
            <?php if ($pdf_type !== 'contract'): ?>
                <li role="presentation">
                    <a href="#items_table" aria-controls="items_table" role="tab"
                        data-toggle="tab"><?php echo _l('items_table'); ?></a>
                </li>
            <?php endif ?>
        </ul>
    </div>
</div>
<!-- horizontal tabs: end -->
<div class="tab-content mtop15">
    <!-- cover page section: start -->
    <div role="tabpanel" class="tab-pane active" id="cover_page">
        <!-- cover image: start -->
        <?php $cover_image = getPdfOptions($pdf_type, 'cover_page', 'image'); ?>
        <div class="form-group">
            <label for="cover_image" class="control-label" style="width: 100%;">
                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                    data-title="<?php echo _l('cover_image_tooltip'); ?>"></i>
                <?php echo _l('cover_image'); ?>
                <small class="text-muted"><?php echo _l('allowed_extensions'); ?></small>
                <?php if ('' != $cover_image) {
                    echo anchor_popup(base_url('uploads/custom_pdf/' . $pdf_type . '/' . $cover_image . '?=' . time()), _l('preview_image'), ['window_name' => 'cover_image', 'class' => 'pull-right btn btn-xs btn-warning', 'style' => 'margin: 0 0 0 5px']);
                    echo anchor(admin_url('custom_pdf/remove_pdf_image/' . $pdf_type . '/' . 'cover_page'), _l('remove'), ['class' => 'pull-right _delete btn btn-xs btn-danger']);
                } ?>
            </label>
            <input type="file" name="settings[<?php echo $pdf_type; ?>][cover_page][image]" class="form-control"
                value="" data-toggle="tooltip"
                title="<?php echo _l('recommanded_dimensions') . getRecommendedDimensions($pdf_type, 'page'); ?>">
        </div>
        <!-- cover image: end -->
        <hr class="hr-panel-heading">
        <!-- cover page text: start -->
        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
            data-title="<?php echo _l('cover_page_text_tooltip'); ?>"></i>
        <?php echo render_textarea($pdf_type . '_cover_page_text', 'cover_page_text', getPdfOptions($pdf_type, 'cover_page', 'text'), [], [], '', 'tinymce'); ?>
        <!-- merge fields: start -->
        <p>
            <?php $merge_field_type = ($pdf_type == 'payment') ? 'invoice' : $pdf_type; ?>
            <?php foreach (array_column(get_instance()->app_merge_fields->get_by_name($merge_field_type), 'key') as $key => $value) { ?>
                <a href="#" class="tinymce-merge-field"
                    data-to="<?php echo $pdf_type; ?>_cover_page_text"><?php echo $value; ?></a>
            <?php } ?>
        </p>
        <!-- merge fields: end -->
        <!-- cover page text: end -->
        <hr class="hr-panel-heading">
        <div class="row">
            <div class="col-md-6">
                <!-- align from top: start -->
                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                    data-title="<?php echo _l('align_from_top_help', _l('cover_page_text')); ?>"></i>
                <?php echo render_input('settings[' . $pdf_type . '][cover_page][align_from_top]', 'align_from_top', getPdfOptions($pdf_type, 'cover_page', 'align_from_top'), 'number'); ?>
                <!-- align from top: end -->
            </div>
            <div class="col-md-6">
                <!-- align from left: start -->
                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                    data-title="<?php echo _l('align_from_left_help', _l('cover_page_text')); ?>"></i>
                <?php echo render_input('settings[' . $pdf_type . '][cover_page][align_from_left]', 'align_from_left', getPdfOptions($pdf_type, 'cover_page', 'align_from_left'), 'number'); ?>
                <!-- align from left: end -->
            </div>
        </div>
        <hr class="hr-panel-heading">
        <div class="pull-right">
            <a href="<?php echo admin_url(CUSTOM_PDF_MODULE . '/custom_pdf/pdf/' . $pdf_type . '/cover_page'); ?>"
                target="_blank" class="btn btn-warning"><?php echo _l('preview'); ?></a>
        </div>
        <!-- horizontal tabs: start -->
    </div>
    <!-- cover page section: end -->
    <!-- header section: start -->
    <div role="tabpanel" class="tab-pane" id="head">
        <!-- header image: start -->
        <?php $header_image = getPdfOptions($pdf_type, 'header', 'image'); ?>
        <div class="form-group">
            <label for="header_image" class="control-label" style="width: 100%;">
                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                    data-title="<?php echo _l('header_image_tooltip'); ?>"></i>
                <?php echo _l('header_image'); ?>
                <small class="text-muted"><?php echo _l('allowed_extensions'); ?></small>
                <?php if ('' != $header_image) {
                    echo anchor_popup(base_url('uploads/custom_pdf/' . $pdf_type . '/' . $header_image . '?=' . time()), _l('preview_image'), ['window_name' => 'header_image', 'class' => 'pull-right btn btn-xs btn-warning', 'style' => 'margin: 0 0 0 5px']);
                    echo anchor(admin_url('custom_pdf/remove_pdf_image/' . $pdf_type . '/' . 'header'), _l('remove'), ['class' => 'pull-right _delete btn btn-xs btn-danger']);
                } ?>
            </label>
            <input type="file" name="settings[<?php echo $pdf_type; ?>][header][image]" class="form-control" value=""
                data-toggle="tooltip"
                title="<?php echo _l('recommanded_dimensions') . getRecommendedDimensions($pdf_type, 'header'); ?>">
        </div>
        <!-- header image: end -->
        <hr class="hr-panel-heading">
        <!-- header text: start -->
        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
            data-title="<?php echo _l('header_text_tooltip'); ?>"></i>
        <?php echo render_textarea($pdf_type . '_header_text', 'header_text', getPdfOptions($pdf_type, 'header', 'text'), [], [], '', 'tinymce'); ?>
        <!-- header text: end -->
        <hr class="hr-panel-heading">
        <div class="pull-right">
            <a href="<?php echo admin_url(CUSTOM_PDF_MODULE . '/custom_pdf/pdf/' . $pdf_type . '/header'); ?>"
                target="_blank" class="btn btn-warning"><?php echo _l('preview'); ?></a>
        </div>
    </div>
    <!-- header section: end -->
    <!-- footer section: start -->
    <div role="tabpanel" class="tab-pane" id="footer">
        <!-- footer image: start -->
        <?php $footer_image = getPdfOptions($pdf_type, 'footer', 'image'); ?>
        <div class="form-group">
            <label for="footer_image" class="control-label" style="width: 100%;">
                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                    data-title="<?php echo _l('footer_image_tooltip'); ?>"></i>
                <?php echo _l('footer_image'); ?>
                <small class="text-muted"><?php echo _l('allowed_extensions'); ?></small>
                <?php if ('' != $footer_image) {
                    echo anchor_popup(base_url('uploads/custom_pdf/' . $pdf_type . '/' . $footer_image . '?=' . time()), _l('preview_image'), ['window_name' => 'footer_image', 'class' => 'pull-right btn btn-xs btn-warning', 'style' => 'margin: 0 0 0 5px']);
                    echo anchor(admin_url('custom_pdf/remove_pdf_image/' . $pdf_type . '/' . 'footer'), _l('remove'), ['class' => 'pull-right _delete btn btn-xs btn-danger']);
                } ?>
            </label>
            <input type="file" name="settings[<?php echo $pdf_type; ?>][footer][image]" class="form-control" value=""
                data-toggle="tooltip"
                title="<?php echo _l('recommanded_dimensions') . getRecommendedDimensions($pdf_type, 'header'); ?>">
        </div>
        <!-- footer image: end -->
        <hr class="hr-panel-heading">
        <!-- footer text: start -->
        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
            data-title="<?php echo _l('footer_text_tooltip'); ?>"></i>
        <?php echo render_textarea($pdf_type . '_footer_text', 'footer_text', getPdfOptions($pdf_type, 'footer', 'text'), [], [], '', 'tinymce'); ?>
        <!-- footer text: end -->
        <hr class="hr-panel-heading">
        <div class="pull-right">
            <a href="<?php echo admin_url(CUSTOM_PDF_MODULE . '/custom_pdf/pdf/' . $pdf_type . '/footer'); ?>"
                target="_blank" class="btn btn-warning"><?php echo _l('preview'); ?></a>
        </div>
    </div>
    <!-- footer section: end -->
    <!-- closing page section: start -->
    <div role="tabpanel" class="tab-pane" id="closing_page">
        <!-- closing image: start -->
        <?php $closing_image = getPdfOptions($pdf_type, 'closing_page', 'image'); ?>
        <div class="form-group">
            <label for="closing_page" class="control-label" style="width: 100%;">
                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                    data-title="<?php echo _l('closing_image_tooltip'); ?>"></i>
                <?php echo _l('closing_image'); ?>
                <small class="text-muted"><?php echo _l('allowed_extensions'); ?></small>
                <?php if ('' != $closing_image) {
                    echo anchor_popup(base_url('uploads/custom_pdf/' . $pdf_type . '/' . $closing_image . '?=' . time()), _l('preview_image'), ['window_name' => 'closing_page', 'class' => 'pull-right btn btn-xs btn-warning', 'style' => 'margin: 0 0 0 5px']);
                    echo anchor(admin_url('custom_pdf/remove_pdf_image/' . $pdf_type . '/' . 'closing_page'), _l('remove'), ['class' => 'pull-right _delete btn btn-xs btn-danger']);
                } ?>
            </label>
            <input type="file" name="settings[<?php echo $pdf_type; ?>][closing_page][image]" class="form-control"
                value="" data-toggle="tooltip"
                title="<?php echo _l('recommanded_dimensions') . getRecommendedDimensions($pdf_type, 'page'); ?>">
        </div>
        <!-- closing image: end -->
        <hr class="hr-panel-heading">
        <!-- closing page text: start -->
        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
            data-title="<?php echo _l('closing_page_text_tooltip'); ?>"></i>
        <?php echo render_textarea($pdf_type . '_closing_page_text', 'closing_page_text', getPdfOptions($pdf_type, 'closing_page', 'text'), [], [], '', 'tinymce'); ?>
        <!-- merge fields: start -->
        <?php foreach (array_column(get_instance()->app_merge_fields->get_by_name($merge_field_type), 'key') as $key => $value) { ?>
            <a href="#" class="tinymce-merge-field"
                data-to="<?php echo $pdf_type; ?>_closing_page_text"><?php echo $value; ?></a>
        <?php } ?>
        <!-- merge fields: end -->
        <!-- closing page text: end -->
        <hr class="hr-panel-heading">
        <div class="row">
            <div class="col-md-6">
                <!-- align from top: start -->
                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                    data-title="<?php echo _l('align_from_top_help', _l('closing_page_text')); ?>"></i>
                <?php echo render_input('settings[' . $pdf_type . '][closing_page][align_from_top]', 'align_from_top', getPdfOptions($pdf_type, 'closing_page', 'align_from_top'), 'number'); ?>
                <!-- align from top: end -->
            </div>
            <div class="col-md-6">
                <!-- align from left: start -->
                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                    data-title="<?php echo _l('align_from_left_help', _l('closing_page_text')); ?>"></i>
                <?php echo render_input('settings[' . $pdf_type . '][closing_page][align_from_left]', 'align_from_left', getPdfOptions($pdf_type, 'closing_page', 'align_from_left'), 'number'); ?>
                <!-- align from left: end -->
            </div>
        </div>
        <hr class="hr-panel-heading">
        <div class="pull-right">
            <a href="<?php echo admin_url(CUSTOM_PDF_MODULE . '/custom_pdf/pdf/' . $pdf_type . '/closing_page'); ?>"
                target="_blank" class="btn btn-warning"><?php echo _l('preview'); ?></a>
        </div>
    </div>
    <!-- cover page section: end -->
    <!-- items table section: start -->
    <div role="tabpanel" class="tab-pane" id="items_table">
        <h4><strong><?php echo _l('heading_section') ?></strong></h4>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="settings[<?php echo $pdf_type ?>][items_table][heading_row_bg_color]" class="control-label">
                    <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                        data-title="<?php echo _l('bg_color_tooltip') ?>"></i>
                    <?php echo _l('bg_color') ?>
                </label>
                <div class="input-group mbot15 colorpicker-input colorpicker-element"
                    data-target=".<?php echo $pdf_type . '_sample_table' ?> thead tr" data-css="background-color"
                    data-additional="">
                    <input type="text"
                        value="<?php echo getPdfOptions($pdf_type, 'items_table', 'heading_row_bg_color') ?>"
                        name="settings[<?php echo $pdf_type ?>][items_table][heading_row_bg_color]"
                        id="settings[<?php echo $pdf_type ?>][items_table][heading_row_bg_color]" class="form-control"
                        data-id="<?php echo $pdf_type ?>_heading_row_bg_color">
                    <span class="input-group-addon"><i></i></span>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="settings[<?php echo $pdf_type ?>][items_table][heading_row_text_color]"
                    class="control-label">
                    <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                        data-title="<?php echo _l('text_color_tooltip') ?>"></i>
                    <?php echo _l('text_color') ?>
                </label>
                <div class="input-group mbot15 colorpicker-input colorpicker-element"
                    data-target=".<?php echo $pdf_type . '_sample_table' ?> thead tr" data-css="color"
                    data-additional="">
                    <input type="text"
                        value="<?php echo getPdfOptions($pdf_type, 'items_table', 'heading_row_text_color') ?>"
                        name="settings[<?php echo $pdf_type ?>][items_table][heading_row_text_color]"
                        id="settings[<?php echo $pdf_type ?>][items_table][heading_row_text_color]" class="form-control"
                        data-id="<?php echo $pdf_type ?>_heading_row_text_color">
                    <span class="input-group-addon"><i></i></span>
                </div>
            </div>
        </div>
        <hr class="hr-panel-heading">
        <?php if ($pdf_type !== 'payment'): ?>
            <h4><strong><?php echo _l('odd_rows_section') ?></strong></h4>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="settings[<?php echo $pdf_type ?>][items_table][odd_rows_bg_color]" class="control-label">
                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                            data-title="<?php echo _l('bg_color_tooltip') ?>"></i>
                        <?php echo _l('bg_color') ?>
                    </label>
                    <div class="input-group mbot15 colorpicker-input colorpicker-element"
                        data-target=".<?php echo $pdf_type . '_sample_table' ?> tbody tr:first-child"
                        data-css="background-color" data-additional="">
                        <input type="text"
                            value="<?php echo getPdfOptions($pdf_type, 'items_table', 'odd_rows_bg_color') ?>"
                            name="settings[<?php echo $pdf_type ?>][items_table][odd_rows_bg_color]"
                            id="settings[<?php echo $pdf_type ?>][items_table][odd_rows_bg_color]" class="form-control"
                            data-id="<?php echo $pdf_type ?>_odd_rows_bg_color">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="settings[<?php echo $pdf_type ?>][items_table][odd_rows_text_color]" class="control-label">
                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                            data-title="<?php echo _l('text_color_tooltip') ?>"></i>
                        <?php echo _l('text_color') ?>
                    </label>
                    <div class="input-group mbot15 colorpicker-input colorpicker-element"
                        data-target=".<?php echo $pdf_type . '_sample_table' ?> tbody tr:first-child>td" data-css="color"
                        data-additional="">
                        <input type="text"
                            value="<?php echo getPdfOptions($pdf_type, 'items_table', 'odd_rows_text_color') ?>"
                            name="settings[<?php echo $pdf_type ?>][items_table][odd_rows_text_color]"
                            id="settings[<?php echo $pdf_type ?>][items_table][odd_rows_text_color]" class="form-control"
                            data-id="<?php echo $pdf_type ?>_odd_rows_text_color">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>
            <hr class="hr-panel-heading">
            <h4><strong><?php echo _l('even_rows_section') ?></strong></h4>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="settings[<?php echo $pdf_type ?>][items_table][even_rows_bg_color]" class="control-label">
                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                            data-title="<?php echo _l('bg_color_tooltip') ?>"></i>
                        <?php echo _l('bg_color') ?>
                    </label>
                    <div class="input-group mbot15 colorpicker-input colorpicker-element"
                        data-target=".<?php echo $pdf_type . '_sample_table' ?> tbody tr:nth-child(2)"
                        data-css="background-color" data-additional="">
                        <input type="text"
                            value="<?php echo getPdfOptions($pdf_type, 'items_table', 'even_rows_bg_color') ?>"
                            name="settings[<?php echo $pdf_type ?>][items_table][even_rows_bg_color]"
                            id="settings[<?php echo $pdf_type ?>][items_table][even_rows_bg_color]" class="form-control"
                            data-id="<?php echo $pdf_type ?>_even_rows_bg_color">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="settings[<?php echo $pdf_type ?>][items_table][even_rows_text_color]" class="control-label">
                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                            data-title="<?php echo _l('text_color_tooltip') ?>"></i>
                        <?php echo _l('text_color') ?>
                    </label>
                    <div class="input-group mbot15 colorpicker-input colorpicker-element"
                        data-target=".<?php echo $pdf_type . '_sample_table' ?> tbody tr:nth-child(2)>td" data-css="color"
                        data-additional="">
                        <input type="text"
                            value="<?php echo getPdfOptions($pdf_type, 'items_table', 'even_rows_text_color') ?>"
                            name="settings[<?php echo $pdf_type ?>][items_table][even_rows_text_color]"
                            id="settings[<?php echo $pdf_type ?>][items_table][even_rows_text_color]" class="form-control"
                            data-id="<?php echo $pdf_type ?>_even_rows_text_color">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>
            <hr class="hr-panel-heading">
            <h4><strong><?php echo _l('total_row_section') ?></strong></h4>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="settings[<?php echo $pdf_type ?>][items_table][total_row_bg_color]" class="control-label">
                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                            data-title="<?php echo _l('bg_color_tooltip') ?>"></i>
                        <?php echo _l('bg_color') ?>
                    </label>
                    <div class="input-group mbot15 colorpicker-input colorpicker-element"
                        data-target=".<?php echo $pdf_type . '_sample_table' ?> tbody tr:nth-child(3)"
                        data-css="background-color" data-additional="">
                        <input type="text"
                            value="<?php echo getPdfOptions($pdf_type, 'items_table', 'total_row_bg_color') ?>"
                            name="settings[<?php echo $pdf_type ?>][items_table][total_row_bg_color]"
                            id="settings[<?php echo $pdf_type ?>][items_table][total_row_bg_color]" class="form-control"
                            data-id="<?php echo $pdf_type ?>_total_row_bg_color">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="settings[<?php echo $pdf_type ?>][items_table][total_row_text_color]" class="control-label">
                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                            data-title="<?php echo _l('text_color_tooltip') ?>"></i>
                        <?php echo _l('text_color') ?>
                    </label>
                    <div class="input-group mbot15 colorpicker-input colorpicker-element"
                        data-target=".<?php echo $pdf_type . '_sample_table' ?> tbody tr:nth-child(3)>td" data-css="color"
                        data-additional="">
                        <input type="text"
                            value="<?php echo getPdfOptions($pdf_type, 'items_table', 'total_row_text_color') ?>"
                            name="settings[<?php echo $pdf_type ?>][items_table][total_row_text_color]"
                            id="settings[<?php echo $pdf_type ?>][items_table][total_row_text_color]" class="form-control"
                            data-id="<?php echo $pdf_type ?>_total_row_text_color">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>
            <hr class="hr-panel-heading">
            <table class="<?php echo $pdf_type . '_sample_table' ?> table table-sample_table">
                <thead>
                    <tr>
                        <th><?= _l('heading_row'); ?></th>
                        <th><?= _l('heading_row'); ?></th>
                        <th><?= _l('heading_row'); ?></th>
                        <th><?= _l('heading_row'); ?></th>
                        <th><?= _l('heading_row'); ?></th>
                        <th><?= _l('heading_row'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= _l('odd_row'); ?></td>
                        <td><?= _l('odd_row'); ?></td>
                        <td><?= _l('odd_row'); ?></td>
                        <td><?= _l('odd_row'); ?></td>
                        <td><?= _l('odd_row'); ?></td>
                        <td><?= _l('odd_row'); ?></td>
                    </tr>
                    <tr>
                        <td><?= _l('even_row'); ?></td>
                        <td><?= _l('even_row'); ?></td>
                        <td><?= _l('even_row'); ?></td>
                        <td><?= _l('even_row'); ?></td>
                        <td><?= _l('even_row'); ?></td>
                        <td><?= _l('even_row'); ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?= _l('total_row'); ?></td>
                        <td><?= _l('total_row'); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <h4><strong><?php echo _l('row_section') ?></strong></h4>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="settings[<?php echo $pdf_type ?>][items_table][row_bg_color]" class="control-label">
                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                            data-title="<?php echo _l('bg_color_tooltip') ?>"></i>
                        <?php echo _l('bg_color') ?>
                    </label>
                    <div class="input-group mbot15 colorpicker-input colorpicker-element"
                        data-target=".<?php echo $pdf_type . '_sample_table' ?> tbody tr>td" data-css="background-color"
                        data-additional="">
                        <input type="text" value="<?php echo getPdfOptions($pdf_type, 'items_table', 'row_bg_color') ?>"
                            name="settings[<?php echo $pdf_type ?>][items_table][row_bg_color]"
                            id="settings[<?php echo $pdf_type ?>][items_table][row_bg_color]" class="form-control"
                            data-id="table-other-bg">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="settings[<?php echo $pdf_type ?>][items_table][row_text_color]" class="control-label">
                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                            data-title="<?php echo _l('text_color_tooltip') ?>"></i>
                        <?php echo _l('text_color') ?>
                    </label>
                    <div class="input-group mbot15 colorpicker-input colorpicker-element"
                        data-target=".<?php echo $pdf_type . '_sample_table' ?> tbody tr>td" data-css="color"
                        data-additional="">
                        <input type="text" value="<?php echo getPdfOptions($pdf_type, 'items_table', 'row_text_color') ?>"
                            name="settings[<?php echo $pdf_type ?>][items_table][row_text_color]"
                            id="settings[<?php echo $pdf_type ?>][items_table][row_text_color]" class="form-control"
                            data-id="table-other-text">
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>
            <hr class="hr-panel-heading">
            <table class="<?php echo $pdf_type . '_sample_table' ?> table table-sample_table">
                <thead>
                    <tr>
                        <th><?= _l('heading_row'); ?></th>
                        <th><?= _l('heading_row'); ?></th>
                        <th><?= _l('heading_row'); ?></th>
                        <th><?= _l('heading_row'); ?></th>
                        <th><?= _l('heading_row'); ?></th>
                        <th><?= _l('heading_row'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= _l('other_row'); ?></td>
                        <td><?= _l('other_row'); ?></td>
                        <td><?= _l('other_row'); ?></td>
                        <td><?= _l('other_row'); ?></td>
                        <td><?= _l('other_row'); ?></td>
                        <td><?= _l('other_row'); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif ?>
    </div>
    <!-- items table section: end -->
</div>