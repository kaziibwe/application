<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Interaction_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
    }

    /**
     * Get all interaction messages from the database.
     *
     * @return array Array of interaction messages
     */
    public function get_interactions()
    {
        // Fetch interactions ordered by time_sent in descending order
        $interactions = $this->db->order_by('time_sent', 'DESC')->get(db_prefix() . 'wtc_interactions')->result_array();

        // Fetch messages for each interaction
        foreach ($interactions as &$interaction) {
            $interaction['agent'] = (is_string($interaction['agent'])) ? json_decode($interaction['agent']) : $interaction['agent'];
            if (!empty($interaction['agent']->agent_id) && is_array($interaction['agent']->agent_id)) {
                $agent_ids = $interaction['agent']->agent_id;
                $agent_name = implode(',', array_map('get_staff_full_name', $agent_ids));
            }

            $interaction['agent_icon'] = $this->load->view('interaction_staff', $interaction, true);

            $interaction['agent_name'] = [
                'agent_name' => $agent_name ?? '',
                'assign_name' => !empty($interaction['agent']->assign_id) ? get_staff_full_name($interaction['agent']->assign_id) : '',
            ];
            $interaction_id = $interaction['id'];
            $messages       = $this->get_interaction_messages($interaction_id);
            $this->map_interaction($interaction);
            $interaction['messages'] = $messages;

            // Fetch staff name for each message in the interaction
            foreach ($interaction['messages'] as &$message) {
                if (!empty($message['staff_id'])) {
                    $message['staff_name'] = get_staff_full_name($message['staff_id']);
                } else {
                    $message['staff_name'] = null;
                }

                // Check if URL is already a base name
                if ($message['url'] && false === strpos($message['url'], '/')) {
                    // If URL doesn't contain "/", consider it as a file name
                    // Assuming base URL is available
                    $message['asset_url'] = WHATSBOT_MODULE_UPLOAD_URL . $message['url'];
                } else {
                    // Otherwise, use the URL directly
                    $message['asset_url'] = $message['url'] ?? null;
                }
            }
        }

        return $interactions;
    }

    /**
     * Insert a new interaction message into the database.
     *
     * @param array $data Data to be inserted
     *
     * @return int Insert ID
     */
    public function insert_interaction($data)
    {
        $existing_interaction = $this->db->where('receiver_id', $data['receiver_id'])->where('wa_no', $data['wa_no'])->where('wa_no_id', $data['wa_no_id'])->get(db_prefix() . 'wtc_interactions')->row();

        if ($existing_interaction) {
            // Existing interaction found with matching 'receiver_id' and 'wa_no'
            $this->db->where('id', $existing_interaction->id)->update(db_prefix() . 'wtc_interactions', $data);

            return $existing_interaction->id;
        }
        // No existing interaction found with matching 'receiver_id' and 'wa_no'
        $this->db->insert(db_prefix() . 'wtc_interactions', $data);

        return $this->db->insert_id();
    }

    /**
     * Get all interaction messages for a specific interaction ID.
     *
     * @param int $interaction_id ID of the interaction
     *
     * @return array Array of interaction messages
     */
    public function get_interaction_messages($interaction_id)
    {
        $this->db->where('interaction_id', $interaction_id)->order_by('time_sent', 'asc');

        return $this->db->get(db_prefix() . 'wtc_interaction_messages')->result_array();
    }

    /**
     * Insert a new interaction message into the database.
     *
     * @param array $data Data to be inserted
     *
     * @return int Insert ID
     */
    public function insert_interaction_message($data)
    {
        // Assuming 'wtc_interaction_messages' is the table name
        $this->db->insert(db_prefix() . 'wtc_interaction_messages', $data);

        // Check if the insert was successful
        if ($this->db->affected_rows() > 0) {
            // Return the ID of the inserted message
            return $this->db->insert_id();
        }
        // Return false if the insert failed
        return false;
    }

    /**
     * Get the ID of the last message for a given interaction.
     *
     * @param int $interaction_id ID of the interaction
     *
     * @return int ID of the last message
     */
    public function get_last_message_id($interaction_id)
    {
        $this->db->select_max('id')
            ->where('interaction_id', $interaction_id);
        $query  = $this->db->get(db_prefix() . 'wtc_interaction_messages');
        $result = $query->row_array();

        return $result['id'];
    }

    /**
     * Update the status of a message in the database.
     *
     * @param int    $interaction_id ID of the interaction
     * @param string $status         Status to be updated
     *
     * @return void
     */
    public function update_message_status($interaction_id, $status)
    {
        $this->db->where('message_id', $interaction_id)
            ->update(db_prefix() . 'wtc_interaction_messages', ['status' => $status]);
    }

    /**
     * Map interaction data to entities based on receiver ID.
     *
     * @param array $interaction interaction data
     *
     * @return void
     */
    public function map_interaction($interaction)
    {
        if (null === $interaction['type'] || null === $interaction['type_id'] || empty($interaction['type_id'])) {
            $interaction_id = $interaction['id'];
            $receiver_id    = $interaction['receiver_id'];
            $customer       = $this->db->where('phonenumber', $receiver_id)->get(db_prefix() . 'clients')->row();
            $contact        = $this->db->where('phonenumber', $receiver_id)->get(db_prefix() . 'contacts')->row();
            $lead           = $this->db->where('phonenumber', $receiver_id)->get(db_prefix() . 'leads')->row();
            $staff          = $this->db->where('phonenumber', $receiver_id)->get(db_prefix() . 'staff')->row();

            $entity = null;
            $type   = null;

            if ($customer) {
                $entity = $customer->userid;
                $type   = 'customer';
            } elseif ($contact) {
                $entity = $contact->id;
                $type   = 'contacts';
            } elseif ($staff) {
                $entity = $staff->staffid;
                $type   = 'staff';
            } else {
                $type = 'leads';
                $entity = (!empty($lead)) ? $lead->id : hooks()->apply_filters('ctl_auto_lead_creation', $receiver_id, $interaction['name']);
            }

            $data = [
                'type'        => $type,
                'type_id'     => $entity,
                'wa_no'       => $interaction['wa_no'] ?? get_option('wac_default_phone_number'),
                'receiver_id' => $receiver_id,
            ];

            $existing_interaction = $this->db->where('id', $interaction_id)->get(db_prefix() . 'wtc_interactions')->row();

            if ($existing_interaction) {
                $this->db->where('id', $interaction_id)->update(db_prefix() . 'wtc_interactions', $data);
            } else {
                $data['id'] = $interaction_id;
                $this->db->insert(db_prefix() . 'wtc_interactions', $data);
            }
        }

        if (null === $interaction['wa_no'] || null === $interaction['wa_no_id']) {
            $interaction_id = $interaction['id'];

            // Use null coalescing operator to provide default values if 'wa_no' or 'wa_no_id' is null
            $wa_no    = $interaction['wa_no'] ?? get_option('wac_default_phone_number');
            $wa_no_id = $interaction['wa_no_id'] ?? get_option('wac_phone_number_id');

            // Prepare data for update
            $data = [
                'wa_no'    => $wa_no,
                'wa_no_id' => $wa_no_id,
            ];

            // Check if the interaction exists
            $existing_interaction = $this->db->where('id', $interaction_id)->get(db_prefix() . 'wtc_interactions')->row();

            if ($existing_interaction) {
                // Update the existing interaction
                $this->db->where('id', $interaction_id)->update(db_prefix() . 'wtc_interactions', $data);
            }
        }

        $agent_data =  (is_string($interaction['agent'])) ? json_decode($interaction['agent']) : $interaction['agent'];

        $agent_id = $agent_data->agent_id ?? 0;
        $data = [
            'assign_id' => 0,
            'agent_id'  => $agent_id
        ];
        
        if ($interaction['type'] == 'leads') {
            $asign_id = $this->leads_model->get($interaction['type_id'])->assigned ?? 0;
            $data = [
                'assign_id' => $asign_id,
                'agent_id'  => $agent_id
            ];
        }
        $this->db->update(db_prefix() . 'wtc_interactions', ['agent' => json_encode($data)], ['id' => $interaction['id']]);
    }

    public function chat_mark_as_read($id)
    {
        return $this->db->update(db_prefix() . 'wtc_interaction_messages', ['is_read' => 1], ['interaction_id' => $id]);
    }

    public function get_interaction($id)
    {
        return $this->db->get_where(db_prefix() . 'wtc_interactions', ['id' => $id])->row_array();
    }

    public function add_assign_staff($post_data)
    {
        $staff_id = $post_data['staff_id'];
        $interaction_id = $post_data['interaction_id'];
        $interaction = $this->get_interaction($interaction_id);
        $asign_id = 0;
        if ($interaction && $interaction['type'] == 'leads') {
            $asign_id = $this->leads_model->get($interaction['type_id'])->assigned;
        }
        $data = [
            'assign_id' => $asign_id,
            'agent_id'  => $staff_id
        ];
        return $this->db->update(db_prefix() . 'wtc_interactions', ['agent' => json_encode($data)], ['id' => $interaction_id]);
    }

    public function remove_staff($post_data)
    {
        $staff_id = $post_data['staff_id'];
        $interaction_id = $post_data['interaction_id'];
        $interaction = $this->get_interaction($interaction_id);
        $agent_ids = json_decode($interaction['agent'])['agent_id'];
        $assign_id = json_decode($interaction['agent'])['assign_id'];
        unset($agent_ids[$staff_id]);
        $agent_ids = json_encode(array_values($agent_ids));
        $data = [
            'assign_id' => $assign_id,
            'agent_id'  => $agent_ids
        ];
        return $this->db->update(db_prefix() . 'wtc_interactions', ['agent' => json_encode($data)], ['id' => $interaction_id]);
    }
}
