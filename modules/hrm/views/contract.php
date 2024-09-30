<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
<div class="content">
   <div class="row">
      <?php if(isset($contracts)){ ?>
      <div class="col-md-12">
         <div class="panel_s">
         </div>
      </div>
      <div class="member">
         <?php echo form_hidden('isedit'); ?>
         <?php echo form_hidden('contractid',$contracts[0]['id_contract']); ?>
      </div>
      <?php } ?>
    
      <?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'staff-form','autocomplete'=>'off')); ?>

        <div class="col-md-12" >
         <div class="panel_s">
           <div class="panel-body">
               <?php if(isset($contracts)){ ?>
                  <h4 class="no-margin"><?php echo _l('edit_contract') ?> 
                  </h4>
               <?php }else{?>
                 <h4 class="no-margin"><?php echo _l('new_contract') ?> 
                 </h4>
               <?php } ?>
            </div>
            <br>
            <div class="panel-body">
              <!-- start tab -->
               <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                   <a href="#tab_public_information" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
                   <span class="glyphicon glyphicon-file"></span>&nbsp;<?php echo _l('public_information'); ?>
                   </a>
                </li>
               <li role="presentation" >
                   <a href="#tab_wages_allowances" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
                   <span class="glyphicon glyphicon-usd"></span>&nbsp;<?php echo _l('wages_allowances'); ?>
                   </a>
                </li>
                <li role="presentation">
                   <a href="#tab_signed_information" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
                   <span class="glyphicon glyphicon-pencil"></span>&nbsp;<?php echo _l('signed_information'); ?>
                   </a>
                </li>
              </ul>
              <div class="tab-content">
                     <!-- start -->
                <div role="tabpanel" class="tab-pane active" id="tab_public_information">
                    <div class="col-md-12">
                      <h4 class="no-margin"><?php echo _l('public_information') ?></h4>
                      <hr class="hr-panel-heading">
                      <div class="row">
                          <?php $value = (isset($contracts) ? $contracts[0]['name_contract'] : ''); ?>
                          <?php $attrs = (isset($contracts) ? array() : array('autofocus'=>true)); ?>
                        <div class="col-md-6">

                           <?php 
                           $contract_code = (isset($contracts) ? $contracts[0]['contract_code'] : '');
                           echo render_input('contract_code','contract_code',$contract_code,'text',$attrs); ?>   
                        </div>
                        <div class="col-md-6">
                            <label for="staff" class="control-label"><?php echo _l('staff'); ?></label>
                            <select name="staff" class="selectpicker" id="staff" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                               <option value=""></option>                  
                              <?php foreach($staff as $s){ ?>
                              <option value="<?php echo htmlspecialchars($s['staffid']); ?>"  <?php if(isset($contracts) && $contracts[0]['staff'] == $s['staffid'] ){echo 'selected';} ?>> <?php echo htmlspecialchars($s['firstname']).' '.htmlspecialchars($s['lastname']); ?></option>                  
                              <?php }?>
                            </select>
                        </div>
                      </div>
          
                      <div class="row">
                        <div class="col-md-6">
                        <label for="name_contract" class="control-label"><?php echo _l('name_contract'); ?></label>
                          <select name="name_contract" class="selectpicker" id="name_contract" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                             <option value=""></option>                  
                             <?php foreach($contract_type as $c){ ?>
                              <option value="<?php echo htmlspecialchars($c['id_contracttype']); ?>" <?php if(isset($contracts) && $contracts[0]['name_contract'] == $c['id_contracttype'] ){echo 'selected';} ?>><?php echo htmlspecialchars($c['name_contracttype']); ?> </option>
                             <?php }?>
                          </select>
                        </div>



                      <div class="col-md-6">

                        <label for="contract_status" class="control-label"><?php echo _l('contract_status'); ?></label>
                        <select name="contract_status" class="selectpicker" id="contract_status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                          <option value=""></option> 
                          <option value="draft" <?php if(isset($contracts) && $contracts[0]['contract_status'] == 'draft' ){echo 'selected';} ?> ><?php echo _l('draft') ?></option>
                          <option value="valid" <?php if(isset($contracts) && $contracts[0]['contract_status'] == 'valid' ){echo 'selected';} ?>><?php echo _l('valid') ?></option>
                          <option value="invalid" <?php if(isset($contracts) && $contracts[0]['contract_status'] == 'invalid' ){echo 'selected';} ?>><?php echo _l('invalid') ?></option>
                        </select>

                      </div>


						


                      </div><br>
                    <div class="row">
                      <div class="col-md-6">
                            <?php
                            $start_valid = (isset($contracts) ? $contracts[0]['start_valid'] : '');
                            echo render_date_input('start_valid','start_valid',_d($start_valid)); ?>
                        
                      </div>
                      <div class="col-md-6">
                            <?php
                            $end_valid = (isset($contracts) ? $contracts[0]['end_valid'] : '');
                            echo render_date_input('end_valid','end_valid',_d($end_valid)); ?>
                      </div>

                    </div>

                    </div>
                </div>
                    <!-- end -->

                    <!-- start -->
                                  <?php $key_total =1; $key =1 ?>
                <div role="tabpanel" class="tab-pane" id="tab_wages_allowances">
                    <div class="col-md-12 ">
                        <h4 class="no-margin"><?php echo _l('information_wages_allowances') ?></h4>
						<hr class="hr-panel-heading">
                      <div class="class-wages-allowances" >
                        <!-- foreach start -->
                        <?php  if(isset($contract_details) && count($contract_details) != 0){
                            foreach ($contract_details as $keydetails => $value) {
                              $keydetails = $keydetails +1;
                          ?>
                        <div id="id_wages_allowances" class="col-md-12 hrm-margin9 hrm-bgblue">
                          <br><br>
                          <div class="row">
                            <div class="col-md-6">
                                <?php
                                $since_date = (isset($value['since_date']) ? $value['since_date'] : '');
                                echo render_date_input('since_date['.$keydetails.']','since_date',_d($since_date)); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $contract_note = (isset($value['contract_note']) ? $value['contract_note'] : ''); ?>
                                <?php echo render_input('contract_note['.$keydetails.']','contract_note',$contract_note); ?>
                            </div>
                          </div>

                          <div class="row " >
                            <!-- start -->
                            <div class="col-md-6 contract-expense-al">
                            
                            <?php if(isset($value['contract_salary_expense'])){

                              $contract_salary_Array = json_decode($value['contract_salary_expense'], true);
                            foreach ($contract_salary_Array as $keycontract => $contract_salary_expense) {
                              $keycontract = $keycontract + 1;
                              
                            ?>
                              <!-- foreach contract-expense -->
                              <div id ="contract-expense" class="row">
                                <div class="col-md-5 ">

                                  <label for="salary_form[<?php echo htmlspecialchars($keydetails); ?>][<?php echo htmlspecialchars($keycontract); ?>]" class="control-label get_id_row" value="<?php echo htmlspecialchars($keydetails); ?>"><?php echo _l('salary_form'); ?></label>
                                  

                                  <select onchange="OnSelectionChange_salsaryform (this)" name="salary_form[<?php echo htmlspecialchars($keydetails); ?>][<?php echo htmlspecialchars($keycontract); ?>]" class="selectpicker" id="salary_form[<?php echo htmlspecialchars($keydetails); ?>][<?php echo htmlspecialchars($keycontract); ?>]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                    <option value=""></option> 
                                    <?php
                                    foreach($salary_form as $s){                             
                                      ?>
                                      <option value="<?php echo htmlspecialchars($s['form_id']); ?>" <?php if(isset($contract_salary_expense) && $contract_salary_expense['type'] == $s['form_id'] ){echo 'selected';} ?>><?php echo htmlspecialchars($s['form_name']); ?></option>

                                     <?php } ?>
                                  </select>
                                </div>
                                <div class="col-md-5 ">
                                     <?php $value_expense = (isset($contract_salary_expense['value']) ? $contract_salary_expense['value'] : ''); ?>
                           
                                      <?php  echo render_input('contract_expense['.$keydetails.']['. $keycontract.']','amount', $value_expense); ?> 


                                </div>
                                <?php if($keycontract == 1){ ?>
                                <div class="col-md-2 hrm-lineheight84 hrm-nowrap" name="button_add">
                                  <button name="add" class="btn new_contract_expense btn-success hrm-radius20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                </div>
                                <?php } else{ ?>
                                  <div class="col-md-2 hrm-lineheight84 hrm-nowrap" name="button_add">
                                  <button name="add" class="btn remove_contract_expense btn-danger hrm-radius20" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                                </div>
                                <?php } ?>

                              </div>
                               <?php } ?>
                            <?php } ?>

                              <!-- end foreach contract-expense -->
                            </div>
                            <!-- end -->
                            
                            <!-- start -->
                            <div class="col-md-6 contract-allowance-type">
                              <?php 

                                if(isset($value['contract_allowance_expense'])){
                              $contract_allowance_Array = json_decode($value['contract_allowance_expense'], true);
                            foreach ($contract_allowance_Array as $keycontract => $contract_allowance_expense) {
                              $keycontract = $keycontract + 1;
                            ?>
                              <!-- foreach contract-allowancetype -->
                              <div id ="contract-allowancetype" class="row">

                                <div class="col-md-5">
                                   <label  for="allowance_type[<?php echo htmlspecialchars($keydetails); ?>][<?php echo htmlspecialchars($keycontract); ?>]" class="control-label get_id_row_allowance" value="<?php echo htmlspecialchars($keydetails); ?>"><?php echo _l('allowance_type'); ?></label>
                                <select onchange="OnSelectionChange_allowancetype (this)" name="allowance_type[<?php echo htmlspecialchars($keydetails); ?>][<?php echo htmlspecialchars($keycontract); ?>]" class="selectpicker" id="allowance_type[<?php echo htmlspecialchars($keydetails); ?>][<?php echo htmlspecialchars($keycontract); ?>]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                  <option value=""></option> 
                                  <?php
                                  foreach($allowance_type as $s){                             
                                    ?>
                                    <option value="<?php echo htmlspecialchars($s['type_id']); ?>" <?php if(isset($contract_allowance_expense) && $contract_allowance_expense['type'] == $s['type_id'] ){echo 'selected';} ?>><?php echo htmlspecialchars($s['type_name']); ?></option>

                                   <?php } ?>
                                </select>
                                </div>
                                <div class="col-md-5">
                                   <?php $value_allowance = (isset($contract_allowance_expense) ? $contract_allowance_expense['value'] : ''); ?>

                                    <?php echo render_input('allowance_expense['. $keydetails.']['. $keycontract.']','amount',$value_allowance); ?>
                   

                                </div>
                                <?php if($keycontract == 1){ ?>
                                <div class="col-md-2 hrm-lineheight84 hrm-nowrap" name="button_allowance_type">
                                  <button name="add" class="btn new_contract_allowance_type btn-success hrm-radius20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                </div>
                              <?php }else{ ?>
                                <div class="col-md-2 hrm-lineheight84 hrm-nowrap" name="button_allowance_type">
                                  <button name="add" class="btn remove_contract_allowance_type btn-danger hrm-radius20" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                                </div>
                              <?php } ?>
                              </div>
                            <?php } ?>
                            <?php } ?>
                           

                            </div>
                            <!-- end -->
                          </div>
                          <!-- button for wages_allowances -->
                          <?php if($keydetails == 1){ ?>
                          <div class="col-md-2 hrm-lineheight84 hrm-nowrap" name="button_wages_allowances">
                                  <button name="add_wages_allowances" class="btn new_wages_allowances btn-success hrm-radius20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                          </div>
                        <?php }else{ ?>
                          <div class="col-md-2 hrm-lineheight84 hrm-nowrap" name="button_wages_allowances">
                                  <button name="add_wages_allowances" class="btn  remove_wages_allowances btn-danger hrm-radius20" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                          </div>
                        <?php } ?>
                        </div>
                       <?php } ?>
                      <?php }else{ ?>

                        <div id="id_wages_allowances" class="col-md-12 hrm-bgblue hrm-margin9">
                          <hr class="hr-panel-heading">
                          <div class="row">
                            <div class="col-md-6">
                                <?php
                                $since_date = (isset($contracts[0]['since_date']) ? $contracts[0]['since_date'] : '');
                                echo render_date_input('since_date['.$key_total.']','since_date',_d($since_date)); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($expense) ? $expense->expense_name : ''); ?>
                                <?php echo render_input('contract_note['.$key_total.']','contract_note',$value); ?>
                            </div>
                          </div>

                          <div class="row " >
                            <!-- start -->
                            <div class="col-md-6 contract-expense-al">
                              <div id ="contract-expense" class="row">
                                <div class="col-md-5 ">

                                  <label for="salary_form[<?php echo htmlspecialchars($key_total); ?>][<?php echo htmlspecialchars($key); ?>]" class="control-label"><?php echo _l('salary_form'); ?></label>
                      

                                  <select name="salary_form[<?php echo htmlspecialchars($key_total); ?>][<?php echo htmlspecialchars($key); ?>]" class="selectpicker" id="salary_form[<?php echo htmlspecialchars($key_total); ?>][<?php echo htmlspecialchars($key); ?>]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                    <option value=""></option> 
                                    <?php
                                    foreach($salary_form as $s){                             
                                      ?>
                                      <option value="<?php echo htmlspecialchars($s['form_id']); ?>" <?php if(isset($contracts) && $contracts[0]['salary_form'] == $s['form_id'] ){echo 'selected';} ?>><?php echo htmlspecialchars($s['form_name']); ?></option>

                                     <?php } ?>
                                  </select>
                                </div>
                                <div class="col-md-5 ">
                                     <?php $value = (isset($expense) ? $expense->expense_name : ''); ?>

                                      <div class="form-group" app-field-wrapper="contract_expense[1][1]">
                                        <label for="contract_expense[1][1]" class="control-label get_id_row" value="1"><?php echo _l('amount_of_money'); ?></label>
                                        <input type="text" id="contract_expense[1][1]" name="contract_expense[1][1]" class="form-control" >
                                      </div>

                                </div>
                                <div class="col-md-2 hrm-lineheight84 hrm-nowrap" name="button_add">
                                  <button name="add" class="btn new_contract_expense btn-success hrm-radius20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                </div>
                              </div>
                            </div>
                            <!-- end -->
                            
                            <!-- start -->
                            <div class="col-md-6 contract-allowance-type">
                              <div id ="contract-allowancetype" class="row">

                                <div class="col-md-5">
                                   <label for="allowance_type[<?php echo htmlspecialchars($key_total); ?>][<?php echo htmlspecialchars($key); ?>]" class="control-label"><?php echo _l('allowance_type'); ?></label>
                                <select name="allowance_type[<?php echo htmlspecialchars($key_total); ?>][<?php echo htmlspecialchars($key); ?>]" class="selectpicker" id="allowance_type[<?php echo htmlspecialchars($key_total); ?>][<?php echo htmlspecialchars($key); ?>]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                  <option value=""></option> 
                                  <?php
                                  foreach($allowance_type as $s){                             
                                    ?>
                                    <option value="<?php echo htmlspecialchars($s['type_id']); ?>" <?php if(isset($contracts) && $contracts[0]['allowance_type'] == $s['type_id'] ){echo 'selected';} ?>><?php echo htmlspecialchars($s['type_name']); ?></option>

                                   <?php } ?>
                                </select>
                                </div>
                                <div class="col-md-5">
                                   <?php $value = (isset($expense) ? $expense->expense_name : ''); ?>

                                      <div class="form-group" app-field-wrapper="allowance_expense[1][1]">
                                        <label for="allowance_expense[1][1]" class="control-label get_id_row_allowance" value="1"><?php echo _l('amount_of_money'); ?></label>
                                        <input type="text" id="allowance_expense[1][1]" name="allowance_expense[1][1]" class="form-control" value="">
                                      </div>

                                </div>
                                <div class="col-md-2 hrm-lineheight84 hrm-nowrap" name="button_allowance_type">
                                  <button name="add" class="btn new_contract_allowance_type btn-success hrm-radius20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                </div>
                              </div>
                            </div>
                            <!-- end -->
                          </div>
                          <!-- button for wages_allowances -->
                          <div class="col-md-2 hrm-lineheight84 hrm-nowrap" name="button_wages_allowances">
                                  <button name="add_wages_allowances" class="btn new_wages_allowances btn-success hrm-radius20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                          </div>

                        </div>

                      <?php } ?>
                        <!-- foreach  end-->
                      </div>
                      <br>
    
                    </div>
                </div>
                    <!-- end -->

                    <!-- start -->
                <div role="tabpanel" class="tab-pane" id="tab_signed_information">
                    <div class="row">
                      <div class="col-md-6">
                          <?php
                          $sign_day = (isset($contracts) ? $contracts[0]['sign_day'] : '');
                          echo render_date_input('sign_day','sign_day',_d($sign_day)); ?>
                        
                      </div>
                      <div class="col-md-6">
                        <label for="staff_delegate" class="control-label"><?php echo _l('staff_delegate'); ?></label>
                            <select name="staff_delegate" class="selectpicker" id="staff_delegate" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                               <option value=""></option>                  
                              <?php foreach($staff as $s){ ?>
                              <option value="<?php echo htmlspecialchars($s['staffid']); ?>"  <?php if(isset($contracts) && $contracts[0]['staff_delegate'] == $s['staffid'] ){echo 'selected';} ?>> <?php echo htmlspecialchars($s['firstname']).''.htmlspecialchars($s['lastname']); ?></option>                  
                              <?php }?>
                            </select>
                      </div>
					  
					  
                    </div>
                </div>
                    <!-- end -->

              </div>
              <!-- endtab -->
            </div>
            <!-- end panel-body -->
         </div>
      </div>
      <div class="btn-bottom-toolbar text-right btn-toolbar-container-out">
         <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      </div>
      <?php echo form_close(); ?>
   </div>

   <div class="btn-bottom-pusher"></div>
</div>
<?php init_tail(); ?>
</body>
</html>
