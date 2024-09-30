<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
 <?php echo form_open(admin_url('hrm/setting_hrm_permission'), array('id'=>'setting_hrm_permission')); ?>

<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="applicable_object">

    <div class="row">
      <h5 class="publib-infor-title hrm-borderbottomlblue">
          <span class="publib-infor hrm-colorblue hrm-fontsize15"><?php echo _l('permission'); ?></span>
      </h5>
   </div>
   <hr>
   <div class="row">
     <div class="col-md-6">
       <label for="per_salary_allow[]" class="control-label"><?php echo _l('permission_edit_salary_allowance'); ?></label>
          <select name="per_salary_allow[]" id="per_salary_allow" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

        <?php if(get_hrm_option('per_salary_allow')){ $per_salary_allow = json_decode(get_hrm_option('per_salary_allow'), true) ;} ; ?>

            <?php foreach($staff as $staff_key =>  $staff_value){ ?>
            <option value="<?php echo htmlspecialchars($staff_value['staffid']); ?>"  <?php if(isset($per_salary_allow) && in_array($staff_value['staffid'], $per_salary_allow) == true ){echo 'selected';} ?>> <?php echo htmlspecialchars($staff_value['firstname']). ' ' . htmlspecialchars($staff_value['lastname']); ?></option>                  
            <?php }?>
          </select>
     </div>
     <div class="col-md-6">
          <label for="per_insurrance[]" class="control-label"><?php echo _l('permission_edit_change_insurance_rates'); ?></label>
          <select name="per_insurrance[]" id="per_insurrance" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        <?php if(get_hrm_option('per_insurrance')){ $per_insurrance = json_decode(get_hrm_option('per_insurrance'), true) ;} ; ?>

            <?php foreach($staff as $staff_key =>  $staff_value){ ?>
            <option value="<?php echo htmlspecialchars($staff_value['staffid']); ?>"  <?php if(isset($per_insurrance) && in_array($staff_value['staffid'], $per_insurrance) == true ){echo 'selected';} ?>> <?php echo htmlspecialchars($staff_value['firstname']). ' ' . htmlspecialchars($staff_value['lastname']); ?></option>                  
            <?php }?>
          </select>
       
     </div>
   </div>
   <br>
   <div class="row">
     <div class="col-md-6">
       <label for="per_create_payroll_t[]" class="control-label"><?php echo _l('permission_create_salary_model'); ?></label>
          <select name="per_create_payroll_t[]" id="per_create_payroll_t" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        <?php if(get_hrm_option('per_create_payroll_t')){ $per_create_payroll_t = json_decode(get_hrm_option('per_create_payroll_t'), true) ;} ; ?>

            <?php foreach($staff as $staff_key =>  $staff_value){ ?>
            <option value="<?php echo htmlspecialchars($staff_value['staffid']); ?>"  <?php if(isset($per_create_payroll_t) && in_array($staff_value['staffid'], $per_create_payroll_t) == true ){echo 'selected';} ?>> <?php echo htmlspecialchars($staff_value['firstname']). ' ' . htmlspecialchars($staff_value['lastname']); ?></option>                  
            <?php }?>
          </select>
     </div>
     <div class="col-md-6">
          <label for="per_create_payslip[]" class="control-label"><?php echo _l('permission_create_payroll'); ?></label>
          <select name="per_create_payslip[]" id="per_create_payslip" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        <?php if(get_hrm_option('per_create_payslip')){ $per_create_payslip = json_decode(get_hrm_option('per_create_payslip'), true) ;} ; ?>

            <?php foreach($staff as $staff_key =>  $staff_value){ ?>
            <option value="<?php echo htmlspecialchars($staff_value['staffid']); ?>"  <?php if(isset($per_create_payslip) && in_array($staff_value['staffid'], $per_create_payslip) == true ){echo 'selected';} ?>> <?php echo htmlspecialchars($staff_value['firstname']). ' ' . htmlspecialchars($staff_value['lastname']); ?></option>                  
            <?php }?>
          </select>
       
     </div>
   </div>
   <br>

   <div class="row">
     <div class="col-md-6">
       <label for="per_latch_payslip[]" class="control-label"><?php echo _l('permission_latch_payroll'); ?></label>
          <select name="per_latch_payslip[]" id="per_latch_payslip" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        <?php if(get_hrm_option('per_latch_payslip')){ $per_latch_payslip = json_decode(get_hrm_option('per_latch_payslip'), true) ;} ; ?>

            <?php foreach($staff as $staff_key =>  $staff_value){ ?>
            <option value="<?php echo htmlspecialchars($staff_value['staffid']); ?>"  <?php if(isset($per_latch_payslip) && in_array($staff_value['staffid'], $per_latch_payslip) == true ){echo 'selected';} ?>> <?php echo htmlspecialchars($staff_value['firstname']). ' ' . htmlspecialchars($staff_value['lastname']); ?></option>                  
            <?php }?>
          </select>
     </div>
     <div class="col-md-6">
          <label for="per_dependen_person[]" class="control-label"><?php echo _l('permission_approved_dependencies'); ?></label>
          <select name="per_dependen_person[]" id="per_dependen_person" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        <?php if(get_hrm_option('per_dependen_person')){ $per_dependen_person = json_decode(get_hrm_option('per_dependen_person'), true) ;} ; ?>

            <?php foreach($staff as $staff_key =>  $staff_value){ ?>
            <option value="<?php echo htmlspecialchars($staff_value['staffid']); ?>"  <?php if(isset($per_dependen_person) && in_array($staff_value['staffid'], $per_dependen_person) == true ){echo 'selected';} ?>> <?php echo htmlspecialchars($staff_value['firstname']). ' ' . htmlspecialchars($staff_value['lastname']); ?></option>                  
            <?php }?>
          </select>
       
     </div>
   </div>
   
  </div>
</div>
<hr/>
<div class="text-right btn-toolbar-container-out">
    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
</div>
<?php echo form_close(); ?>