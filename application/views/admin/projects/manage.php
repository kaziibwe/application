<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div id="vueApp">
            <div class="row">
                <div class="col-md-12">
                    <div class="_buttons tw-mb-2 sm:tw-mb-4">
                        <?php if (staff_can('create',  'projects')) { ?>
                        <a href="<?php echo admin_url('projects/project'); ?>"
                            class="btn btn-primary pull-left display-block mright5">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('new_project'); ?>
                        </a>
                        <?php } ?>
                        <a href="<?php echo admin_url('projects/gantt'); ?>" data-toggle="tooltip"
                            data-title="<?php echo _l('project_gant'); ?>" class="btn btn-default btn-with-tooltip">
                            <i class="fa fa-align-left" aria-hidden="true"></i>
                        </a>
                        <div class="tw-inline pull-right">
                            <app-filters
                                id="<?php echo $table->id(); ?>"
                                view="<?php echo $table->viewName(); ?>"
                                :rules="extra.projectsRules || <?php echo app\services\utilities\Js::from($this->input->get('status') ? $table->findRule('status')->setValue([(int) $this->input->get('status')]) : []); ?>"
                                :saved-filters="<?php echo $table->filtersJs(); ?>"
                                :available-rules="<?php echo $table->rulesJs(); ?>">
                            </app-filters>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="panel_s tw-mt-2 sm:tw-mt-4">
                        <div class="panel-body">
                            <div class="row mbot15">
                                <div class="col-md-12">
                                    <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="tw-w-5 tw-h-5 tw-text-neutral-500 tw-mr-1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <span>
                                            <?php echo _l('projects_summary'); ?>
                                        </span>
                                    </h4>
                                    <?php
                                        $_where = '';
                                        if (staff_cant('view', 'projects')) {
                                            $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
                                        }
                                    ?>
                                </div>
                                <div class="_filters _hidden_inputs">
                                    <?php foreach ($statuses as $status) { ?>
                                    <div
                                        class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <?php $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = ' . $status['id']; ?>
                                        <a href="#"
                                            class="tw-text-neutral-600 hover:tw-opacity-70 tw-inline-flex tw-items-center"
                                            @click.prevent="extra.projectsRules = <?php echo app\services\utilities\Js::from($table->findRule('status')->setValue([(int) $status['id']])); ?>"
                                            >
                                            <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                                <?php echo total_rows(db_prefix() . 'projects', $where); ?>
                                            </span>
                                            <span style="color: <?php echo e($status['color']); ?>" class="<?php echo 'project-status-'.$status['color']; ?>">
                                                <?php echo e($status['name']); ?>
                                            </span>
                                        </a>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <hr class="hr-panel-separator" />
                            <div class="panel-table-full">
                                <?php echo form_hidden('custom_view'); ?>
                                <?php $this->load->view('admin/projects/table_html'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php init_tail(); ?>
<script>
$(function() {
    initDataTable('.table-projects', admin_url + 'projects/table', undefined, undefined, {},
        <?php echo hooks()->apply_filters('projects_table_default_order', json_encode([5, 'asc'])); ?>);

    init_ajax_search('customer', '#clientid_copy_project.ajax-search');
});
</script>
</body>
</html>