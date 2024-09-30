$(function() {
  // init map
  window.KITYMINDER_CONFIG = {
    readOnly: false,
    maxUndoCount: 20,
    lang: 'en-us',
    maxImageWidth: 200,
    maxImageHeight: 200,
    autoSave: 2
  };
  
  var langs = location.href.match(/lang=([a-z]+)/);
  if(langs) {
    var lang = langs[1];
  }
  
  km = KM.getMinder('kityminder', window.KITYMINDER_CONFIG);
  if (MINDMAP_CONTENT) {
    km.importJson(JSON.parse(MINDMAP_CONTENT));
  }
  km.initUI();

  //save
  var prevent_save = false;
  $("#update_button").on("click", function(){
    if(prevent_save){
      return;
    }
    prevent_save = true;

    km.exportData("png").then(function(thumb) {

      var reqData = {
        csrf_token_name: APP_CSRF_TOKEN,
        csrf_token: APP_CSRF_TOKEN,
        article_id: ARTICLE_ID,
        mindmap_content: JSON.stringify(km.exportJson()),
        mindmap_thumb: thumb.replace('data:image/png;base64,', ''),
      };

      
      $.ajax({
        url: MINDMAP_SAVE_URL,
        type : 'POST',
        data: reqData,
        dataType: 'json',
        success: function(data){
          Swal.fire({
            position: 'top-end',
            timer: 1200,
            customClass: {
              popup: 'alert-popup',
            },
            width:50,
            html: '<i class="fa fa-check-circle text-success"></i> <small>'+'</smal>',
            showConfirmButton: false,
          });
          prevent_save = false;
        },
        error: function(){
          prevent_save = false;
        },
      });
    });
    
    
  });

});