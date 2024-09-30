<?php

defined('BASEPATH') or exit('No direct script access allowed');

function perfex_dashboard_render_dashboard_widgets($container)
{
    $widgetsHtml = [];

    static $widgets     = null;
    static $widgetsData = null;

    include_once(APPPATH . 'third_party/simple_html_dom.php');

    $CI = &get_instance();

    if (!$widgets) {
        $widgetsData       = [];
        $widgets           = get_dashboard_widgets();

        foreach ($widgets as $key => $widget) {
            $html = str_get_html($CI->load->view($widget['path'], [], true));
            if ($html) {
                $widgetContainer = $html->firstChild();
                if ($widgetContainer) {
                    $htmlID = $widgetContainer->getAttribute('id');

                    $widgetsData[$htmlID] = [
                        'widgetIndex'     => $key,
                        'widgetPath'      => $widget['path'],
                        'widgetContainer' => $widget['container'],
                        'html'            => $widgetContainer,
                    ];

                    $widget['widgetID']         = $htmlID;
                    $widget['html']             = $widgetContainer;
                    $widgets[$key]['settingID'] = strafter($htmlID, 'widget-');
                    $widgets[$key]['html']      = $widgetContainer;
                } else {
                    // Not compatible widget
                    unset($widgets[$key]);
                }
            } else {
                // Not compatible widget
                unset($widgets[$key]);
            }
        }
    }
    foreach ($widgets as $widget) {
        if ($widget['container'] == $container) {
            $widgetsHtml[$widget['settingID']] = $widget['html'];
        }
    }
    foreach ($widgetsHtml as $widgetID => $widgetHTML) {
        echo $widgetHTML;
    }
}

function perfex_dashboard_render_widgets($widgets)
{
    $CI = &get_instance();

    foreach ($widgets as $widget) {
        echo '<div class="col-md-12">';
        echo $CI->load->view('perfex_dashboard/partials/widget_info', ['widget' => $widget], true);
        echo $CI->load->view('perfex_dashboard/widgets/' . $widget['widget_name'], [], true);
        echo '</div>';
    }
}

function perfex_dashboard_render_widgets_from_dashboard($dashboard, $container)
{
    $CI = &get_instance();
    $CI->load->model('perfex_dashboard_model');

    $ids = [];
    if(isset($dashboard['dashboard_widgets'][$container])) {
        $ids = $dashboard['dashboard_widgets'][$container];
    }

    $widgets = $CI->perfex_dashboard_model->select_widgets_by_ids($ids);

    foreach ($widgets as $widget) {
        echo $CI->load->view('perfex_dashboard/widgets/' . $widget['widget_name'], [
            'widget' => $widget,
        ], true);
    }
}

function perfex_dashboard_get_available_widgets($dashboard)
{
    $CI = &get_instance();
    $CI->load->model('perfex_dashboard_model');

    $containers = ['top-12', 'top-left-first-4', 'top-left-last-4', 'top-right-first-4', 'top-right-last-4', 'middle-left-6', 'middle-right-6', 'left-8', 'right-4', 'bottom-left-4', 'bottom-middle-4', 'bottom-right-4'];
    
    $present_widgets = [];
    foreach($containers as $container) {
        if(isset($dashboard['dashboard_widgets'][$container])) {
            $present_widgets = array_merge($present_widgets, $dashboard['dashboard_widgets'][$container]);
        }
    }
    $present_widgets= array_unique($present_widgets);

    $widgets = $CI->perfex_dashboard_model->select_widgets_except_ids($present_widgets);

    return $widgets;
}

function perfex_dashboard_get_categories() 
{
    $CI = &get_instance();
    $CI->load->model('perfex_dashboard_model');

    $categories = $CI->perfex_dashboard_model->get_categories();

    return $categories;
}

function perfex_dashboard_scan_widgets_2()
{
    $widget_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../views/widgets';
    $widgets = directory_map($widget_path, 1);

    // only files that start with prefix widget-
    $widgets = array_filter($widgets, function ($v) {
      if(strlen($v) > 7) {
        return substr($v, 0, 7) === 'widget-';
      }
      return false;
    });

    // generate data
    $widgets_data = [];
    if ($widgets) {

        foreach ($widgets as $widget_name) {
            $widget_name = strtolower(trim($widget_name));
            
            foreach (['\\', '/'] as $trim) {
                $widget_name = rtrim($widget_name, $trim);
            }

            $name = substr($widget_name, 7);
            $name = substr($name, 0, stripos($name, '.') - 0);

            $path = $widget_path . DIRECTORY_SEPARATOR . $widget_name;

            $header = perfex_dashboard_get_headers_widget($path);

            array_push($widgets_data, [
                'name' =>  $name,
                'file_name' => $widget_name,
                'path' => $path,
                'header' => $header,
            ]);
        }
    }

    return $widgets_data;
}

function perfex_dashboard_scan_widgets()
{
    $CI = &get_instance();
    $CI->load->model('perfex_dashboard_model');

    $widget_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../views/widgets';
    $widgets = directory_map($widget_path, 1);

    $widgets = array_filter($widgets, function ($v) {
      if(strlen($v) > 7) {
        return substr($v, 0, 7) === 'widget-';
      }
      return false;
    });

    $widgets_data = [];

    if ($widgets) {
        
        $db_widgets = $CI->perfex_dashboard_model->select_widgets_except_names();
        $db_widgets = array_map(function($widget) {
            return substr($widget['widget_name'], 7);
        }, $db_widgets);

        foreach ($widgets as $widget_name) {
            $widget_name = strtolower(trim($widget_name));
            
            foreach (['\\', '/'] as $trim) {
                $widget_name = rtrim($widget_name, $trim);
            }

            $name = substr($widget_name, 7);
            $name = substr($name, 0, stripos($name, '.') - 0);

            $path = $widget_path . DIRECTORY_SEPARATOR . $widget_name;

            $header = perfex_dashboard_get_headers_widget($path);

            array_push($widgets_data, [
            'name' =>  $name,
            'file_name' => $widget_name,
            'path' => $path,
            'header' => $header,
            'active' => in_array($name, $db_widgets),
            ]);
        }
    }

    return $widgets_data;
}

function perfex_dashboard_scan_widgets_with_activation($active)
{
    $data = perfex_dashboard_scan_widgets();
    return array_filter($data, function ($v) use ($active) {
        return $v['active'] == $active;
    });
}

function perfex_dashboard_get_headers_widget($widget_path)
{
    $widget_data = read_file($widget_path);

    preg_match('|Widget Name:(.*)$|mi', $widget_data, $name);
    preg_match('|Description:(.*)$|mi', $widget_data, $description);
    preg_match('|Category:(.*)$|mi', $widget_data, $category);

    $arr = [];

    if (isset($name[1])) {
        $arr['name'] = trim($name[1]);
    }

    if (isset($description[1])) {
        $arr['description'] = trim($description[1]);
    }

    if (isset($category[1])) {
        $arr['category'] = trim($category[1]);
    }

    return $arr;
}

function get_widgest_folder_path(){
    
    return APP_MODULES_PATH.PERFEX_DASHBOARD_MODULE_NAME.'/views/widgets';
    
}