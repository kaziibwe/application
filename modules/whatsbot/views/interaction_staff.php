<?php if (!empty($agent->agent_id && (is_array($agent->agent_id) || is_object($agent->agent_id)))) { ?>
    <?php foreach ($agent->agent_id as $agent_id) { ?>
        <span class="tw-group tw-relative" data-title="<?php echo get_staff_full_name($agent_id) ?>" data-toggle="tooltip">
            <?php echo staff_profile_image($agent_id, ['tw-inline-block tw-h-7 tw-w-7 tw-rounded-full tw-ring-2 tw-ring-white', '']); ?>
        </span>
    <?php } ?>
<?php } ?>
