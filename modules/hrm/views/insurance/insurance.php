<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'insurance-form','autocomplete'=>'off')); ?>
            <?php if(isset($insurances)){ ?>
                <input type="hidden" name="insurance_id" value="<?php echo htmlspecialchars($insurances[0]['insurance_id']); ?>">
           <?php } else { ?>

                <input type="hidden" name="insurance_id" value="">
           <?php } ?>

        <div class="col-md-12">
            <div class="panel_s" >
                <div class="panel-body">
                                <h4 class="publib-infor-title">
									<span class="publib-infor"><?php echo _l('public_information_insurance'); ?></span>
                                </h4>
                    <div class="row">
                        <div class="col-md-12">
						
                         <div class="row">
                             <div class="col-md-8">
								<br><br>
                             </div>
                         </div>   
                         <div class="form" id="new_insurance">
                            <div class="row">
                                <div class="col-md-12">
                                <label for="staff_id" class="control-label"><?php echo _l('insurance_staff'); ?></label>
                                <select onchange="OnSelectStaff(this)" name="staff_id" class="selectpicker" id="staff_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                   <option value=""></option>                  
                                  <?php foreach($staff as $s){ ?>
                                  <option value="<?php echo htmlspecialchars($s['staffid']); ?>"  <?php if(isset($insurances) && $insurances[0]['staff_id'] == $s['staffid'] ){echo 'selected';} ?>> <?php echo htmlspecialchars($s['firstname']).''.htmlspecialchars($s['lastname']); ?></option>                  
                                  <?php }?>
                                </select>
                                </div>
                            </div>
                            <div class="row">
                                <br>
                                <div class="col-md-6">
                             <?php $insurance_book_num = isset($insurances) ? $insurances[0]['insurance_book_num'] : '' ?>     
                                <?php 
                            echo render_input('insurance_book_num','insurance_book_number', $insurance_book_num); ?>
                                </div>
                                <div class="col-md-6">
                                <?php $health_insurance_num = isset($insurances) ? $insurances[0]['health_insurance_num'] : '' ?>
                                <?php 
                                echo render_input('health_insurance_num','health_insurance_number', $health_insurance_num); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                <?php $city_code = isset($insurances) ? $insurances[0]['city_code'] : '' ?>
                                <?php 
                                echo render_input('city_code','province_city_id', $city_code); ?>

                                </div>
                                <div class="col-md-6">
                                <?php $registration_medical = isset($insurances) ? $insurances[0]['registration_medical'] : '' ?>
                                <?php 
                                echo render_input('registration_medical','registration_medical_care', $registration_medical); ?>
                                    
                                </div>
                            </div>



                        </div>
                        </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                    </div>
                    </div>
                </div><!-- .modal-content -->
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

//check validate form
var addMoreVendorsInputKey = $('#total_insurance_histtory').children().length;
      addMoreVendorsInputKey = addMoreVendorsInputKey + 1;
