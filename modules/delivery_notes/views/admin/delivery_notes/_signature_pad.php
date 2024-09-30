<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$formData = '<div class="col-md-12">';
$formData .= form_hidden('action', 'sign_contract');
$formData .= render_input('signature_title', 'delivery_note_signature_title', $staff_signature->signature_title ?? '', 'text', ['required' => 'required', 'placeholder' => 'i.e Delivery Manager', 'autofocus' => 'true']);
$formData .= '</div><div class="col-md-12"><hr /></div>';

get_template_part('identity_confirmation_form', ['formAction' => admin_url('delivery_notes/append_signature/' . $delivery_note->id), 'formData' => $formData, 'contact' => get_staff()]);
$this->app_scripts->add('signature-pad', 'assets/plugins/signature-pad/signature_pad.min.js', $this->app_scripts->default_theme_group());
echo $this->app_scripts->compile($this->app_scripts->default_theme_group());
?>
<script>
    $('#identityConfirmationForm').appFormValidator({
        rules: {
            signature_title: 'required',
            acceptance_firstname: 'required',
            acceptance_lastname: 'required',
            signature: 'required',
            acceptance_email: {
                email: true,
                required: true
            }
        },
        messages: {
            signature: {
                required: app.lang.sign_document_validation,
            },
        },
    });
    $('body.identity-confirmation #accept_action').on('click', function() {
        var $submitForm = $('#identityConfirmationForm');
        $('#identityConfirmationModal').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        return false;
    });
    $('body').on('shown.bs.modal', '#identityConfirmationModal', function() {
        $(this).find(":input:not(:button):visible:enabled:not([readonly]):first").click().focus();
    });
</script>
<style>
    .signature-pad--body {
        border-radius: 4px;
        position: relative;
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        border: 1px solid #c0cbda;
    }
</style>