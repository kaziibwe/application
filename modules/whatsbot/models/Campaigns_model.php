<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Campaigns_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['leads_model', 'clients_model']);
    }

    public function save($post_data)
    {
        unset($post_data['image']);
        $post_data['scheduled_send_time']   = isset($post_data['scheduled_send_time']) ? to_sql_date($post_data['scheduled_send_time'], true) : null;
        $post_data['send_now']              = (isset($post_data['send_now']) ? 1 : 0);
        $post_data['select_all']            = (isset($post_data['select_all']) ? 1 : 0);
        $post_data['header_params']         = json_encode($post_data['header_params'] ?? []);
        $post_data['body_params']           = json_encode($post_data['body_params'] ?? []);
        $post_data['footer_params']         = json_encode($post_data['footer_params'] ?? []);

        $rel_ids  = (isset($post_data['lead_ids']) && !empty($post_data['lead_ids'])) ? $post_data['lead_ids'] : ((isset($post_data['contact_ids']) && !empty($post_data['contact_ids'])) ? $post_data['contact_ids'] : '');
        $rel_type = (isset($post_data['lead_ids']) && !empty($post_data['lead_ids'])) ? 'leads' : ((isset($post_data['contact_ids']) && !empty($post_data['contact_ids'])) ? 'contacts' : '');

        unset($post_data['lead_ids'], $post_data['contact_ids']);

        if (1 == $post_data['select_all']) {
            if ('leads' == $post_data['rel_type']) {
                $leads    = $this->leads_model->get();
                $rel_ids  = array_column($leads, 'id');
                $rel_type = 'leads';
            } elseif ('contacts' == $post_data['rel_type']) {
                $contacts = $this->clients_model->get_contacts();
                $rel_ids  = array_column($contacts, 'id');
                $rel_type = 'contacts';
            }
        }

        $insert   = $update   = false;
        $template = wb_get_whatsapp_template($post_data['template_id']);
        if (!empty($post_data['id'])) {
            $update = $this->db->update(db_prefix().'wtc_campaigns', $post_data, ['id' => $post_data['id']]);
            if ($update) {
                $this->db->delete(db_prefix().'wtc_campaign_data', ['campaign_id' => $post_data['id']]);
                foreach ($rel_ids as $rel_id) {
                    $this->db->insert(db_prefix().'wtc_campaign_data', [
                        'campaign_id'       => $post_data['id'],
                        'rel_id'            => $rel_id,
                        'rel_type'          => $rel_type,
                        'header_message'    => $template['header_data_text'],
                        'body_message'      => $template['body_data'],
                        'footer_message'    => $template['footer_data'],
                        'status'            => 1,
                    ]);
                }
            }
        } else {
            $insert = $this->db->insert(db_prefix().'wtc_campaigns', $post_data);
            if ($insert) {
                $insert_id = $this->db->insert_id();
                foreach ($rel_ids as $rel_id) {
                    $this->db->insert(db_prefix().'wtc_campaign_data', [
                        'campaign_id'       => $insert_id,
                        'rel_id'            => $rel_id,
                        'rel_type'          => $rel_type,
                        'header_message'    => $template['header_data_text'],
                        'body_message'      => $template['body_data'],
                        'footer_message'    => $template['footer_data'],
                        'status'            => 1,
                    ]);
                }
            }
        }

        $campaign_id   = !empty($post_data['id']) ? $post_data['id'] : $insert_id;
        wb_handle_campaign_upload($campaign_id, 'campaign');
        if ($post_data['send_now']) {
            $scheduledData = $this->db
                ->select(db_prefix().'wtc_campaigns.*, '.db_prefix().'wtc_templates.*, '.db_prefix().'wtc_campaign_data.*')
                ->join(db_prefix().'wtc_campaigns', db_prefix().'wtc_campaigns.id = '.db_prefix().'wtc_campaign_data.campaign_id', 'left')
                ->join(db_prefix().'wtc_templates', db_prefix().'wtc_campaigns.template_id = '.db_prefix().'wtc_templates.id', 'left')
                ->where(db_prefix().'wtc_campaign_data.status', 1)
                ->where(db_prefix().'wtc_campaigns.is_bot', 0)
                ->where(db_prefix().'wtc_campaign_data.campaign_id', $campaign_id)
                ->get(db_prefix().'wtc_campaign_data')->result_array();

            if (!empty($scheduledData)) {
                $this->load->model('whatsbot_model');
                $this->whatsbot_model->send_campaign($scheduledData);
            }
        }

        return [
            'type'      => $insert || $update ? 'success' : 'danger',
            'message'   => $insert ? _l('added_successfully', _l('campaign')) : ($update ? _l('updated_successfully', _l('campaign')) : _l('something_went_wrong')),
            'campaign_id'=> $campaign_id,
        ];
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            return $this->db->select(
                db_prefix().'wtc_campaigns.*,'.
                    db_prefix().'wtc_templates.template_name as template_name,'.
                    db_prefix().'wtc_templates.template_id as tmp_id,'.
                    db_prefix().'wtc_templates.header_params_count,'.
                    db_prefix().'wtc_templates.body_params_count,'.
                    db_prefix().'wtc_templates.footer_params_count,'.
                    'CONCAT("[", GROUP_CONCAT('.db_prefix().'wtc_campaign_data.rel_id SEPARATOR ","), "]") as rel_ids,'
            )
                ->join(db_prefix().'wtc_templates', db_prefix().'wtc_templates.id = '.db_prefix().'wtc_campaigns.template_id')
                ->join(db_prefix().'wtc_campaign_data', db_prefix().'wtc_campaign_data.campaign_id = '.db_prefix().'wtc_campaigns.id', 'LEFT')
                ->get_where(db_prefix().'wtc_campaigns', [db_prefix().'wtc_campaigns.id' => $id])->row_array();
        }

        return $this->db->get(db_prefix().'wtc_campaigns')->result_array();
    }

    public function delete($id)
    {
        $campaign = $this->get($id);
        $delete = $this->db->delete(db_prefix().'wtc_campaigns', ['id' => $id]);

        if ($delete) {
            $this->db->delete(db_prefix().'wtc_campaign_data', ['campaign_id' => $id]);

            $path = WHATSBOT_MODULE_UPLOAD_FOLDER . '/campaign/' . $campaign['filename'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        return [
            'message' => $delete ? _l('deleted', _l('campaign')) : _l('something_went_wrong'),
        ];
    }

    public function pause_resume_campaign($id)
    {
        $campaign = $this->get($id);
        $update   = $this->db->update(db_prefix().'wtc_campaigns', ['pause_campaign' => (1 == $campaign['pause_campaign'] ? 0 : 1)], ['id' => $id]);

        return ['message' => $update && 1 == $campaign['pause_campaign'] ? _l('campaign_resumed') : _l('campaign_paused')];
    }

    public function delete_campaign_files($id)
    {
        $campaign = $this->get($id);
        $type = ($campaign['is_bot'] == 1) ? 'template' : 'campaign';

        $update = $this->db->update(db_prefix() . 'wtc_campaigns', ['filename' => NULL], ['id' => $id]);
        $path = WHATSBOT_MODULE_UPLOAD_FOLDER . '/' . $type . '/' . $campaign['filename'];
        if ($update && file_exists($path)) {
            unlink($path);
        }

        return [
            'message' => ($update) ? _l('image_deleted_successfully') : _l('something_went_wrong'),
            'url'     => ($campaign['is_bot'] == 1) ? admin_url('whatsbot/bots/bot/template/' . $id) : admin_url('whatsbot/campaigns/campaign/' . $id),
        ];
    }
}
