<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open_multipart(admin_url('mmb/custom_pdf/store')); ?>
        <div class="row">
            <!-- vertical tabs: start -->
            <div class="col-md-3 mbot20">
                <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800"><?php echo _l('custom_pdf'); ?></h4>
                <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
                    <li class="settings-group-proposals <?php echo ('proposals' == $pdf_type) ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('mmb/custom_pdf/settings/proposals'); ?>" data-group="general">
                            <i class="fa-regular fa-file-powerpoint menu-icon"></i>
                            <?php echo _l('proposals'); ?>
                        </a>
                    </li>
                    <li class="settings-group-estimate <?php echo ('estimate' == $pdf_type) ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('mmb/custom_pdf/settings/estimate'); ?>" data-group="general">
                            <i class="fa-regular fa-file menu-icon"></i>
                            <?php echo _l('estimate'); ?>
                        </a>
                    </li>
                    <li class="settings-group-invoice <?php echo ('invoice' == $pdf_type) ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('mmb/custom_pdf/settings/invoice'); ?>" data-group="general">
                            <i class="fa fa-file-text menu-icon"></i>
                            <?php echo _l('invoice'); ?>
                        </a>
                    </li>
                    <li class="settings-group-credit_note <?php echo ('credit_note' == $pdf_type) ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('mmb/custom_pdf/settings/credit_note'); ?>" data-group="general">
                            <i class="fa-regular fa-file-lines menu-icon"></i>
                            <?php echo _l('credit_note'); ?>
                        </a>
                    </li>
                    <li class="settings-group-contract <?php echo ('contract' == $pdf_type) ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('mmb/custom_pdf/settings/contract'); ?>" data-group="general">
                            <i class="fa fa-file-contract menu-icon"></i>
                            <?php echo _l('contract'); ?>
                        </a>
                    </li>
                    <li class="settings-group-payment <?php echo ('payment' == $pdf_type) ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('mmb/custom_pdf/settings/payment'); ?>" data-group="general">
                            <i class="fa-solid fa-cart-shopping menu-icon"></i>
                            <?php echo _l('payment'); ?>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- vertical tabs: end -->
            <div class="col-md-9 mbot20">
                <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800">
                    <?php echo _l('pdf_sections', _l($pdf_type)); ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tab-content">
                            <?php $this->load->view('mmb/custom_pdf/settings/pdf_sections', ['pdf_type' => $pdf_type]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn-bottom-toolbar text-right">
                <button type="submit" class="btn btn-primary">
                    <?php echo _l('save'); ?>
                </button>
            </div>
        </div>
        <?php form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>