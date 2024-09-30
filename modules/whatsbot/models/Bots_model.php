<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Bots_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function saveBots($data)
    {
        unset($data['bot_file']);
        $insert = $update = false;

        if (empty($data['id'])) {
            $data['addedfrom'] = get_staff_user_id();
            $insert            = $this->db->insert(db_prefix().'wtc_bot', $data);
            $bot_id            = $this->db->insert_id();
        } else {
            $update = $this->db->update(db_prefix().'wtc_bot', $data, ['id' => $data['id']]);
            $bot_id = $data['id'];
        }

        return [
            'type'    => ($insert || $update) ? 'success' : 'danger',
            'message' => ($insert) ? _l('bot_create_successfully') : ($update ? _l('bot_update_successfully') : _l('something_went_wrong')),
            'id'      => $bot_id,
        ];
    }

    public function getMessageBot($id = '')
    {
        if (!empty($id)) {
            return $this->db->get_where(db_prefix().'wtc_bot', ['id' => $id])->row_array();
        }

        return $this->db->get(db_prefix().'wtc_bot')->result_array();
    }

    public function deleteMessageBot($type, $id)
    {
        $message = _l('something_went_wrong');

        $bot = ('template' == $type) ? $this->getTemplateBot($id) : $this->getMessageBot($id);
        $table = ('template' == $type) ? 'wtc_campaigns' : 'wtc_bot';

        $this->db->delete(db_prefix() . $table, ['id' => $id]);

        if ($this->db->affected_rows() > 0) {
            $dir_name = ('template' == $type) ? 'template' : 'bot_files';
            $path = WHATSBOT_MODULE_UPLOAD_FOLDER . '/' . $dir_name . '/' . $bot['filename'];
            if (file_exists($path) && !is_dir($path)) {
                unlink($path);
            }
            $message = _l('bot_deleted_successfully');
        }

        return [
            'type'    => 'danger',
            'message' => $message,
        ];
    }

    public function saveTemplateBot($data)
    {
        $data['header_params']         = json_encode($data['header_params'] ?? []);
        $data['body_params']           = json_encode($data['body_params'] ?? []);
        $data['footer_params']         = json_encode($data['footer_params'] ?? []);

        $insert = $update = false;
        if (empty($data['id'])) {
            $insert = $this->db->insert(db_prefix().'wtc_campaigns', $data);
            $bot_id = $this->db->insert_id();
        } else {
            $update = $this->db->update(db_prefix().'wtc_campaigns', $data, ['id' => $data['id']]);
            $bot_id = $data['id'];
        }

        return [
            'type'    => $insert || $update ? 'success' : 'danger',
            'message' => ($insert) ? _l('template_bot_create_successfully') : ($update ? _l('template_bot_update_successfully') : _l('something_went_wrong')),
            'temp_id' => $bot_id,
        ];
    }

    public function getTemplateBot($id = '')
    {
        if (!empty($id)) {
            return $this->db->get_where(db_prefix() . 'wtc_campaigns', ['id' => $id])->row_array();
        }

        return $this->db->get_where(db_prefix() . 'wtc_campaigns', ['is_bot' => '1'])->result_array();
    }

    public function getTemplateBotsByRelType($relType, $message, $botType = null)
    {
        if (!is_null($botType)) {
            $this->db->where("bot_type", $botType);
        }

        if(!empty($message)){
            $this->db->select(db_prefix() . 'wtc_campaigns.id AS campaign_table_id, ' . db_prefix() . 'wtc_campaigns.*, ' . db_prefix() . 'wtc_templates.*');
            $messageWords = explode(' ', $message);

            foreach ($messageWords as $value) {
                $value = str_replace(["'", "\""], "", $value);
                $this->db->or_where("FIND_IN_SET(" . $this->db->escape($value) . ", `trigger`) >", 0);
            }
        }

        $this->db->join(db_prefix() . 'wtc_templates', db_prefix() . 'wtc_campaigns.template_id = ' . db_prefix() . 'wtc_templates.id', 'left');
        $data = $this->db->get_where(db_prefix() . 'wtc_campaigns', ['rel_type' => $relType, 'is_bot' => 1, 'is_bot_active' => 1]);

        if($data->num_rows() == 0 && $botType != 4){
            return $this->getTemplateBotsByRelType($relType, '', 4);
        }

        return $data->result_array();
    }

    public function getMessageBotsByRelType($relType, $message, $replyType = null)
    {
        if (!is_null($replyType)) {
            $this->db->where("reply_type", $replyType);
        }

        if(!empty($message)){
            $messageWords = explode(' ', $message);

            foreach ($messageWords as $value) {
                $value = str_replace(["'", "\""], "", $value);
                $this->db->or_where("FIND_IN_SET('$value', `trigger`) >", 0);
            }
        }

        $data = $this->db->get_where(db_prefix() . 'wtc_bot', ['rel_type' => $relType, 'is_bot_active' => 1]);
        
        if($data->num_rows() == 0 && $replyType != 4){
            return $this->getMessageBotsByRelType($relType, '', 4);
        }

        return $data->result_array();
    }

    public function change_active_status($type, $id, $status)
    {
        if ('message' == $type) {
            return $this->db->update(db_prefix() . 'wtc_bot', ['is_bot_active' => $status], ['id' => $id]);
        } elseif ('template' == $type) {
            return $this->db->update(db_prefix() . 'wtc_campaigns', ['is_bot_active' => $status], ['id' => $id, 'is_bot' => 1]);
        }
    }

    public function update_sending_count($table, $count, $where)
    {
        return $this->db->update($table, ['sending_count' => $count], $where);
    }

    public function delete_bot_files($id)
    {
        $bot = $this->getMessageBot($id);

        $update = $this->db->update(db_prefix() . 'wtc_bot', ['filename' => NULL], ['id' => $id]);
        $path = WHATSBOT_MODULE_UPLOAD_FOLDER . '/bot_files/' . $bot['filename'];
        if ($update && file_exists($path)) {
            unlink($path);
        }

        return [
            'message' => ($update) ? _l('image_deleted_successfully') : _l('something_went_wrong'),
        ];
    }

    public function clone_bot($type, $id)
    {
        if ($type == 'text') {
            $bot_data = $this->getMessageBot($id);
            $bot_data['id'] = '';
            if (!empty($bot_data['filename'])) {
                $new_file_name = time() . '.' . pathinfo($bot_data['filename'], PATHINFO_EXTENSION);
                $bot_data['filename'] = copy(WHATSBOT_MODULE_UPLOAD_FOLDER . '/bot_files/' . $bot_data['filename'], WHATSBOT_MODULE_UPLOAD_FOLDER . '/bot_files/' . $new_file_name) ? $new_file_name : '';
            }
            $clone_bot = $this->saveBots($bot_data);
        } else {
            $bot_data = $this->getTemplateBot($id);
            $bot_data['id'] = '';
            if (!empty($bot_data['filename'])) {
                $new_file_name = time() . '.' . pathinfo($bot_data['filename'], PATHINFO_EXTENSION);
                $bot_data['filename'] = copy(WHATSBOT_MODULE_UPLOAD_FOLDER . '/template/' . $bot_data['filename'], WHATSBOT_MODULE_UPLOAD_FOLDER . '/template/' . $new_file_name) ? $new_file_name : '';
            }
            $clone_bot = $this->saveTemplateBot($bot_data);
        }
        return [
            'id' => $type == 'template' ? $clone_bot['temp_id'] : $clone_bot['id'],
            'type' => $clone_bot ? 'success' : 'danger',
            'message' => $clone_bot ? _l('bot_clone_successfully') : _l('something_went_wrong')
        ];
    }
}
