<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'payroll-type-form','autocomplete'=>'off')); ?>
        <div class="col-md-12">
            <div class="panel_s" >

                <div class="panel-body">
				
                    <h4 class="modal-title">
                        <?php if(isset($payroll_id)){ ?>
                        <span class="edit-title"><?php echo _l('payroll_type_update'); ?></span>
                    <?php } else{?>
                        <span class="add-title"><?php echo _l('payroll_type_add'); ?></span>
                    <?php } ?>
                    </h4>
				<br>
                    <div class="row">
					
                        <div class="col-md-12"><br><br>
                         <div id="additional_contract_type"></div> 
                         <div class="form" id="new_insurance">
                            <div class="row">
                                <div class="col-md-6">
                               <?php $payrolltypename = isset($payrolls->payroll_type_name) ? $payrolls->payroll_type_name : '' ?>     
                                <?php 
                            echo render_input('payroll_type_name','payroll_type_name', $payrolltypename); ?>
                                </div>

                            </div>
                            <div class="row">
                                <br>
                                <div class="col-md-3">
                                <label for="role_id" class="control-label"><?php echo _l('role'); ?></label>
                                <select name="role_id[]" id="role_id" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                  <?php if(isset($payrolls->role_id)){ $payroll_role = json_decode($payrolls->role_id, true) ;} ; ?>

                                  <?php foreach($roles as $rkey => $role){ ?>
                                  <option value="<?php echo htmlspecialchars($role['roleid']); ?>"  <?php if(isset($payroll_role) && in_array($role['roleid'], $payroll_role) == true ){echo 'selected';} ?>> <?php echo htmlspecialchars($role['name']); ?></option>                  
                                  <?php }?>
                                </select>
                                </div>

                                <div class="col-md-3">
                                <label for="position_id" class="control-label"><?php echo _l('position_of_applicable'); ?></label>
                                <select name="position_id[]" id="position_id" data-live-search="true" class="selectpicker" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                  <?php if(isset($payrolls->position_id)){ $payroll_po = json_decode($payrolls->position_id, true) ;} ; ?>

                                  <?php foreach($positions as $pokey => $po){ ?>
                                  <option value="<?php echo htmlspecialchars($po['position_id']); ?>" <?php if(isset($payrolls->position_id) && in_array($po['position_id'], $payroll_po) == true ){echo 'selected';} ?>> <?php echo htmlspecialchars($po['position_name']); ?></option>                  
                                  <?php }?>
                                </select>
                                </div>
                                <div class="col-md-6">
                                <label for="salary_form_id" class="control-label"><?php echo _l('bl'); ?></label>
                                <select  name="salary_form_id" class="selectpicker" id="salary_form_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                   <option value=""></option>  
                                  <option value="1"  <?php if(isset($payrolls->salary_form_id) && $payrolls->salary_form_id == 1 ){echo 'selected';} ?>> <?php echo _l('primary'); ?></option>
                                  <option value="2"  <?php if(isset($payrolls->salary_form_id) && $payrolls->salary_form_id == 2 ){echo 'selected';} ?>> <?php echo _l('allowance'); ?></option>                  
                                </select>
                                </div>
                            </div>
                            <div class="row">
                                <br>

                                <div class="col-md-6">
                                <label for="manager_id" class="control-label"><?php echo _l('manager'); ?></label>
                                <select name="manager_id" class="selectpicker" id="manager_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                   <option value=""></option>                  
                                  <?php foreach($staffs as $sm){ ?>
                                  <option value="<?php echo htmlspecialchars($sm['staffid']); ?>"  <?php if(isset($payrolls->manager_id) && $payrolls->manager_id == $sm['staffid'] ){echo 'selected';} ?>> <?php echo htmlspecialchars($sm['firstname']).''.htmlspecialchars($sm['lastname']); ?></option>                  
                                  <?php }?>
                                </select>

                                </div>
                                <div class="col-md-6">
                               <label for="follower_id" class="control-label"><?php echo _l('follower'); ?></label>
                                <select name="follower_id" class="selectpicker" id="follower_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                   <option value=""></option>                  
                                  <?php foreach($staffs as $sf){ ?>
                                  <option value="<?php echo htmlspecialchars($sf['staffid']); ?>"  <?php if(isset($payrolls->follower_id) && $payrolls->follower_id == $sf['staffid'] ){echo 'selected';} ?>> <?php echo htmlspecialchars($sf['firstname']).''.htmlspecialchars($sf['lastname']); ?></option>                  
                                  <?php }?>
                                </select>
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
								<br>
                                </div>
                            </div>


                        </div>
                        </div>

                    <div class="modal-footer">
                       <a href="<?php echo admin_url('hrm/payroll?group=payroll_type'); ?>"  class="btn btn-default "><?php echo _l('close'); ?></a>
                        <button type="submit" class="btn btn-info payroll-submit"><?php echo _l('submit'); ?></button>
                    </div>
                    </div>
                </div><!-- /.modal-content -->
            </div>
                <?php echo form_close(); ?>


                    <!-- </div>
                </div>
            </div> -->
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    $(function(){

    appValidateForm($('.payroll-type-form'),{
		
        position_id: {
            required: true
        },
        role_id: {
            required: true
        },
        payroll_type_name: {
            required: true
        },
        salary_form_id: {
                    required: true
                },

    });
});

    <?php if(isset($data_object)){ ?>

      var dataObject = <?php echo json_encode($data_object) ; ?>;
    <?php }else{ ?>



    var  dataObject = [
      ['column_value','','','',''],
      ['column_title','','','',''],
      ['column_key','','','',''],
      ['type','','','',''],
      ['calculation','','','',''],
      ['value','','','',''],
      ['payslip','','','',''],
      ['description','','','',''],
    ];

  <?php } ?>


  var  salaryData = {order_number :'<?php echo _l('order_number') ?>', hr_code: '<?php echo _l('hr_code') ; ?>', firstname:'<?php echo _l('full_name') ; ?>', job_position :'<?php echo _l('position') ; ?>', name :'<?php echo _l('deparment_name') ; ?>', company_name:'<?php echo _l('company_name') ; ?>', sex :'<?php echo _l('sex') ; ?>', email:'<?php echo _l('email') ; ?>', birthday:'<?php echo _l('birth_date') ; ?>', phonenumber :'<?php echo _l('phone_number') ; ?>', name_account :'<?php echo _l('bank_account') ; ?>', account_number :'<?php echo _l('bank_account_number') ; ?>', issue_bank:'<?php echo _l('bank_name') ; ?>', work_place :'<?php echo _l('work_place') ; ?>', work_number :'<?php echo _l('work_number') ; ?>', salary_l :'<?php echo _l('salary_l') ; ?>', salary_insurance_:'<?php echo _l('salary_insurance_') ; ?>', salary_alowance :'<?php echo _l('salary_alowance') ; ?>', salary_alowance_taxable :'<?php echo _l('salary_alowance_taxable') ; ?>', salary_alowance_no_taxable :'<?php echo _l('salary_alowance_no_taxable') ; ?>', individual_deduction_level :'<?php echo _l('individual_deduction_level') ; ?>', tax_exemption_level:'<?php echo _l('tax_exemption_level') ; ?>', date_entered_company :'<?php echo _l('date_entered_company') ; ?>', contract_code :'<?php echo _l('contract_code') ; ?>', contract_name:'<?php echo _l('contract_name') ; ?>', contract_type :'<?php echo _l('contract_type') ; ?>', hours_date :'<?php echo _l('hours_date') ; ?>', hours_week :'<?php echo _l('hours_week') ; ?>', month :'<?php echo _l('month') ; ?>', year :'<?php echo _l('year') ; ?>', date_total :'<?php echo _l('date_total') ; ?>', sunday_total :'<?php echo _l('sunday_total') ; ?>', saturday_total :'<?php echo _l('saturday_total') ; ?>', saturday_total_odd :'<?php echo _l('saturday_total_odd') ; ?>', saturday_total_even :'<?php echo _l('saturday_total_even') ; ?>', total_work_time :'<?php echo _l('total_work_time') ; ?>', effort_ceremony :'<?php echo _l('effort_ceremony') ; ?>', effort_by_collaborate :'<?php echo _l('effort_by_collaborate') ; ?>', effort_advance_payment :'<?php echo _l('effort_advance_payment') ; ?>', number_work_late :'<?php echo _l('number_work_late') ; ?>', number_leave_company_early :'<?php echo _l('number_leave_company_early') ; ?>', number_minu_late :'<?php echo _l('number_minu_late') ; ?>', number_minu_early :'<?php echo _l('number_minu_early') ; ?>', number_effort_leave :'<?php echo _l('number_effort_leave') ; ?>', number_effort_no_leave :'<?php echo _l('number_effort_no_leave') ; ?>', effort_work :'<?php echo _l('effort_work') ; ?>', total_actual_working_hours:'<?php echo _l('total_actual_working_hours') ; ?>', penalty_timekeeping :'<?php echo _l('penalty_timekeeping') ; ?>', effort_work_late :'<?php echo _l('effort_work_late') ; ?>', effort_work_early :'<?php echo _l('effort_work_early') ; ?>', effort_leave_without_reason :'<?php echo _l('effort_leave_without_reason') ; ?>', business_sales :'<?php echo _l('business_sales') ; ?>', actual_sales_turnover :'<?php echo _l('actual_sales_turnover') ; ?>', business_commission :'<?php echo _l('business_commission') ; ?>', business_contract_number:'<?php echo _l('Business_contract_number') ; ?>', business_order_number:'<?php echo _l('business_order_number') ; ?>', salary_day:'<?php echo _l('salary_day') ; ?>', hours_salary :'<?php echo _l('hours_salary') ; ?>', monthly_KPI_points :'<?php echo _l('monthly_KPI_points') ; ?>',total_money:'<?php echo _l('total_money') ; ?>', salary_transferred_company_account:'<?php echo _l('salary_transferred_company_account') ; ?>', salary_transferred_personal_account:'<?php echo _l('salary_transferred_personal_account') ; ?>', salary_paid:'<?php echo _l('salary_paid') ; ?>', unpaid_wages: '<?php echo _l('unpaid_wages') ; ?>', total_income:'<?php echo _l('total_income') ; ?>', income_taxes:'<?php echo _l('income_taxes') ; ?>', taxable_income:'<?php echo _l('taxable_income') ; ?>', personal_income_tax:'<?php echo _l('personal_income_tax') ; ?>', formula:'<?php echo _l('formula') ; ?>', constant:'<?php echo _l('constant') ; ?>'};


 

