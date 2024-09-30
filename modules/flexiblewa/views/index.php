<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
init_head();
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <span class="tw-font-bold">
                            <?php echo $title ?>
                        </span>

                        <a href="#" data-toggle="modal" data-target="#flexiblewa_rule_config" class="btn btn-link">
                            <i class="fa-regular fa-plus"></i>
                            <?php echo _l('flexiblewa_add_rule'); ?>
                        </a>
                        <a href="#" data-toggle="modal" data-target="#flexiblewa_action_sequence" class="btn btn-link">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <?php echo _l('flexiblewa_order_action'); ?>
                        </a>
                    </div>
                    <div class="panel-body">
                        <div class="panel-table-full">
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <?php
                                            echo _l('flexiblewa_rule_name');
                                            ?>
                                        </th>
                                        <th>
                                            <?php
                                            echo _l('flexiblewa_section');
                                            ?>
                                        </th>
                                        <th>
                                            <?php
                                            echo _l('flexiblewa_rule');
                                            ?>
                                        </th>
                                        <th>
                                            <?php
                                            echo _l('flexiblewa_value');
                                            ?>
                                        </th>
                                        <th>
                                            <?php echo _l('flexiblewa_options'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rules as $rule) { ?>
                                        <tr class="has-row-options ">
                                            <td>
                                                <?php echo $rule['title']; ?>
                                            </td>
                                            <td>
                                                <?php echo $rule['section_name']; ?>
                                            </td>
                                            <td>
                                                <?php echo $rule['rule_name']; ?>
                                            </td>
                                            <td>
                                                <?php echo $rule['display_value']; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo admin_url('flexiblewa/delete_rule/' . $rule['id']); ?>"
                                                    class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete"
                                                    title="<?php echo _l('flexiblewa_delete') ?>">
                                                    <i class="fa-regular fa-trash-can fa-lg"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" value="<?php echo admin_url('flexiblewa/ajax'); ?>" id="flexiblewa_ajax_url" />
<?php flexiblewa_modals(); ?>
<?php init_tail(); ?>

<script>
    $(document).ready(function () {
        function getDisplayContainer(){
            return $(".flexiblewa-rule-lhs");
        }
        function makeSpinner(){
            return `<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>`
        }
        function getBaseURL(){
            return $('#flexiblewa_ajax_url').val();
        }
        function makeDueDateInput(){
            return `<div class='row'>
                <div class='col-md-6 col-md-offset-3'>
                    <div class='form-group'>
                        <label for='time_count'>
                            <?php echo _l('flexiblewa_due_date') ?>
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-3">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_SET_DUE_DATE_TO_ACTION ?>' />
                    <div class='form-group'>
                        <div class="input-group">
                            <span class="input-group-addon" id="time_count_addon">
                                <i class='fa fa-plus'></i>
                            </span>
                            <input id='time_count' name='time_count' type="number" class="form-control" min="0.01" step='0.01' value='1' aria-describedby="time_count_addon" required>
                        </div>
                    </div>
                </div>
                <div class='col-md-3'>
                    <?php echo render_select('period', flexiblewa_get_periods(), ['id', 'name'], '', 'hours', [
                        'required' => 'required'
                    ]); ?>
                </div>
            </div>`;
        }
        function makeAssigneesInput(members) {
            let auto_assign = "<?php echo get_option('new_task_auto_assign_current_member') == '1' ?>";
            let current_staff_id = "<?php echo get_staff_user_id() ?>";
            let html = ''
            html += `<div class='row'>
                <div class="col-md-6 col-md-offset-3">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_SET_ASSIGNED_TO_ACTION ?>' />
                    <div class="form-group select-placeholder>">
                        <label for="assignees"><?php echo _l('flexiblewa_assignees'); ?></label>
                        <select name="assignees[]" id="assignees" class="selectpicker" data-width="100%"
                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                            multiple data-live-search="true" required>`

            for (let index = 0; index < members.length; index++) {
                const member = members[index];
                html += `<option value="${member['staffid']}" ${auto_assign && (current_staff_id == member.staffid) ? 'selected' : ''}>
                                ${member.firstname} ${member.lastname}
                            </option>`
            }

            html += `</select>
                    </div>
                </div>
            </div>`

            return html;
        }
        function makePriorityInput() {
            let html = ''
            html += `<div class='row'>
                <div class="col-md-6 col-md-offset-3">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_SET_PRIORITY_TO_ACTION ?>' />
                    <?php echo render_select('priority', get_tasks_priorities(), ['id', 'name'], 'flexiblewa_priority', '1', [
                        'required' => 'required'
                    ]); ?>
                </div>
            </div>`

            return html;
        }
        function makeChecklistInput() {
            let html = ''
            html += `<div class='row'>
                <div class="col-md-6 col-md-offset-3">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_ADD_NEW_CHECKLIST_ITEM_ACTION ?>' />
                    <?php echo render_textarea('checklist', 'flexiblewa_checklist', '', [
                        'required' => 'required',
                        'placeholder' => _l('flexiblewa_checklist_placeholder')
                    ]); ?>
                </div>
            </div>`

            return html;
        }
        function makeCommentInput() {
            let html = ''
            html += `<div class='row'>
                <div class="">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_ADD_NEW_COMMENT_ACTION ?>' />
                    <label for="comment"><?php echo _l('flexiblewa_comment'); ?></label>
                        <textarea name="comment" placeholder="<?php echo _l('flexiblewa_comment_placeholder') ?>" id="flexiblewa_task_comment"
                            rows="3" class="form-control ays-ignore"></textarea>
                </div>
            </div>`

            return html;
        }
        function makeReminderInput() {
            let html = ''
            html += `<div class='row'>
                <div class="col-md-6 col-md-offset-3">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_ADD_NEW_REMINDER_ACTION ?>' />
                    <label><?php echo _l('flexiblewa_reminder') ?></label>
                </div>
                <div class="col-md-3 col-md-offset-3">
                    <div class='form-group'>
                        <div class="input-group">
                            <span class="input-group-addon" id="time_count_addon">
                                <i class='fa fa-plus'></i>
                            </span>
                            <input id='time_count' name='time_count' type="number" class="form-control" min="0.01" step='0.01' value='1' aria-describedby="time_count_addon" required>
                        </div>
                    </div>
                </div>
                <div class='col-md-3'>
                    <?php echo render_select('period', flexiblewa_get_periods(true), ['id', 'name'], '', 'hours', [
                        'required' => 'required'
                    ]); ?>
                </div>
                <div class="col-md-6 col-md-offset-3">
                    <?php echo render_select('reminder_user_id', flexiblewa_get_staff_members(), ['staffid', ['firstname', 'lastname']], 'flexiblewa_remind_who', '', ['required' => true], [], '', '', false); ?>
                </div>
            </div>`

            return html;
        }
        function makeFollowersInput() {
            let html = ''
            html += `<div class='row'>
                <div class="col-md-6 col-md-offset-3">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_ADD_NEW_FOLLOWER_ACTION ?>' />
                    <?php echo render_select('followers[]', flexiblewa_get_staff_members(), ['staffid', ['firstname', 'lastname']], 'flexiblewa_followers', '', ['multiple' => true, 'required' => true], [], '', 'followers_picker', false); ?>
                </div>
            </div>`

            return html;
        }
        function makeFileInput() {
            let html = ''
            html += `<div class='row'>
                <div class="col-md-6 col-md-offset-3">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_ADD_NEW_FILE_ACTION ?>' />
                    <?php echo render_input('file', 'flexiblewa_file', '', 'file'); ?>
                </div>
            </div>`

            return html;
        }
        function makeAnotherRelationInput() {
            let html = ''
            html += `<div class='row relations'>
                <div class="col-md-6 col-md-offset-3">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_MOVE_TO_ANOTHER_RELATION_ACTION ?>' />
                    <?php echo render_select('rel_type', flexiblewa_get_relation_types(), ['id', 'name'], 'flexiblewa_relation_type', '', ['required' => true]); ?>
                </div>
            </div>`

            return html;
        }
        function makeMarkAsCompleteInput() {
            let html = ''
            html += `<div class='row'>
                <div class="col-md-6 col-md-offset-3">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_MARK_AS_COMPLETE_ACTION ?>'/>
                    <div class="form-group">
                        <div class="form-check">
                        <span class="form-check-input-styled-success">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</svg>
                          </span>
                          <label class="form-check-label" for="mark_as_complete">
                            <?php echo _l('flexiblewa_mark_as_complete'); ?>
                          </label>

                        </div>
                    </div>
                </div>`
            return html;
        }
        function makeRelationsInput(relations) {
            let html = ''
            html += `
                <div id="relations-container" class="col-md-6 col-md-offset-3">
                    <div class="form-group select-placeholder>">
                        <label for="relation_id"><?php echo _l('flexiblewa_relations'); ?></label>
                        <select name="relation_id" id="relation_id" class="selectpicker" data-width="100%"
                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" required>`

            for (let index = 0; index < relations.length; index++) {
                const relation = relations[index];
                html += `<option value="${relation.id}">
                                ${relation.name}
                            </option>`
            }

            html += `</select>
                    </div>
                </div>`

            return html;
        }
        
        function makeSectionInput(statuses) {
            let html = ''
            html += `<div class='row'>
                <div class="col-md-6 col-md-offset-3">
                    <input type='hidden' name='rule_id' value='<?php echo FLEXIBLEWA_MOVE_TO_SECTION_ACTION ?>' />
                    <div class="form-group select-placeholder>">
                        <label for="move_to_section"><?php echo _l('flexiblewa_move_to_section'); ?></label>
                        <select name="move_to_section" id="move_to_section" class="selectpicker" data-width="100%"
                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                            multiple data-live-search="true" required>`

            for (let index = 0; index < statuses.length; index++) {
                const status = statuses[index];
                html += `<option value="${status.id}">
                                ${status.name}
                            </option>`
            }

            html += `</select>
                    </div>
                </div>
            </div>`

            return html;
        }

        function flexible_init_new_task_comment(manual) {
            if (tinymce.editors.flexiblewa_task_comment) {
                tinymce.remove("#flexiblewa_task_comment");
            }

            var editorConfig = _simple_editor_config();

            if (typeof manual == "undefined" || manual === false) {
                editorConfig.auto_focus = true;
            }

            // Not working fine on iOs
            var iOS = is_ios();

            editorConfig.plugins[0] += " mention";

            editorConfig.content_style =
                "span.mention {\
                    background-color: #eeeeee;\
                    padding: 3px;\
                }";

            editorConfig.setup = function (editor) {
                initStickyTinyMceToolbarInModal(
                    editor,
                    document.querySelector(".task-modal-single")
                );

                editor.on("init", function () {
                    if ($("#mention-autocomplete-css").length === 0) {
                        $("<link>")
                            .appendTo("head")
                            .attr({
                                id: "mention-autocomplete-css",
                                type: "text/css",
                                rel: "stylesheet",
                                href:
                                    site_url +
                                    "assets/plugins/tinymce/plugins/mention/autocomplete.css",
                            });
                    }

                    if ($("#mention-css").length === 0) {
                        $("<link>")
                            .appendTo("head")
                            .attr({
                                type: "text/css",
                                id: "mention-css",
                                rel: "stylesheet",
                                href:
                                    site_url +
                                    "assets/plugins/tinymce/plugins/mention/rte-content.css",
                            });
                    }
                });
            };

            var UserMentions = [];
            editorConfig.mentions = {
                source: function (query, process, delimiter) {
                    if (UserMentions.length < 1) {
                        $.getJSON(
                            getBaseURL(),
                            {
                                action: "get_mentions",
                            },
                            function (data) {
                                UserMentions = data;
                                process(data);
                            }
                        );
                    } else {
                        process(UserMentions);
                    }
                },
                insert: function (item) {
                    return (
                        '<span class="mention" contenteditable="false" data-mention-id="' +
                        item.id +
                        '">@' +
                        item.name +
                        "</span>&nbsp;"
                    );
                },
            };

            if (!iOS) {
                init_editor("#flexiblewa_task_comment", editorConfig);
            }
        }

        getDisplayContainer().on('change', '#rel_type', function(e){
            e.preventDefault()
            const rel_type = $(this).val()
            const reqData = {
                action: 'relations',
                rel_type: rel_type
            }
            $.get(getBaseURL(),reqData,
                function(data, textStatus, jqXHR){
                    if (data.success) {
                        $('#relations-container').remove();
                        $('.relations').append(makeRelationsInput(data.data.relations))
                        // Programatically re-render select picker
                        $('#relation_id').selectpicker('render');
                    }
                },
                "json"
            );
        })
        
        $('.action-btn').on('click', function (e) {
            e.preventDefault();
            const section_id = $('#section_id').val();
            const action_id = $(this).data('id')

            if (section_id) {
                getDisplayContainer().html(makeSpinner())
                switch (action_id) {
                    case 'set_assigned_to':
                        let rData = {
                            action: 'assign_to_staff',
                            action_id: action_id
                        }
                        $.get(getBaseURL(), rData,
                            function(data, textStatus, jqXHR){
                                if (data.success) {
                                    getDisplayContainer().html(makeAssigneesInput(data.data.members))
                                    // Programatically re-render select picker
                                    $('#assignees').selectpicker('render');
                                }
                            },
                            "json"
                        );

                        break;
                    case 'set_due_date_to':
                        getDisplayContainer().html(makeDueDateInput())
                        
                        // Programatically re-render select picker
                        $('#period').selectpicker('render');
                        break;
                    case 'set_priority_to':
                        getDisplayContainer().html(makePriorityInput())
                        
                        // Programatically re-render select picker
                        $('#priority').selectpicker('render');
                        break;
                    case 'add_new_checklist_item':
                        getDisplayContainer().html(makeChecklistInput())

                        break;
                    case 'add_new_reminder':
                        getDisplayContainer().html(makeReminderInput())

                        // Programatically re-render select picker
                        $('#period').selectpicker('render');
                        // Programatically re-render select picker
                        $('#reminder_user_id').selectpicker('render');
                        break;
                    case 'add_new_comment':
                        getDisplayContainer().html(makeCommentInput())
                        flexible_init_new_task_comment();
                        break;
                    case 'add_new_follower':
                        getDisplayContainer().html(makeFollowersInput())
                        // Programatically re-render select picker
                        $('.followers_picker').selectpicker('render');

                    break;
                    case 'add_new_file':
                        getDisplayContainer().html(makeFileInput())

                    break;
                    case 'move_to_another_relation':
                        getDisplayContainer().html(makeAnotherRelationInput())
                        // Programatically re-render select picker
                        $('#rel_type').selectpicker('render');
                        break;
                    case 'move_to_section':
                        const requestData = {
                            action: 'move_to_section',
                            action_id: action_id,
                            status_id: section_id
                        }
                        $.get(getBaseURL(), requestData,
                            function(data, textStatus, jqXHR){
                                if (data.success) {
                                    let html = makeSectionInput(data.data.statuses)
                                    getDisplayContainer().html(html)
                                    // Programatically re-render select picker
                                    $('#move_to_section').selectpicker('render');
                                }
                            },
                            "json"
                        );
                    break;
                    case 'mark_as_complete':
                        getDisplayContainer().html(makeMarkAsCompleteInput())
                    break;
                    default:
                        break;
                }
            } else {
                let message = "<?php echo _l('flexiblewa_select_section') ?>";
                alert_float('danger', message);
            }
        })
    });
</script>
</body>

</html>