   $(function() {
       appValidateForm($('.staff-form'), {

           name_contract: 'required',
           staff: 'required',
           contract_code: {
               required: true,
               remote: {
                url: site_url + "admin/hrm/contract_code_exists",
                type: 'post',
                data: {
                    contract_code: function() {
                        return $('input[name="contract_code"]').val();
                    },
                    contractid: function() {
                        return $('input[name="contractid"]').val();
                    }
                }
            }
           }
       });
   });
  window.addEventListener('load',function(){
  
   appValidateForm($('#contract-form'),{
      contract_form:'required'},manage_contract_type);
    $('#form').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#form input[name="contract_form"]').val('');
        $('.add-title').removeClass('hide');
    });
 });
   function manage_contract_type(form) {
   
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response){
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('contract') && typeof(response.id) != 'undefined') {
                var ctype = $('#contract_forms');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        $('#form').modal('hide')
    });
    return false;
  }
  function new_form(){
  
    $('#form').modal('show');
  }


    var addMoreVendorsInputKey = $('#id_wages_allowances .contract-expense-al').children().length;
      addMoreVendorsInputKey = addMoreVendorsInputKey + 1;

   $("body").on('click', '.new_contract_expense', function() {
   
      //get position row
      var idrow = $(this).parents('.contract-expense-al').find('.get_id_row').attr("value");

         if ($(this).hasClass('disabled')) { return false; }

        var newattachment = $(this).parents('.contract-expense-al').find('#contract-expense').eq(0).clone().appendTo($(this).parents('.contract-expense-al'));

        newattachment.find('button[type="button"]').remove();
        newattachment.find('select').selectpicker('refresh');
        
        newattachment.find('input[id="contract_expense[' + idrow + '][1]"]').attr('name', 'contract_expense[' + idrow + '][' + addMoreVendorsInputKey + ']').val('');
        newattachment.find('input[id="contract_expense[' + idrow + '][1]"]').attr('id', 'contract_expense[' + idrow + '][' + addMoreVendorsInputKey + ']').val('');

        newattachment.find('label[for="salary_form[' + idrow + '][1]"]').remove();
        newattachment.find('label[for="contract_expense[' + idrow + '][1]"]').remove();
        newattachment.find('div[name="button_add"]').removeAttr("style");

        newattachment.find('select[name="salary_form[' + idrow + '][1]"]').attr('name', 'salary_form[' + idrow + '][' + addMoreVendorsInputKey + ']');
        newattachment.find('select[id="salary_form[' + idrow + '][1]"]').attr('id', 'salary_form[' + idrow + '][' + addMoreVendorsInputKey + ']').selectpicker('refresh');

        newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
        newattachment.find('button[name="add"]').removeClass('new_contract_expense').addClass('remove_contract_expense').removeClass('btn-success').addClass('btn-danger');

        newattachment.find('select').selectpicker('val', '');
        addMoreVendorsInputKey++;

    });

    $("body").on('click', '.remove_contract_expense', function() {
	
        $(this).parents('#contract-expense').remove();
    });
    
      var InputKeyallowance = $('#id_wages_allowances .contract-allowance-type').children().length;
      InputKeyallowance = InputKeyallowance + 1;

   $("body").on('click', '.new_contract_allowance_type', function() {
   
         if ($(this).hasClass('disabled')) { return false; }
        
      var idrow_allowance = $(this).parents('.contract-allowance-type').find('.get_id_row_allowance').attr("value");


        var newattachment = $(this).parents('.contract-allowance-type').find('#contract-allowancetype').eq(0).clone().appendTo($(this).parents('.contract-allowance-type'));
        newattachment.find('button[type="button"]').remove();
        newattachment.find('select').selectpicker('refresh');
        
        newattachment.find('input[id="allowance_expense[' + idrow_allowance + '][1]"]').attr('name', 'allowance_expense[' + idrow_allowance + '][' + InputKeyallowance + ']').val('');
        newattachment.find('input[id="allowance_expense[' + idrow_allowance + '][1]"]').attr('id', 'allowance_expense[' + idrow_allowance + '][' + InputKeyallowance + ']').val('');

        newattachment.find('label[for="allowance_type[' + idrow_allowance + '][1]"]').remove();
        newattachment.find('label[for="allowance_expense[' + idrow_allowance + '][1]"]').remove();
        newattachment.find('div[name="button_allowance_type"]').removeAttr("style");

        newattachment.find('select[name="allowance_type[' + idrow_allowance + '][1]"]').attr('name', 'allowance_type[' + idrow_allowance + '][' + InputKeyallowance + ']');
        newattachment.find('select[id="allowance_type[' + idrow_allowance + '][1]"]').attr('id', 'allowance_type[' + idrow_allowance + '][' + InputKeyallowance + ']').selectpicker('refresh');

        newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
        newattachment.find('button[name="add"]').removeClass('new_contract_allowance_type').addClass('remove_contract_allowance_type').removeClass('btn-success').addClass('btn-danger');
        
        newattachment.find('select').selectpicker('val', '');

        InputKeyallowance++;

    });

    $("body").on('click', '.remove_contract_allowance_type', function() {
	
        $(this).parents('#contract-allowancetype').remove();
    });


    //add addMore totall
    var Input_totall = $('#tab_wages_allowances .class-wages-allowances').children().length;
    Input_totall = Input_totall + 1;
    var addMore = 1;

   $("body").on('click', '.new_wages_allowances', function() {
   
         if ($(this).hasClass('disabled')) { return false; }

        
        var newattachment = $('.class-wages-allowances').find('#id_wages_allowances').eq(0).clone().appendTo('.class-wages-allowances');

        for(var i = 0; i <= newattachment.find('#contract-expense').length ; i++){
            if(i > 0){
              newattachment.find('#contract-expense').eq(i).remove();
            }
            newattachment.find('#contract-expense').eq(1).remove();
        }
        for(var i = 0; i <= newattachment.find('#contract-allowancetype').length ; i++){
            if(i > 0){
              newattachment.find('#contract-allowancetype').eq(i).remove();
            }
            newattachment.find('#contract-allowancetype').eq(1).remove();
        }


        newattachment.find('button[type="button"]').remove();
        newattachment.find('select').selectpicker('refresh');
        // start expense
         newattachment.find('input[id="contract_expense[1][1]"]').attr('name', 'contract_expense[' + Input_totall + '][' + addMore + ']').val('');
        newattachment.find('input[id="contract_expense[1][1]"]').attr('id', 'contract_expense[' + Input_totall + '][' + addMore + ']').val('');
        newattachment.find('label[for="salary_form[1][1]"]').attr('value',  Input_totall);
        newattachment.find('input[id="contract_expense[1][1]"]').attr('value', '');

        newattachment.find('label[for="salary_form[1][1]"]').attr('for', 'salary_form[' + Input_totall + '][' + addMore + ']');
        newattachment.find('label[for="contract_expense[1][1]"]').attr('for', 'contract_expense[' + Input_totall + '][' + addMore + ']');


        newattachment.find('select[name="salary_form[1][1]"]').attr('name', 'salary_form[' + Input_totall + '][' + addMore + ']');
        newattachment.find('select[id="salary_form[1][1]"]').attr('id', 'salary_form[' + Input_totall + '][' + addMore + ']').selectpicker('refresh');

        //start allowances
        newattachment.find('select').selectpicker('refresh');
        newattachment.find('input[id="allowance_expense[1][1]"]').attr('name', 'allowance_expense[' + Input_totall + '][' + addMore + ']').val('');
        newattachment.find('input[id="allowance_expense[1][1]"]').attr('id', 'allowance_expense[' + Input_totall + '][' + addMore + ']').val('');
        newattachment.find('input[id="allowance_expense[1][1]"]').attr('value', '');
        newattachment.find('label[for="allowance_type[1][1]"]').attr('value',  Input_totall);
        newattachment.find('label[for="allowance_type[1][1]"]').attr('for', 'allowance_type[' + Input_totall + '][' + addMore + ']');
        newattachment.find('label[for="allowance_expense[1][1]"]').attr('for', 'allowance_expense[' + Input_totall + '][' + addMore + ']');

      
        newattachment.find('label[for="salary_form[1]"]').attr('for', 'salary_form[' + addMore + ']');

        newattachment.find('select[name="allowance_type[1][1]"]').attr('name', 'allowance_type[' + Input_totall + '][' + addMore + ']');
        newattachment.find('select[id="allowance_type[1][1]"]').attr('id', 'allowance_type[' + Input_totall + '][' + addMore + ']').selectpicker('refresh');
        //add since date, contract note
         newattachment.find('input[id="since_date[1]"]').attr('id', 'since_date[' + Input_totall + ']').val('');
         newattachment.find('input[name="since_date[1]"]').attr('name', 'since_date[' + Input_totall + ']').val('');
         newattachment.find('input[id="contract_note[1]"]').attr('id', 'contract_note[' + Input_totall + ']').val('');
         newattachment.find('input[name="contract_note[1]"]').attr('name', 'contract_note[' + Input_totall + ']').val('');

        newattachment.find('button[name="add_wages_allowances"] i').removeClass('fa-plus').addClass('fa-minus');
        newattachment.find('button[name="add_wages_allowances"]').removeClass('new_wages_allowances').addClass('remove_wages_allowances').removeClass('btn-success').addClass('btn-danger');

        newattachment.find('select').selectpicker('val', '');

        init_datepicker();
        Input_totall++;

    });

    $("body").on('click', '.remove_wages_allowances', function() {
	
        $(this).parents('#id_wages_allowances').remove();
        
    });

    // + button for adding more attachments
    var addMoreAttachmentsInputKey = 1;
    //button for adding more attachment in project
    $("body").on('click', '.add_more_attachments_file', function() {
	
        if ($(this).hasClass('disabled')) {
            return false;
        }

        var total_attachments = $('.attachments input[name*="file"]').length;
        if ($(this).data('max') && total_attachments >= $(this).data('max')) {
            return false;
        }

        var newattachment = $('.attachments').find('.attachment').eq(0).clone().appendTo('.attachments');
        newattachment.find('input').removeAttr('aria-describedby aria-invalid');
        newattachment.find('input').attr('name', 'file[' + addMoreAttachmentsInputKey + ']').val('');
        newattachment.find($.fn.appFormValidator.internal_options.error_element + '[id*="error"]').remove();
        newattachment.find('.' + $.fn.appFormValidator.internal_options.field_wrapper_class).removeClass($.fn.appFormValidator.internal_options.field_wrapper_error_class);
        newattachment.find('i').removeClass('fa-plus').addClass('fa-minus');
        newattachment.find('button').removeClass('add_more_attachments_file').addClass('remove_attachment_file').removeClass('btn-success').addClass('btn-danger');
        addMoreAttachmentsInputKey++;
    });

    // Remove attachment
    $("body").on('click', '.remove_attachment_file', function() {
	
        $(this).parents('.attachment').remove();
    }); 

    //disabled input jobposition
    $( "#job_position" ).prop( "disabled", false );
    $("#staff_delegate").change(function(){
	
      var formData = new FormData();
      formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
      formData.append("id", $(this).children("option:selected").val());
        $.ajax({ 
            url: admin_url + 'hrm/get_staff_role', 
            method: 'post', 
            data: formData, 
            contentType: false, 
            processData: false
        }).done(function(response) {
          response = JSON.parse(response);
          if(response.name != null ){
             $('#job_position').val(response.name);
          }
      });
      return false;

    });


    function OnSelectionChange_salsaryform (select) {
	
        var selectedOption = select.options[select.selectedIndex];
        var ex = select.name.substring(11);

      
      var formData = new FormData();
      formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
      formData.append("id", selectedOption.value);
        $.ajax({ 
            url: admin_url + 'hrm/get_staff_salary_form', 
            method: 'post', 
            data: formData, 
            contentType: false, 
            processData: false
        }).done(function(response) {
          response = JSON.parse(response);
          if(response.salary_val != null){
             document.getElementById("contract_expense"+ex).value = response.salary_val;
          }
      });
      return false;

    }

    function OnSelectionChange_allowancetype (allowancetype_value) {
	
        var selectedOption = allowancetype_value.options[allowancetype_value.selectedIndex];
        var ex = allowancetype_value.name.substring(14);
      
      var formData = new FormData();
      formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
      formData.append("id", selectedOption.value);
        $.ajax({ 
            url: admin_url + 'hrm/get_staff_allowance_type', 
            method: 'post', 
            data: formData, 
            contentType: false, 
            processData: false
        }).done(function(response) {
          response = JSON.parse(response);
          if(response.allowance_val != null){
             document.getElementById("allowance_expense"+ex).value = response.allowance_val;
          }
      });
      return false;
    }