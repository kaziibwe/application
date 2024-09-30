"use strict";

  _validate_form($('#resource-form'),{resource_name:'required',resource_group:'required'});
  
  function new_resource(){
    $('#resource').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#additional').html('');
  }
  function edit_resource(invoker,id){
    $('#additional').append(hidden_input('id',id));
    $('#resource input[name="resource_name"]').val($(invoker).data('resource_name'));
    $('#resource select[name="manager"]').val($(invoker).data('manager'));
    $('#resource select[name="manager"]').change();
    $('#resource select[name="status"]').val($(invoker).data('status'));
    $('#resource select[name="status"]').change();
    $('#resource select[name="resource_group"]').val($(invoker).data('resource_group'));
    $('#resource select[name="resource_group"]').change();
   
    $('#resource textarea[name="description"]').val($(invoker).data('description'));
    var enc = $(invoker).data('approved');
    if(enc == 1){
      $('#y_opt_1_').prop('checked',true);
    }else{
      $('#y_opt_2_').prop('checked',true);
    } 
    $('#resource').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
    var color_picked = $(invoker).data('color');
    if($('div[data-color='+color_picked+']')){
      $('.cpicker-big').addClass('cpicker-small');
      $('.cpicker-big').removeClass('cpicker-big');
      $('div[data-color='+color_picked+']').removeClass('cpicker-small');
      $('div[data-color='+color_picked+']').addClass('cpicker-big');
      $('input[name="color"]').val(color_picked);
    }
  }
