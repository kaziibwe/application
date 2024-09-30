<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s accounting-template delivery_note">
    <div class="panel-body">
        <div class="row">
            <?php
            if (isset($delivery_note_request_id) && $delivery_note_request_id != '') {
                echo form_hidden('delivery_note_request_id', $delivery_note_request_id);
            }
            ?>
            <div class="col-md-6">
                <div class="f_client_id">
                    <div class="form-group select-placeholder">
                        <label for="clientid" class="control-label"><?php echo _l('delivery_note_select_customer'); ?></label>
                        <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if (isset($delivery_note) && empty($delivery_note->clientid)) {
                                                                                                                                echo ' customer-removed';
                                                                                                                            } ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php $selected = (isset($delivery_note) ? $delivery_note->clientid : '');
                            if ($selected == '') {
                                $selected = (isset($customer_id) ? $customer_id : '');
                            }
                            if ($selected != '') {
                                $rel_data = get_relation_data('customer', $selected);
                                $rel_val  = get_relation_values($rel_data, 'customer');
                                echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group select-placeholder projects-wrapper<?php if ((!isset($delivery_note)) || (isset($delivery_note) && !customer_has_projects($delivery_note->clientid))) {
                                                                                echo (isset($customer_id) && (!isset($project_id) || !$project_id)) ? ' hide' : '';
                                                                            } ?>">
                    <label for="project_id"><?php echo _l('project'); ?></label>
                    <div id="project_ajax_search_wrapper">
                        <select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php
                            if (!isset($project_id)) {
                                $project_id = '';
                            }
                            if (isset($delivery_note) && $delivery_note->project_id) {
                                $project_id = $delivery_note->project_id;
                            }
                            if ($project_id) {
                                echo '<option value="' . $project_id . '" selected>' . get_project_name_by_id($project_id) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <a href="#" class="edit_shipping_billing_info" data-toggle="modal" data-target="#billing_and_shipping_details"><i class="fa-regular fa-pen-to-square"></i></a>
                        <?php include_once(__DIR__ . '/billing_and_shipping_template.php'); ?>
                    </div>
                    <div class="col-md-6">
                        <p class="bold"><?php echo _l('invoice_bill_to'); ?></p>
                        <address>
                            <span class="billing_street">
                                <?php $billing_street = (isset($delivery_note) ? $delivery_note->billing_street : '--'); ?>
                                <?php $billing_street = ($billing_street == '' ? '--' : $billing_street); ?>
                                <?php echo $billing_street; ?></span><br>
                            <span class="billing_city">
                                <?php $billing_city = (isset($delivery_note) ? $delivery_note->billing_city : '--'); ?>
                                <?php $billing_city = ($billing_city == '' ? '--' : $billing_city); ?>
                                <?php echo $billing_city; ?></span>,
                            <span class="billing_state">
                                <?php $billing_state = (isset($delivery_note) ? $delivery_note->billing_state : '--'); ?>
                                <?php $billing_state = ($billing_state == '' ? '--' : $billing_state); ?>
                                <?php echo $billing_state; ?></span>
                            <br />
                            <span class="billing_country">
                                <?php $billing_country = (isset($delivery_note) ? get_country_short_name($delivery_note->billing_country) : '--'); ?>
                                <?php $billing_country = ($billing_country == '' ? '--' : $billing_country); ?>
                                <?php echo $billing_country; ?></span>,
                            <span class="billing_zip">
                                <?php $billing_zip = (isset($delivery_note) ? $delivery_note->billing_zip : '--'); ?>
                                <?php $billing_zip = ($billing_zip == '' ? '--' : $billing_zip); ?>
                                <?php echo $billing_zip; ?></span>
                        </address>
                    </div>
                    <div class="col-md-6">
                        <p class="bold"><?php echo _l('ship_to'); ?></p>
                        <address>
                            <span class="shipping_street">
                                <?php $shipping_street = (isset($delivery_note) ? $delivery_note->shipping_street : '--'); ?>
                                <?php $shipping_street = ($shipping_street == '' ? '--' : $shipping_street); ?>
                                <?php echo $shipping_street; ?></span><br>
                            <span class="shipping_city">
                                <?php $shipping_city = (isset($delivery_note) ? $delivery_note->shipping_city : '--'); ?>
                                <?php $shipping_city = ($shipping_city == '' ? '--' : $shipping_city); ?>
                                <?php echo $shipping_city; ?></span>,
                            <span class="shipping_state">
                                <?php $shipping_state = (isset($delivery_note) ? $delivery_note->shipping_state : '--'); ?>
                                <?php $shipping_state = ($shipping_state == '' ? '--' : $shipping_state); ?>
                                <?php echo $shipping_state; ?></span>
                            <br />
                            <span class="shipping_country">
                                <?php $shipping_country = (isset($delivery_note) ? get_country_short_name($delivery_note->shipping_country) : '--'); ?>
                                <?php $shipping_country = ($shipping_country == '' ? '--' : $shipping_country); ?>
                                <?php echo $shipping_country; ?></span>,
                            <span class="shipping_zip">
                                <?php $shipping_zip = (isset($delivery_note) ? $delivery_note->shipping_zip : '--'); ?>
                                <?php $shipping_zip = ($shipping_zip == '' ? '--' : $shipping_zip); ?>
                                <?php echo $shipping_zip; ?></span>
                        </address>
                    </div>
                </div>
                <?php
                $next_delivery_note_number = get_option('next_delivery_note_number');
                $format               = get_option('delivery_note_number_format');

                if (isset($delivery_note)) {
                    $format = $delivery_note->number_format;
                }

                $prefix = get_option('delivery_note_prefix');

                if ($format == 1) {
                    $__number = $next_delivery_note_number;
                    if (isset($delivery_note)) {
                        $__number = $delivery_note->number;
                        $prefix   = '<span id="prefix">' . $delivery_note->prefix . '</span>';
                    }
                } elseif ($format == 2) {
                    if (isset($delivery_note)) {
                        $__number = $delivery_note->number;
                        $prefix   = $delivery_note->prefix;
                        $prefix   = '<span id="prefix">' . $prefix . '</span><span id="prefix_year">' . date('Y', strtotime($delivery_note->date)) . '</span>/';
                    } else {
                        $__number = $next_delivery_note_number;
                        $prefix   = $prefix . '<span id="prefix_year">' . date('Y') . '</span>/';
                    }
                } elseif ($format == 3) {
                    if (isset($delivery_note)) {
                        $yy       = date('y', strtotime($delivery_note->date));
                        $__number = $delivery_note->number;
                        $prefix   = '<span id="prefix">' . $delivery_note->prefix . '</span>';
                    } else {
                        $yy       = date('y');
                        $__number = $next_delivery_note_number;
                    }
                } elseif ($format == 4) {
                    if (isset($delivery_note)) {
                        $yyyy     = date('Y', strtotime($delivery_note->date));
                        $mm       = date('m', strtotime($delivery_note->date));
                        $__number = $delivery_note->number;
                        $prefix   = '<span id="prefix">' . $delivery_note->prefix . '</span>';
                    } else {
                        $yyyy     = date('Y');
                        $mm       = date('m');
                        $__number = $next_delivery_note_number;
                    }
                }

                $_delivery_note_number     = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                $isedit               = isset($delivery_note) ? 'true' : 'false';
                $data_original_number = isset($delivery_note) ? $delivery_note->number : 'false';
                ?>
                <div class="form-group">
                    <label for="number"><?php echo _l('delivery_note_add_edit_number'); ?></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <?php if (isset($delivery_note)) { ?>
                                <a href="#" onclick="return false;" data-toggle="popover" data-container='._transaction_form' data-html="true" data-content="<label class='control-label'><?php echo _l('settings_sales_delivery_note_prefix'); ?></label><div class='input-group'><input name='s_prefix' type='text' class='form-control' value='<?php echo $delivery_note->prefix; ?>'></div><button type='button' onclick='save_sales_number_settings(this); return false;' data-url='<?php echo admin_url('delivery_notes/update_number_settings/' . $delivery_note->id); ?>' class='btn btn-primary btn-block mtop15'><?php echo _l('submit'); ?></button>"><i class="fa fa-cog"></i></a>
                            <?php }
                            echo $prefix;
                            ?>
                        </span>
                        <input type="text" name="number" class="form-control" value="<?php echo $_delivery_note_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>">
                        <?php if ($format == 3) { ?>
                            <span class="input-group-addon">
                                <span id="prefix_year" class="format-n-yy"><?php echo $yy; ?></span>
                            </span>
                        <?php } elseif ($format == 4) { ?>
                            <span class="input-group-addon">
                                <span id="prefix_month" class="format-mm-yyyy"><?php echo $mm; ?></span>
                                /
                                <span id="prefix_year" class="format-mm-yyyy"><?php echo $yyyy; ?></span>
                            </span>
                        <?php } ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php $value = (isset($delivery_note) ? _d($delivery_note->date) : _d(date('Y-m-d'))); ?>
                        <?php echo render_date_input('date', 'delivery_note_add_edit_date', $value); ?>
                    </div>
                </div>
                <div class="clearfix mbot15"></div>
                <?php $rel_id = (isset($delivery_note) ? $delivery_note->id : false); ?>
                <?php
                if (isset($custom_fields_rel_transfer)) {
                    $rel_id = $custom_fields_rel_transfer;
                }
                ?>
                <?php echo render_custom_fields('delivery_note', $rel_id); ?>
            </div>
            <div class="col-md-6">
                <div class="tw-ml-3">
                    <div class="form-group">
                        <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                            <?php echo _l('tags'); ?></label>
                        <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($delivery_note) ? prep_tags_input(get_tags_in($delivery_note->id, 'delivery_note')) : ''); ?>" data-role="tagsinput">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php

                            $currency_attr = ['disabled' => true, 'data-show-subtext' => true];
                            $currency_attr = apply_filters_deprecated('delivery_note_currency_disabled', [$currency_attr], '2.3.0', 'delivery_note_currency_attributes');
                            foreach ($currencies as $currency) {
                                if ($currency['isdefault'] == 1) {
                                    $currency_attr['data-base'] = $currency['id'];
                                }
                                if (isset($delivery_note)) {
                                    if ($currency['id'] == $delivery_note->currency) {
                                        $selected = $currency['id'];
                                    }
                                } else {
                                    if ($currency['isdefault'] == 1) {
                                        $selected = $currency['id'];
                                    }
                                }
                            }
                            $currency_attr = hooks()->apply_filters('delivery_note_currency_attributes', $currency_attr);
                            ?>
                            <?php echo render_select('currency', $currencies, ['id', 'name', 'symbol'], 'delivery_note_add_edit_currency', $selected, $currency_attr); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group select-placeholder">
                                <label class="control-label"><?php echo _l('delivery_note_status'); ?></label>
                                <select class="selectpicker display-block mbot15" name="status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php foreach ($delivery_note_statuses as $status) { ?>
                                        <option value="<?php echo $status; ?>" <?php if (isset($delivery_note) && $delivery_note->status == $status) {
                                                                                    echo 'selected';
                                                                                } ?>>
                                            <?php echo format_delivery_note_status($status, '', false); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <?php $value = (isset($delivery_note) ? $delivery_note->reference_no : ''); ?>
                            <?php echo render_input('reference_no', 'reference_no', $value); ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                            $selected = !isset($delivery_note) && get_option('automatically_set_logged_in_staff_sales_agent') == '1' ? get_staff_user_id() : '';
                            foreach ($staff as $member) {
                                if (isset($delivery_note)) {
                                    if ($delivery_note->sale_agent == $member['staffid']) {
                                        $selected = $member['staffid'];
                                    }
                                }
                            }
                            echo render_select('sale_agent', $staff, ['staffid', ['firstname', 'lastname']], 'sale_agent_string', $selected);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group select-placeholder">
                                <label for="discount_type" class="control-label"><?php echo _l('discount_type'); ?></label>
                                <select name="discount_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="" selected><?php echo _l('no_discount'); ?></option>
                                    <option value="before_tax" <?php
                                                                if (isset($delivery_note)) {
                                                                    if ($delivery_note->discount_type == 'before_tax') {
                                                                        echo 'selected';
                                                                    }
                                                                } ?>><?php echo _l('discount_type_before_tax'); ?>
                                    </option>
                                    <option value="after_tax" <?php if (isset($delivery_note)) {
                                                                    if ($delivery_note->discount_type == 'after_tax') {
                                                                        echo 'selected';
                                                                    }
                                                                } ?>><?php echo _l('discount_type_after_tax'); ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php $value = (isset($delivery_note) ? $delivery_note->adminnote : ''); ?>
                    <?php echo render_textarea('adminnote', 'delivery_note_add_edit_admin_note', $value); ?>

                </div>
            </div>
        </div>
    </div>

    <hr class="hr-panel-separator" />

    <div class="delivery-notes items">
        <?php $this->load->view('admin/delivery_notes/_add_edit_items'); ?>
    </div>

    <hr class="hr-panel-separator" />

    <div class="panel-body">
        <?php
        $value = (isset($delivery_note) ? $delivery_note->clientnote : get_option('predefined_clientnote_delivery_note'));
        echo render_textarea('clientnote', 'delivery_note_add_edit_client_note', $value);
        $value = (isset($delivery_note) ? $delivery_note->terms : get_option('predefined_terms_delivery_note'));
        echo render_textarea('terms', 'terms_and_conditions', $value, [], [], 'mtop15');
        ?>
    </div>
</div>

<div class="btn-bottom-pusher"></div>
<div class="btn-bottom-toolbar text-right">
    <div class="btn-group dropup">
        <button type="button" class="btn-tr btn btn-primary delivery_note-form-submit transaction-submit">
            <?php echo _l('submit'); ?>
        </button>
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right width200">
            <li>
                <a href="#" class="delivery_note-form-submit save-and-send transaction-submit">
                    <?php echo _l('save_and_send'); ?>
                </a>
            </li>
            <?php if (!isset($delivery_note)) { ?>
                <li>
                    <a href="#" class="delivery_note-form-submit save-and-send-later transaction-submit">
                        <?php echo _l('save_and_send_later'); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>