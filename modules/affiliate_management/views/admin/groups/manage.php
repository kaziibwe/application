<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="<?= admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/groups/new'); ?>"
                        class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('new'); ?>
                    </a>
                </div>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <table class="table dt-table" data-order-col="0" data-order-type="desc">
                            <thead>
                                <th><?php echo _l('id'); ?></th>
                                <th><?php echo _l('affiliate_mangement_group_name'); ?></th>
                                <th><?php echo _l('options'); ?></th>
                            </thead>
                            <tbody>
                                <?php foreach ($groups as $group_id => $group) { ?>
                                <tr>
                                    <td>
                                        <?php echo $group_id; ?>
                                    </td>
                                    <td>
                                        <?php echo $group['name']; ?>
                                    </td>
                                    <td>
                                        <div class="tw-flex tw-items-center tw-space-x-3">
                                            <a href="<?= admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/groups/edit/' . $group_id); ?>"
                                                class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                                                <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                            </a>
                                            <?php if ($group_id !== AffiliateManagementHelper::DEFAULT_GROUP_ID) : ?>
                                            <a href="<?= admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/groups/delete/' . $group_id); ?>"
                                                class="tw-mt-px tw-text-neutral-500 _delete">
                                                <i class="fa-regular fa-trash-can fa-lg"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
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


<?php init_tail(); ?>
</body>

</html>