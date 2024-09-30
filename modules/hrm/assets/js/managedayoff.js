  function new_leave(){
  "use strict";
    $('#leave_modal').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    appValidateForm($('#leave_modal-form'),{leave_reason:'required',leave_type:'required'});
  }
  function edit_day_off(invoker,id){
  "use strict";
    $('#leave_modal_update').modal('show');
    $('#additional_leave_update').html('');
    $('#additional_leave_update').append(hidden_input('id',id));
    $('#leave_modal_update input[name="leave_reason"]').val($(invoker).data('off_reason'));
    $('#leave_modal_update select[name="leave_type"]').val($(invoker).data('off_type'));
    $('#leave_modal_update select[name="leave_type"]').change();
    $('#leave_modal_update select[name="timekeeping"]').val($(invoker).data('timekeeping'));
    $('#leave_modal_update select[name="timekeeping"]').change();
    $('#leave_modal_update select[name="department"]').val($(invoker).data('department'));
    $('#leave_modal_update select[name="department"]').change();													
    $('#leave_modal_update select[name="position"]').val($(invoker).data('position'));
    $('#leave_modal_update select[name="position"]').change();
    $('#leave_modal_update input[name="break_date"]').val($(invoker).data('break_date'));
    appValidateForm($('#leave_modal_update-form'),{leave_reason:'required',leave_type:'required'});
    
  }
  window.addEventListener('load',function(){
	"use strict";
  var addMoreBreakdateInputKey = $('.list_break_date input[name*="break_date"]').length;
  $("body").on('click', '.new_break_date', function() {
       if ($(this).hasClass('disabled')) { return false; }

      
      var newattachment = $('.list_break_date').find('#break_date-item').eq(0).clone().appendTo('.list_break_date');
      newattachment.find('button[type="button"]').remove();
      newattachment.find('select').selectpicker('refresh');
      
      newattachment.find('input[id="break_date[0]"]').attr('name', 'break_date[' + addMoreBreakdateInputKey + ']').val('');
      newattachment.find('input[id="break_date[0]"]').attr('id', 'break_date[' + addMoreBreakdateInputKey + ']').val('');

      newattachment.find('label[for="timekeeping[0]"]').attr('for', 'timekeeping[' + addMoreBreakdateInputKey + ']');
      newattachment.find('select[name="timekeeping[0]"]').attr('name', 'timekeeping[' + addMoreBreakdateInputKey + ']');
      newattachment.find('select[id="timekeeping[0]"]').attr('id', 'timekeeping[' + addMoreBreakdateInputKey + ']').selectpicker('refresh');

      newattachment.find('label[for="department[0]"]').attr('for', 'department[' + addMoreBreakdateInputKey + ']');
      newattachment.find('select[name="department[0]"]').attr('name', 'department[' + addMoreBreakdateInputKey + ']');
      newattachment.find('select[id="department[0]"]').attr('id', 'department[' + addMoreBreakdateInputKey + ']').selectpicker('refresh');

      newattachment.find('label[for="position[0]"]').attr('for', 'position[' + addMoreBreakdateInputKey + ']');
      newattachment.find('select[name="position[0]"]').attr('name', 'position[' + addMoreBreakdateInputKey + ']');
      newattachment.find('select[id="position[0]"]').attr('id', 'position[' + addMoreBreakdateInputKey + ']').selectpicker('refresh');
       
      newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
      newattachment.find('button[name="add"]').removeClass('new_break_date').addClass('remove_break_date').removeClass('btn-success').addClass('btn-danger');
      init_datepicker();
      addMoreBreakdateInputKey++;

  });

  $("body").on('click', '.remove_break_date', function() {
      $(this).parents('#break_date-item').remove();
  });
});