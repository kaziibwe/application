<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .zoom {
        transition: transform .2s; 
        width: 80px;
        height: 80px;
    }

    .zoom:hover {
        transform: scale(1.4); 
    }
</style>
<div class="panel_s section-heading section-invoices">
    <div class="panel-body">
        <h4 class="no-margin section-text"><?php echo _l('assets'); ?></h4>
    </div>
</div>
<div class="panel_s">
 <div class="panel-body">
     <table class="table dt-table table-invoices" data-order-col="1" data-order-type="desc">
         <thead>
            <tr>
                <th class="th-asset-image"><?php echo _l('asset_image'); ?></th>
                <th class="th-asset-code"><?php echo _l('asset_code'); ?></th>
                <th class="th-asset-name"><?php echo _l('asset_name'); ?></th>
                <th class="th-asset-group"><?php echo _l('asset_group'); ?></th>
                <th class="th-date-buy"><?php echo _l('date_buy'); ?></th>
                <th class="th-amount-allocate"><?php echo _l('amount_allocate'); ?></th>
                <th class="th-amount-rest"><?php echo _l('amount_rest'); ?></th>
                <th class="th-original-price"><?php echo _l('original_price'); ?></th>
                <th class="th-unit"><?php echo _l('unit'); ?></th>
                <th class="th-department"><?php echo _l('department'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allocated_asset as $asset) { ?>
                <tr>
                    <th class="th-asset-image"><img alt='<?php echo module_dir_url('assets', 'uploads').'/'.$asset['asset_image']; ?>' src='<?php echo module_dir_url('assets', 'uploads').'/'.$asset['asset_image']; ?>' class='img-thumbnail img-responsive zoom' onerror="this.src='<?php echo module_dir_url('assets', 'uploads'); ?>/image-not-available.png'"></th>
                    <th class="th-asset-code"><?php echo $asset['assets_code']; ?></th>
                    <th class="th-asset-name"><?php echo $asset['assets_name']; ?></th>
                    <th class="th-asset-group"><?php echo $asset['group_name']; ?></th>
                    <th class="th-date-buy"><?php echo _d($asset['date_buy']); ?></th>
                    <th class="th-amount-allocate"><?php echo $asset['total_allocation']; ?></th>
                    <th class="th-amount-rest"><?php echo $asset['amount'] - $asset['total_allocation']; ?></th>
                    <th class="th-original-price"><?php echo app_format_money($asset['unit_price'] * $asset['amount'], ''); ?></th>
                    <th class="th-unit"><?php echo $asset['unit_name']; ?></th>
                    <th class="th-department"><?php echo $asset['dpm_name']; ?></th>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</div>
