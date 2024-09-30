<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-heading">
                    <div class="tw-flex tw-justify-between tw-items-center">
                        <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-self-end">
                            <?php echo flexibleleadfinder_lang('search-details'); ?>
                        </h4>
                        <div>
                            <a href="<?php echo flexibleleadfinder_admin_url() ?>" class="btn btn-primary mright5">
                                <i class="fa-solid fa-arrow-left tw-mr-1"></i>
                                <?php echo flexibleleadfinder_lang('lead-finder'); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <strong>
                                <?php echo flexibleleadfinder_lang('name') ?>:
                            </strong>
                            <?php
                            echo $search['name'];
                            ?>
                        </div>
                        <div class="col-md-12">
                            <strong>
                                <?php echo flexibleleadfinder_lang('keyword') ?>:
                            </strong>
                            <?php
                            echo $search['keyword'];
                            ?>
                        </div>
                        <div class="col-md-12">
                            <strong>
                                <?php echo flexibleleadfinder_lang('address') ?>:
                            </strong>
                            <?php
                            echo $search['location'];
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-heading">
                    <div class="tw-flex tw-justify-between tw-items-center">
                        <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-self-end">
                            <?php echo flexibleleadfinder_lang('contacts'); ?>
                        </h4>

                        <div>
                            <button data-id="<?php echo $search['id'] ?>" class="btn btn-warning mright5 flexlf-sync-all">
                                <i class="fa-solid fa-rotate tw-mr-1"></i>
                                <?php echo flexibleleadfinder_lang('sync-all'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="panel-body panel-table-full">
                    <table class="table dt-table" data-order-col="3" data-order-type="desc">
                        <thead>
                            <th>
                                <?php echo flexibleleadfinder_lang('name') ?>
                            </th>
                            <th>
                                <?php echo flexibleleadfinder_lang('email') ?>
                            </th>
                            <th>
                                <?php echo flexibleleadfinder_lang('phone') ?>
                            </th>
                            <th>
                                <?php echo flexibleleadfinder_lang('website'); ?>
                            </th>
                            <th>
                                <?php echo flexibleleadfinder_lang('address'); ?>
                            </th>
                            <th>
                                <?php echo flexibleleadfinder_lang('actions') ?>
                            </th>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                                <tr>
                                    <td data-order="<?php echo $contact['id'] ?>">
                                        <?php echo $contact['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $contact['email'] ?>
                                    </td>
                                    <td>
                                        <?php echo $contact['phonenumber'] ?>
                                    </td>
                                    <td>
                                        <?php echo $contact['website'] ?>
                                    </td>
                                    <td>
                                        <?php echo $contact['address'] ?>
                                    </td>
                                    <td>
                                        <?php if (!$contact['synced']) { ?>
                                            <button data-id="<?php echo $contact['id']; ?>" title="<?php echo flexibleleadfinder_lang('sync-to-lead'); ?>"
                                                class="btn btn-primary btn-sm tw-my-2 tw-block flexlf-sync-contact">
                                                <i class="fa-solid fa-rotate"></i>
                                            </button>
                                        <?php } ?>
                                        <button data-id="<?php echo $contact['id']; ?>" title="<?php echo _l('delete'); ?>"
                                            class="btn btn-danger btn-sm tw-my-2 tw-block flexlf-delete-contact">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
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