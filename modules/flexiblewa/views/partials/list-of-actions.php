<div class="flexiblewa-actions-list">
    <?php if(!empty($actions)): ?>
        <div class="alert alert-info">
            <?php echo _l('flexiblewa_action_sequence_desc'); ?>
        </div>
    <ul class="list-unstyled text-left" id="flexiblewa-action-list-item"
        data-success="<?php echo _l('flexiblewa_actions_order_updated_successfully'); ?>">
        <?php foreach($actions as $action): ?>
            <li class="flexiblewa-action-item list-group-item" data-id="<?php echo $action['id'] ?>">
                <div class="flexiblewa-action-item-title">
                    <div class="widget-dragger ui-sortable-handle"></div>
                    <?php echo $action['title']; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
        <div class="flexiblewa-action-item">
            <div class="flexiblewa-action-item-title">
                <?php echo _l('flexiblewa_no_rules_for_this_section'); ?>
            </div>
        </div>
    <?php endif; ?>
</div>