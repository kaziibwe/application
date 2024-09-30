<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop15 preview-top-wrapper">
    <div class="row">
        <div class="col-md-3">
            <div class="mbot30">
                <div class="delivery-notehtml-logo">
                    <?php echo get_dark_company_logo(); ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="top" data-sticky data-sticky-class="preview-sticky-header">
        <div class="container preview-sticky-container">
            <div class="sm:tw-flex sm:tw-justify-between -tw-mx-4">
                <div class="sm:tw-self-end">
                    <h3 class="bold tw-my-0 delivery-notehtml-number">
                        <span class="sticky-visible hide tw-mb-2">
                            <?php echo format_delivery_note_number($delivery_note->id); ?>
                        </span>
                    </h3>
                    <span class="delivery-notehtml-status">
                        <?php echo format_delivery_note_status($delivery_note->status, '', true); ?>
                    </span>
                    <?php if (!empty($delivery_note->signature)) : ?>
                    <span class="label label-success -tw-mt-1 tw-self-start tw-ml-4">
                        <?php echo _l('delivery_note_confirmed'); ?>
                    </span>
                    <?php endif; ?>
                </div>

                <div class="tw-flex tw-items-end tw-space-x-2 tw-mt-3 sm:tw-mt-0">
                    <?php echo form_open($this->uri->uri_string(), ['class' => 'action-button']); ?>
                    <button type="submit" name="delivery_notepdf" class="btn btn-default action-button download"
                        value="delivery_notepdf">
                        <i class="fa-regular fa-file-pdf"></i>
                        <?php echo _l('clients_invoice_html_btn_download'); ?>
                    </button>
                    <?php echo form_close(); ?>

                    <?php if ($can_be_confirmed) : ?>
                    <button type="submit" id="accept_action" class="btn btn-success action-button">
                        <i class="fa-solid fa-signature"></i>
                        <?php echo _l('delivery_note_confirm_delivery'); ?>
                    </button>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel_s tw-mt-6">
        <div class="panel-body">
            <div class="col-md-10 col-md-offset-1">
                <div class="row mtop20">
                    <div class="col-md-6 col-sm-6 transaction-html-info-col-left">
                        <h4 class="bold delivery-notehtml-number">
                            <?php echo format_delivery_note_number($delivery_note->id); ?></h4>
                        <address class="delivery-notehtml-company-info tw-text-neutral-500 tw-text-normal">
                            <?php echo format_organization_info(); ?>
                        </address>
                    </div>
                    <div class="col-sm-6 text-right transaction-html-info-col-right">
                        <span class="tw-font-medium tw-text-neutral-600 delivery_note_to">
                            <?php echo _l('delivery_note_to'); ?>
                        </span>
                        <address class="delivery-notehtml-customer-billing-info tw-text-neutral-500 tw-text-normal">
                            <?php echo format_customer_info($delivery_note, 'delivery_note', 'billing'); ?>
                        </address>
                        <!-- shipping details -->
                        <?php if ($delivery_note->include_shipping == 1 && $delivery_note->show_shipping_on_delivery_note == 1) { ?>
                        <span class="tw-font-medium tw-text-neutral-700 delivery_note_ship_to">
                            <?php echo _l('ship_to'); ?>
                        </span>
                        <address class="delivery-notehtml-customer-shipping-info tw-text-neutral-500 tw-text-normal">
                            <?php echo format_customer_info($delivery_note, 'delivery_note', 'shipping'); ?>
                        </address>
                        <?php } ?>
                        <p class="delivery-notehtml-date tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700">
                                <?php echo _l('delivery_note_data_date'); ?>:
                            </span>
                            <?php echo _d($delivery_note->date); ?>
                        </p>
                        <?php if (!empty($delivery_note->reference_no)) { ?>
                        <p class="delivery-notehtml-reference-no tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?php echo _l('reference_no'); ?>:</span>
                            <?php echo $delivery_note->reference_no; ?>
                        </p>
                        <?php } ?>
                        <?php if ($delivery_note->sale_agent && get_option('show_sale_agent_on_delivery_notes') == 1) { ?>
                        <p class="delivery-notehtml-sale-agent tw-mb-0 tw-text-normal">
                            <span
                                class="tw-font-medium tw-text-neutral-700"><?php echo _l('sale_agent_string'); ?>:</span>
                            <?php echo get_staff_full_name($delivery_note->sale_agent); ?>
                        </p>
                        <?php } ?>
                        <?php if ($delivery_note->project_id && get_option('show_project_on_delivery_note') == 1) { ?>
                        <p class="delivery-notehtml-project tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?php echo _l('project'); ?>:</span>
                            <?php echo get_project_name_by_id($delivery_note->project_id); ?>
                        </p>
                        <?php } ?>
                        <?php $pdf_custom_fields = get_custom_fields('delivery_note', ['show_on_pdf' => 1, 'show_on_client_portal' => 1]);
                        foreach ($pdf_custom_fields as $field) {
                            $value = get_custom_field_value($delivery_note->id, $field['id'], 'delivery_note');
                            if ($value == '') {
                                continue;
                            } ?>
                        <p class="tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700">
                                <?php echo $field['name']; ?>:
                            </span>
                            <?php echo $value; ?>
                        </p>
                        <?php
                        } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <?php
                            $items = delivery_notes_get_items_table_data($delivery_note, 'delivery_note');
                            echo $items->table();
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-md-offset-6">
                        <table class="table text-right tw-text-normal">
                            <tbody>
                                <tr id="subtotal">
                                    <td>
                                        <span class="bold tw-text-neutral-700">
                                            <?php echo _l('delivery_note_subtotal'); ?>
                                        </span>
                                    </td>
                                    <td class="subtotal">
                                        <?php echo app_format_money($delivery_note->subtotal, $delivery_note->currency_name); ?>
                                    </td>
                                </tr>
                                <?php if (is_sale_discount_applied($delivery_note)) { ?>
                                <tr>
                                    <td>
                                        <span
                                            class="bold tw-text-neutral-700"><?php echo _l('delivery_note_discount'); ?>
                                            <?php if (is_sale_discount($delivery_note, 'percent')) { ?>
                                            (<?php echo app_format_number($delivery_note->discount_percent, true); ?>%)
                                            <?php } ?>
                                        </span>
                                    </td>
                                    <td class="discount">
                                        <?php echo '-' . app_format_money($delivery_note->discount_total, $delivery_note->currency_name); ?>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php
                                foreach ($items->taxes() as $tax) {
                                    echo '<tr class="tax-area"><td class="bold !tw-text-neutral-700">' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)</td><td>' . app_format_money($tax['total_tax'], $delivery_note->currency_name) . '</td></tr>';
                                }
                                ?>
                                <?php if ((int)$delivery_note->adjustment != 0) { ?>
                                <tr>
                                    <td>
                                        <span class="bold tw-text-neutral-700">
                                            <?php echo _l('delivery_note_adjustment'); ?>
                                        </span>
                                    </td>
                                    <td class="adjustment">
                                        <?php echo app_format_money($delivery_note->adjustment, $delivery_note->currency_name); ?>
                                    </td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td>
                                        <span class="bold tw-text-neutral-700">
                                            <?php echo _l('delivery_note_total'); ?>
                                        </span>
                                    </td>
                                    <td class="total">
                                        <?php echo app_format_money($delivery_note->total, $delivery_note->currency_name); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    if (get_option('total_to_words_enabled') == 1 && !delivery_note_item_field_hidden('amount')) { ?>
                    <div class="col-md-12 text-center delivery-notehtml-total-to-words">
                        <p class="tw-font-medium">
                            <?php echo _l('num_word'); ?>:<span class="tw-text-neutral-500">
                                <?php echo $this->numberword->convert($delivery_note->total, $delivery_note->currency_name); ?>
                            </span>
                        </p>
                    </div>
                    <?php } ?>
                    <?php if (count($delivery_note->attachments) > 0 && $delivery_note->visible_attachments_to_customer_found == true) { ?>
                    <div class="clearfix"></div>
                    <div class="delivery-notehtml-files">
                        <div class="col-md-12">
                            <hr />
                            <p class="bold mbot15 font-medium"><?php echo _l('delivery_note_files'); ?></p>
                        </div>
                        <?php foreach ($delivery_note->attachments as $attachment) {
                                // Do not show hidden attachments to customer
                                if ($attachment['visible_to_customer'] == 0) {
                                    continue;
                                }
                                $attachment_url = site_url('download/file/sales_attachment/' . $attachment['attachment_key']);
                                if (!empty($attachment['external'])) {
                                    $attachment_url = $attachment['external_link'];
                                } ?>
                        <div class="col-md-12 mbot15">
                            <div class="pull-left"><i
                                    class="<?php echo get_mime_class($attachment['filetype']); ?>"></i>
                            </div>
                            <a href="<?php echo $attachment_url; ?>"><?php echo $attachment['file_name']; ?></a>
                        </div>
                        <?php
                            } ?>
                    </div>
                    <?php } ?>
                    <?php if (!empty($delivery_note->clientnote)) { ?>
                    <div class="col-md-12 delivery-notehtml-note">
                        <p class="tw-mb-2.5 tw-font-medium">
                            <b><?php echo _l('delivery_note_note'); ?></b>
                        </p>
                        <div class="tw-text-neutral-500">
                            <?php echo $delivery_note->clientnote; ?>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if (!empty($delivery_note->terms)) { ?>
                    <div class="col-md-12 delivery-notehtml-terms-and-conditions">
                        <hr />
                        <p class="tw-mb-2.5 tw-font-medium">
                            <b><?php echo _l('terms_and_conditions'); ?></b>
                        </p>
                        <div class="tw-text-neutral-500">
                            <?php echo $delivery_note->terms; ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    if ($identity_confirmation_enabled == '1' && $can_be_confirmed) {
        get_template_part('identity_confirmation_form', ['formAction' => site_url('delivery_notes/client/sign_delivery/' . $delivery_note->id)]);
    }
    ?>
    <script>
    $(function() {
        new Sticky('[data-sticky]');
    })
    </script>