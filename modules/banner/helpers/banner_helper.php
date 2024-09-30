<?php

defined('BASEPATH') || exit('No direct script access allowed');

if (!function_exists('handleBannerImageUpload')) {
    function handleBannerImageUpload($id = '')
    {
        $path          = get_upload_path_by_type('banner').'/';
        $CI            = &get_instance();
        $totalUploaded = 0;

        if (
            isset($_FILES['file']['name'])
            && ('' != $_FILES['file']['name'] || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
        ) {
            _file_attachments_index_fix('file');
            // Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && '' != $tmpFilePath) {
                $extension          = strtolower(pathinfo($_FILES['file']['name'], \PATHINFO_EXTENSION));
                $allowed_extensions = [
                    'jpg',
                    'jpeg',
                    'png',
                    'bmp',
                    'webp',
                ];

                if (
                    _perfex_upload_error($_FILES['file']['error'])
                    || !in_array($extension, $allowed_extensions)
                ) {
                    set_alert('danger', _l('image_extenstion_not_allowed'));

                    return false;
                }

                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES['file']['name']);
                $newFilePath = $path.$filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $adminIds  = $CI->input->post('staff_ids');
                    $clientIds = $CI->input->post('client_ids');

                    $attachment = [
                        'detail'       => $filename,
                        'title'        => $CI->input->post('title'),
                        'start_date'   => to_sql_date($CI->input->post('start_date')),
                        'end_date'     => to_sql_date($CI->input->post('end_date')),
                        'admin_area'   => !empty($adminIds) ? 1 : 0,
                        'clients_area' => !empty($clientIds) ? 1 : 0,
                        'staff_ids'    => !empty($adminIds) ? serialize($adminIds) : '',
                        'client_ids'   => !empty($clientIds) ? serialize($clientIds) : '',
                    ];

                    $CI->banner_model->addBannerImageToDB($attachment, $id);

                    log_activity('Banner Added Successfully');

                    ++$totalUploaded;
                }
            }
        }

        return (bool) $totalUploaded;
    }
}

function is_serialized($data)
{
    // If it's not a string, it can't be serialized
    if (!is_string($data)) {
        return false;
    }

    // Trim any whitespace
    $data = trim($data);

    // Serialized data starts with 'a:', 's:', 'i:', etc.
    return 'N;' === $data || preg_match('/^([sia]:|O:|a:|b:|d:|i:|s:)/', $data);
}

/*
 * Get details of banners with status set to 1 from the database.
 *
 * @return array An array containing details of banners with status set to 1.
 */
if (!function_exists('getBannerDetails')) {
    function getBannerDetails($allowArea)
    {
        $res = [];

        $details = get_instance()->db->get_where(db_prefix().'banner', ['status' => 1])->result_array();

        // Filter out banners whose time duration is finished or not available for currently logged-in staff member
        $filteredData = array_filter($details, function ($value, $key) use ($allowArea) {
            $today     = date('Y-m-d');
            $isInRange = $today >= $value['start_date'] && $today <= $value['end_date'];

            $ids = ('admin_area' == $allowArea) ? $value['staff_ids'] : $value['client_ids'];
            if ($isInRange) {
                if (is_serialized($ids)) {
                    return in_array(('admin_area' == $allowArea) ? get_staff_user_id() : get_client_user_id(), unserialize($ids));
                }
            }

            return false;
        }, \ARRAY_FILTER_USE_BOTH);

        return $filteredData;
    }
}

if (!function_exists('renderBanner')) {
    function renderBanner($details)
    {
        $preparList    = '<ol class="carousel-indicators mtop20">';
        $preparContent = '<div class="carousel-inner">';
        $i             = 0;

        foreach ($details as $detail) {
            $active = (0 == $i) ? 'active' : '';
            $target = ($detail['action_target'] == 1) ? 'blank' : '';
            $action_url = !empty($detail['action_url']) ? $detail['action_url'] : 'javascript:void(0)';
            $preparList .= '<li data-target="#myCarousel" data-slide-to="' . $i . '" class="' . $active . '"></li>';
            $preparContent .= '<div class="item '. $active .'">
                                    <div class="panel">';

            $circle = (!is_mobile()) ? 'circle' : '';
            $preparContent .= '<span class="' . $circle . ' numbertext">'. $i + 1 .' / ' . count($details) . '</span>';
            if ($detail['has_action'] == 1) {
                $preparContent .= '<a href="' . $action_url . '" target="' . $target . '">';
            }
            $preparContent .= '<img src="'. site_url().'uploads/banner/' . $detail['detail'] . '" alt="' . $detail['detail'] . '" class="tw-w-full image-slideshow">';

            if ($detail['has_action'] == 1) {
                $preparContent .= '</a>';
                $caption = '<a href="' . $action_url . '" target="' . $target . '" style="color:' . $detail['label_color'] . '">' . $detail['action_label'] . '</a>';
                $preparContent .= '<div class="caption_text">' . $caption . '</div>';
            }
            $preparContent .= '</div>
                            </div>';
            $i++;
        }
        $preparList .= '</ol>';
        $preparContent .= '</div>';
        $content = '<div class="col-md-12 mbot15">';
        $content .= '<div id="myCarousel" class="carousel slide" data-ride="carousel">';
        $content .= $preparList;
        $content .= $preparContent;
        if (count($details) > 1) {
            $content .= '<a class="carousel-control" href="#myCarousel" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left text-dark"></span>
                        </a>';
            $content .= '<a class="carousel-control" href="#myCarousel" data-slide="next" style="right:0; left:auto">
                            <span class="glyphicon glyphicon-chevron-right text-dark"></span>
                        </a>';
        }
        $content .= '</div></div>';

        return $content;
    }
}