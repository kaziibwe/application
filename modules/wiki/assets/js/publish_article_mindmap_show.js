(function(){
  // init map
  window.KITYMINDER_CONFIG = {
    readOnly: true,
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
  km.importJson(JSON.parse(MINDMAP_CONTENT));
  km.initUI();

})();