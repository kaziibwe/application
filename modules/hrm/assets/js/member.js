   //preview file
   function preview_file_staff(invoker){
   "use strict";
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_hrmstaff_file(id, rel_id);
  }

   //function view hrm_file
   function view_hrmstaff_file(id, rel_id) {   
      "use strict";
      $('#contract_file_data').empty();
      $("#contract_file_data").load(admin_url + 'hrm/hrm_file/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
  }

     function delete_contract_attachment(wrapper, id) {
	 "use strict";
    if (confirm_delete()) {
       $.get(admin_url + 'hrm/delete_hrm_staff_attachment/' + id, function (response) {
          if (response.success == true) {
             $(wrapper).parents('.contract-attachment-wrapper').remove();

             var totalAttachmentsIndicator = $('.attachments-indicator');
             var totalAttachments = totalAttachmentsIndicator.text().trim();
             if(totalAttachments == 1) {
               totalAttachmentsIndicator.remove();
             } else {
               totalAttachmentsIndicator.text(totalAttachments-1);
             }
          } else {
             alert_float('danger', response.message);
          }
       }, 'json');
    }
    return false;
   }