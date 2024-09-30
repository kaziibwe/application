<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Affiliate_management_client extends ClientsController
{
    public $redirect_url;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('string');

        $this->redirect_url = base_url('clients/' . AFFILIATE_MANAGEMENT_MODULE_NAME . '/profile');
    }

    /**
     * Register on the system using an affiliate link
     *
     * @param string $affiliate_slug
     * @return void
     */
    public function register($affiliate_slug)
    {
        AffiliateManagementHelper::set_referral_cookie($affiliate_slug);

        return redirect(base_url('register?' . AffiliateManagementHelper::URL_IDENTIFIER . '=' . $affiliate_slug));
    }

    /**
     * Display join page
     *
     * @return void
     */
    public function join($affiliate_id = '')
    {
        $data = ['affiliate' => null];

        if (is_client_logged_in()) {
            $contact_id = get_contact_user_id();
            $data['affiliate'] = $this->affiliate_management_model->get_affiliate_by_contact_id($contact_id);
        }

        if (is_admin() && !empty($affiliate_id)) {
            $data['affiliate'] = $this->affiliate_management_model->get_affiliate($affiliate_id);
        }

        $this->data($data);
        $this->view('join');
        $this->layout();
        return;
    }

    /**
     * Signup client as affiliate
     *
     * @return void
     */
    public function signup()
    {
        $this->check_authorization();

        $contact_id = get_contact_user_id();
        $affiliate = $this->affiliate_management_model->get_affiliate_by_contact_id($contact_id);

        if (!empty($affiliate->affiliate_slug)) return redirect($this->redirect_url);

        if ($this->affiliate_management_model->add_affiliate([
            'contact_id' => $contact_id,
            'status' => AffiliateManagementHelper::get_option('affiliate_management_auto_approve_signup') == '1' ? AffiliateManagementHelper::STATUS_ACTIVE : AffiliateManagementHelper::STATUS_PENDING,
        ])) {

            $affiliate = $this->affiliate_management_model->get_affiliate_by_contact_id($contact_id);

            // Send email notification to admin
            $contact = $this->clients_model->get_contact($contact_id);
            $affiliate->name = $contact->firstname . ' ' . $contact->lastname;
            $admin = AffiliateManagementHelper::get_admin_contact();
            AffiliateManagementHelper::notify(
                AffiliateManagementHelper::EMAIL_TEMPLATE_NEW_AFFILIATE_SIGNUP_FOR_ADMIN,
                $admin->email,
                $contact->userid,
                $contact->id,
                ['affiliate' => $affiliate]
            );
        }

        return redirect($this->redirect_url);
    }

    /**
     * Show affiliate profile and dashboard
     *
     * @return void
     */
    public function profile()
    {
        $this->check_authorization();

        $affiliate = $this->affiliate_management_model->get_affiliate_by_contact_id(get_contact_user_id());
        if (empty($affiliate->affiliate_slug)) {

            return redirect(AFFILIATE_MANAGEMENT_MODULE_NAME . '/join');
        }

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

        $this->data($data);
        $this->view('admin/affiliates/view');
        $this->layout();
    }

    /**
     * Handle changing of affiliate slug
     *
     * @return void
     */
    public function update_affiliate_slug()
    {
        $this->check_authorization();

        $slug = slug_it($this->input->post('affiliate_slug', true));

        $status = 'danger';
        $message = _l('affiliate_management_error_updating', _l('affiliate_management_slug'));

        $affiliate = $this->affiliate_management_model->get_affiliate_by_contact_id(get_contact_user_id());
        if ($this->affiliate_management_model->update_affiliate($affiliate->affiliate_id, ['affiliate_slug' => $slug])) {
            $status = 'success';
            $message =  _l('updated_successfully', _l('affiliate_management_slug'));
            $slug = $this->affiliate_management_model->get_affiliate($affiliate->affiliate_id)->affiliate_slug;
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => $status, 'message' => $message, 'slug' => $slug]);
            exit;
        }

        set_alert($status, $message);

        return redirect($this->redirect_url);
    }

    /**
     * Method to handle making payout request
     *
     * @return void
     */
    public function payout()
    {
        $this->check_authorization();

        $affiliate = $this->affiliate_management_model->get_affiliate_by_contact_id(get_contact_user_id());

        if (!$affiliate) {
            return show_404();
        }

        $pending_payouts = $this->affiliate_management_model->get_all_payouts([
            'affiliate_id' => $affiliate->affiliate_id,
            'status' => AffiliateManagementHelper::STATUS_PENDING
        ]);

        $processing_payouts = $this->affiliate_management_model->get_all_payouts([
            'affiliate_id' => $affiliate->affiliate_id,
            'status' => AffiliateManagementHelper::STATUS_PROCESSING
        ]);

        if (!empty($pending_payouts) || !empty($processing_payouts)) {
            set_alert('danger', _l('affiliate_management_pending_payout_exist'));
            return redirect($this->redirect_url);
        }

        // Return the table data for ajax request
        if ($this->input->post()) {

            $amount = (float)$this->input->post('amount', true);

            $data = [
                'amount' => $amount,
                'note_for_admin' => $this->input->post('note_for_admin', true),
                'payout_method' => $this->input->post('payout_method', true),
                'affiliate_id' => $affiliate->affiliate_id,
                'status' => AffiliateManagementHelper::STATUS_PENDING
            ];

            $min_payout = (float)AffiliateManagementHelper::get_option('affiliate_management_payout_min', $affiliate);

            // Process only when amount is greater than zero
            if ($amount > 0) {

                if ((float)$affiliate->balance < $amount) {
                    set_alert('danger', _l('affiliate_management_insufficient_balance'));
                } elseif ($amount < $min_payout) {
                    set_alert('danger', _l('affiliate_management_payout_invalid_amount', app_format_money($min_payout, get_base_currency())));
                } elseif (($payout_id = $this->affiliate_management_model->add_payout($data))) {

                    $payout = $this->affiliate_management_model->get_payout($payout_id);

                    $admin = AffiliateManagementHelper::get_admin_contact();
                    $contact = $this->clients_model->get_contact($affiliate->contact_id);
                    $affiliate->name = $contact->firstname . ' ' . $contact->lastname;
                    AffiliateManagementHelper::notify(
                        AffiliateManagementHelper::EMAIL_TEMPLATE_NEW_PAYOUT_REQUEST_FOR_ADMIN,
                        $admin->email,
                        $contact->userid,
                        $contact->id,
                        ['affiliate' => $affiliate, 'payout' => $payout]
                    );

                    set_alert('success', _l('added_successfully', _l('affiliate_management_payout')));
                }
            }
        }
        return redirect($this->redirect_url);
    }

    /**
     * Method to handle cancling of a payout
     *
     * @param mixed $payout_id
     * @return void
     */
    public function cancel_payout($payout_id)
    {
        $this->check_authorization();

        $payout = $this->affiliate_management_model->get_payout((int)$payout_id);
        if ($payout && $payout->status === AffiliateManagementHelper::STATUS_PENDING) {

            if ($this->affiliate_management_model->update_payout($payout_id, ['status' => AffiliateManagementHelper::STATUS_CANCELLED])) {

                $affiliate = $this->affiliate_management_model->get_affiliate($payout->affiliate_id);

                $admin = AffiliateManagementHelper::get_admin_contact();
                $contact = $this->clients_model->get_contact($affiliate->contact_id);
                $affiliate->name = $contact->firstname . ' ' . $contact->lastname;
                AffiliateManagementHelper::notify(
                    AffiliateManagementHelper::EMAIL_TEMPLATE_PAYOUT_UPDATED_FOR_ADMIN,
                    $admin->email,
                    $contact->userid,
                    $contact->id,
                    ['affiliate' => $affiliate, 'payout' => $payout]
                );

                set_alert('success', _l('updated_successfully', _l('affiliate_management_payout')));
            }
        }

        return redirect($this->redirect_url);
    }

    /**
     * Method to show a transaction invoice from invoice id.
     * This is needed as the core invoice controller requires hash.
     * We are saving complexity in transaction list by using this method.
     *
     * @param int $invoice_id
     * @return void
     */
    public function invoice($invoice_id)
    {
        $invoice = $this->invoices_model->get($invoice_id);
        $url = base_url('invoice/' . $invoice_id . '/' . $invoice->hash);
        return redirect($url);
    }

    /**
     * Method to check if a client is logged. It redirect to login page if not authed.
     *
     * @return void
     */
    private function check_authorization()
    {
        // Authorize
        if (!is_client_logged_in()) {
            redirect_after_login_to_current_url();
            return redirect(base_url('login'));
        }
    }
}