   _validate_form($('#add_edit_booking-form'),{purpose:'required',resource_group:'required',resource:'required',start_time:'required',end_time:'required'});
   $('#resource_group').on('change', function(){
	   
      $.post(admin_url+'resourcebooking/get_resource_by_group/'+this.value).done(function(response){
         response = JSON.parse(response);
         $("#resource").html('');
         $html = '<option value=""></option>';
         $.each(response.cont,function(){
			 "use strict";
            $html += '<option value="'+ this.id +'">'+ this.resource_name +'</option>';
         });
         $("#resource").html($html);
         $("#resource").selectpicker('refresh');
      });
   });
   $('#resource').on('change', function(){
	   "use strict";
      $.post(admin_url+'resourcebooking/get_resource_activity_now/'+this.value).done(function(response){
         response = JSON.parse(response);
         $('.resource-activity').html('');
         $('.resource-activity').append(response.cont);
      });
   });
   
   