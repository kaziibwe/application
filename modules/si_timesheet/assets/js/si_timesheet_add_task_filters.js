$(function(){
"use strict";
	var _serverData = {};
	var _rel_id_add = $('#si_ts_rel_id_add'),_rel_type_add = $('#si_ts_rel_type_add'),_rel_id_wrapper_add = $('#si_ts_rel_id_wrapper_add'),data = {};
	//_serverData.rel_id = _rel_id_add.val();
	//_serverData.rel_type = _rel_type_add.val();
	//task search with rel type search
	//init_ajax_search('tasks', '#si_ts_task_id', _serverData, admin_url + 'si_timesheet/ajax_search_assign_task_to_timer');
	si_ts_task_select_add();
	//$('#si_ts_rel_id_add').on('change', function() {
	$(document).on("change",'#si_ts_rel_id_add',function() {
	 si_ts_task_select_add();
	});
	function si_ts_task_select_add(){
		var _aa = $('#si_ts_task_id');
		var clonedSelect = $('#si_ts_task_id').html('').clone();
		 _aa.selectpicker('destroy').remove();
		 _aa = clonedSelect;
		 $('#si_ts_task_id_wrapper_add').append(clonedSelect);
		 //$('.si_ts_rel_id_label_add').html(_rel_type_add.find('option:selected').text());							  
		_serverData.rel_id = _rel_id_add.val();
		_serverData.rel_type = _rel_type_add.val();
		//task search with rel type search
		init_ajax_search('tasks', '#si_ts_task_id', _serverData, admin_url + 'si_timesheet/ajax_search_assign_task_to_timer');							  
	}
	//query for selected change rel type
	$('.si_ts_rel_id_label_add').html(_rel_type_add.find('option:selected').text());
	_rel_type_add.on('change', function() {
		 var clonedSelect = _rel_id_add.html('').clone();
		 _rel_id_add.selectpicker('destroy').remove();
		 _rel_id_add = clonedSelect;
		 $('#si_ts_rel_id_select_add').append(clonedSelect);
		 $('.si_ts_rel_id_label_add').html(_rel_type_add.find('option:selected').text());
		 si_ts_task_rel_select_add();
		 if($(this).val() != ''){
		  _rel_id_wrapper_add.removeClass('hide');
		} else {
		  _rel_id_wrapper_add.addClass('hide');
		}
	});
	si_ts_task_rel_select_add();
	function si_ts_task_rel_select_add(){
		var serverData = {};
		serverData.rel_id = _rel_id_add.val();
		data.type = _rel_type_add.val();
		init_ajax_search(_rel_type_add.val(),_rel_id_add,serverData);
		si_ts_task_select_add();
	}
	//end query for selected change rel type
});