function titlleCheck(instance, td, row, col, prop, value, cellProperties) {


  //  conditions for now           
    if (td.innerHTML === undefined || td.innerHTML === null || td.innerHTML === "") {
    // test selectbox
    var selectbox = " <select  id=" + 'titlleselection' + col + " style=" + 'width:100%;' +'height:100%;'+ 'border:none'+" >";
        selectbox +=    "<option value =''></option>";
      <?php $payrolltypename = isset($column_value) ? $column_value : '' ?> 

        for (let elem in salaryData) {  
        selectbox +=  "<option value ="+elem+">"+salaryData[elem]+"</option>";
            };
   selectbox += "</select>";

        var $td = $(td);
        var $text = $(selectbox);
        $text.on('mousedown', function (event) {
                      event.stopPropagation(); //prevent selection quirk
                    });

            $td.empty().append($text);
            $('#titlleselection' + col).change(function () {
                var value = this[this.selectedIndex].value;
                instance.setDataAtCell(row, prop, value);
            });

        }

}

function titlleCheck_update(instance, td, row, col, prop, value, cellProperties) {
  //  conditions for now           
    if (td.innerHTML === undefined || td.innerHTML === null || td.innerHTML === "") {
    // test selectbox
    var selectbox = " <select  id=" + 'titlleselection' + col + " style=" + 'width:100%;' +'height:100%;'+ 'border:none'+" >";
        selectbox +=    "<option value =''></option>";
      
      <?php if(isset($column_value)){ ?>
      var column_value_array = <?php echo json_encode($column_value) ; ?>;
    <?php } ?>

        for (let elem in salaryData) {  
          if(elem == column_value_array[col]){
              selectbox +=  "<option value ="+elem+" selected>"+salaryData[elem]+"</option>";
            }else{
              selectbox +=  "<option value ="+elem+">"+salaryData[elem]+"</option>";
            }

            };
        selectbox += "</select>";

        var $td = $(td);
        var $text = $(selectbox);
        $text.on('mousedown', function (event) {
                      event.stopPropagation(); //prevent selection quirk
                    });

            $td.empty().append($text);
            $('#titlleselection' + col).change(function () {
                var value = this[this.selectedIndex].value;
                instance.setDataAtCell(row, prop, value);
            });

        }

}


  var salaryCheckdata ={
    total_row: '<?php echo _l('total_row') ; ?>', minimum_value_of_row: '<?php echo _l('minimum_value_of_row') ; ?>',maximum_value_of_row :'<?php echo _l('maximum_value_of_row') ; ?>', the_value_of_last_row:'<?php echo _l('the_value_of_last_row') ; ?>'
  }
