<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Banner_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add or update banner image details in the database.
     *
     * @param array $attachment an array containing banner image details
     * @param int   $id         the ID of the banner (optional)
     *
     * @return bool true on success, false on failure
     */
    public function addBannerImageToDB($attachment, $id)
    {
        // If no ID is provided, insert new banner image details
        if (empty($id)) {
            return $this->db->insert(db_prefix().'banner', $attachment);
        }

        // Update existing banner image details based on the provided ID
        return $this->db->update(db_prefix().'banner', $attachment, ['id' => $id]);
    }

    /**
     * Change the status of a banner in the database.
     *
     * @param int $id     the ID of the banner to change the status for
     * @param int $status the new status for the banner
     *
     * @return bool true if the status is changed, false otherwise
     */
    public function changeBannerStatus($id, $status)
    {
        // Update the status of the specified banner
        return $this->db->update('banner', ['status' => $status], ['id' => $id]);
    }

    /**
     * Delete a banner from the database and unlink associated image file if exists.
     *
     * @param int $id the ID of the banner to delete
     *
     * @return bool true if the banner is deleted, false otherwise
     */
    public function deleteBanner($id)
    {
        // Retrieve banner image details based on the provided ID and type
        $bannerImage = $this->db->get_where(db_prefix().'banner', ['id' => $id])->row();

        // Delete the banner from the database
        $this->db->delete('banner', ['id' => $id]);

        // If a banner image is found and deleted, unlink the associated file
        if (!empty($bannerImage) && $this->db->affected_rows() > 0) {
            unlink(get_upload_path_by_type('banner').'/'.$bannerImage->detail);
        }

        // Return true if the banner is deleted, false otherwise
        if ($this->db->affected_rows() > 0) {
            log_activity('Banner Deleted.');

            return true;
        }

        return false;
    }

    /**
     * Retrieve details of a banner (both image and content) based on the provided ID.
     *
     * @param int $id the ID of the banner
     *
     * @return object an object containing details of the banner
     */
    public function get($id)
    {
        // Retrieve banner image details based on the provided ID
        $banner['image'] = $this->db->get_where(db_prefix().'banner', ['id' => $id])->row();

        return $banner;
    }

    /**
     * Update banner image data in the database.
     *
     * @param array $postData an array containing banner image data to be updated
     *
     * @return bool true on success, false on failure
     */
    public function updateBannerImageData($postData)
    {
        $adminIds  = $postData['staff_ids'] ?? '';
        $clientIds = $postData['client_ids'] ?? '';

        $updateData = [
            'title'         => $postData['title'],
            'start_date'    => to_sql_date($postData['start_date']),
            'end_date'      => to_sql_date($postData['end_date']),
            'admin_area'    => !empty($adminIds) ? 1 : 0,
            'clients_area'  => !empty($clientIds) ? 1 : 0,
            'staff_ids'     => !empty($adminIds) ? serialize($adminIds) : '',
            'client_ids'    => !empty($clientIds) ? serialize($clientIds) : '',
            'has_action'    => isset($postData['has_action']) ? 1 : 0,
            'action_target' => isset($postData['action_target']) ? 1 : 0,
            'action_label'  => isset($postData['has_action']) ? $postData['action_label'] : '',
            'action_url'    => isset($postData['has_action']) ? $postData['action_url'] : '',
            'label_color'   => $postData['label_color'],
        ];

        log_activity('Banner Updated Successfully');

        // Update banner image data in the database based on the provided ID
        return $this->db->update(db_prefix().'banner', $updateData, ['id' => $postData['id']]);
    }
}
