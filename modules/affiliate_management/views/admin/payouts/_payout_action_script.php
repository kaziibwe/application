<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="modal_payout_note" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span id="target_payout_title"></span> : <span id="target_payout_info"></span>
                </h4>
            </div>
            <div class="modal-body">
                <?= render_textarea('note_for_affiliate', _l('affiliate_management_note_for_affiliate') . ' (' . _l('affiliate_management_optional') . ')'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-primary" id="submit-payout" onclick="affiliateSubmitPayout();" data-loading-text="..." data-text="<?php echo _l('submit'); ?>"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    "use strict";


    const affiliatePayoutOptionClick = (anchor) => {
        $('#submit-payout').attr('data-href', anchor.attr('data-href'));
        $("#modal_payout_note").modal().show();
        $('#note_for_affiliate').val('');
        $('#target_payout_title').html(anchor.text());
        $('#target_payout_info').html(anchor.data('payoutinfo'));
    }

    const affiliateSubmitPayout = () => {

        const button = $("#submit-payout");
        button.attr('disabled', 'disabled');
        button.text(button.data('loading-text'));
        $.post(button.attr('data-href'), {
            note_for_affiliate: $('#note_for_affiliate').val(),
        }).done(function(response) {

            try {
                response = JSON.parse(response);
                alert_float(response.status, response.message);
                if (response.status === 'success') $('[data-dismiss]').click();

            } catch (error) {
                console.log(error);
            }

            button.removeAttr('disabled');
            button.text(button.data('text'));
            $('.payouts .btn-dt-reload').click();

        }).fail(function(error) {
            alert_float('danger', error.responseText);
            button.removeAttr('disabled');
            button.text(button.data('text'));

        });
    };
</script>