$(function(){

    appValidateForm($('.insurance-form'),{

        staff_id: {
            required: true
        },
        insurance_book_num: {
            required: true,
            remote: {
                url: site_url + "admin/hrm/insurance_book_exists",
                type: 'post',
                data: {
                    insurance_id:function(){
                        return $('input[name = "insurance_id"]').val();
                    },
                    insurance_book_num:function(){
                        return $('input[name = "insurance_book_num"]').val();
                    }

                }
            }
        },
        health_insurance_num: {
            required: true,
            remote: {
                url: site_url + "admin/hrm/health_insurance_exists",
                type: 'post',
                data: {
                    insurance_id:function(){
                        return $('input[name = "insurance_id"]').val();
                    },
                    health_insurance_num:function(){
                        return $('input[name = "health_insurance_num"]').val();
                    }
                }
            }
        }
    });


});



   $("body").on('click', '.new_insurance_history', function() {
      //get position row
         if ($(this).hasClass('disabled')) { return false; }

        var newinsurrance = $(this).parents('#new_insurance').find('.insurance-history').eq(0).clone().appendTo($(this).parents('#total_insurance_histtory'));

        newinsurrance.find('button[type="button"]').remove();
        newinsurrance.find('button[name="add_new_insurance_history"]').attr('id', '');
        newinsurrance.find('select[id="reason[1]"]').children().remove();
        
        newinsurrance.find('select[name="from_month[1]"]').attr('name', 'from_month[' + addMoreVendorsInputKey + ']').val('');
        newinsurrance.find('select[id="from_month[1]"]').attr('id', 'from_month[' + addMoreVendorsInputKey + ']').val(''); 
        newinsurrance.find('button[data-id="from_month[1]"]').attr('data-id', 'from_month[' + addMoreVendorsInputKey + ']').val('');

        newinsurrance.find('select[name="formality[1]"]').attr('name', 'formality[' + addMoreVendorsInputKey + ']').val('');
        newinsurrance.find('select[id="formality[1]"]').attr('id', 'formality[' + addMoreVendorsInputKey + ']').val('');

        newinsurrance.find('select[name="reason[1]"]').attr('name', 'reason[' + addMoreVendorsInputKey + ']').val('');
        newinsurrance.find('select[id="reason[1]"]').attr('id', 'reason[' + addMoreVendorsInputKey + ']').val('');
        newinsurrance.find('select').selectpicker('val', '');

        newinsurrance.find('input[name="premium_rates[1]"]').attr('name', 'premium_rates[' + addMoreVendorsInputKey + ']').val('');
        newinsurrance.find('input[id="premium_rates[1]"]').attr('id', 'premium_rates[' + addMoreVendorsInputKey + ']').val('');
        newinsurrance.find('input[name="id_history[1]"]').attr('name', 'id_history[' + addMoreVendorsInputKey + ']').val('');
        newinsurrance.find('input[name="id_history[1]"]').attr('value', '');

        newinsurrance.find('label[for="from_month[1]"]').remove();
        newinsurrance.find('label[for="formality[1]"]').remove();
        newinsurrance.find('label[for="reason[1]"]').remove();
        newinsurrance.find('label[for="premium_rates[1]"]').remove();
        newinsurrance.find('select').selectpicker('refresh');

        newinsurrance.find('div[name="add_insurance_history"]').removeAttr("style");
        newinsurrance.find('div[name="add_new_insurance_history"]').removeAttr("style");

        newinsurrance.find('button[name="add_new_insurance_history"] i').removeClass('fa-plus').addClass('fa-minus');
        newinsurrance.find('button[name="add_new_insurance_history"]').removeClass('new_insurance_history').addClass('remove_insurance_history').removeClass('btn-success').addClass('btn-danger');
        newinsurrance.find('button[title="<?php echo _l('add') ?>"]').attr('title', '<?php echo _l('delete') ?>');

        $("input[data-type='currency']").on({
            keyup: function() {        
              formatCurrency($(this));
            },
            blur: function() { 
              formatCurrency($(this), "blur");
            }
        });

        addMoreVendorsInputKey++;
        

    });

    $("body").on('click', '.remove_insurance_history', function() {
        if (confirm_delete()) {
       var insurance_history_id =  $(this).parents('.insurance-history').find('input[type="hidden"]').attr('value');
       if(insurance_history_id != undefined && insurance_history_id != ''){

        $(this).parents('.insurance-history').remove();
        var formData = new FormData();
      formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
      formData.append("insurance_history_id", insurance_history_id);
        $.ajax({ 
            url: admin_url + 'hrm/delete_insurance_history', 
            method: 'post', 
            data: formData, 
            contentType: false, 
            processData: false
        }).done(function(response) {
          response = JSON.parse(response);
          if(response.data == true){
            alert_float('success', response.message);

          }else{
            alert_float('warning', response.message);

          }
      });
       }else{
        $(this).parents('.insurance-history').remove();

       }
    }
    });

    function removeOptions(selectbox)
    {
        var i;
        for(i = selectbox.options.length - 1 ; i >= 0 ; i--)
        {
            selectbox.remove(i);
        }
    }

    function OnSelectReason (value_input) {
        var selectedOption = value_input.options[value_input.selectedIndex].value;
        var index_value = value_input.name.substring(9);
        document.getElementById("reason"+index_value).innerHTML = "";
        $('.selectpicker').selectpicker('refresh');
        
    if(selectedOption != ''){
        var flag = 0;
        if(selectedOption == 'increase'){
            flag = 1;
        }
      var formData = new FormData();
      formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
      formData.append("formality", selectedOption);
        $.ajax({ 
            url: admin_url + 'hrm/get_hrm_formality', 
            method: 'post', 
            data: formData, 
            contentType: false, 
            processData: false
        }).done(function(response) {
          response = JSON.parse(response);
            var select = document.getElementById("reason"+index_value);
          if(flag == 1){
            if(response.sign_a_labor_contract == 1)
            {
             select.options[select.options.length] = new Option('<?php echo _l('sign_a_labor_contract'); ?>','sign_a_labor_contract' );   
            }
            if(response.maternity_leave_to_return_to_work == 1)
            {
             select.options[select.options.length] = new Option( '<?php echo _l('maternity_leave_to_return_to_work'); ?>','maternity_leave_to_return_to_work');   
            }
            if(response.unpaid_leave_to_return_to_work == 1)
            {
             select.options[select.options.length] = new Option('<?php echo _l('unpaid_leave_to_return_to_work'); ?>','unpaid_leave_to_return_to_work' );   
            }
            if(response.increase_the_premium == 1)
            {
             select.options[select.options.length] = new Option('<?php echo _l('increase_the_premium'); ?>', 'increase_the_premium' );   
            }
            $('.selectpicker').selectpicker('refresh');

          }
          if(flag == 0){
            if(response.contract_paid_for_unemployment == 1)
            {
             select.options[select.options.length] = new Option('<?php echo _l('contract_paid_for_unemployment'); ?>','contract_paid_for_unemployment' );   
            }
            if(response.maternity_leave_regime == 1)
            {
             select.options[select.options.length] = new Option( '<?php echo _l('maternity_leave_regime'); ?>','maternity_leave_regime');   
            }
            if(response.unpaid_leave_to_return_to_work == 1)
            {
             select.options[select.options.length] = new Option('<?php echo _l('reduced_premiums'); ?>','reduced_premiums' );   
            }
            
            $('.selectpicker').selectpicker('refresh');

          }
      });
    }

      return false;
    }

    function OnSelectStaff (value_input) {
        var selectedOption = value_input.options[value_input.selectedIndex].value;
        console.log('sss', selectedOption);

    if(selectedOption != ''){

      var formData = new FormData();

        $.ajax({ 
            url: admin_url + 'hrm/get_hrm_staff?staffid=' + selectedOption, 
            method: 'get', 

            contentType: false, 
            processData: false
        }).done(function(response) {
            response = JSON.parse(response);
        if(response.id != ''){
            $('#total_insurance_histtory').children().remove();
            $('#total_insurance_histtory').append(response.data);


            $('#insurance_book_num').val(response.insurance_book_num);
            $('#health_insurance_num').val(response.health_insurance_num);
            $('#city_code').val(response.city_code);
            $('#registration_medical').val(response.registration_medical);
           

            $('body').find('input[name="insurance_id"]').attr('value', response.id);
            $('#total_insurance_histtory').find('select').selectpicker('refresh');
            $("input[data-type='currency']").on({
            keyup: function() {        
              formatCurrency($(this));
            },
            blur: function() { 
              formatCurrency($(this), "blur");
            }
            });

        }else{
            $('#total_insurance_histtory').children().remove();
            $('#total_insurance_histtory').append(response.data_null);


            $('#insurance_book_num').val('');
            $('#health_insurance_num').val('');
            $('#city_code').val('');
            $('#registration_medical').val('');

            $('body').find('input[name="insurance_id"]').attr('value', '');
            $('#total_insurance_histtory').find('select').selectpicker('refresh');
            $("input[data-type='currency']").on({
            keyup: function() {        
              formatCurrency($(this));
            },
            blur: function() { 
              formatCurrency($(this), "blur");
            }
            });

        }

        });
    }

      return false;
    }
    
</script>
</body>
</html>