function totalrowvalue(instance, td, row, col, prop, value, cellProperties) {

  //  conditions for now           
    if (td.innerHTML === undefined || td.innerHTML === null || td.innerHTML === "") {
    // test selectbox
        var selectbox = " <select id=" + 'salaryCheck' + col + "  style=" + 'width:100%;' +'height:100%;'+ 'border:none;'+ " >";
        selectbox +=    "<option value =''></option>";

        for (let elem in salaryCheckdata) {  
        selectbox +=  "<option value ="+elem+">"+salaryCheckdata[elem]+"</option>";
            };
   selectbox += "</select>";


            var $td = $(td);
        var $text = $(selectbox);
        $text.on('mousedown', function (event) {
                      event.stopPropagation(); //prevent selection quirk
                    });

            $td.empty().append($text);
            $('#salaryCheck' + col).change(function () {
                var value = this[this.selectedIndex].value;
                instance.setDataAtCell(row, prop, value);
            });

        }

}

function totalrowvalue_update(instance, td, row, col, prop, value, cellProperties) {
         
    if (td.innerHTML === undefined || td.innerHTML === null || td.innerHTML === "") {
    // test selectbox
        var selectbox = " <select id=" + 'salaryCheck' + col + "  style=" + 'width:100%;' +'height:100%;'+ 'border:none;'+ " >";
            selectbox +=    "<option value =''></option>";

        <?php if(isset($value_total)){ ?>
        var value_total_array = <?php echo json_encode($value_total) ; ?>;
        <?php } ?>
          for (let elem in salaryCheckdata) {  
            if(elem == value_total_array[col]){
                selectbox +=  "<option value ="+elem+" selected>"+salaryCheckdata[elem]+"</option>";
              }else{
                selectbox +=  "<option value ="+elem+">"+salaryCheckdata[elem]+"</option>";
              }

            };
            selectbox += "</select>";

        var $td = $(td);
        var $text = $(selectbox);
        $text.on('mousedown', function (event) {
                event.stopPropagation(); //prevent selection quirk
              });

        $td.empty().append($text);
        $('#salaryCheck' + col).change(function () {
            var value = this[this.selectedIndex].value;
            instance.setDataAtCell(row, prop, value);
        });

  }

}

