<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This class describes a wallet.
 */
class Walletmanager
{
    public $wallet_table;
    public $ledger_table;
    public $db;
    public $encryption;
    public $initial_opening_balance;

    /**
     * Tag flags
     */
    const TAG_FUNDING = 'funding';
    const TAG_SYSTEM = 'system';
    const TAG_INVOICE_PAYMENT = 'invoice payment';
    const TAG_REVERSAL = 'reversal';
    const TAG_WITHDRAWAL = 'withdrawal';
    const TAG_CANCELLED_WITHDRAWAL = 'cancelled_withdrawal';

    /**
     * Mode flags
     */
    const MODE_DEBIT = 'debit';
    const MODE_CREDIT = 'credit';
    const MODE_LOG = 'log';

    function __construct()
    {

        $ci = &get_instance();
        $this->db = $ci->db;
        $this->encryption = $ci->encryption;

        $this->wallet_table = db_prefix() . 'wallet';
        $this->ledger_table = db_prefix() . 'wallet_transaction';
        $this->initial_opening_balance = (float)get_option('wallet_initial_credit_amount');
    }

    /**
     * Locks the tables.
     */
    private function lock_tables()
    {
        $this->db->query("LOCK TABLES $this->ledger_table write, $this->wallet_table write");
    }

    /**
     * Unlocks the tables.
     */
    private function unlock_tables()
    {
        $this->db->query("UNLOCK TABLES");
    }

    /**
     * Get wallet by field name
     *
     * @param string $field_name id
     * @param string $id
     * @return object
     */
    public function get_user_wallet(string $contact_id)
    {
        $row = $this->db->where("contact_id", $contact_id)->get($this->wallet_table)->row();
        if (!$row) {
            $balance = $this->encryption->encrypt(0);
            $data = ['contact_id' => $contact_id, 'balance' => $balance];
            $data['id'] = $this->db->insert($this->wallet_table, $data);

            // Initial credit
            $initial_credit = $this->initial_opening_balance;
            if ($initial_credit > 0) {
                $this->credit($initial_credit, _l('wallet_initial_credit_amount_transaction_description'), $contact_id, NULL, time() . '_' . $contact_id, NULL, self::TAG_SYSTEM);
                return $this->get_user_wallet($contact_id);
            }

            return (object)$data;
        }
        return $row;
    }

    /**
     * Check wallet balance
     *
     * @param string $contact_id  The user identifier
     *
     * @return float|int
     */
    public function balance(string $contact_id)
    {
        $wallet = $this->get_user_wallet($contact_id);
        return (float)$this->encryption->decrypt($wallet->balance);
    }

    /**
     * Update a wallet balance
     *
     * @param int $contact_id
     * @param float $balance
     * @return	bool	TRUE on success, FALSE on failure
     */
    public function set_balance(int $contact_id, float $balance)
    {
        $balance = $this->encryption->encrypt($balance);
        return $this->db->where('contact_id', $contact_id)
            ->set('balance', $balance)
            ->update($this->wallet_table);
    }

    /**
     * Charge a wallet
     *
     * @param      float|int  $amount       The amount
     * @param      string     $description  The description
     * @param      string     $contact_id      The user identifier
     * @param      string  $invoice_id      The invoice identifier
     * @param      string     $ref_id       The reference identifier
     * @param      array|object  $metadata   The extra meta info about the log
     * @param      string     $tag          The tag
     *
     * @return     bool
     */
    public function debit(float $amount, string $description, $contact_id, $invoice_id, $ref_id, $metadata = NULL, $tag = self::TAG_INVOICE_PAYMENT)
    {

        $amount = (float)$amount;

        // Prevent negative value
        if ($amount < 0) return false;

        if (!$this->can_transact_with_ref($ref_id)) return false;

        // lock the needed tables
        $this->lock_tables();
        $this->db->trans_begin();

        // make sure we have enough in the bank account
        $balance = (float)$this->balance($contact_id);

        if ($balance < $amount) {
            // release the locks
            $this->unlock_tables();
            $this->db->trans_complete();
            return false;
        }

        // take the money out of the wallet
        if ($this->set_balance($contact_id, $balance - $amount)) {

            // record on ledger
            $data = [
                'contact_id' => $contact_id,
                'description' => $description,
                'amount' => $amount,
                'tag' => $tag,
                'metadata' => json_encode($metadata),
                'invoice_id' => $invoice_id,
                'ref_id' => $ref_id,
                'mode' => self::MODE_DEBIT,
            ];

            $this->db->insert($this->ledger_table, $data);

            $txn_id = $this->db->insert_id();

            $this->db->trans_complete();

            // release the locks
            $this->unlock_tables();

            return $txn_id;
        }

        // Release locks
        $this->db->trans_rollback();
        $this->unlock_tables();

        return false;
    }

