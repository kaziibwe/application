<div class="modal fade flexiblewa_form" id="flexiblewa_rule_config" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <?php echo form_open(admin_url('flexiblewa/rule'), [
            'enctype' => 'multipart/form-data'
        ]); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('flexiblewa_add_rule'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="container fwa-container">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php echo render_input('title', 'flexiblewa_rule_name', '', 'text', [
                                        'required' => 'required'
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8 text-center">
                            <h4 class="bold"><?php echo _l('flexiblewa_add_action'); ?></h4>
                            <small><?php echo _l('flexiblewa_add_action_desc'); ?>  </small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 flexiblewa-rule-rhs">
                            <div class="">
                                <?php echo render_select('section_id', $statuses, ['id', 'name'], 'flexiblewa_when_task_move_to_section', '', [
                                    'required' => 'required'
                                ]); ?>
                                <h5 class="bold"><?php echo _l('flexiblewa_i_want_to'); ?> </h5>
                            </div>
                            <hr/>
                            <div class="action-row-section">
                                <div class="fwa-title">
                                    <h5><?php echo _l('flexiblewa_set_task_field_to'); ?></h5>
                                </div>
                                <div class="fwa-content">
                                    <p>
                                        <button class="btn btn-link action-btn" data-id="<?php echo FLEXIBLEWA_SET_ASSIGNED_TO_ACTION ?>">
                                            <i class="fa fa-user"></i> <span class=""> <?php echo _l('flexiblewa_set_assigned_to'); ?></span>
                                        </button>
                                    </p>
                                    <p>
                                        <button class="btn btn-link action-btn" data-id="<?php echo FLEXIBLEWA_SET_DUE_DATE_TO_ACTION ?>">
                                            <i class="fa fa-calendar"></i> <span class=""><?php echo _l('flexiblewa_due_date_to'); ?>  </span>
                                        </button>
                                    </p>
                                    <p>
                                        <button class="btn btn-link action-btn"  data-id="<?php echo FLEXIBLEWA_SET_PRIORITY_TO_ACTION ?>">
                                            <i class="fa fa-bar-chart"></i> <span class=""><?php echo _l('flexiblewa_set_priority_to'); ?> </span>
                                        </button>
                                    </p>
                                </div>
                            </div>
                            <hr/>
                            <div class="action-row-section">
                                <div class="fwa-title">
                                    <h5>Create New </h5>
                                </div>
                                <div class="fwa-content">
                                    <p>
                                        <button class="btn btn-link action-btn" data-id="<?php echo FLEXIBLEWA_ADD_NEW_CHECKLIST_ITEM_ACTION ?>">
                                            <i class="fa fa-tasks"></i><span class=""><?php echo _l('flexiblewa_new_checklist_item'); ?></span>
                                        </button>
                                    </p>
                                    <p>
                                        <button class="btn btn-link action-btn" data-id="<?php echo FLEXIBLEWA_ADD_NEW_REMINDER_ACTION ?>">
                                            <i class="fa fa-bell"></i> <span class=""><?php echo _l('flexiblewa_new_reminder'); ?></span>
                                        </button>
                                    </p>
                                    <p>
                                        <button class="btn btn-link action-btn" data-id="<?php echo FLEXIBLEWA_ADD_NEW_COMMENT_ACTION ?>">
                                            <i class="fa fa-comment"></i> <span class=""><?php echo _l('flexiblewa_new_comment'); ?></span>
                                        </button>
                                    </p>
                                    <p>
                                        <button class="btn btn-link action-btn" data-id="<?php echo FLEXIBLEWA_ADD_NEW_FOLLOWER_ACTION ?>">
                                            <i class="fa fa-users"></i> <span class=""><?php echo _l('flexiblewa_new_follower'); ?></span>
                                        </button>
                                    </p>
                                    <p>
                                        <button class="btn btn-link action-btn" data-id="<?php echo FLEXIBLEWA_ADD_NEW_FILE_ACTION ?>">
                                            <i class="fa fa-file"></i> <span class=""><?php echo _l('flexiblewa_new_file'); ?></span>
                                        </button>
                                    </p>
                                </div>
                            </div>
                            <hr/>
                            <div class="action-row-section">
                                <div class="fwa-title">
                                    <h5> <?php echo _l('flexiblewa_move_to_section'); ?> </h5>
                                </div>
                                <div class="fwa-content">
                                    <p>
                                        <button class="btn btn-link action-btn" data-id="<?php echo FLEXIBLEWA_MOVE_TO_SECTION_ACTION ?>">
                                            <i class="fa fa-arrow-right"></i> <span
                                                    class=""><?php echo _l('flexiblewa_move_to_a_section'); ?> </span> 
                                        </button>
                                    </p>
                                    <p>
                                        <button class="btn btn-link action-btn" data-id="<?php echo FLEXIBLEWA_MOVE_TO_ANOTHER_RELATION_ACTION ?>">
                                            <i class="fa fa-network-wired"></i> <span class=""><?php echo _l('flexiblewa_move_to_another_relation'); ?> </span>
                                        </button>
                                    </p>
                                    <p>
                                        <button class="btn btn-link action-btn" data-id="<?php echo FLEXIBLEWA_MARK_AS_COMPLETE_ACTION ?>">
                                            <i class="fa fa-check"></i> <span class=""><?php echo _l('flexiblewa_mark_as_complete'); ?> </span>
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8 flexiblewa-rule-lhs text-center tw-pt-5 mtop3">
                            <h4 class="text-white">
                                <?php echo _l('flexiblewa_action_notice'); ?>
                            </h4>
                            <div>
                                <span class="tw-animate-spin"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('flexiblewa_create_rule'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->