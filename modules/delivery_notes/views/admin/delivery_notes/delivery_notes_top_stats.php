<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="stats-top" class="hide">

    <div class="_filters _hidden_inputs hidden">
        <?php
        if (isset($delivery_notes_sale_agents)) {
            foreach ($delivery_notes_sale_agents as $agent) {
                echo form_hidden('sale_agent_' . $agent['sale_agent']);
            }
        }
        if (isset($delivery_note_statuses)) {
            foreach ($delivery_note_statuses as $_status) {
                $val = '';
                if ($_status == $this->input->get('status')) {
                    $val = $_status;
                }
                echo form_hidden('delivery_notes_' . $_status, $val);
            }
        }
        if (isset($delivery_notes_years)) {
            foreach ($delivery_notes_years as $year) {
                echo form_hidden('year_' . $year['year'], $year['year']);
            }
        }
        echo form_hidden('not_sent', $this->input->get('filter'));
        echo form_hidden('project_id');
        echo form_hidden('invoiced');
        echo form_hidden('not_invoiced');
        ?>
    </div>
    <div class="quick-top-stats">
        <dl class="tw-mt-5 tw-grid tw-grid-cols-1 tw-divide-y tw-divide-solid tw-divide-neutral-200 tw-overflow-hidden md:tw-grid-cols-3 lg:tw-grid-cols-4 md:tw-divide-y-0 md:tw-divide-x tw-mb-0">
            <?php foreach ($delivery_note_statuses as $status) {
                $percent_data = get_delivery_notes_percent_by_status(
                    $status,
                    (isset($project) ? $project->id : null)
                ); ?>
                <div class="tw-px-3 tw-py-4 sm:tw-p-4 tw-bg-white">
                    <dt class="tw-font-medium text-<?php echo delivery_note_status_color_class($status); ?>">
                        <?php echo format_delivery_note_status($status, '', false); ?>
                    </dt>
                    <dd class="tw-mt-1 tw-flex tw-items-baseline tw-justify-between md:tw-block lg:tw-flex">
                        <div class="tw-flex tw-items-baseline tw-text-base tw-font-semibold tw-text-primary-600">
                            <?php echo $percent_data['total_by_status']; ?> / <?php echo $percent_data['total']; ?>
                            <span class="tw-ml-2 tw-text-sm tw-font-medium tw-text-neutral-500">
                                <a href="#" data-cview="delivery_notes_<?php echo $status; ?>" onclick="dt_custom_view('delivery_notes_<?php echo $status; ?>','.table-delivery_notes','delivery_notes_<?php echo $status; ?>',true); return false;">
                                    <?php echo _l('view'); ?>
                                </a>
                            </span>
                        </div>
                        <div class="tw-font-medium md:tw-mt-2 lg:tw-mt-0">
                            <?php echo $percent_data['percent']; ?>%
                        </div>
                    </dd>
                </div>

            <?php
            } ?>
        </dl>
    </div>
    <hr />
</div>