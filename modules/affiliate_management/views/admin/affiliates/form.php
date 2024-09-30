<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <?= $title; ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">

                        <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
                        <?php $this->load->view('authentication/includes/alerts'); ?>

                        <?php echo form_open($this->uri->uri_string(), ['id' => 'affiliates_form']); ?>

                        <?= render_select('contact_id', $contacts, ['id', ['email']], 'affiliate_management_contact', ''); ?>

                        <div class="text-right">
                            <button type="submit" data-loading-text="..." data-form="#affiliates_form" class="btn btn-primary mtop15 mbot15"><?php echo _l('submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>

</html>