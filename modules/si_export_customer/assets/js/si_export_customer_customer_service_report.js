(function($) {
"use strict";
$('input[name="filter_by"]').on('change', function() {
	var filter_by = $(this).val();
	$.ajax({
		url: "client_services_report",
		data: {'filter_by':filter_by},
		method:'POST'
	}).done(function(data) {
		data = JSON.parse(data);
		var toAppend = '';
		$.each(data.search_list, function(i, item) {
			toAppend += '<option value="'+item.id+'">'+item.name+'</option>';
		});
		$('#search_list').find('option').not(':first').remove();
		$('#search_list').append(toAppend);
		$('#search_list').selectpicker('refresh');
		toAppend = '';
		$.each(data.group_list, function(i, item) {
			toAppend += '<option value="'+item.id+'">'+item.name+'</option>';
		});
		$('#group_list').find('option').not(':first').remove();
		$('#group_list').append(toAppend);
		$('#group_list').selectpicker('refresh');
		$('#label_filter_by').text(filter_by=='customer'?txt_customer:txt_service);
	});
});
$(document).ready(function() {
	var table = $('.dt-table').DataTable();
});
})(jQuery);	