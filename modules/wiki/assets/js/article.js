$(function(){
	"use strict";

    // init editor
    init_editor('.tinymce-content',{
      toolbar1: 'visualblocks fullscreen styleselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | image link codesample | bullist numlist | restoredraft',
      visualblocks_default_state: true,
      content_style: "body {  line-height: 1.4; margin: 2rem auto; max-width: 800px; } table { border-collapse: collapse; } table th, table td { border: 1px solid #ccc; padding: 0.4rem; } figure { display: table; margin: 1rem auto; } figure figcaption { color: #999; display: block; margin-top: 0.25rem; text-align: center; } hr { border-color: #ccc; border-style: solid; border-width: 1px 0 0 0; } code { background-color: #e8e8e8; border-radius: 3px; padding: 0.1rem 0.2rem; } img { max-width: 100%; } div.callout { border-radius: 4px; background-color: #f7f6f3; padding: 1rem 1rem 1rem 3rem; position: relative; } div.callout:before { content: '📣'; display: block; position: absolute; top: 1rem; left: 1rem; font-size: 20px; } .mce-content-body:not([dir=rtl]) blockquote { border-left: 2px solid #ccc; margin-left: 1.5rem; padding-left: 1rem; } .mce-content-body[dir=rtl] blockquote { border-right: 2px solid #ccc; margin-right: 1.5rem; padding-right: 1rem; }",
        
     }
    );

   

    // bind slug
    var fnGenerateSlug = function(sTitle){
        return sTitle.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
    }
    $('#title').on('change', function(){
        if($('#is_publish').is(':checked') && $('#slug').val() == ''){
            $('#slug').val(fnGenerateSlug($(this).val())).trigger('keyup');
        }
    });
    var fnCheckPublish = function (){
        if($('#is_publish').is(':checked')){
            $('.wiki-input-slug-wrap').removeClass('hide');
         }else{
             $('.wiki-input-slug-wrap').addClass('hide');
         }
         var title = $('#title').val();
         var slug = $('#slug').val();
         if (!slug) {
            $('#slug').val(fnGenerateSlug(title));
         }

    }
    $('#is_publish').on('change', function(){
        fnCheckPublish();
    });
    fnCheckPublish();

    appValidateForm($('#form_main'), {
        title: 'required',
        description: 'required',
        book_id: 'required',
        type: 'required',
    });

    // remove article
    $(".btn-remove").on('click',function(){
        var lang = $(this).data('lang');
        return confirm(lang);
    });

    // action copy
    $(document).ready(function(){
        var actionShow = null;
        var spanAlert = null;
        $('body').on('click', '.wiki-btn-copy', function(){
            if(actionShow != null){
                window.clearTimeout(actionShow);
                actionShow = null;
                spanAlert.remove();
            }
            var lang = $(this).data('lang');
            var content = $(this).data('copy');
            var dumpInput = document.createElement('input');
            dumpInput.value = content;
            document.getElementsByTagName('body')[0].appendChild(dumpInput);
            dumpInput.select();
            dumpInput.setSelectionRange(0, 99999);
            document.execCommand("copy");
            dumpInput.remove();
            spanAlert = $(` <span class="d-inline-block text-denter text-success">${lang}<span>`);
            $(this).append(spanAlert);
            actionShow = setTimeout(function(){
                spanAlert.remove();
            }, 3000);
        });
    });

});

(function(){
    // control switch type field
    var fnCheckAndSwitch = function(){
        var value = $('[name="type"]').val();
        $('.wiki-article-type-wrap').addClass('hide');
        if(value != undefined && value != null && value != ''){
            $('.wiki-article-type-wrap[data-type="' + value + '"]').removeClass('hide');
        }
    }
    $('[name="type"]').on('change', function(){
        fnCheckAndSwitch();
    });

})();