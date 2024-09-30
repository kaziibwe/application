<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$total_hideable_fields = 3;
$rate_class = delivery_note_item_field_hidden('rate') ? 'hidden' : '';
$tax_class = delivery_note_item_field_hidden('tax') ? 'hidden' : '';
$amount_class = delivery_note_item_field_hidden('amount') ? 'hidden' : '';
?>
<div class="panel-body">
    <div class="row">
        <div class="col-md-4">
            <?php $this->load->view('admin/invoice_items/item_select'); ?>
        </div>
        <div class="col-md-8 text-right show_quantity_as_wrapper">
            <div class="mtop10">
                <span><?php echo _l('show_quantity_as'); ?></span>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" value="1" id="1" name="show_quantity_as" data-text="<?php echo _l('delivery_note_table_quantity_heading'); ?>" <?php echo isset($delivery_note) && $delivery_note->show_quantity_as == 1 ? 'checked' : 'checked'; ?>>
                    <label for="1"><?php echo _l('quantity_as_qty'); ?></label>
                </div>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" value="2" id="2" name="show_quantity_as" data-text="<?php echo _l('delivery_note_table_hours_heading'); ?>" <?php echo isset($delivery_note) && $delivery_note->show_quantity_as == 2 ? 'checked' : ''; ?>>
                    <label for="2"><?php echo _l('quantity_as_hours'); ?></label>
                </div>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" id="3" value="3" name="show_quantity_as" data-text="<?php echo _l('delivery_note_table_quantity_heading'); ?>/<?php echo _l('delivery_note_table_hours_heading'); ?>" <?php echo isset($delivery_note) && $delivery_note->show_quantity_as == 3 ? 'checked' : ''; ?>>
                    <label for="3">
                        <?php echo _l('delivery_note_table_quantity_heading'); ?>/<?php echo _l('delivery_note_table_hours_heading'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive s_table">
        <table class="table delivery_note-items-table items table-main-delivery_note-edit has-calculations no-mtop">
            <thead>
                <tr>
                    <th></th>
                    <th align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i>
                        <?php echo _l('delivery_note_table_item_heading'); ?></th>
                    <th align="left"><?php echo _l('delivery_note_table_item_description'); ?></th>
                    <?php
                    $custom_fields = get_custom_fields('items');
                    foreach ($custom_fields as $cf) {
                        echo '<th width="15%" align="left" class="custom_field">' . $cf['name'] . '</th>';
                    }

                    $qty_heading = _l('delivery_note_table_quantity_heading');
                    if (isset($delivery_note) && $delivery_note->show_quantity_as == 2) {
                        $qty_heading = _l('delivery_note_table_hours_heading');
                    } elseif (isset($delivery_note) && $delivery_note->show_quantity_as == 3) {
                        $qty_heading = _l('delivery_note_table_quantity_heading') . '/' . _l('delivery_note_table_hours_heading');
                    }
                    ?>
                    <th class="qty" align="right"><?php echo $qty_heading; ?></th>
                    <th align="right" class="<?= $rate_class; ?>"><?php echo _l('delivery_note_table_rate_heading'); ?>
                    </th>
                    <th align="right" class="<?= $tax_class; ?>"><?php echo _l('delivery_note_table_tax_heading'); ?>
                    </th>
                    <th align="right" class="<?= $amount_class; ?>">
                        <?php echo _l('delivery_note_table_amount_heading'); ?>
                    </th>
                    <th align="center"><i class="fa fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                <tr class="main">
                    <td></td>
                    <td>
                        <textarea name="description" rows="4" class="form-control" placeholder="<?php echo _l('item_description_placeholder'); ?>"></textarea>
                    </td>
                    <td>
                        <textarea name="long_description" rows="4" class="form-control" placeholder="<?php echo _l('item_long_description_placeholder'); ?>"></textarea>
                    </td>
                    <?php echo render_custom_fields_items_table_add_edit_preview(); ?>
                    <td>
                        <input type="number" name="quantity" min="0" value="1" class="form-control" placeholder="<?php echo _l('item_quantity_placeholder'); ?>">
                        <input type="text" placeholder="<?php echo _l('unit'); ?>" data-toggle="tooltip" 612 data-title="e.q kg, lots, packs" name="unit" class="form-control input-transparent text-right">
                    </td>
                    <td class="<?= $rate_class; ?>">
                        <input type="number" name="rate" class="form-control" placeholder="<?php echo _l('item_rate_placeholder'); ?>">
                    </td>
                    <td class="<?= $tax_class; ?>">
                        <?php
                        $default_tax = unserialize(get_option('default_tax'));
                        $select      = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="taxname" multiple data-none-selected-text="' . _l('no_tax') . '">';
                        foreach ($taxes as $tax) {
                            $selected = '';
                            if (is_array($default_tax)) {
                                if (in_array($tax['name'] . '|' . $tax['taxrate'], $default_tax)) {
                                    $selected = ' selected ';
                                }
                            }
                            $select .= '<option value="' . $tax['name'] . '|' . $tax['taxrate'] . '"' . $selected . 'data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
                        }
                        $select .= '</select>';
                        echo $select;
                        ?>
                    </td>

                    <td class="<?= empty(delivery_note_items_hidden_fields()) ? '' : (count(delivery_note_items_hidden_fields()) > 0 ? 'hidden' : ''); ?>">
                    </td>
                    <td>
                        <?php
                        $new_item = 'undefined';
                        if (isset($delivery_note)) {
                            $new_item = true;
                        }
                        ?>
                        <button type="button" onclick="add_item_to_table('undefined','undefined',<?php echo $new_item; ?>); return false;" class="btn pull-right btn-primary"><i class="fa fa-check"></i></button>
                    </td>
                </tr>
                <?php if (isset($delivery_note) || isset($add_items)) {
                    $i               = 1;
                    $items_indicator = 'newitems';
                    if (isset($delivery_note)) {
                        $add_items       = $delivery_note->items;
                        $items_indicator = 'items';
                    }

                    foreach ($add_items as $item) {
                        $manual    = false;
                        $table_row = '<tr class="sortable item">';
                        $table_row .= '<td class="dragger">';
                        if ($item['qty'] == '' || $item['qty'] == 0) {
                            $item['qty'] = 1;
                        }
                        if (!isset($is_proposal)) {
                            $delivery_note_item_taxes = get_delivery_note_item_taxes($item['id']);
                        } else {
                            $delivery_note_item_taxes = get_proposal_item_taxes($item['id']);
                        }
                        if ($item['id'] == 0) {
                            $delivery_note_item_taxes = $item['taxname'];
                            $manual              = true;
                        }
                        $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);
                        $amount = $item['rate'] * $item['qty'];
                        $amount = app_format_number($amount);
                        // order input
                        $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';
                        $table_row .= '</td>';
                        $table_row .= '<td class="bold description"><textarea name="' . $items_indicator . '[' . $i . '][description]" class="form-control" rows="5">' . clear_textarea_breaks($item['description']) . '</textarea></td>';
                        $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][long_description]" class="form-control" rows="5">' . clear_textarea_breaks($item['long_description']) . '</textarea></td>';
                        $table_row .= render_custom_fields_items_table_in($item, $items_indicator . '[' . $i . ']');
                        $table_row .= '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $item['qty'] . '" class="form-control">';
                        $unit_placeholder = '';
                        if (!$item['unit']) {
                            $unit_placeholder = _l('unit');
                            $item['unit']     = '';
                        }
                        $table_row .= '<input type="text" placeholder="' . $unit_placeholder . '" name="' . $items_indicator . '[' . $i . '][unit]" class="form-control input-transparent text-right" value="' . $item['unit'] . '">';
                        $table_row .= '</td>';
                        $table_row .= '<td class="rate ' . $rate_class . '"><input type="number" data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['rate'] . '" class="form-control"></td>';
                        $table_row .= '<td class="taxrate ' . $tax_class . '">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $delivery_note_item_taxes, (isset($is_proposal) ? 'proposal' : 'delivery_note'), $item['id'], true, $manual) . '</td>';
                        $table_row .= '<td class="amount ' . $amount_class . '" align="right">' . $amount . '</td>';
                        $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
                        $table_row .= '</tr>';
                        echo $table_row;
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-8 col-md-offset-4 <?= $amount_class; ?>">
        <table class="table text-right">
            <tbody>
                <tr id="subtotal">
                    <td><span class="bold tw-text-neutral-700"><?php echo _l('delivery_note_subtotal'); ?> :</span>
                    </td>
                    <td class="subtotal">
                    </td>
                </tr>
                <tr id="discount_area" class="hidden">
                    <td>
                        <div class="row">
                            <div class="col-md-7">
                                <span class="bold tw-text-neutral-700"><?php echo _l('delivery_note_discount'); ?></span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group" id="discount-total">

                                    <input type="number" value="<?php echo (isset($delivery_note) ? $delivery_note->discount_percent : 0); ?>" class="form-control pull-left input-discount-percent<?php if (isset($delivery_note) && !is_sale_discount($delivery_note, 'percent') && is_sale_discount_applied($delivery_note)) {
                                                                                                                                                                                                        echo ' hide';
                                                                                                                                                                                                    } ?>" min="0" max="100" name="discount_percent">

                                    <input type="number" data-toggle="tooltip" data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php echo (isset($delivery_note) ? $delivery_note->discount_total : 0); ?>" class="form-control pull-left input-discount-fixed<?php if (!isset($delivery_note) || (isset($delivery_note) && !is_sale_discount($delivery_note, 'fixed'))) {
                                                                                                                                                                                                                                                                                                echo ' hide';
                                                                                                                                                                                                                                                                                            } ?>" min="0" name="discount_total">

                                    <div class="input-group-addon">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" href="#" id="dropdown_menu_tax_total_type" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <span class="discount-total-type-selected">
                                                    <?php if (!isset($delivery_note) || isset($delivery_note) && (is_sale_discount($delivery_note, 'percent') || !is_sale_discount_applied($delivery_note))) {
                                                        echo '%';
                                                    } else {
                                                        echo _l('discount_fixed_amount');
                                                    }
                                                    ?>
                                                </span>
                                                <span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu" id="discount-total-type-dropdown" aria-labelledby="dropdown_menu_tax_total_type">
                                                <li>
                                                    <a href="#" class="discount-total-type discount-type-percent<?php if (!isset($delivery_note) || (isset($delivery_note) && is_sale_discount($delivery_note, 'percent')) || (isset($delivery_note) && !is_sale_discount_applied($delivery_note))) {
                                                                                                                    echo ' selected';
                                                                                                                } ?>">%</a>
                                                </li>
                                                <li>
                                                    <a href="#" class="discount-total-type discount-type-fixed<?php if (isset($delivery_note) && is_sale_discount($delivery_note, 'fixed')) {
                                                                                                                    echo ' selected';
                                                                                                                } ?>">
                                                        <?php echo _l('discount_fixed_amount'); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="discount-total"></td>
                </tr>
                <tr class="hidden">
                    <td>
                        <div class="row">
                            <div class="col-md-7">
                                <span class="bold tw-text-neutral-700"><?php echo _l('delivery_note_adjustment'); ?></span>
                            </div>
                            <div class="col-md-5">
                                <input type="number" data-toggle="tooltip" data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php if (isset($delivery_note)) {
                                                                                                                                                            echo $delivery_note->adjustment;
                                                                                                                                                        } else {
                                                                                                                                                            echo 0;
                                                                                                                                                        } ?>" class="form-control pull-left" name="adjustment">
                            </div>
                        </div>
                    </td>
                    <td class="adjustment"></td>
                </tr>
                <tr>
                    <td><span class="bold tw-text-neutral-700"><?php echo _l('delivery_note_total'); ?> :</span>
                    </td>
                    <td class="total">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="removed-items"></div>
</div>
<style>
    <?php $hidden_classes = [];
    if (!empty($rate_class)) $hidden_classes[] = '.delivery-notes.items td.rate';
    if (!empty($tax_class)) $hidden_classes[] = '.delivery-notes.items td.taxrate';
    if (!empty($amount_class)) $hidden_classes[] = '.delivery-notes.items td.amount';

    if (!empty($hidden_classes)) {
        echo implode(',', $hidden_classes) . '{display: none !important}';
    }

    ?>
</style>