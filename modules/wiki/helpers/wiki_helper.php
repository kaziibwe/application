<?php

defined('BASEPATH') or exit('No direct script access allowed');

function wiki_generate_code($s){
  return  md5(uniqid($s, true));
}

function wiki_get_mindmap_thumb($filename = ''){
  if($filename != ''){
    return base_url(WIKI_UPLOAD_PATH.'/storage/mindmap') . '/' . $filename;
  }else{
    return base_url(WIKI_ASSETS_PATH.'/builder/ui/default_thumb.png');
  }
}

function wiki_get_mindmap_content(){
  return '{"data":{"text":"My New Mind Map"},"template":"default","theme":"fresh-blue","version":"1.3.5"}';
}