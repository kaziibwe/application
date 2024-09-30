<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Affiliate_management extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('affiliate_management_model');
        $this->load->helper('string');
    }

    /**
     * Display the list of all affiliates.
     */
    function index()
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'view')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        // Return the table data for ajax request
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(AFFILIATE_MANAGEMENT_MODULE_NAME, 'admin/affiliates/table'));
        }

        // Show list of comapnies
        $data['title'] = _l('affiliate_management_affiliates');

        $currency = get_base_currency();

        $stats = [];

        $total_clients = $this->affiliate_management_model->count('clients');
        $total_referrals = $this->affiliate_management_model->count($this->affiliate_management_model->referral_table);

        $stats[] = [
            'label' => _l('affiliate_management_total_affiliates'),
            'value' => $this->affiliate_management_model->count($this->affiliate_management_model->affiliate_table),
            'icon' => 'fa fa-user',
            'url' => admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/affiliates')
        ];
        $pending_affiliates = $this->affiliate_management_model->count($this->affiliate_management_model->affiliate_table, ['status' => AffiliateManagementHelper::STATUS_PENDING]);
        $stats[] = [
            'label' => _l('affiliate_management_pending_affiliates'),
            'value' => $pending_affiliates,
            'icon' => 'fa fa-clock',
            'style' => $pending_affiliates > 0 ? 'border: 1px solid red;' : 'border: 1px solid green;',
            'url' => admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/affiliates')
        ];

        $stats[] = [
            'label' => _l('affiliate_management_total_clients'),
            'value' => $total_clients,
            'icon' => 'fa fa-user-plus'
        ];
        $stats[] = [
            'label' => _l('affiliate_management_total_referrals'),
            'value' => $total_referrals,
            'icon' => 'fa fa-refresh'
        ];
        $stats[] = [
            'label' => _l('affiliate_management_affiliate_influence'),
            'value' => $total_clients <= 0 ? 0 : app_format_number(($total_referrals / $total_clients) * 100) . '%',
            'icon'  => 'fa fa-percent'
        ];
        $stats[] = [
            'label' => _l('affiliate_management_lifetime_earnings'),
            'value' => app_format_money($this->affiliate_management_model->get_total_earnings(), $currency),
            'icon' => 'fa fa-coins'
        ];
        $stats[] = [
            'label' => _l('affiliate_management_lifetime_payouts'),
            'value' => app_format_money($this->affiliate_management_model->get_total_payouts(), $currency),
            'icon' => 'fa fa-money-bill'
        ];
        $pending_payouts = $this->affiliate_management_model->get_total_payouts(['status' => AffiliateManagementHelper::STATUS_PENDING]);
        $stats[] = [
            'label' => _l('affiliate_management_pending_payouts'),
            'value' => app_format_money($pending_payouts, $currency),
            'icon' => 'fa fa-clock',
            'style' => $pending_payouts > 0 ? 'border: 1px solid red;' : 'border: 1px solid green;',
        ];


        $data['stats'] = $stats;
        $data['pending_payouts'] = $pending_payouts;
        $data['pending_affiliates'] = $pending_affiliates;

        $filter_col = AffiliateManagementHelper::COMMISION_RULE_NO_PAYMENT === AffiliateManagementHelper::get_commission_rule(null) ? 'total_referrals' : 'total_earnings';
        $this->affiliate_management_model->db->order_by($filter_col, 'DESC')->limit(10);
        $data['top_affiliates'] = $this->affiliate_management_model->get_all_affiliates();

        $this->affiliate_management_model->db->order_by('created_at', 'DESC')->limit(10);
        $data['latest_referrals'] = $this->affiliate_management_model->get_all_referrals();

        $data['currency'] = $currency;

        $this->load->view('admin/dashboard', $data);
    }

    /**
     * Display the list of all affiliates.
     */
    function affiliates()
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'view')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        // Return the table data for ajax request
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(AFFILIATE_MANAGEMENT_MODULE_NAME, 'admin/affiliates/table'));
        }

        // Show list of comapnies
        $data['title'] = _l('affiliate_management_affiliates');
        $data['affiliates'] = $this->affiliate_management_model->get_all_affiliates();
        $this->load->view('admin/affiliates/manage', $data);
    }

    /**
     * Create new affiliate
     *
     * @return void
     */
    public function add_affiliate()
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'create')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        $data['title'] = _l('affiliate_management_add_new_affiliate');

        if ($this->input->post()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('contact_id', _l('affiliate_management_contact'), 'required');

            if ($this->form_validation->run() !== FALSE) {
                $contact_id = $this->input->post('contact_id');
                $contact = $this->clients_model->get_contact($contact_id);
                if ($contact) {

                    if ($this->affiliate_management_model->add_affiliate(['contact_id' => $contact_id, 'status' => AffiliateManagementHelper::STATUS_ACTIVE])) {
                        set_alert('success', _l('affiliate_management_contact_added'));
                        redirect(admin_url('affiliate_management/affiliates'));
                    }
                } else {
                    set_alert('alert', _l('affiliate_management_contact_not_found'));
                }
            }
        }

        $data['contacts'] = $this->clients_model->get_contacts('');

        $this->load->view('admin/affiliates/form', $data);
    }

    /**
     * Method to handle updating of affiliate status
     *
     * @param [type] $status
     * @param [type] $affiliate_id
     * @return void
     */
    public function update_affiliate_status($status, $affiliate_id)
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'edit')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }


        $affiliate = $this->affiliate_management_model->get_affiliate($affiliate_id);
        if ($affiliate && in_array($status, [AffiliateManagementHelper::STATUS_ACTIVE, AffiliateManagementHelper::STATUS_INACTIVE, AffiliateManagementHelper::STATUS_PENDING])) {

            $data = array(
                'status' => $status,
            );

            $this->affiliate_management_model->update_affiliate($affiliate_id, $data);

            set_alert('success', _l('updated_successfully', _l('affiliate_management_affiliates')));
            redirect(admin_url('affiliate_management/affiliates'));
        }

        return redirect(admin_url('affiliate_management/affiliates'));
    }

    /**
     * View affiliate details and dashboard
     *
     * @param mixed $affiliate_id
     * @return void
     */
    public function view_affiliate($affiliate_id)
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'view')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        $affiliate = $this->affiliate_management_model->get_affiliate($affiliate_id);

        // Return the table data for ajax request
        if ($this->input->is_ajax_request()) {
            $table = $this->input->get('table', true);
            $data = ['table_affiliate_id' => $affiliate->affiliate_id];
            $this->app->get_table_data(module_views_path(AFFILIATE_MANAGEMENT_MODULE_NAME, 'admin/' . $table . '/table'), $data);
        }

        $currency = get_base_currency();
        $stats = [];
        $stats[] = [
            'label' => _l('affiliate_management_total_referrals'),
            'value' => (int)$affiliate->total_referrals,
            'icon' => 'fa fa-user'
        ];
        $stats[] = [
            'label' => _l('affiliate_management_lifetime_earnings'),
            'value' => app_format_money($affiliate->total_earnings, $currency),
            'icon' => 'fa fa-coins'
        ];
        $stats[] = [
            'label' => _l('affiliate_management_lifetime_payouts'),
            'value' => app_format_money($affiliate->total_payouts, $currency),
            'icon' => 'fa fa-money-bill'
        ];
        $stats[] = [
            'label' => _l('affiliate_management_current_balance'),
            'value' => app_format_money($affiliate->balance, $currency),
            'icon' => 'fa fa-wallet'
        ];

        $data['affiliate'] = $affiliate;
        $data['stats'] = $stats;
        $data['currency'] = $currency;

        $this->load->view('admin/affiliates/view', $data);
    }

    /**
     * Method to add client to an affiliate.
     *
     * @return void
     */
    public function assign_client_affiliate()
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'edit')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        try {
            if (!$this->input->post())
                throw new \Exception(_l('affiliate_management_unsupported_http_method'), 1);

            $client_id = $this->input->post('client_id', true);
            $affiliate_id = $this->input->post('affiliate_id', true);

            $referral = $this->affiliate_management_model->get_referral_by_client_id($client_id);
            if ($referral)
                throw new \Exception(_l("affiliate_management_assign_client_already_assigned"), 1);

            $affiliate = $this->affiliate_management_model->get_affiliate($affiliate_id);
            if (empty($affiliate->affiliate_id))
                throw new \Exception(_l("affiliate_management_not_found", _l("affiliate_management_affiliate")), 1);

            $client = $this->clients_model->get($client_id);
            if (empty($client->userid))
                throw new \Exception(_l("affiliate_management_not_found", _l("affiliate_management_contact")), 1);

            if (!$this->affiliate_management_model->add_referral([
                'client_id' => $client_id,
                'affiliate_id' => $affiliate_id,
            ]))
                throw new \Exception(_l("affiliate_management_an_error_occured") . ' ' . ($this->db->error() ?? ''), 1);

            echo json_encode([
                'status' => 'success',
                'message' => _l('added_successfully', _l('affiliate_management_referrals'))
            ]);
            exit;
        } catch (\Throwable $th) {

            $this->affiliate_management_model->db->trans_rollback();

            echo json_encode([
                'status' => 'danger',
                'message' => $th->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Display the list of all referred clients and referral details
     */
    public function referrals()
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'view')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        // Return the table data for ajax request
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(AFFILIATE_MANAGEMENT_MODULE_NAME, 'admin/referrals/table'));
        }

        $data['title'] = _l('affiliate_management_referrals');
        $data['affiliates'] = $this->affiliate_management_model->get_all_affiliates();
        $this->load->view('admin/referrals/manage', $data);
    }

    /**
     * Remove a referral and its commissions
     *
     * @param int $referral_id
     * @return void
     */
    public function remove_referral($referral_id)
    {
        $referral_removal_enabled = AffiliateManagementHelper::get_option('affiliate_management_enable_referral_removal') == '1';
        if ($referral_removal_enabled && $this->affiliate_management_model->remove_referral((int)$referral_id))
            set_alert('success', _l('deleted', _l('affiliate_management_referral')));

        redirect(admin_url('affiliate_management/referrals'));
    }

    /**
     * Display the list of all commissions
     */
    public function commissions()
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'view')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        // Return the table data for ajax request
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(AFFILIATE_MANAGEMENT_MODULE_NAME, 'admin/commissions/table'));
        }

        $data['title'] = _l('affiliate_management_commissions');
        $this->load->view('admin/commissions/manage', $data);
    }

    /**
     * Display the list of all payout requests
     */
    public function payouts()
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'view')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        // Return the table data for ajax request
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(AFFILIATE_MANAGEMENT_MODULE_NAME, 'admin/payouts/table'));
        }

        $data['title'] = _l('affiliate_management_payouts');
        $this->load->view('admin/payouts/manage', $data);
    }

    /**
     * Method to approve or reject a payout request from affiliate
     *
     * @param int $payout_id
     * @param string $status
     * @return mixed
     */
    public function update_payouts($payout_id, $status)
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'edit')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        if ($status === AffiliateManagementHelper::STATUS_PROCESSING) {

            if ($this->affiliate_management_model->update_payout($payout_id, ['status' => $status]))
                set_alert('success', _l('updated_successfully', _l('affiliate_management_payout')));

            return redirect(AFFILIATE_MANAGEMENT_MODULE_NAME . '/payouts');
        }

        try {

            $status_list = [
                AffiliateManagementHelper::STATUS_PROCESSING,
                AffiliateManagementHelper::STATUS_APPROVED,
                AffiliateManagementHelper::STATUS_REJECTED
            ];

            $processed_status_list = [
                AffiliateManagementHelper::STATUS_APPROVED,
                AffiliateManagementHelper::STATUS_REJECTED
            ];

            if (!$this->input->post())
                throw new \Exception(_l('affiliate_management_unsupported_http_method'), 1);

            if (!in_array($status, $status_list)) {
                throw new \Exception(_l('affiliate_management_unknown_status', _l('affiliate_management_payout')), 1);
            }

            $this->affiliate_management_model->db->trans_begin();


            $note = $this->input->post('note_for_affiliate', true);
            $data = [
                'note_for_affiliate' => $note,
                'status' => $status,
            ];

            $payout = $this->affiliate_management_model->get_payout($payout_id);
            $affiliate = $this->affiliate_management_model->get_affiliate($payout->affiliate_id);

            if (in_array($payout->status, $processed_status_list))
                throw new \Exception(_l('affiliate_management_payout_already_processed', $payout->status), 1);

            $balance = (float)$affiliate->balance;
            $amount = (float) $payout->amount;

            if (
                $status === AffiliateManagementHelper::STATUS_APPROVED ||
                $status === AffiliateManagementHelper::STATUS_PROCESSING
            ) {
                // Check if balance matches
                if ($balance < $amount)
                    throw new \Exception(_l('affiliate_management_insufficient_balance'), 1);
            }

            // Ensure affiliate have only one processing payout at a time.
            if ($status === AffiliateManagementHelper::STATUS_PROCESSING) {

                $processing_payouts = $this->affiliate_management_model->get_all_payouts([
                    'affiliate_id' => $affiliate->affiliate_id,
                    'status' => AffiliateManagementHelper::STATUS_PROCESSING
                ]);

                if (!empty($processing_payouts))
                    throw new \Exception(_l('affiliate_management_pending_payout_exist'), 1);
            }

            // Debit affiliate if approving
            if ($status === AffiliateManagementHelper::STATUS_APPROVED) {
                // Deduct the affiliate balance
                $new_balance = $balance - $amount;
                if (!$this->affiliate_management_model->update_affiliate($affiliate->affiliate_id, ['balance' => $new_balance]))
                    throw new \Exception(_l('affiliate_management_error_update_balance'), 1);
            }

            if (!$this->affiliate_management_model->update_payout($payout_id, $data))
                throw new \Exception(_l('affiliate_management_error_updating', _l('affiliate_management_payout')), 1);

            $payout = $this->affiliate_management_model->get_payout($payout_id);

            $this->affiliate_management_model->db->trans_commit();

            // Send notifications to the user
            $contact = $this->clients_model->get_contact($affiliate->contact_id);
            $affiliate->name = $contact->firstname . ' ' . $contact->lastname;
            AffiliateManagementHelper::notify(
                AffiliateManagementHelper::EMAIL_TEMPLATE_PAYOUT_UPDATED,
                $contact->email,
                $contact->userid,
                $contact->id,
                ['affiliate' => $affiliate, 'payout' => $payout]
            );

            // Send notifications to the admin
            $admin = AffiliateManagementHelper::get_admin_contact();
            $affiliate->name = $contact->firstname . ' ' . $contact->lastname;
            AffiliateManagementHelper::notify(
                AffiliateManagementHelper::EMAIL_TEMPLATE_PAYOUT_UPDATED_FOR_ADMIN,
                $admin->email,
                $contact->userid,
                $contact->id,
                ['affiliate' => $affiliate, 'payout' => $payout]
            );

            echo json_encode([
                'status' => 'success',
                'message' => _l('updated_successfully', _l('affiliate_management_payout'))
            ]);
            exit;
        } catch (\Throwable $th) {

            $this->affiliate_management_model->db->trans_rollback();

            echo json_encode([
                'status' => 'danger',
                'message' => $th->getMessage()
            ]);
            exit;
        }
    }

    public function groups($action = 'index', $id = '')
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'edit')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        $groups = AffiliateManagementHelper::get_affiliate_groups();
        $redirect_url = admin_url(AFFILIATE_MANAGEMENT_MODULE_NAME . '/groups');

        $data['groups'] = $groups;
        $data['title'] = _l('affiliate_management_affiliate_groups');


        if ($action === 'new') {

            if ($this->input->post()) {

                $id = time();
                $groups[$id] = [
                    'name' => $this->input->post('name', true),
                    'settings' => $this->input->post('settings', true)
                ];

                if (!empty($groups[$id]['name'])) {
                    if (AffiliateManagementHelper::update_affiliate_groups($groups))
                        set_alert('success', _l('added_successfully', _l('affiliate_management_affiliate_groups')));

                    return redirect($redirect_url);
                }
            }

            $group = $groups[AffiliateManagementHelper::DEFAULT_GROUP_ID];
            $group['name'] = '';
            $data['group'] = $group;
            return $this->load->view('admin/groups/form', $data);
        }


        if ($action === 'edit') {

            $group = $groups[$id] ?? [];
            if (empty($group)) return redirect($redirect_url);

            if ($this->input->post()) {

                $groups[$id] = [
                    'name' => $this->input->post('name', true),
                    'settings' => $this->input->post('settings', true)
                ];

                if (AffiliateManagementHelper::update_affiliate_groups($groups))
                    set_alert('success', _l('updated_successfully', _l('affiliate_management_affiliate_groups')));

                return redirect($redirect_url);
            }

            $data['group'] = $group;
            $data['title'] .= ': ' . $group['name'];
            return $this->load->view('admin/groups/form', $data);
        }


        if ($action === 'delete') {

            if (isset($groups[$id]) && $id != AffiliateManagementHelper::DEFAULT_GROUP_ID) {

                unset($groups[$id]);
                if (AffiliateManagementHelper::update_affiliate_groups($groups))
                    set_alert('success', _l('deleted', _l('affiliate_management_affiliate_groups')));
            }
            return redirect($redirect_url);
        }

        $this->load->view('admin/groups/manage', $data);
    }

    /**
     * Update affiliate group
     *
     * @param mixed $affiliate_id
     * @return void
     */
    public function update_affiliate_group($affiliate_id)
    {
        // Check for permission
        if (!has_permission(AFFILIATE_MANAGEMENT_MODULE_NAME, '', 'edit')) {
            return access_denied(AFFILIATE_MANAGEMENT_MODULE_NAME);
        }

        if ($this->input->post()) {
            $affiliate_id = (int)$affiliate_id;
            $affiliate = $this->affiliate_management_model->get_affiliate($affiliate_id);
            $group_id = $this->input->post('group_id', true);
            if ($affiliate && isset(AffiliateManagementHelper::get_affiliate_groups()[$group_id])) {
                if ($this->affiliate_management_model->update_affiliate($affiliate_id, ['group_id' => $group_id]))
                    set_alert('success', _l('updated_successfully', _l('affiliate_management_affiliate')));
            }
        }

        return redirect(AFFILIATE_MANAGEMENT_MODULE_NAME . "/view_affiliate/$affiliate_id");
    }
}