var payrolldata ={acitve: '<?php echo _l('acitve') ; ?>', unactive: '<?php echo _l('unactive') ; ?>'}

function salaryCheck(instance, td, row, col, prop, value, cellProperties) {

  //  conditions for now           
    if (td.innerHTML === undefined || td.innerHTML === null || td.innerHTML === "") {
    // test selectbox
     var selectbox = " <select id=" + 'totalrowvalue' + col + "   style=" + 'width:100%;' +'height:100%;'+ 'border:none;'+ " ><option value =''></option>";

        for (let elem in payrolldata) {  
              selectbox +=  "<option value ="+elem+">"+payrolldata[elem]+"</option>";
            }
        var $td = $(td);
        var $text = $(selectbox);
        $text.on('mousedown', function (event) {
              event.stopPropagation(); //prevent selection quirk
            });

        $td.empty().append($text);
        $('#totalrowvalue' + col).change(function () {
            var value = this[this.selectedIndex].value;
            instance.setDataAtCell(row, prop, value);
        });

  }

}

function salaryCheck_update(instance, td, row, col, prop, value, cellProperties) {

    if (td.innerHTML === undefined || td.innerHTML === null || td.innerHTML === "") {
    // test selectbox

    var selectbox = " <select id=" + 'totalrowvalue' + col + "   style=" + 'width:100%;' +'height:100%;'+ 'border:none;'+ " ><option value =''></option>";

      <?php if(isset($payroll)) { ?>
        var payroll_array = <?php echo json_encode($payroll) ; ?>;
      <?php } ?>
        for (let elem in payrolldata) {  
          if(elem == payroll_array[col]){
              selectbox +=  "<option value ="+elem+" selected>"+payrolldata[elem]+"</option>";
            }else{
              selectbox +=  "<option value ="+elem+">"+payrolldata[elem]+"</option>";
            }
        };

        var $td = $(td);
        var $text = $(selectbox);
        $text.on('mousedown', function (event) {
            event.stopPropagation(); //prevent selection quirk
          });

        $td.empty().append($text);
        $('#totalrowvalue' + col).change(function () {
            var value = this[this.selectedIndex].value;
            instance.setDataAtCell(row, prop, value);
        });

  }

}

