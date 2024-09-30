//staff role end  
    function delete_staff_member(id){
	"use strict";
        $('#delete_staff').modal('show');
        $('#transfer_data_to').find('option').prop('disabled',false);
        $('#transfer_data_to').find('option[value="'+id+'"]').prop('disabled',true);
        $('#delete_staff .delete_id input').val(id);
        $('#transfer_data_to').selectpicker('refresh');
    }