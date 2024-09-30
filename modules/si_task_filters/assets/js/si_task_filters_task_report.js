(function($) {
"use strict";
var _rel_id = $('#si_tf_rel_id'),_rel_type = $('#si_tf_rel_type'),_rel_id_wrapper = $('#si_tf_rel_id_wrapper'),data = {};
$('.si_tf_rel_id_label').html(_rel_type.find('option:selected').text());
$("body").on('changed.bs.select', '#si_tf_rel_id', function(e) {
    //disable groups if client is selected
	if(_rel_type.val()=='customer')
	{
		if($(this).val()!=''){
			$('#group_id').selectpicker('val','');
			$('#group_id').selectpicker('refresh');
			$('#group_id').prop('disabled',true);
		}else
			$('#group_id').prop('disabled',false);
	}
});							
_rel_type.on('change', function() {
	 var clonedSelect = _rel_id.html('').clone();
	 _rel_id.selectpicker('destroy').remove();
	 _rel_id = clonedSelect;
	 $('#si_tf_rel_id_select').append(clonedSelect);
	 $('.si_tf_rel_id_label').html(_rel_type.find('option:selected').text());
	 si_tf_task_rel_select();
	 if($(this).val() != ''){
	  _rel_id_wrapper.removeClass('hide');
	} else {
	  _rel_id_wrapper.addClass('hide');
	}
});
si_tf_task_rel_select();
function si_tf_task_rel_select(){
	var serverData = {};
	serverData.rel_id = _rel_id.val();
	data.type = _rel_type.val();
	if(_rel_type.val()=='customer')
	{
		$('#group_id_wrapper').removeClass('hide');
		$('#include_rel_type_wrapper').removeClass('hide');
	} else {
		$('#group_id_wrapper').addClass('hide');
		$('#group_id').selectpicker('val','');
		$('#group_id').selectpicker('refresh');
		$('#include_rel_type_wrapper').addClass('hide');
	}
	init_ajax_search(_rel_type.val(),_rel_id,serverData);
}
$('#report_months').on('change', function() {
     var val = $(this).val();
	 var report_from = $('#report_from');
	 var report_to = $('#report_to');
	 var date_range = $('#date-range');
	 
	 if(val!='')
	 	$("#date_by_wrapper").removeClass('hide');
	 else
	 	$("#date_by_wrapper").addClass('hide');
	 
     report_to.val('');
     report_from.val('');
     if (val == 'custom') {
       date_range.addClass('fadeIn').removeClass('hide');
       return;
     } else {
       if (!date_range.hasClass('hide')) {
         date_range.removeClass('fadeIn').addClass('hide');
       }
     }
	 	
});
$('#si_save_filter').on('click',function(){
	var checked = this.checked;
	$('#si_filter_name').attr('disabled',!checked);
});
$('#si_form_task_filter').on('submit',function(){
	if($('#si_save_filter').is(":checked") && $('#si_filter_name').val()=='')
	{
		$('#si_filter_name').focus();
		return false;
	}
});
$(document).ready(function() {
	$('.dt-table').each(function(i,a) {					   	
		var table = $(a).DataTable();
		var hide_view = [];
		$('.dt-table thead tr th').each(function(i,a) { 
			if( $(this).hasClass('not-export'))
				hide_view.push($(this).index());	
		});
		table.button().add( 0, 'colvis' );
		table.columns( hide_view ).visible( false );
		$('.buttons-colvis').addClass('btn-sm');//for Perfex version 3.0
	});
});
$(".buttons-colvis").text("Columns");
//added for kanban view
$('#switch_kanban').on('click',function(){
	var val = $(this).attr('value');
	$('input[name=kanban]').val((val==1?0:1));
	//remove kanban if its there in url
	var url = $('#si_form_task_filter').attr('action').replace("/kanban", "");
	$('#si_form_task_filter').attr('action',url);
});
init_kanban_si_tasks('', si_tasks_kanban_update, '.tasks-status', 265, 360);
// Updates task when action performed form kan ban area eq status changed.
function si_tasks_kanban_update(ui, object) {
    if (object === ui.item.parent()[0]) {
        var status = $(ui.item.parent()[0]).data('task-status-id');
        var tasks = $(ui.item.parent()[0]).find('[data-task-id]');

        var data = {};
        data.order = [];
        var i = 0;
        $.each(tasks, function () {
            data.order.push([$(this).data('task-id'), i]);
            i++;
        });

        task_mark_as(status, $(ui.item).data('task-id'));
        check_kanban_empty_col('[data-task-id]');
        setTimeout(function () {
			init_kanban_si_tasks('', si_tasks_kanban_update, '.tasks-status', 265, 360);
        }, 200);
    }
}
function init_kanban_si_tasks(url, callbackUpdate, connect_with, column_px, container_px, callback_after_load) 
{
    if ($('#kan-ban').length === 0) {
        return;
    }
    delay(function () {
       $("body").append('<div class="dt-loader"></div>');
	   $.post($('#si_form_task_filter').attr('action'), $('#si_form_task_filter').serialize()).done(function (response) {
			$('#kan-ban').html(response);
            fix_kanban_height(column_px, container_px);
            var scrollingSensitivity = 20,
                scrollingSpeed = 60;

            if (typeof (callback_after_load) != 'undefined') {
                callback_after_load();
            }

            $(".status").sortable({
                connectWith: connect_with,
                helper: 'clone',
                appendTo: '#kan-ban',
                placeholder: "ui-state-highlight-card",
                revert: 'invalid',
                scrollingSensitivity: 50,
                scrollingSpeed: 70,
                sort: function (event, uiHash) {
                    var scrollContainer = uiHash.placeholder[0].parentNode;
                    // Get the scrolling parent container
                    scrollContainer = $(scrollContainer).parents('.kan-ban-content-wrapper')[0];
                    var overflowOffset = $(scrollContainer).offset();
                    if ((overflowOffset.top + scrollContainer.offsetHeight) - event.pageY < scrollingSensitivity) {
                        scrollContainer.scrollTop = scrollContainer.scrollTop + scrollingSpeed;
                    } else if (event.pageY - overflowOffset.top < scrollingSensitivity) {
                        scrollContainer.scrollTop = scrollContainer.scrollTop - scrollingSpeed;
                    }
                    if ((overflowOffset.left + scrollContainer.offsetWidth) - event.pageX < scrollingSensitivity) {
                        scrollContainer.scrollLeft = scrollContainer.scrollLeft + scrollingSpeed;
                    } else if (event.pageX - overflowOffset.left < scrollingSensitivity) {
                        scrollContainer.scrollLeft = scrollContainer.scrollLeft - scrollingSpeed;

                    }
                },
                change: function () {
                    var list = $(this).closest('ul');
                    var KanbanLoadMore = $(list).find('.kanban-load-more');
                    $(list).append($(KanbanLoadMore).detach());
                },
                start: function (event, ui) {
                    $('body').css('overflow', 'hidden');

                    $(ui.helper).addClass('tilt');
                    $(ui.helper).find('.panel-body').css('background', '#fbfbfb');
                    // Start monitoring tilt direction
                    tilt_direction($(ui.helper));
                },
                stop: function (event, ui) {
                    $('body').removeAttr('style');
                    $(ui.helper).removeClass("tilt");
                    // Unbind temporary handlers and excess data
                    $("html").off('mousemove', $(ui.helper).data("move_handler"));
                    $(ui.helper).removeData("move_handler");
                },
                update: function (event, ui) {
                    callbackUpdate(ui, this);
                }
            });

            $('.status').sortable({
                cancel: '.not-sortable'
            });

        });

    }, 200);
}
//end kanban view
})(jQuery);	