"use strict";

   _validate_form($('#resource_group-form'),{group_name:'required', icon:'required'});
  initDataTable('.table-table_resource_group', admin_url+'resourcebooking/resource_group_table');
  function new_resource_group(){
    $('#resource_group').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#additional').html('');
  }
  function edit_resource_group(invoker,id){
    $('#additional').append(hidden_input('id',id));
    $('#resource_group input[name="group_name"]').val($(invoker).data('group_name'));
    $('#resource_group input[name="icon"]').val($(invoker).data('icon'));
    $('#resource_group i[id="icon"]').attr("class",'fa '+$(invoker).data('icon'));
    $('#resource_group textarea[name="description"]').val($(invoker).data('description'));
    $('#resource_group').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
  }
  $('.icon-picker').iconpicker();