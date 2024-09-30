<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons">
                    <?php if (staff_can('create',  'customers')) { ?>
                    <a href="<?php echo admin_url('clients/client'); ?>"
                        class="btn btn-primary mright5 test pull-left display-block">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('new_client'); ?></a>
                    <a href="<?php echo admin_url('clients/import'); ?>"
                        class="btn btn-primary pull-left display-block mright5 hidden-xs">
                        <i class="fa-solid fa-upload tw-mr-1"></i>
                        <?php echo _l('import_customers'); ?></a>
                    <?php } ?>
                    <a href="<?php echo admin_url('clients/all_contacts'); ?>"
                        class="btn btn-default pull-left display-block mright5">
                        <i class="fa-regular fa-user tw-mr-1"></i>
                        <?php echo _l('customer_contacts'); ?>
                    </a>
                    <div class="visible-xs">
                        <div class="clearfix"></div>
                    </div>
                    <div id="vueApp" class="tw-inline pull-right tw-ml-0 sm:tw-ml-1.5">
                            <app-filters 
                                id="<?php echo $table->id(); ?>" 
                                view="<?php echo $table->viewName(); ?>"
                                :saved-filters="<?php echo $table->filtersJs(); ?>"
                                :available-rules="<?php echo $table->rulesJs(); ?>">
                        </app-filters>
                </div>
                </div>
                <div class="clearfix"></div>
                <div class="panel_s tw-mt-2 sm:tw-mt-4">
                    <div class="panel-body">

                        <?php if (staff_can('view',  'customers') || have_assigned_customers()) {
                      $where_summary = '';
                      if (staff_cant('view', 'customers')) {
                          $where_summary = ' AND userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
                      } ?>
                        <div class="mbot15">
                            <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="tw-w-5 tw-h-5 tw-text-neutral-500 tw-mr-1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>

                                <span>
                                    <?php echo _l('customers_summary'); ?>
                                </span>
                            </h4>
                            <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-6 tw-gap-2">
                                <div
                                    class="md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center">
                                    <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                        <?php echo total_rows(db_prefix() . 'clients', ($where_summary != '' ? substr($where_summary, 5) : '')); ?>
                                    </span>
                                    <span
                                        class="text-dark tw-truncate sm:tw-text-clip"><?php echo _l('customers_summary_total'); ?></span>
                                </div>
                                <div
                                    class="md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center">
                                    <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                        <?php echo total_rows(db_prefix() . 'clients', 'active=1' . $where_summary); ?></span>
                                    <span
                                        class="text-success tw-truncate sm:tw-text-clip"><?php echo _l('active_customers'); ?></span>
                                </div>
                                <div
                                    class="md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center">
                                    <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                        <?php echo total_rows(db_prefix() . 'clients', 'active=0' . $where_summary); ?></span>
                                    <span
                                        class="text-danger tw-truncate sm:tw-text-clip"><?php echo _l('inactive_active_customers'); ?></span>
                                </div>
                                <div
                                    class="md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center">
                                    <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                        <?php echo total_rows(db_prefix() . 'contacts', 'active=1' . $where_summary); ?>
                                    </span>
                                    <span
                                        class="text-info tw-truncate sm:tw-text-clip"><?php echo _l('customers_summary_active'); ?></span>
                                </div>
                                <div
                                    class="md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center">
                                    <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                        <?php echo total_rows(db_prefix() . 'contacts', 'active=0' . $where_summary); ?>
                                    </span>
                                    <span
                                        class="text-danger tw-truncate sm:tw-text-clip"><?php echo _l('customers_summary_inactive'); ?></span>
                                </div>
                                <div
                                    class="tw-flex tw-items-center md:tw-border-r md:tw-border-solid tw-flex-1 md:tw-border-neutral-300 lg:tw-border-r-0">
                                    <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                        <?php echo total_rows(db_prefix() . 'contacts', 'last_login LIKE "' . date('Y-m-d') . '%"' . $where_summary); ?>
                                    </span>
                                    <span class="text-muted tw-truncate" data-toggle="tooltip"
                                        data-title="<?php echo _l('customers_summary_logged_in_today'); ?>">
                                        <?php
                                            $contactsTemplate = '';
                                                if (count($contacts_logged_in_today) > 0) {
                                                    foreach ($contacts_logged_in_today as $contact) {
                                                        $url          = admin_url('clients/client/' . $contact['userid'] . '?contactid=' . $contact['id']);
                                                        $fullName     = e($contact['firstname'] . ' ' . $contact['lastname']);
                                                        $dateLoggedIn = e(_dt($contact['last_login']));
                                                        $html         = "<a href='$url' target='_blank'>$fullName</a><br /><small>$dateLoggedIn</small><br />";
                                                        $contactsTemplate .= html_escape('<p class="mbot5">' . $html . '</p>');
                                                    } ?>
                                                <?php } ?>
                                        <span<?php if ($contactsTemplate != '') { ?> class="pointer text-has-action"
                                            data-toggle="popover"
                                            data-title="<?php echo _l('customers_summary_logged_in_today'); ?>"
                                            data-html="true" data-content="<?php echo $contactsTemplate; ?>"
                                            data-placement="bottom" <?php } ?>>
                                            <?php echo _l('customers_summary_logged_in_today'); ?>
                                    </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php
                  } ?>
                        <hr class="hr-panel-separator" />
                        <a href="#" data-toggle="modal" data-target="#customers_bulk_action"
                            class="bulk-actions-btn table-btn hide"
                            data-table=".table-clients"><?php echo _l('bulk_actions'); ?></a>
                        <div class="modal fade bulk_actions" id="customers_bulk_action" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php if (staff_can('delete',  'customers')) { ?>
                                        <div class="checkbox checkbox-danger">
                                            <input type="checkbox" name="mass_delete" id="mass_delete">
                                            <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                        </div>
                                        <hr class="mass_delete_separator" />
                                        <?php } ?>
                                        <div id="bulk_change">
                                            <?php echo render_select('move_to_groups_customers_bulk[]', $groups, ['id', 'name'], 'customer_groups', '', ['multiple' => true], [], '', '', false); ?>
                                            <p class="text-danger">
                                                <?php echo _l('bulk_action_customers_groups_warning'); ?></p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default"
                                            data-dismiss="modal"><?php echo _l('close'); ?></button>
                                        <a href="#" class="btn btn-primary"
                                            onclick="customers_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                     $table_data  = [];
                     $_table_data = [
                      '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label></label></div>',
                       [
                         'name'     => _l('the_number_sign'),
                         'th_attrs' => ['class' => 'toggleable', 'id' => 'th-number'],
                        ],
                         [
                         'name'     => _l('clients_list_company'),
                         'th_attrs' => ['class' => 'toggleable', 'id' => 'th-company'],
                        ],
                         [
                         'name'     => _l('contact_primary'),
                         'th_attrs' => ['class' => 'toggleable', 'id' => 'th-primary-contact'],
                        ],
                         [
                         'name'     => _l('company_primary_email'),
                         'th_attrs' => ['class' => 'toggleable', 'id' => 'th-primary-contact-email'],
                        ],
                        [
                         'name'     => _l('clients_list_phone'),
                         'th_attrs' => ['class' => 'toggleable', 'id' => 'th-phone'],
                        ],
                         [
                         'name'     => _l('customer_active'),
                         'th_attrs' => ['class' => 'toggleable', 'id' => 'th-active'],
                        ],
                        [
                         'name'     => _l('customer_groups'),
                         'th_attrs' => ['class' => 'toggleable', 'id' => 'th-groups'],
                        ],
                        [
                         'name'     => _l('date_created'),
                         'th_attrs' => ['class' => 'toggleable', 'id' => 'th-date-created'],
                        ],
                      ];
                     foreach ($_table_data as $_t) {
                         array_push($table_data, $_t);
                     }

                     $custom_fields = get_custom_fields('customers', ['show_on_table' => 1]);

                     foreach ($custom_fields as $field) {
                         array_push($table_data, [
                           'name'     => $field['name'],
                           'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
                         ]);
                     }
                     $table_data = hooks()->apply_filters('customers_table_columns', $table_data);
                     ?>
                        <div class="panel-table-full">
                            <?php
                                render_datatable($table_data, 'clients', ['number-index-2'], [
                                    'data-last-order-identifier' => 'customers',
                                    'data-default-order'         => get_table_last_order('customers'),
                                    'id'=>'clients'
                                ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    var tAPI = initDataTable('.table-clients', admin_url + 'clients/table', [0], [0], {},
        <?php echo hooks()->apply_filters('customers_table_default_order', json_encode([2, 'asc'])); ?>);
});

function customers_bulk_action(event) {
    var r = confirm(app.lang.confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        var mass_delete = $('#mass_delete').prop('checked');
        var ids = [];
        var data = {};
        if (mass_delete == false || typeof(mass_delete) == 'undefined') {
            data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
            if (data.groups.length == 0) {
                data.groups = 'remove_all';
            }
        } else {
            data.mass_delete = true;
        }
        var rows = $('.table-clients').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids.push(checkbox.val());
            }
        });
        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
            $.post(admin_url + 'clients/bulk_action', data).done(function() {
                window.location.reload();
            });
        }, 50);
    }
}
</script>
</body>

</html>