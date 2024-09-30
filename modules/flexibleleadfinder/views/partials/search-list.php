<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="tw-flex tw-justify-between tw-items-center tw-mb-2 sm:tw-mb-4">
                <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-self-end">
                    <?php echo flexibleleadfinder_lang('lead-finder'); ?>
                </h4>
                <div>
                    <a href="#" data-toggle="modal" data-target="#leadfinder_new_search-modal"
                        class="btn btn-primary mright5">
                        <i class="fa-solid fa-magnifying-glass tw-mr-1"></i>
                        <?php echo flexibleleadfinder_lang('new-search'); ?>
                    </a>
                    <a href="#" data-toggle="modal" data-target="#leadfinder_settings_modal"
                        class="btn btn-success mright5">
                        <i class="fa-solid fa-cog tw-mr-1"></i>
                        <?php echo flexibleleadfinder_lang('settings'); ?>
                    </a>
                    <?php if(is_admin(get_staff_user_id()) && count($searches) > 0){ ?>
                        <a href="<?php echo admin_url('flexibleleadfinder/delete_all_searches') ?>"
                            class="btn btn-danger mright5 _delete">
                            <i class="fa-solid fa-trash tw-mr-1"></i>
                            <?php echo flexibleleadfinder_lang('delete-all-searches'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body panel-table-full">
                    <table class="table dt-table" data-order-col="3" data-order-type="desc">
                        <thead>
                            <th>
                                <?php echo flexibleleadfinder_lang('name') ?>
                            </th>
                            <th>
                                <?php echo flexibleleadfinder_lang('keyword') ?>
                            </th>
                            <th>
                                <?php echo flexibleleadfinder_lang('address') ?>
                            </th>
                            <th>
                                <?php echo flexibleleadfinder_lang('result-count'); ?>
                            </th>
                            <th>
                                <?php echo flexibleleadfinder_lang('actions') ?>
                            </th>
                        </thead>
                        <tbody>
                            <?php foreach ($searches as $search): ?>
                                <tr>
                                    <td>
                                        <?php echo $search['name']; ?>
                                        <div class="row-options">
                                            <a href="<?php echo flexibleleadfinder_admin_url('view/' . $search['id']) ?>">
                                                <?php echo flexibleleadfinder_lang('view-result'); ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo $search['keyword'] ?>
                                    </td>
                                    <td>
                                        <?php echo $search['location'] ?>
                                    </td>
                                    <td>
                                        <?php echo $search['results_count'] ?>
                                    </td>
                                    <td data-order="<?php echo $search['id'] ?>">
                                        <button class="btn btn-danger btn-sm flexlf-delete-search"
                                            data-id="<?php echo $search['id'] ?>">
                                            <i class="fa-solid fa-trash"></i>
                                            <?php echo _l('delete'); ?>
                                        </button>
                                        <a class="btn btn-info" href="<?php echo flexibleleadfinder_admin_url('view/' . $search['id']) ?>">
                                            <i class="fa-solid fa-eye tw-mr-1"></i>
                                            <?php echo flexibleleadfinder_lang('view-result'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>