    /**
     * Credit a wallet
     *
     * @param      float    $amount       The amount
     * @param      string  $description  The description
     * @param      string  $contact_id      The user identifier
     * @param      string  $invoice_id      The invoice identifier
     * @param      string  $ref_id       The payment reference identifier
     * @param      array|object  $metadata   The extra meta info about the transaction log
     * @param      string  $tag          The tag
     *
     * @return     bool
     */
    public function credit(float $amount, string $description, $contact_id, $invoice_id, $ref_id, $metadata = NULL, $tag = self::TAG_FUNDING)
    {

        $amount = (float)$amount;

        // Prevent negative value
        if ($amount < 0) return false;

        if (!$this->can_transact_with_ref($ref_id)) return false;

        // lock the needed tables
        $this->lock_tables();

        $this->db->trans_begin();

        // add the money to the wallet
        $balance = (float)$this->balance($contact_id);
        if ($this->set_balance($contact_id, $balance + (float)$amount)) {

            // record on ledger
            $data = [
                'contact_id' => $contact_id,
                'description' => $description,
                'amount' => $amount,
                'tag' => $tag,
                'metadata' => json_encode($metadata),
                'invoice_id' => $invoice_id,
                'ref_id' => $ref_id,
                'mode' => self::MODE_CREDIT,
            ];

            $this->db->insert($this->ledger_table, $data);

            $txn_id = $this->db->insert_id();

            $this->db->trans_complete();

            // release the locks
            $this->unlock_tables();

            return $txn_id;
        }

        // Release locks
        $this->db->trans_rollback();
        $this->unlock_tables();

        return false;
    }


    /**
     * Method to determines ability to transact with reference.
     *
     * @param string $ref_id  The reference identifier
     *
     * @return bool True if able to transact with reference, False otherwise.
     */
    public function can_transact_with_ref($ref_id)
    {
        $ledger = $this->ledger_find_by_ref($ref_id);
        return $ledger ? false : true;
    }


    /**
     * Get wallet history for this user
     *
     * @param      string  $contact_id  The user identifier
     *
     * @return     array   The array of transactions
     */
    public function ledger(string $contact_id)
    {

        return $this->db->where("contact_id", (int)$contact_id)->order_by('created_at', 'DESC')->get($this->ledger_table)->result();
    }

    /**
     * Find a transaction by invoice id
     *
     * @param      int  $invoice_id  The invoice reference identifier
     *
     * @return     Object  Transaction
     */
    public function ledger_find_by_invoice_id($invoice_id, $where = [])
    {
        if (!empty($where)) $this->db->where($where);

        return $this->db->where("invoice_id", $invoice_id)->get($this->ledger_table)->row();
    }

    /**
     * Find a transaction by payment id
     *
     * @param      int  $ref_id  The reference identifier
     *
     * @return     Object  Transaction
     */
    public function ledger_find_by_ref($ref_id, $where = [])
    {
        if (!empty($where)) $this->db->where($where);

        return $this->db->where("ref_id", $ref_id)->get($this->ledger_table)->row();
    }


    /**
     * Find a transaction by id
     *
     * @param      int  $id  The identifier
     *
     * @return     Object  Transaction
     */
    public function ledger_find_by_id(int $id)
    {

        return $this->db->where("id", (int)$id)->get($this->ledger_table)->row();
    }


    /**
     * Get sum of all debit charge on the given account.
     *
     * @param      string  $contact_id  The user identifier
     *
     * @return     float   The total debit
     */
    public function total_debit(string $contact_id)
    {
        if (!empty($contact_id))
            $this->db->where("contact_id", $contact_id);

        return (float)$this->db->select_sum('amount')->where('mode', 'debit')->get($this->ledger_table)->row()->amount;
    }

    /**
     * Get sum of all credit on a given account.
     *
     * @param      string  $contact_id  The user identifier
     *
     * @return     float   The total credit
     */
    public function total_credit(string $contact_id)
    {
        if (!empty($contact_id))
            $this->db->where("contact_id", $contact_id);

        return (float)$this->db->select_sum('amount')->where('mode', 'credit')->get($this->ledger_table)->row()->amount;
    }


    /**
     * Log a funding attempt
     *
     * @param      float    $amount       The amount
     * @param      string  $description  The description
     * @param      string  $contact_id      The user identifier
     * @param      string  $invoice_id      The invoice identifier
     * @param      string  $ref_id       The reference identifier
     * @param      array|object  $metadata   The extra meta info about the log
     *
     * @return     bool
     */
    public function log(float $amount, string $description, $contact_id, $invoice_id, $ref_id, $metadata = NULL, $tag = self::TAG_FUNDING)
    {

        if (!$this->can_transact_with_ref($ref_id)) return false;

        // lock the needed tables
        $this->lock_tables();

        $this->db->trans_begin();

        // record on ledger
        $data = [
            'contact_id' => $contact_id,
            'description' => $description,
            'amount' => $amount,
            'tag' => $tag,
            'metadata' => json_encode($metadata),
            'invoice_id' => $invoice_id,
            'ref_id' => $ref_id,
            'mode' => self::MODE_LOG,
        ];

        $this->db->insert($this->ledger_table, $data);

        $txn_id = $this->db->insert_id();

        $this->db->trans_complete();

        // release the locks
        $this->unlock_tables();

        return $txn_id;
    }

    /**
     * Update log
     *
     * @param integer $log_id
     * @param array $data
     * @return int
     */
    public function log_update(int $log_id, array $data)
    {

        // lock the needed tables
        $this->lock_tables();

        $this->db->trans_begin();

        $this->db->where('id', $log_id);
        $this->db->update($this->ledger_table, $data);

        $this->db->trans_complete();

        // release the locks
        $this->unlock_tables();

        return $log_id;
    }

    /**
     * Remove a transaction log identified by id
     *
     * @param int $id
     * @return bool
     */
    public function unlog($id)
    {
        $id = (int) $id;
        $ledger = $this->ledger_find_by_id($id);
        if ($ledger) {
            if ($ledger->mode == self::MODE_LOG)
                return $this->db->delete($this->ledger_table, ['id' => $id]);
        }
        return false;
    }
}
