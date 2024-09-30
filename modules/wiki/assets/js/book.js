$(function(){
	"use strict";

    appValidateForm($('#form_main'), {
        name: 'required',
        short_description: 'required',
        assign_type: 'required',
    });

    var $assignTypeInput = $('input[name="assign_type"]');
    $assignTypeInput.on('change',function(){
        $('.types-assign').addClass('hide');
        $("#" + $(this).val() + "_assign").removeClass('hide');
    });
    $assignTypeInput.each(function(ele){
        if($(ele).is(":checked")){
            $(ele).removeClass('hide');
        }else{
            $(ele).addClass('hide');
        }
    });
    $(".btn-remove").on('click',function(){
        var lang = $(this).data('lang');
        return confirm(lang);
    });

});
