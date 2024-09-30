<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-6 col-md-offset-3">
            <div class="panel_s">
               <div class="panel-body">
                  <h4>Module activation</h4>
                  <hr class="hr-panel-heading">
                  Please activate your product, using your license purchase key (<a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">where can I find my purchase key?</a>)
                  <br><br>
                  <?php echo form_open($submit_url, ['autocomplete' => 'off', 'id' => 'verify-form']); ?>
                  <?php echo form_hidden('original_url', $original_url); ?>
                  <?php echo form_hidden('module_name', $module_name); ?>
                  <?php echo render_input('purchase_key', 'purchase_key', '', 'text', ['required' => true]); ?>
                  <div class="checkbox">
                     <input type="checkbox" id="confirmation" name="confirmation" value="">
                     <label for="confirmation">I do confirm that I comply with <a href="https://codecanyon.net/licenses/standard" target="_blank">Envato Licensing Terms</a> and my license will be used in a single domain. Attempting to activate my license in more than one domain simultaneously, is considered as violation.</label>
                  </div>
                  <div class="row mbot20">
                     <div class="col-md-12">
                        <button id="submit" type="submit" class="btn btn-primary pull-right">Activate License</button>
                     </div>
                  </div>
                  <?php echo form_close(); ?>
                  <hr class="hr-panel-heading">
                  <p class="text-muted">A single Purchase Code (License) is valid for a single domain. If you are using this item on a second instance, you are required to purchase a new license.</p>
               </div>
               <div class="panel-footer"><?php echo 'Version ' . $this->app_modules->get($module_name)['headers']['version'] ?? ''; ?></div>
            </div>
         </div>
         <div class="col-md-3">
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
   "use strict";
   appValidateForm($('#verify-form'), {
      purchase_key: 'required',
      confirmation: 'required'
   }, manage_verify_form, {
      confirmation: {
         required: "Please accept the terms and conditions before activating the license."
      }
   });

   function manage_verify_form(form) {
      $("#submit").prop('disabled', true).prepend('<i class="fa fa-spinner fa-pulse"></i> ');
      $.post(form.action, $(form).serialize()).done(function(response) {
         var response = $.parseJSON(response);
         if (!response.status) {
            alert_float("danger", response.message);
         }
         if (response.status) {
            alert_float("success", "Activating....");
            window.location.href = response.original_url;
         }
         $("#submit").prop('disabled', false).find('i').remove();
      });
   }
</script>