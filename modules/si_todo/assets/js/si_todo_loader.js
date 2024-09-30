$(function(){
"use strict";	
	var page_unfinished = 0;
	var page_finished = 0;
	$('.unfinished-loader').on('click', function(e) {
		e.preventDefault();
		if (page_unfinished <= total_pages_unfinished) {
			$.post(window.location.href, {
				finished: 0,
				todo_page: page_unfinished
			}).done(function(response) {
				response = JSON.parse(response);
				if (response.length == 0) {
					$('.unfinished-todos .no-todos').removeClass('hide');
				}
				$.each(response, function(i, obj) {
					$('.unfinished-todos').append(si_render_li_items(0, obj));
				});
				page_unfinished++;
			});
			if (page_unfinished >= total_pages_unfinished - 1) {
				$(".unfinished-loader").addClass("disabled");
			}
		}
	});

	$('.finished-loader').on('click', function(e) {
		e.preventDefault();
		if (page_finished <= total_pages_finished) {
			$.post(window.location.href, {
				finished: 1,
				todo_page: page_finished
			}).done(function(response) {
				response = JSON.parse(response);
				if (response.length == 0) {
					$('.finished-todos .no-todos').removeClass('hide');
				}
				$.each(response, function(i, obj) {
					$('.finished-todos').append(si_render_li_items(1, obj));
				});
				page_finished++;
			});
			if (page_finished >= total_pages_finished - 1) {
				$(".finished-loader").addClass("disabled");
			}
		}
	});
	$('.unfinished-loader').click();
	$('.finished-loader').click();
});	