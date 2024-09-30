<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div role="tabpanel" class="tab-pane" id="delivery_notes">
    <div class="form-group">
        <label class="control-label" for="delivery_note_prefix"><?php echo _l('settings_sales_delivery_note_prefix'); ?></label>
        <input type="text" name="settings[delivery_note_prefix]" class="form-control" value="<?php echo get_option('delivery_note_prefix'); ?>">
    </div>
    <hr />
    <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="<?php echo _l('settings_sales_next_delivery_note_number_tooltip'); ?>"></i>
    <?php echo render_input('settings[next_delivery_note_number]', 'settings_sales_next_delivery_note_number', get_option('next_delivery_note_number'), 'number', ['min' => 1]); ?>
    <hr />

    <?php render_yes_no_option('delete_only_on_last_delivery_note', 'settings_delete_only_on_last_delivery_note'); ?>
    <hr />
    <?php render_yes_no_option('delivery_note_number_decrement_on_delete', 'settings_sales_decrement_delivery_note_number_on_delete', 'settings_sales_decrement_delivery_note_number_on_delete_tooltip'); ?>
    <hr />
    <?php echo render_yes_no_option('allow_staff_view_delivery_notes_assigned', 'allow_staff_view_delivery_notes_assigned'); ?>
    <hr />

    <?php render_yes_no_option('view_delivery_note_only_logged_in', 'settings_sales_require_client_logged_in_to_view_delivery_note'); ?>
    <hr />
    <?php render_yes_no_option('show_sale_agent_on_delivery_notes', 'settings_show_sale_agent_on_delivery_notes'); ?>
    <hr />
    <?php render_yes_no_option('show_project_on_delivery_note', 'show_project_on_delivery_note'); ?>
    <hr />
    <?php render_yes_no_option('show_delivery_note_status_widget_on_dashboard', 'show_delivery_note_status_widget_on_dashboard'); ?>
    <hr />
    <?php render_yes_no_option('exclude_delivery_note_from_client_area_with_waiting_status', 'settings_exclude_delivery_note_from_client_area_with_waiting_status'); ?>
    <hr />
    <div class="form-group">
        <label for="delivery_note_number_format" class="control-label clearfix"><?php echo _l('settings_sales_delivery_note_number_format'); ?></label>
        <div class="radio radio-primary radio-inline">
            <input type="radio" name="settings[delivery_note_number_format]" value="1" id="e_number_based" <?php if (get_option('delivery_note_number_format') == '1') {
                                                                                                                echo 'checked';
                                                                                                            } ?>>
            <label for="e_number_based"><?php echo _l('settings_sales_delivery_note_number_format_number_based'); ?></label>
        </div>
        <div class="radio radio-primary radio-inline">
            <input type="radio" name="settings[delivery_note_number_format]" value="2" id="e_year_based" <?php if (get_option('delivery_note_number_format') == '2') {
                                                                                                                echo 'checked';
                                                                                                            } ?>>
            <label for="e_year_based"><?php echo _l('settings_sales_delivery_note_number_format_year_based'); ?>
                (YYYY/000001)</label>
        </div>
        <div class="radio radio-primary radio-inline">
            <input type="radio" name="settings[delivery_note_number_format]" value="3" id="e_short_year_based" <?php if (get_option('delivery_note_number_format') == '3') {
                                                                                                                    echo 'checked';
                                                                                                                } ?>>
            <label for="e_short_year_based">000001-YY</label>
        </div>
        <div class="radio radio-primary radio-inline">
            <input type="radio" name="settings[delivery_note_number_format]" value="4" id="e_year_month_based" <?php if (get_option('delivery_note_number_format') == '4') {
                                                                                                                    echo 'checked';
                                                                                                                } ?>>
            <label for="e_year_month_based">000001/MM/YYYY</label>
        </div>
        <hr />
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php echo render_input('settings[delivery_notes_pipeline_limit]', 'pipeline_limit_status', get_option('delivery_notes_pipeline_limit')); ?>
        </div>
        <div class="col-md-7">
            <label for="default_proposals_pipeline_sort" class="control-label"><?php echo _l('default_pipeline_sort'); ?></label>
            <select name="settings[default_delivery_notes_pipeline_sort]" id="default_delivery_notes_pipeline_sort" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                <option value="datecreated" <?php if (get_option('default_delivery_notes_pipeline_sort') == 'datecreated') {
                                                echo 'selected';
                                            } ?>><?php echo _l('delivery_notes_sort_datecreated'); ?></option>
                <option value="date" <?php if (get_option('default_delivery_notes_pipeline_sort') == 'date') {
                                            echo 'selected';
                                        } ?>><?php echo _l('delivery_notes_sort_delivery_note_date'); ?></option>
                <option value="pipeline_order" <?php if (get_option('default_delivery_notes_pipeline_sort') == 'pipeline_order') {
                                                    echo 'selected';
                                                } ?>><?php echo _l('delivery_notes_sort_pipeline'); ?></option>
            </select>
        </div>
        <div class="col-md-5">
            <div class="mtop30 text-right">
                <div class="radio radio-inline radio-primary">
                    <input type="radio" id="k_desc_delivery_note" name="settings[default_delivery_notes_pipeline_sort_type]" value="asc" <?php if (get_option('default_delivery_notes_pipeline_sort_type') == 'asc') {
                                                                                                                                                echo 'checked';
                                                                                                                                            } ?>>
                    <label for="k_desc_delivery_note"><?php echo _l('order_ascending'); ?></label>
                </div>
                <div class="radio radio-inline radio-primary">
                    <input type="radio" id="k_asc_delivery_note" name="settings[default_delivery_notes_pipeline_sort_type]" value="desc" <?php if (get_option('default_delivery_notes_pipeline_sort_type') == 'desc') {
                                                                                                                                                echo 'checked';
                                                                                                                                            } ?>>
                    <label for="k_asc_delivery_note"><?php echo _l('order_descending'); ?></label>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <hr />
    <?php
    $options_entry = ['value', ['name']];
    $signatory_field_options = array(
        array('value' => 'name', 'name' => _l('document_signed_by')),
        array('value' => 'date', 'name' => _l('document_signed_date')),
        array('value' => 'ip', 'name' => _l('document_signed_ip')),
    );
    $signature_layout_options = array(
        array('value' => 'linear', 'name' => _l('delivery_notes_signature_layout_linear')),
        array('value' => 'grid', 'name' => _l('delivery_notes_signature_layout_grid')),
    );
    $item_fields_options = array(
        //array('value' => 'qty', 'name' => _l('invoice_table_quantity_heading', '', false)),
        array('value' => 'rate', 'name' => _l('invoice_table_rate_heading', '', false)),
        array('value' => 'amount', 'name' => _l('invoice_table_amount_heading', '', false)),
        array('value' => 'tax', 'name' => _l('invoice_table_tax_heading', '', false)),
    ); ?>

    <?php
    $key = 'delivery_note_items_hidden_fields';
    $label = _l($key) . ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l($key . '_hint') . '"></i>';
    $value = delivery_note_items_hidden_fields();
    ?>
    <?php echo render_select('settings[' . $key . '][]', $item_fields_options, $options_entry, $label, $value, ['multiple' => true], [], '', '', true); ?>

    <?php
    $key = 'delivery_note_signatory_allowed_fields';
    $label =  'delivery_note_signatory_allowed_fields';
    $name = 'settings[' . $key . '][]';
    $value = get_option($key);
    $value = empty($value) ? [] : (array)json_decode($value);
    ?>
    <?php echo render_select($name, $signatory_field_options, $options_entry, $label, $value, ['multiple' => true], [], '', '', true); ?>
    <hr />
    <?php
    $key = 'delivery_notes_signature_layout';
    $label =  $key;
    $name = 'settings[' . $key . ']';
    $value = get_option($key);
    ?>
    <?php echo render_select($name, $signature_layout_options, $options_entry, $label, $value, [], [], '', '', false); ?>
    <hr />
    <?php
    $conversion_options = ['estimate' => 'delivery_note_allow_creating_from_estimate', 'invoice' => 'delivery_note_allow_creating_from_invoice', 'purchase_order' => 'delivery_note_allow_creating_from_purchase_order'];
    foreach ($conversion_options as $type => $key) {
        render_yes_no_option($key, _l('delivery_note_allow_creating_from', _l($type, '', false)));
    } ?>
    <hr />
    <?php
    $conversion_options = ['invoice' => 'delivery_note_allow_convert_to_invoice'];
    foreach ($conversion_options as $type => $key) {
        render_yes_no_option($key, _l('delivery_note_allow_converting_to', _l($type, '', false)));
    }
    ?>
    <hr />
    <?php $key = 'delivery_note_allow_transfer_of_non_similar_custom_fields'; ?>
    <?php render_yes_no_option($key, $key, $key . '_hint'); ?>
    <hr />

    <?php echo render_textarea('settings[predefined_clientnote_delivery_note]', 'settings_predefined_clientnote', get_option('predefined_clientnote_delivery_note'), ['rows' => 6]); ?>
    <?php echo render_textarea('settings[predefined_terms_delivery_note]', 'settings_predefined_predefined_term', get_option('predefined_terms_delivery_note'), ['rows' => 6]); ?>
</div>