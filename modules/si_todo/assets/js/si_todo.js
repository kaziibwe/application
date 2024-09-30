$(function(){
"use strict";
	/*validate form*/
	appValidateForm($("#si_add_new_todo_item"), {
		description: {'required':true,'maxlength':3000},
		category:{'required':true},
	});
	/* Todo status change checkbox click*/
	$("body").on('change', '.todo input[type="checkbox"]', function() {
		var finished = $(this).prop('checked') === true ? 1 : 0;
		var id = $(this).val();
		window.location.href = admin_url + 'si_todo/change_todo_status/' + id + '/' + finished;
	});
	var todos_sortable = $(".si-todos-sortable");
	if (todos_sortable.length > 0) {
		/* Makes todos sortable*/
		todos_sortable = todos_sortable.sortable({
			connectWith: ".todo",
			items: "li",
			handle: '.dragger',
			appendTo: "body",
			update: function(event, ui) {
				if (this === ui.item.parent()[0]) {
					si_update_todo_items();
				}
			}
		});
	}
	/* Clear todo modal values when modal is hidden*/
	$("body").on("hidden.bs.modal", '#si_todo_modal', function() {
		var $toDo = $('#si_todo_modal');
		$toDo.find('input[name="todoid"]').val('');
		$toDo.find('textarea[name="description"]').val('');
		$toDo.find('select[name="category"]').selectpicker('val', '');
		$toDo.find('input[name="priority"][value=1]').click();
		$toDo.find('.add-title').addClass('hide');
		$toDo.find('.edit-title').addClass('hide');
	});
	/* Focus staff todo description*/
	$("body").on("shown.bs.modal", '#si_todo_modal', function() {
		var $toDo = $('#si_todo_modal');
		$toDo.find('textarea[name="description"]').focus();
		if ($toDo.find('input[name="todoid"]').val() !== '') {
			$toDo.find('.add-title').addClass('hide');
			$toDo.find('.edit-title').removeClass('hide');
		} else {
			$toDo.find('.add-title').removeClass('hide');
			$toDo.find('.edit-title').addClass('hide');
		}
	});	
});
	
function si_render_li_items(finished, obj) {
	var todo_finished_class = '';
	var checked = '';
	if (finished == 1) {
		todo_finished_class = ' line-throught';
		checked = 'checked';
	}
	return '<li class="si_todos_priorities_border_'+obj.priority+'"><div class="media"><div class="media-left no-padding-right"><div class="dragger todo-dragger"></div> <input type="hidden" value="' + finished + '" name="finished"><input type="hidden" value="' + obj.item_order + '" name="todo_order"><div class="checkbox checkbox-default todo-checkbox"><input type="checkbox" name="todo_id" value="' + obj.todoid + '" '+checked+'><label></label></div></div> <div class="media-body"><p class="todo-description' + todo_finished_class + ' no-padding-left"><i class="fa fa-flag mright5 si_todos_priorities_'+obj.priority+'"></i> ' + obj.description + '<a href="#" onclick="si_delete_todo_item(this,' + obj.todoid + '); return false;" class="pull-right text-danger"><i class="fa fa-remove"></i></a><a href="#" onclick="si_edit_todo_item('+obj.todoid+'); return false;" class="pull-right text-info mright5"><i class="fa fa-pencil-square-o"></i></a></p><small class="todo-date">' + obj.dateadded + '</small></div></div></li>';
}
/* Update todo items when drop happen*/
function si_update_todo_items() {
	var unfinished_items = $('.unfinished-todos li:not(.no-todos)');
	var finished = $('.finished-todos li:not(.no-todos)');
	var i = 1;
	/* Refresh orders*/
	$.each(unfinished_items, function() {
		$(this).find('input[name="todo_order"]').val(i);
		$(this).find('input[name="finished"]').val(0);
		i++;
	});
	if (unfinished_items.length === 0) {
		$('.nav-total-todos').addClass('hide');
		$('.unfinished-todos li.no-todos').removeClass('hide');
	} else if (unfinished_items.length > 0) {
		if (!$('.unfinished-todos li.no-todos').hasClass('hide')) {
			$('.unfinished-todos li.no-todos').addClass('hide');
		}
		$('.nav-total-todos').removeClass('hide').html(unfinished_items.length);
	}
	x = 1;
	$.each(finished, function() {
		$(this).find('input[name="todo_order"]').val(x);
		$(this).find('input[name="finished"]').val(1);
		$(this).find('input[type="checkbox"]').prop('checked', true);
		i++;
		x++;
	});
	if (finished.length === 0) {
		$('.finished-todos li.no-todos').removeClass('hide');
	} else if (finished.length > 0) {
		if (!$('.finished-todos li.no-todos').hasClass('hide')) {
			$('.finished-todos li.no-todos').addClass('hide');
		}
	}
	var update = [];
	$.each(unfinished_items, function() {
		var description = $(this).find('.todo-description');
		if (description.hasClass('line-throught')) {
			description.removeClass('line-throught');
		}
		$(this).find('input[type="checkbox"]').prop('checked', false);
		update.push([
			$(this).find('input[name="todo_id"]').val(),
			$(this).find('input[name="todo_order"]').val(),
			$(this).find('input[name="finished"]').val(),
		]);
	});
	$.each(finished, function() {
		var description = $(this).find('.todo-description');
		if (!description.hasClass('line-throught')) {
			description.addClass('line-throught');
		}
		update.push([
			$(this).find('input[name="todo_id"]').val(),
			$(this).find('input[name="todo_order"]').val(),
			$(this).find('input[name="finished"]').val(),
		]);
	});
	data = {};
	data.data = update;
	$.post(admin_url + 'si_todo/update_todo_items_order', data);
}
/* Delete single todo item*/
function si_delete_todo_item(list, id) {
	requestGetJSON('si_todo/delete_todo_item/' + id).done(function(response) {
		if (response.success === true || response.success == 'true') {
			$(list).parents('li').remove();
			si_update_todo_items();
		}
	});
}
/* Edit todo item*/
function si_edit_todo_item(id) {
	requestGetJSON('si_todo/get_by_id/' + id).done(function(response) {
		var todo_modal = $('#si_todo_modal');
		todo_modal.find('input[name="todoid"]').val(response.todoid);
		todo_modal.find('textarea[name="description"]').val(response.description);
		todo_modal.find('select[name="category"]').selectpicker('val', response.category);
		todo_modal.find('input[name="priority"][value='+response.priority+']').click();
		todo_modal.modal('show');
	});
}