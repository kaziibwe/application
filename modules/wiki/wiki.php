<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: PerfexWiki
Description: An internal wikipedia solution to help organize and manage your knowledge resources. Intuitive, efficient, easy to use.
Version: 1.0.3
Requires at least: 2.3.*
*/

define('WIKI_MODULE_NAME', 'wiki');
define('WIKI_ASSETS_PATH', 'modules/wiki/assets');

$CI = &get_instance();

hooks()->add_action('admin_init', 'wiki_module_menu_admin_items');
hooks()->add_action('admin_init', 'wiki_permissions');

/**
* Load the module helper
*/
$CI->load->helper(WIKI_MODULE_NAME . '/wiki');

function wiki_module_menu_admin_items()
{
  $CI = &get_instance();

  $has_permission_books = true;
  $has_permission_articles = true;
  
  if($has_permission_books || $has_permission_articles){

    $CI->app_menu->add_sidebar_menu_item('wiki-module-menu-wiki-master', [
        'name'     => _l('wiki_book'),
        'href'     => 'javascript:void(0);',
        'position' => 2,
        'icon'     => 'fa fa-question-circle',
    ]);

  }
  
  if($has_permission_books){
    $CI->app_menu->add_sidebar_children_item('wiki-module-menu-wiki-master', [
      'name'     => _l('wiki_book'),
      'href'     => admin_url('wiki/books'),
      'position' => 1,
      'slug'     => 'wiki-books',
    ]);
  }

  if($has_permission_articles){

    $CI->app_menu->add_sidebar_children_item('wiki-module-menu-wiki-master', [
      'name'     => _l('wiki_articles'),
      'href'     => admin_url('wiki/articles'),
      'position' => 2,
      'slug'     => 'wiki-articles',
    ]);

    $CI->app_menu->add_sidebar_children_item('wiki-module-menu-wiki-master', [
      'name'     => _l('posted_by_me'),
      'href'     => admin_url('wiki/articles?filter_is_owner=1'),
      'position' => 3,
      'slug'     => 'wiki-articles-posted-by-me',
    ]);

    $CI->app_menu->add_sidebar_children_item('wiki-module-menu-wiki-master', [
      'name'     => _l('wiki_bookmark'),
      'href'     => admin_url('wiki/articles?filter_is_bookmark=1'),
      'position' => 4,
      'slug'     => 'wiki-articles-bookmark',
    ]);
  }
  
}

function wiki_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view_own' => ['not_applicable' => true, 'name' => _l('permission_view_own')],
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    $capabilities['help'] = [
      'view'     => _l('help_wiki_book_permissions'),
      'view_own' => _l('permission_wiki_book_based_on_assignee'),
    ];

    register_staff_capabilities('wiki_books', $capabilities, _l('wiki_book'));

    $capabilities['capabilities'] = [
      'view_own' => ['not_applicable' => true, 'name' => _l('permission_view_own')],
      'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
      'create' => _l('permission_create'),
      'edit'   => _l('permission_edit'),
      'delete' => _l('permission_delete'),
    ];

    $capabilities['help'] = [
      'view'     => _l('help_wiki_articles_permissions'),
      'view_own' => _l('permission_wiki_articles_based_on_assignee'),
    ];

    register_staff_capabilities('wiki_articles', $capabilities, _l('wiki_articles'));
}

/**
* Register activation module hook
*/
register_activation_hook(WIKI_MODULE_NAME, 'wiki_module_activation_hook');

function wiki_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(WIKI_MODULE_NAME, [WIKI_MODULE_NAME]);


module_libs_path(WIKI_MODULE_NAME, 'wiki_serialize');
function wiki_serialize($arr, $prefix){
  $s = '';
  foreach ($arr as $value) {
    if(isset($value) && $value != ''){
      $s .= ',' . $prefix . $value;
    }
  }
  return trim($s, " ,");
}

module_libs_path(WIKI_MODULE_NAME, 'wiki_unserialize');
function wiki_unserialize($s, $prefix){
  $arr = [];
  $s = str_replace($prefix, '', $s);
  $splitArr = explode(',', $s);
  return $splitArr;
}

if (!function_exists('wiki_handle_thumb_mindmap_upload')) {
  function wiki_handle_thumb_mindmap_upload($content, $prefix_name, $file_old = '')
  {
      if (isset($content) && $content != '') {
          $path  = FCPATH.WIKI_ASSETS_PATH . "/storage/mindmap/";

          $filename    = $prefix_name . time(). '.png';
          $new_file_path = $path . $filename;

          _maybe_create_upload_path($path);

          $decoded = base64_decode($content);
          file_put_contents($new_file_path, $decoded);

          if ($file_old) {
              $path_old = $path . $file_old;
              if (file_exists($path_old)) {
                  unlink($path_old);
              }
          }

          return $filename;
      }

      return false;
  }
}

if (!function_exists('wiki_copy_thumb_mindmap')) {
  function wiki_copy_thumb_mindmap($old_image, $prefix_name)
  {
      if (isset($old_image) && $old_image != '') {
          $path  = FCPATH . WIKI_ASSETS_PATH . "/storage/mindmap/";

          $filename    = $prefix_name . time(). '.png';
          $new_file_path = $path . $filename;

          $old_file_path = $path . $old_image;

          _maybe_create_upload_path($path);

          if(file_exists($old_file_path)){
            copy($old_file_path, $new_file_path);

            return $filename;
          }
      }

      return false;
  }
}

if (!function_exists('wiki_remove_thumb_mindmap')) {
  function wiki_remove_thumb_mindmap($old_image)
  {
      if (isset($old_image) && $old_image != '') {
          $path  = FCPATH . WIKI_ASSETS_PATH . "/storage/mindmap/";

          $old_file_path = $path . $old_image;

          unlink($old_file_path);
      }

      return false;
  }
}

if (!function_exists('wiki_copy_default_mindmap_thumb')) {
  function wiki_copy_default_mindmap_thumb($prefix_name)
  {
    $path  = FCPATH . WIKI_ASSETS_PATH . "/storage/mindmap/";

    $new_file_name    = $prefix_name . time(). '.png';
    $new_file_path = $path . $new_file_name;

    $default_file_path = FCPATH . WIKI_ASSETS_PATH . "/builder/ui/default_thumb.png";

    _maybe_create_upload_path($path);

    if(file_exists($default_file_path)){
      copy($default_file_path, $new_file_path);

      return $new_file_name;
    }

    return false;
  }
}