function inputValueColumn(instance, td, row, col, prop, value, cellProperties) {
    var selectbox = '<label class="control-label" disabled><?php echo _l('column_value'); ?></label>';
      td.className = 'htMiddle';
      var $td = $(td);
      var $text = $(selectbox);
        $td.empty().append($text);
}

function inputTitleColumn(instance, td, row, col, prop, value, cellProperties) {
    var selectbox = '<label class="control-label" disabled><?php echo _l('column_title'); ?></label>';
        td.className = 'htMiddle';
        var $td = $(td);
        var $text = $(selectbox);
        $td.empty().append($text);
}
function inputKeyColumn(instance, td, row, col, prop, value, cellProperties) {
    var selectbox = '<label class="control-label" disabled><?php echo _l('column_key'); ?></label>';
        td.className = 'htMiddle';
        var $td = $(td);
        var $text = $(selectbox);
        $td.empty().append($text);
}
function inputTypeColumn(instance, td, row, col, prop, value, cellProperties) {
    var selectbox = '<label class="control-label" disabled><?php echo _l('type'); ?></label>';
        td.className = 'htMiddle';
        var $td = $(td);
        var $text = $(selectbox);
        $td.empty().append($text);
}

function inputCaColumn(instance, td, row, col, prop, value, cellProperties) {
    var selectbox = '<label class="control-label" disabled><?php echo _l('calculation'); ?></label>';
        td.className = 'htMiddle';
        var $td = $(td);
        var $text = $(selectbox);
        $td.empty().append($text);
}
function inputTotalColumn(instance, td, row, col, prop, value, cellProperties) {
    var selectbox = '<label class="control-label" disabled><?php echo _l('value'); ?></label>';
        td.className = 'htMiddle';
        var $td = $(td);
        var $text = $(selectbox);
        $td.empty().append($text);
}
function inputTicketColumn(instance, td, row, col, prop, value, cellProperties) {
    var selectbox = '<label class="control-label" disabled><?php echo _l('payslip'); ?></label>';
        td.className = 'htMiddle';
        var $td = $(td);
        var $text = $(selectbox);
        $td.empty().append($text);
}
function inputDeColumn(instance, td, row, col, prop, value, cellProperties) {
    var selectbox = '<label class="control-label" disabled><?php echo _l('description'); ?></label>';
        td.className = 'htMiddle';
        var $td = $(td);
        var $text = $(selectbox);
        $td.empty().append($text);
}

  var column_value_CT = ['salary_insurance_',
                    'salary_allowance_tax',
                    'salary_allowance_no_taxable',
                    'individual_deduction_level',
                    'tax_exemption_level',
                    'hours_date',
                    'hours_week',
                    'work_time_by_round',
                    'total_work_time_by_round',
                    'effort_by_round',
                    'penalty_timekeeping',
                    'effort_work_late',
                    'effort_work_early',
                    'effort_leave_without_reason',
                    'business_commission',
                    'salary_day',
                    'hours_salary',
                    'total_money',
                    'salary_transferred_company_account',
                    'salary_transferred_personal_account',
                    'salary_paid',
                    'unpaid_wages',
                    'number_of_dependents',
                    'total_income',
                    'income_taxes',
                    'personal_income_tax',
                    'formula'
                    ];

  var column_HS =['individual_deduction_level',
                  'tax_exemption_level',
                  'date_entered_company',
                  'hours_date',
                  'hours_week',
                  'work_time_by_round',
                  'total_work_time_by_round',
                  'effort_by_round',
                  'business_commission',
                  'hours_salary',
                  'number_of_dependents',
                  'constant'

                  ];







//get value table
$('.payroll-submit').on('click', function() {
   $('input[name="financial"]').val(hot.getData());
   });
$('#hot-display-license-info').empty();
  
</script>
</body>
</html>
