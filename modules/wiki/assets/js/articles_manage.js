$(function(){
	"use strict";

	var viewLogs = [];

	var fnIsViewed = function(articleId){

	}

	$(document).ready(function(){
		var fnSearch = function(){
			var form = $('#form_search');

			var valueQuery = $('.input-search-list').val();
			var inputQuery = $('#form_search [name="filter_query"]');
			inputQuery.val(valueQuery);
			
			var valueBookId = $('#filter_book_id').val();
			if(valueBookId != undefined && valueBookId != null && valueBookId != ''){
				var inputBookId = $('#form_search [name="filter_book_id"]');
				if(inputBookId.length == 0){
					form.append('<input type="hidden" name="filter_book_id" value="'+valueBookId+'" />')
				}else{
					inputBookId.val(valueBookId);
				}
			}

			form.submit();
		}
		$(".btn-search-list").on('click',function(){
			fnSearch();
		});
		$('.input-search-list').keypress(function (e) {
			if (e.which == 13) {
				fnSearch();
				return false;
			}
		});
		$('#filter_book_id').on('change', function (e) {
			fnSearch();
		});
	});

	// bookmark
	$(document).ready(function(){
		var bookmarking = false;
		$('.wiki-btn-bookmark').on('click', function(){
			var _this = $(this);
			var articleId = _this.data('id');
			if(articleId == undefined || articleId == null || articleId == ''){
				return;
			}
			var isOn = _this.hasClass('wiki-bookmark-on');

			var reqData = {
				csrf_token: APP_CSRF_TOKEN,
				is_on: isOn ? 0 : 1,
				article_id: articleId,
			};

			function fnSwitch(newIsOn){
				if(newIsOn){
					_this.removeClass('wiki-bookmark-off');
					_this.addClass('wiki-bookmark-on');
				}else{
					_this.removeClass('wiki-bookmark-on');
					_this.addClass('wiki-bookmark-off');
				}
			}
			
			if(bookmarking){
				return;
			}
			bookmarking = true;
			$.ajax({
				url: bookmark_switch_url,
				type : 'POST',
				data: reqData,
				dataType: 'json',
				success: function(data){
					if(data.result != undefined && data.result == true){
						fnSwitch(!isOn);
					}
					bookmarking = false;
				},
				error: function(){
					bookmarking = false;
				},
			});

		});
	});

});
