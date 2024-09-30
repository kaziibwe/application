"use strict";

$(function(){ 
	if (get_url_param('eventid')) {
		view_event(get_url_param('eventid'));
	}
});

_validate_form($('#add_edit_booking-form'), {purpose:'required', resource_group:'required', resource:'required', start_time:'required', end_time:'required'});

$('#resource_group').on('change', function() {
  $.post(admin_url+'resourcebooking/get_resource_by_group/'+this.value).done(function(response) {
     response = JSON.parse(response);

     $("#resource").html('');

     $html = '<option value=""></option>';

     $.each(response.cont,function() {
        $html += '<option value="'+ this.id +'">'+ this.resource_name +'</option>';
     });

     $("#resource").html($html);

     $("#resource").selectpicker('refresh');
  });
});