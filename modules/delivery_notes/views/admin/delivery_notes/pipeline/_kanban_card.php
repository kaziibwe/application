<?php defined('BASEPATH') or exit('No direct script access allowed');
if ($delivery_note['status'] == $status) { ?>
    <li data-delivery_note-id="<?php echo $delivery_note['id']; ?>" class="<?php if ($delivery_note['invoiceid'] != null) {
                                                                                echo 'not-sortable';
                                                                            } ?>">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="tw-font-semibold tw-text-base pipeline-heading tw-mb-0.5">
                        <a href="<?php echo admin_url('delivery_notes/list_delivery_notes/' . $delivery_note['id']); ?>" class="tw-text-neutral-700 hover:tw-text-neutral-900 active:tw-text-neutral-900" onclick="delivery_note_pipeline_open(<?php echo $delivery_note['id']; ?>); return false;">
                            <?php echo format_delivery_note_number($delivery_note['id']); ?>
                        </a>
                        <?php if (staff_can('edit',  'delivery_notes')) { ?>
                            <a href="<?php echo admin_url('delivery_notes/delivery_note/' . $delivery_note['id']); ?>" target="_blank" class="pull-right">
                                <small>
                                    <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                                </small>
                            </a>
                        <?php } ?>
                    </h4>
                    <span class="tw-inline-block tw-w-full tw-mb-2">
                        <a href="<?php echo admin_url('clients/client/' . $delivery_note['clientid']); ?>" target="_blank">
                            <?php echo $delivery_note['company']; ?>
                        </a>
                    </span>
                </div>
                <div class="col-md-12">
                    <div class="tw-flex">
                        <div class="tw-grow">
                            <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                                <span class="tw-text-neutral-500">
                                    <?php echo _l('delivery_note_total'); ?>:
                                </span>
                                <?php echo app_format_money($delivery_note['total'], $delivery_note['currency_name']); ?>
                            </p>
                            <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                                <span class="tw-text-neutral-500">
                                    <?php echo _l('delivery_note_data_date'); ?>:
                                </span>
                                <?php echo _d($delivery_note['date']); ?>
                            </p>

                        </div>
                        <div class="tw-shrink-0 text-right">
                            <small>
                                <i class="fa fa-paperclip"></i>
                                <?php echo _l('delivery_note_notes'); ?>:
                                <?php echo total_rows(db_prefix() . 'notes', [
                                    'rel_id'   => $delivery_note['id'],
                                    'rel_type' => 'delivery_note',
                                ]); ?>
                            </small>
                        </div>
                        <?php $tags = get_tags_in($delivery_note['id'], 'delivery_note'); ?>
                        <?php if (count($tags) > 0) { ?>
                            <div class="kanban-tags tw-text-sm tw-inline-flex">
                                <?php echo render_tags($tags); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </li>
<?php } ?>