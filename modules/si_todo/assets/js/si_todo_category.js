$(function(){
"use strict";
	$(document).ready(function(){
		fetch_categories();
	});
	/*validate form*/
	/*jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || /^[\w. ]+$/i.test(value);
	}, si_todo_alphanumeric_validation);*/
	appValidateForm($("#si_add_new_todo_category"), {
		category_name: {'required':true,/*'alphanumeric':true,*/'maxlength':50},
		color:{'maxlength':7},
	},manage_categories);
	var todos_sortable = $(".si-todos-category-sortable");
	if (todos_sortable.length > 0) {
		/* Makes todos sortable*/
		todos_sortable = todos_sortable.sortable({
			connectWith: ".todo",
			items: "li",
			handle: '.dragger',
			appendTo: "body",
			update: function(event, ui) {
				if (this === ui.item.parent()[0]) {
					si_update_todo_categories();
				}
			}
		});
	}
	/* Clear todo modal values when modal is hidden*/
	$("body").on("hidden.bs.modal", '#si_todo_category_modal', function() {
		var $toDo = $('#si_todo_category_modal');
		$toDo.find('input[name="id"]').val('');
		$toDo.find('input[name="category_name"]').val('');
		$toDo.find('.add-title').addClass('hide');
		$toDo.find('.edit-title').addClass('hide');
	});
	/* Focus staff todo description*/
	$("body").on("shown.bs.modal", '#si_todo_category_modal', function() {
		var $toDo = $('#si_todo_category_modal');
		$toDo.find('input[name="category_name"]').focus();
		if ($toDo.find('input[name="id"]').val() !== '') {
			$toDo.find('.add-title').addClass('hide');
			$toDo.find('.edit-title').removeClass('hide');
		} else {
			$toDo.find('.add-title').removeClass('hide');
			$toDo.find('.edit-title').addClass('hide');
		}
	});	
});
function fetch_categories()
{
	if($('body').hasClass('si-todo-category-page')){
		$('.todos-category li:not(.no-todos)').remove();
		$.post(window.location.href).done(function(response) {
			response = JSON.parse(response);
			if (response.length == 0) {
				$('.todos-category .no-todos').removeClass('hide');
			}
			else
				$('.todos-category .no-todos').addClass('hide');
			$.each(response, function(i, obj) {
				$('.todos-category').append(si_render_li_categories(obj));
			});
		});
	}
}
	
function si_render_li_categories(obj) {
	var percent = (obj.total>0 ? (obj.finished/obj.total*100) : 0).toFixed(2);
	return '<li><div class="media"><div class="media-left no-padding-right"><div class="dragger todo-dragger"></div> <input type="hidden" value="' + obj.id + '" name="id"><input type="hidden" value="' + obj.cat_order + '" name="cat_order"></div> <div class="media-body"><div class="col-md-7"><p class="si-todo-category-name mleft30 mtop5"><span style="color:'+obj.color+'">' + obj.category_name + '</span><a href="#" onclick="si_delete_todo_category(this,' + obj.id + '); return false;" class="pull-right text-danger"><i class="fa fa-remove"></i></a><a href="#" onclick="si_edit_todo_category('+obj.id+'); return false;" class="pull-right text-info mright5"><i class="fa fa-pencil-square-o"></i></a></p></div><div class="col-md-5"><p class="text-uppercase mtop5"><i class="hidden-sm fa fa-check-double"></i>  ' + obj.finished + ' / ' + obj.total + '<span class="pull-right"> '+percent+'%</span></p><div class="progress no-margin progress-bar-mini"><div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="'+percent+'" aria-valuemin="0" aria-valuemax="100" style="width: '+percent+'%;background-color:'+obj.color+'" data-percent="'+percent+'"></div></div></div></div></div></li>';
}
/* Update todo items when drop happen*/
function si_update_todo_categories() {
	var categories = $('.todos-category li:not(.no-todos)');
	var i = 1;
	/* Refresh orders*/
	$.each(categories, function() {
		$(this).find('input[name="cat_order"]').val(i);
		i++;
	});
	if (categories.length === 0) {
		$('.nav-total-todos').addClass('hide');
		$('.todos-category li.no-todos').removeClass('hide');
	} else if (categories.length > 0) {
		if (!$('.todos-category li.no-todos').hasClass('hide')) {
			$('.todos-category li.no-todos').addClass('hide');
		}
		$('.nav-total-todos').removeClass('hide').html(categories.length);
	}
	
	var update = [];
	$.each(categories, function() {
		update.push([
			$(this).find('input[name="id"]').val(),
			$(this).find('input[name="cat_order"]').val(),
		]);
	});
	data = {};
	data.data = update;
	$.post(admin_url + 'si_todo/update_todo_categories_order', data);
}
/* Delete single todo item*/
function si_delete_todo_category(list, id) {
	if(confirm(si_todo_delete_validation)){
		requestGetJSON('si_todo/delete_todo_category/' + id).done(function(response) {
			if (response.success === true || response.success == 'true') {
				$(list).parents('li').remove();
				si_update_todo_categories();
			}
		});
	}
}
/* Edit todo item*/
function si_edit_todo_category(id) {
	requestGetJSON('si_todo/get_category_by_id/' + id).done(function(response) {
		var todo_modal = $('#si_todo_category_modal');
		todo_modal.find('input[name="id"]').val(response.id);
		todo_modal.find('input[name="category_name"]').val(response.category_name);
		todo_modal.find('.colorpicker-input').colorpicker('setValue', response.color);
		todo_modal.modal('show');
	});
}
function manage_categories(form) {
	var data = $(form).serialize();
	var url = form.action;
	$.post(url, data).done(function(response) {
		response = JSON.parse(response);
		if(response.success == true){
			alert_float('success',response.message);
			if($('body').hasClass('si-todo-page') && typeof(response.id) != 'undefined') {
				var category = $('#category');
				category.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
				category.selectpicker('val',response.id);
				category.selectpicker('refresh');
			}
		}
		$('#si_todo_category_modal').modal('hide');
		fetch_categories();
	});
	return false;
}