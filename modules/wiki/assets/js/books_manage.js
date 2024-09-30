$(function(){
	"use strict";

    $(document).ready(function(){
			var fnSearch = function(){
				var path = window.location.pathname;
				var value = $('.input-search-list').val();
				var form = $('#form_search');
				var filterQuery = $('#form_search [name="filter_query"]');
				filterQuery.val(value);
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
		});

});
