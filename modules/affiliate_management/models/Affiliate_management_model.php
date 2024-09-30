<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Affiliate_management_model extends CI_Model
{
    /** Available tables flag for the affiliate module */
    public $affiliate_table = 'affiliate_m_affiliates';
    public $payout_table = 'affiliate_m_payouts';
    public $referral_table = 'affiliate_m_referrals';
    public $commission_table = 'affiliate_m_commissions';
    public $tracking_table = 'affiliate_m_tracking';

    /**
     * Count table data
     *
     * @param string $table
     * @param array $where
     * @return integer
     */
    public function count($table, $where = [])
    {
        if (!empty($where))
            $this->db->where($where);

        $query = $this->db->get($table);
        return $query->num_rows();
    }

    /**
     * Get all affiliate
     *
     * @return array
     */
    public function get_all_affiliates()
    {
        $payout_table = db_prefix() . $this->payout_table;
        $payouts = '(SELECT SUM(amount) FROM ' . $payout_table . ' WHERE ' . $payout_table . '.affiliate_id = ' . db_prefix() . $this->affiliate_table . '.affiliate_id AND `status`=\'' . AffiliateManagementHelper::STATUS_APPROVED . '\') as total_payouts';
        $referrals = '(SELECT COUNT(referral_id) FROM ' . db_prefix() . $this->referral_table . ' WHERE affiliate_id = ' . db_prefix() . $this->affiliate_table . '.affiliate_id) as total_referrals';
        $name = 'CONCAT(`firstname`, \' \', `lastname`) as name';

        $this->db->select("{$this->affiliate_table}.*, $payouts, $referrals, $name, email, userid");

        $contactTable = db_prefix() . 'contacts';
        $this->db->join($contactTable, $contactTable . '.id = ' . db_prefix() . $this->affiliate_table . '.contact_id', 'LEFT');

        $query = $this->db->get($this->affiliate_table);
        return $query->result();
    }

    /**
     * Get affiliate info by id
     *
     * @param integer $affiliate_id
     * @return object|null
     */
    public function get_affiliate(int $affiliate_id)
    {
        $this->db->where('affiliate_id', $affiliate_id);
        $affiliates = $this->get_all_affiliates();
        if (empty($affiliates)) return null;
        return $affiliates[0];
    }

    /**
     * Get usable affiliate balance.
     * It return balance minus any processing amount
     *
     * @param integer $affiliate_id
     * @return float
     */
    public function get_affiliate_available_balance(int $affiliate_id)
    {
        $affiliate = $this->get_affiliate($affiliate_id);
        if (!$affiliate) return 0;

        $balance = (float)$affiliate->balance;

        $this->db->select('SUM(amount) as processing_balance');
        $this->db->where('affiliate_id', $affiliate_id);
        $this->db->where('status', AffiliateManagementHelper::STATUS_PROCESSING);
        $processing_balance = (float)$this->db->get($this->payout_table)->row()->processing_balance;

        return $balance - $processing_balance;
    }

    /**
     * Get affiliate info by the contact id
     *
     * @param integer $contact_id
     * @return object|null
     */
    public function get_affiliate_by_contact_id(int $contact_id)
    {
        $this->db->where(db_prefix() . $this->affiliate_table . '.contact_id', $contact_id);
        $affiliates = $this->get_all_affiliates();
        if (empty($affiliates)) return null;
        return $affiliates[0];
    }

    /**
     * Get an affiliate info by slug
     *
     * @param string $affiliate_slug
     * @return object|null
     */
    public function get_affiliate_by_slug(string $affiliate_slug)
    {
        $this->db->where('affiliate_slug', $affiliate_slug);
        $affiliates = $this->get_all_affiliates();
        if (empty($affiliates)) return null;
        return $affiliates[0];
    }

    /**
     * Adds an affiliate with provided data.
     *
     * @param array $data The data for the affiliate.
     * @return int|null The inserted affiliate's ID or null if unsuccessful.
     */
    public function add_affiliate($data)
    {
        // Generate a unique affiliate slug if not provided
        if (empty($data['affiliate_slug'])) {
            do {
                $data['affiliate_slug'] = random_string('alpha', 8);
            } while (!empty($this->get_affiliate_by_slug($data['affiliate_slug'])->affiliate_slug ?? null));
        }

        $data['affiliate_slug'] = strtolower($data['affiliate_slug']);

        // Insert the affiliate data into the database
        $this->db->insert($this->affiliate_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Updates an affiliate's information.
     *
     * @param int $affiliate_id The ID of the affiliate to update.
     * @param array $data The updated data for the affiliate.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update_affiliate($affiliate_id, $data)
    {
        // Ensure the provided slug is unique if given
        if (!empty($data['affiliate_slug'])) {
            while (
                $this->db->where("affiliate_id != '$affiliate_id'")
                ->where('affiliate_slug', $data['affiliate_slug'])
                ->get($this->affiliate_table)
                ->num_rows() > 0
            ) {
                $data['affiliate_slug'] = strtolower(random_string('alpha', 8));
            }
            $data['affiliate_slug'] = strtolower($data['affiliate_slug']);
        }

        // Update the affiliate data in the database
        $this->db->where('affiliate_id', $affiliate_id);
        return $this->db->update($this->affiliate_table, $data);
    }

    /**
     * Retrieves all referrals based on provided conditions.
     *
     * @param array $where Conditions to filter referrals.
     * @return array An array containing referral data.
     */
    public function get_all_referrals($where = [])
    {
        // Define necessary database tables
        $clientTable = db_prefix() . 'clients';
        $contactTable = db_prefix() . 'contacts';
        $affiliatesTable = db_prefix() . $this->affiliate_table;

        if (!empty($where))
            $this->db->where($where);

        // Join tables and select required fields
        $this->db->join($clientTable, $clientTable . '.userid = ' . db_prefix() . $this->referral_table . '.client_id', 'LEFT');

        $this->db->join($affiliatesTable, $affiliatesTable . '.affiliate_id = ' . db_prefix() . $this->referral_table . '.affiliate_id');
        $this->db->join($contactTable, $contactTable . '.id = ' .  db_prefix() . $this->affiliate_table . '.contact_id', 'LEFT');
        $affiliate = 'CONCAT(`firstname`, \' \', `lastname`) as affiliate, affiliate_slug';

        $this->db->select($this->referral_table . ".*, $affiliate, company, company as name, $clientTable.userid");

        $query = $this->db->get($this->referral_table);
        return $query->result();
    }

    /**
     * Retrieves a referral by the associated client's ID.
     *
     * @param int $client_id The ID of the client.
     * @return object|null The referral data or null if not found.
     */
    public function get_referral_by_client_id($client_id)
    {
        $referrals = $this->get_all_referrals(['client_id' => $client_id]);
        if (empty($referrals)) return null;
        return $referrals[0];
    }

    /**
     * Retrieves a referral by its ID.
     *
     * @param int $referral_id The ID of the referral.
     * @return object|null The referral data or null if not found.
     */
    public function get_referral($referral_id)
    {
        $referrals = $this->get_all_referrals(['referral_id' => $referral_id]);
        if (empty($referrals)) return null;
        return $referrals[0];
    }

    /**
     * Adds a referral with provided data.
     *
     * @param array $data The data for the referral.
     * @return int|null The inserted referral's ID or null if unsuccessful.
     */
    public function add_referral($data)
    {
        $this->db->insert($this->referral_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Remove referrals.
     * Will cascade and remove all related commissions too
     *
     * @param integer $affiliate_id
     * @return object|null
     */
    public function remove_referral($referral_id)
    {
        $this->db->delete($this->referral_table, ['referral_id' => (int)$referral_id]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Retrieves all payouts based on provided conditions.
     *
     * @param array $where Conditions to filter payouts.
     * @return array An array containing payout data.
     */
    public function get_all_payouts($where = [])
    {
        if (!empty($where))
            $this->db->where($where);

        $query = $this->db->get($this->payout_table);
        return $query->result();
    }

    /**
     * Retrieves a payout by its ID.
     *
     * @param int $payout_id The ID of the payout.
     * @return object|null The payout data or null if not found.
     */
    public function get_payout($payout_id)
    {
        $this->db->where('payout_id', $payout_id);
        $query = $this->db->get($this->payout_table);
        return $query->row();
    }

    /**
     * Adds a payout with provided data.
     *
     * @param array $data The data for the payout.
     * @return int|null The inserted payout's ID or null if unsuccessful.
     */
    public function add_payout($data)
    {
        $this->db->insert($this->payout_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Updates a payout's information.
     *
     * @param int $payout_id The ID of the payout to update.
     * @param array $data The updated data for the payout.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update_payout($payout_id, $data)
    {
        $this->db->where('payout_id', $payout_id);
        return $this->db->update($this->payout_table, $data);
    }

    /**
     * Retrieves all commissions based on provided conditions.
     *
     * @param array $where Conditions to filter commissions.
     * @return array An array containing commission data.
     */
    public function get_all_commissions($where = [])
    {
        if (!empty($where))
            $this->db->where($where);

        $query = $this->db->get($this->commission_table);
        return $query->result();
    }

    /**
     * Retrieves a commission by its ID.
     *
     * @param int $commission_id The ID of the commission.
     * @return object|null The commission data or null if not found.
     */
    public function get_commission($commission_id)
    {
        $this->db->where('commission_id', $commission_id);
        $query = $this->db->get($this->commission_table);
        return $query->row();
    }

    /**
     * Adds a commission with provided data.
     *
     * @param array $data The data for the commission.
     * @return int|null The inserted commission's ID or null if unsuccessful.
     */
    public function add_commission($data)
    {
        $this->db->insert($this->commission_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Retrieves the total earnings based on provided conditions.
     *
     * @param array $where Conditions to filter earnings.
     * @return float The total earnings amount.
     */
    public function get_total_earnings($where = [])
    {
        if (!empty($where))
            $this->db->where($where);
        return (float)$this->db->select('SUM(total_earnings) as total')->get($this->affiliate_table)->row()->total;
    }

    /**
     * Retrieves the total payouts based on provided conditions.
     *
     * @param array $where Conditions to filter payouts.
     * @return float The total payout amount.
     */
    public function get_total_payouts($where = ['status' => AffiliateManagementHelper::STATUS_APPROVED])
    {
        if (!empty($where))
            $this->db->where($where);
        return (float)$this->db->select('SUM(amount) as total')->get($this->payout_table)->row()->total;
    }

    public function add_referral_tracking($data)
    {
        $this->db->insert($this->tracking_table, $data);
        return $this->db->insert_id();
    }

    public function find_referral_trackings($where)
    {
        $this->db->where($where);
        $query = $this->db->get($this->tracking_table);
        return $query->result();
    }

    public function find_affilate_slug_from_trackings($where)
    {
        $trackings = $this->find_referral_trackings($where);
        if (isset($trackings[0]->affiliate_slug))
            return $trackings[0]->affiliate_slug;
        return null;
    }

    /**
     * Updates an commission's information.
     *
     * @param int $commission_id The ID of the commission to update.
     * @param array $data The updated data for the commission.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update_commission($commission_id, $data)
    {
        // Update the commission data in the database
        $this->db->where('commission_id', $commission_id);
        return $this->db->update($this->commission_table, $data);
    }
}
