<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<link href="<?php echo base_url('modules/api/assets/main.css'); ?>" rel="stylesheet" type="text/css" />

<div id="wrapper">
   <div class="content">
      <?php echo form_open('admin/api/user/'); ?>      

      <input type="hidden" name="id" value="<?php echo $user_api['id'] ?? ''?>" />
      <div class="row">
         <div class="col-md-4">
            <?php echo render_input('user', 'user_api', $user_api['user'] ?? ''); ?>
         </div>
         <div class="col-md-4">
            <?php echo render_input('name', 'name_api', $user_api['name'] ?? ''); ?>
         </div>
         <div class="col-md-4">
            <?php echo render_datetime_input('expiration_date', 'expiration_date', $user_api['expiration_date'] ?? ''); ?>
         </div>
      </div>
      <div class="row">
         <div class="col-md-12">
            <?php echo render_input('token', 'token_api', $user_api['token'] ?? '', 'text', ['readonly' => true]); ?>
         </div>
      </div>

      <?php $this->load->view('permissions'); ?>

      <div class="row">
         <div class="col-md-12">
            <button type="submit" class="btn btn-primary pull-right permission-save-btn" id="permission-form-submit">
               <?php echo _l('submit'); ?>
            </button>
         </div>
      </div>

      <?php echo form_close(); ?>
   </div>
</div>

<?php init_tail(); ?>

<script src="<?php echo base_url('modules/api/assets/main.js'); ?>"></script>