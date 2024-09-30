<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Affiliate Management Helper class for managing affiliate-related functionalities.
 */
if (!class_exists('AffiliateManagementHelper')) {
    class AffiliateManagementHelper
    {
        /** Available hooks flag */
        const HOOKS_AFFILIATE_MODELS_FILTER = AFFILIATE_MANAGEMENT_MODULE_NAME . '_affiliate_model';
        const HOOKS_COMMISSION_RULES_FILTER = AFFILIATE_MANAGEMENT_MODULE_NAME . '_commission_rules';
        const HOOKS_COMMISSION_TYPES_FILTER = AFFILIATE_MANAGEMENT_MODULE_NAME . '_commission_types';
        const HOOKS_PAYOUT_METHODS_FILTER = AFFILIATE_MANAGEMENT_MODULE_NAME . '_payout_methods';

        /** Affiliate model */
        const AFFILIATE_MODEL_FIRST_CLICK = 'first-click';
        const AFFILIATE_MODEL_LAST_CLICK = 'last-click';

        /** Commission rule flags */
        const COMMISION_RULE_NO_PAYMENT = 'no-payment';
        const COMMISION_RULE_FIRST_PAYMENT = 'first-invoice-payment';
        const COMMISION_RULE_EVERY_PAYMENT = 'every-invoice-payment';

        /** Commission type flags */
        const COMMISION_TYPE_PERCENT = 'percent';
        const COMMISION_TYPE_FIXED = 'fixed';

        /** Payout method flags */
        const PAYOUT_METHOD_BANK = 'bank';
        const PAYOUT_METHOD_PAYPAL = 'paypal';

        /** Status flags */
        const STATUS_ACTIVE = 'active';
        const STATUS_INACTIVE = 'inactive';
        const STATUS_PENDING = 'pending';
        const STATUS_APPROVED = 'approved';
        const STATUS_REJECTED = 'rejected';
        const STATUS_PROCESSING = 'processing';
        const STATUS_CANCELLED = 'cancelled';
        const STATUS_REVERSED = 'rerversed';


        /** Email template flags */
        const EMAIL_TEMPLATE_PAYOUT_UPDATED = 'affiliate_management_payout_updated';
        const EMAIL_TEMPLATE_SIGNUP_THROUGH_AFFILIATE = 'affiliate_management_signup_through_affiliate_link';
        const EMAIL_TEMPLATE_SUCCESSFUL_REFERRAL_COMMISSION = 'affiliate_management_successful_referral_commission';
        const EMAIL_TEMPLATE_REFERRAL_COMMISSION_REVERSAL = 'affiliate_management_referral_commission_reversal';
        const EMAIL_TEMPLATE_PAYOUT_UPDATED_FOR_ADMIN = 'affiliate_management_payout_updated_for_admin';
        const EMAIL_TEMPLATE_NEW_PAYOUT_REQUEST_FOR_ADMIN = 'affiliate_management_new_payout_request_for_admin';
        const EMAIL_TEMPLATE_NEW_AFFILIATE_SIGNUP_FOR_ADMIN = 'affiliate_management_new_affiliate_signup_for_admin';

        const URL_IDENTIFIER = 'afm';
        const DEFAULT_GROUP_ID = 'general';

        /**
         * Get affiliate groups/programs
         *
         * @return array
         */
        public static function get_affiliate_groups()
        {
            $groups = get_option(AFFILIATE_MANAGEMENT_MODULE_NAME . '_groups');

            // @since 1.0.4
            if (empty($groups)) {
                $groups = [];

                $defaultGroupSettings = [];
                $defaultSeedSettings = [
                    'affiliate_management_commission_enabled' => '1',
                    'affiliate_management_commission_rule' => AffiliateManagementHelper::COMMISION_RULE_FIRST_PAYMENT,
                    'affiliate_management_commission_type' => AffiliateManagementHelper::COMMISION_TYPE_PERCENT,
                    'affiliate_management_commission_amount' => '15',
                    'affiliate_management_commission_cap' => '-1',
                    'affiliate_management_payout_min' => '50',
                    'affiliate_management_payout_methods' => implode(', ', [AffiliateManagementHelper::PAYOUT_METHOD_BANK, AffiliateManagementHelper::PAYOUT_METHOD_PAYPAL]),
                ];

                foreach ($defaultSeedSettings as $key => $defaultValue) {
                    $value = get_option($key);
                    if ($key === 'affiliate_management_payout_methods' && stripos($value, '[') !== false && stripos($value, ']')) {
                        $value = json_decode($value);
                        foreach ($value as $i => $v) {
                            $value[$i] = ucfirst(trim($v));
                        }
                        $value = implode(", ", $value);
                    }
                    $defaultGroupSettings[$key] = empty($value) ? $defaultValue : $value;
                }

                $groups[self::DEFAULT_GROUP_ID] = ['name' => 'General', 'settings' => $defaultGroupSettings];

                if (self::update_affiliate_groups($groups)) {
                    foreach ($defaultSeedSettings as $key => $value) {
                        delete_option($key);
                    }
                }
            } else {
                $groups = json_decode($groups, true);
            }

            return $groups;
        }

        /**
         * Update the groups with new groups
         *
         * @param array $groups
         * @return bool
         */
        public static function update_affiliate_groups(array $groups)
        {
            $groups = json_encode($groups);
            return update_option(AFFILIATE_MANAGEMENT_MODULE_NAME . '_groups', $groups);
        }

        /**
         * Retrieve the value of a specific option for an affiliate or fallback to the global option.
         *
         * @param string $key       The option key.
         * @param object $affiliate Optional. The affiliate object. Default is null.
         *
         * @return mixed|null The value of the specified option for the affiliate, or the global option if not found.
         */
        public static function get_option($key, $affiliate = null)
        {
            // Get the affiliate groups
            $groups = self::get_affiliate_groups();

            // Check if affiliate is specified and has a valid group
            if (!empty($affiliate->group_id) && isset($groups[$affiliate->group_id])) {
                // Get affiliate group settings
                $groupSettings = $groups[$affiliate->group_id]['settings'];

                // Check if the specified option exists in the affiliate group settings
                if (isset($groupSettings[$key])) {
                    return $groupSettings[$key];
                }
            }

            // Check if the option exists in the default group settings
            if (isset($groups[self::DEFAULT_GROUP_ID]['settings'][$key])) {
                return $groups[self::DEFAULT_GROUP_ID]['settings'][$key];
            }

            // If no specific affiliate setting or default setting is found, fall back to the global option
            return get_option($key);
        }

        /**
         * Get affiliate models
         *
         * @return array Affiliate models
         */
        public static function get_affiliate_models()
        {
            $options = [
                [
                    'key' => self::AFFILIATE_MODEL_FIRST_CLICK,
                    'label' => _l('affiliate_management_model_' . self::AFFILIATE_MODEL_FIRST_CLICK),
                ],
                [
                    'key' => self::AFFILIATE_MODEL_LAST_CLICK,
                    'label' => _l('affiliate_management_model_' . self::AFFILIATE_MODEL_LAST_CLICK),
                ]
            ];

            // Apply hooks to filter commission rules options
            $options = hooks()->apply_filters(self::HOOKS_AFFILIATE_MODELS_FILTER, $options);
            return $options;
        }

        /**
         * Get the affiliate model
         *
         * @return string
         */
        public static function get_affiliate_model()
        {
            $option = AffiliateManagementHelper::get_option('affiliate_management_affiliate_model');
            if (empty($option))
                $option = self::AFFILIATE_MODEL_FIRST_CLICK;
            return $option;
        }

        /**
         * Retrieves available commission rules with their labels.
         * Uses hooks to filter the options if applicable.
         * @return array Commission rules with keys and labels
         */
        public static function get_commission_rules()
        {
            // Define available commission rules
            $options = [
                [
                    'key' => self::COMMISION_RULE_NO_PAYMENT,
                    'label' => _l('affiliate_management_' . self::COMMISION_RULE_NO_PAYMENT),
                ],
                [
                    'key' => self::COMMISION_RULE_FIRST_PAYMENT,
                    'label' => _l('affiliate_management_' . self::COMMISION_RULE_FIRST_PAYMENT),
                ],
                [
                    'key' => self::COMMISION_RULE_EVERY_PAYMENT,
                    'label' => _l('affiliate_management_' . self::COMMISION_RULE_EVERY_PAYMENT),
                ],
            ];

            // Apply hooks to filter commission rules options
            $options = hooks()->apply_filters(self::HOOKS_COMMISSION_RULES_FILTER, $options);
            return $options;
        }

        /**
         * Retrieves the current commission rule set in the options.
         * @param object $affiliate
         * 
         * @return string Current commission rule
         */
        public static function get_commission_rule($affiliate)
        {
            return AffiliateManagementHelper::get_option('affiliate_management_commission_rule', $affiliate);
        }

        /**
         * Retrieves available commission types with their labels.
         * Uses hooks to filter the options if applicable.
         * @return array Commission types with keys and labels
         */
        public static function get_commission_types()
        {
            // Define available commission types
            $options = [
                [
                    'key' => self::COMMISION_TYPE_FIXED,
                    'label' => _l('affiliate_management_' . self::COMMISION_TYPE_FIXED, get_base_currency()->symbol),
                ],
                [
                    'key' => self::COMMISION_TYPE_PERCENT,
                    'label' => _l('affiliate_management_' . self::COMMISION_TYPE_PERCENT),
                ],
            ];

            // Apply hooks to filter commission types options
            $options = hooks()->apply_filters(self::HOOKS_COMMISSION_TYPES_FILTER, $options);
            return $options;
        }

        /**
         * Check if commission is enabled or not
         *
         * @param object $affiliate
         * 
         * @return bool true if commission enabled otherwise false
         */
        public static function commission_enabled($affiliate)
        {
            return AffiliateManagementHelper::get_option('affiliate_management_commission_enabled', $affiliate) == '1';
        }

        /**
         * Retrieves allowed payout methods based on configured options.
         * Fetches the allowed payout methods based on the configured options.
         * @param object $affiliate
         * @return array An array containing allowed payout methods
         */
        public static function get_allowed_payout_methods($affiliate)
        {
            $options = AffiliateManagementHelper::get_option('affiliate_management_payout_methods', $affiliate);
            if (empty($options)) return [];

            $allowed_options  = [];

            $options = (array)explode(',', $options);
            foreach ($options as $option) {
                $option = trim($option);
                if (!empty($option))
                    $allowed_options[] = ['key' => $option, 'label' => $option];
            }

            $allowed_options = hooks()->apply_filters(self::HOOKS_PAYOUT_METHODS_FILTER, $allowed_options);

            return (array)$allowed_options;
        }

        /**
         * Retrieves the minimum payout amount.
         * Obtains the minimum payout amount configured for the affiliate program.
         * @param object $affiliate
         * @return float The minimum payout amount
         */
        public static function get_payout_min($affiliate)
        {
            return (float)AffiliateManagementHelper::get_option('affiliate_management_payout_min', $affiliate);
        }

        /**
         * Retrieves available email templates for notifications.
         * Returns an array of available email templates for notifications.
         * @return array An array containing email template identifiers
         */
        public static function get_email_templates()
        {
            return [
                self::EMAIL_TEMPLATE_PAYOUT_UPDATED,
                self::EMAIL_TEMPLATE_SIGNUP_THROUGH_AFFILIATE,
                self::EMAIL_TEMPLATE_SUCCESSFUL_REFERRAL_COMMISSION,
                self::EMAIL_TEMPLATE_REFERRAL_COMMISSION_REVERSAL,
                self::EMAIL_TEMPLATE_PAYOUT_UPDATED_FOR_ADMIN,
                self::EMAIL_TEMPLATE_NEW_PAYOUT_REQUEST_FOR_ADMIN,
                self::EMAIL_TEMPLATE_NEW_AFFILIATE_SIGNUP_FOR_ADMIN
            ];
        }

        /**
         * Sends notification email based on the provided template and data.
         * Notifies via email using a specified template, email address, client ID, contact ID, and additional data.
         * @param string $template The email template identifier
         * @param string $email The recipient's email address
         * @param int $client_id The client's ID
         * @param int $contact_id The contact's ID
         * @param array $data Additional data for the email
         * @return mixed
         */
        public static function notify($template, $email, $client_id, $contact_id, $data)
        {
            return send_mail_template($template, AFFILIATE_MANAGEMENT_MODULE_NAME, $email, $client_id, $contact_id, $data);
        }

        /**
         * Retrieves the admin contact.
         * Loads the staff model and fetches the first active admin contact.
         * @return mixed Returns the admin contact if found; otherwise, returns null
         */
        public static function get_admin_contact()
        {
            $CI = &get_instance();
            $CI->load->model('staff_model');
            return $CI->staff_model->db
                ->where('admin', '1')
                ->where('active', '1')
                ->order_by('staffid', 'ASC')
                ->limit(1)
                ->get(db_prefix() . 'staff')
                ->row();
        }

        /**
         * Retrieves table columns based on the provided table name and view mode.
         * @param string $table The name of the table
         * @param bool $client_view Indicates if the client view is enabled or not
         * @return array The columns of the specified table
         */
        public static function get_table_columns($table, $client_view = false)
        {
            $columns = [];

            switch ($table) {
                case 'referrals':
                    $allowed_referral_client_info = AffiliateManagementHelper::get_option('affiliate_management_save_referral_client_info') == '1';
                    $show_commissions = (int)AffiliateManagementHelper::get_option('affiliate_management_show_commission_info_on_referral_table');

                    $columns = [
                        _l('id'),
                        _l('affiliate_management_affiliate'),
                        _l('affiliate_management_company'),
                    ];

                    if ($client_view) {
                        unset($columns[1]);
                    }

                    if ($show_commissions) {
                        $columns[] = _l('affiliate_management_commissions');
                    }

                    if ($allowed_referral_client_info && !is_client_logged_in()) {
                        $columns[] = _l('affiliate_management_ip');
                        $columns[] = _l('affiliate_management_ua');
                    }

                    $columns[] = _l('date_created');
                    break;

                case 'commissions':
                    $columns = [
                        _l('affiliate_management_affiliate'),
                        _l('affiliate_management_company'),
                        _l('affiliate_management_commission_amount'),
                        _l('affiliate_management_commission_rule'),
                        _l('date_created')
                    ];

                    if ($client_view) {
                        unset($columns[0]);
                    }
                    break;

                case 'payouts':
                    $columns = [
                        _l('id'),
                        _l('affiliate_management_affiliate'),
                        _l('affiliate_management_balance'),
                        _l('affiliate_management_amount_requested'),
                        _l('affiliate_management_payout_methods'),
                        _l('affiliate_management_note_for_admin'),
                        _l('affiliate_management_status'),
                        _l('date_created'),
                    ];

                    if ($client_view) {
                        unset($columns[1]);
                    }
                    break;

                default:
                    // Handle default case if necessary
                    break;
            }

            return array_values($columns);
        }

        /**
         * Generates the default content for the affiliate page.
         * Constructs the HTML content for the affiliate join page with placeholders.
         * @return array An array containing the content and placeholder tags
         */
        public static function default_affiliate_page($affiliate = null)
        {
            // Content with HTML structure and placeholders
            $affiliate_join_page_content = '
                    <div class="jumbotron text-center">
                        <h1>Welcome to Our Affiliate Program!</h1>
                        <p>Join us and start earning today.</p>
                        <a href="{SIGNUP_LINK}" class="btn btn-primary btn-lg">Join Now</a>
                    </div>
                    <div id="referral" class="row">
                        <div class="col-md-6">
                            <h2>Referral Program</h2>
                            <p>Join our referral program and start earning rewards!</p>
                            <p>Earn {COMMISSION_AMOUNT} on every referral\'s first payment.</p>
                            <p>Minimum payout is {MIN_PAYOUT}.</p>
                        </div>
                        <div class="col-md-6">
                            <h2>How It Works</h2>
                            <p>1. Register for the affiliate program.</p>
                            <p>2. Share your referral link with friends and colleagues.</p>
                            <p>3. When someone signs up using your link and makes their first payment, you earn {COMMISSION_AMOUNT}.</p>
                            <p>4. Once your earnings reach {MIN_PAYOUT}, you can request a payout.</p>
                        </div>
                    </div>
                ';

            // Obtain necessary configuration options and format them
            $type = AffiliateManagementHelper::get_option('affiliate_management_commission_type', $affiliate);
            $currency = get_base_currency();
            $commission = AffiliateManagementHelper::get_option('affiliate_management_commission_amount', $affiliate);
            $commission = $type === self::COMMISION_TYPE_FIXED ? app_format_money($commission, $currency) : $commission . '%';
            $payout_methods = explode(",", AffiliateManagementHelper::get_option('affiliate_management_payout_methods', $affiliate));
            array_walk($payout_methods, 'trim');

            // Define placeholders for the affiliate join page content
            $affiliate_join_page_content_tags = [
                '{COMMISSION_AMOUNT}' => $commission,
                '{COMMISSION_TYPE}' => $type,
                '{COMMISSION_CAP}' => AffiliateManagementHelper::get_option('affiliate_management_commission_cap', $affiliate),
                '{MIN_PAYOUT}' => app_format_money(AffiliateManagementHelper::get_option('affiliate_management_payout_min', $affiliate), $currency),
                '{PAYOUT_METHODS}' => implode(", ", $payout_methods),
                '{SIGNUP_LINK}' => base_url('clients/' . AFFILIATE_MANAGEMENT_MODULE_NAME . '/signup'),
            ];

            return ['content' => $affiliate_join_page_content, 'tags' => $affiliate_join_page_content_tags];
        }


        /**
         * Retrieves the content for the affiliate page.
         * Replaces predefined tags in the content with corresponding values.
         * @param object $affiliate
         * @return string Affiliate page content
         */
        public static function get_affiliate_page_content($affiliate)
        {
            // Get predefined tags and affiliate page content
            $tags = self::default_affiliate_page($affiliate)['tags'];
            $content = AffiliateManagementHelper::get_option('affiliate_management_join_page_content', $affiliate);

            // Replace tags in the content with actual values
            $content = str_replace(
                array_keys($tags),
                array_values($tags),
                $content
            );

            return $content;
        }

        /**
         * Sets a referral cookie for the affiliate.
         * @param string $affiliate_slug Affiliate's unique identifier
         * @return void
         */
        public static function set_referral_cookie($affiliate_slug)
        {
            $CI = get_instance();

            if (!function_exists('set_cookie'))
                $CI->load->helper('cookie');

            // Check if already referred by an active affiliate
            $existing_slug = self::get_referral_cookie();
            if (!empty($existing_slug) && self::get_affiliate_model() === self::AFFILIATE_MODEL_FIRST_CLICK) {
                $affiliate = $CI->affiliate_management_model->get_affiliate_by_slug($existing_slug);
                if (
                    isset($affiliate->status) &&
                    $affiliate->status === AffiliateManagementHelper::STATUS_ACTIVE
                ) return;
            }

            $affiliate = $CI->affiliate_management_model->get_affiliate_by_slug($affiliate_slug);
            if (!$affiliate) return show_404();
            if ($affiliate->status !== AffiliateManagementHelper::STATUS_ACTIVE) return show_404();


            // Define duration for the cookie (3 months)
            $duration = 60 * 60 * 24 * 31 * 3;

            // Set the cookie with the affiliate's slug and defined duration
            set_cookie(AFFILIATE_MANAGEMENT_MODULE_NAME, $affiliate_slug, $duration);
        }

        /** 
         * Get referral affiliate slug from cookei
         * @return  mixed
         */
        public static function get_referral_cookie()
        {
            if (!function_exists('get_cookie'))
                get_instance()->load->helper('cookie');

            $affiliate_referral_slug = get_cookie(AFFILIATE_MANAGEMENT_MODULE_NAME, true);
            return $affiliate_referral_slug;
        }


        /**
         * Hooks into payment addition process and rewards affiliates accordingly.
         * @param int $paymentid Payment ID
         * @return void
         */
        public static function after_payment_added_hook($paymentid)
        {
            // Get payment details
            $CI = &get_instance();
            $CI->load->model('payments_model');
            $payment = $CI->payments_model->get($paymentid);
            $invoice = $CI->invoices_model->get($payment->invoiceid);
            $clientid = $invoice->clientid;
            $referral = $CI->affiliate_management_model->get_referral_by_client_id($clientid);

            if (empty($referral)) return;

            // Get affiliate and validate
            $affiliate = $CI->affiliate_management_model->get_affiliate($referral->affiliate_id);
            if (!$affiliate) return;

            if ($affiliate->status !== self::STATUS_ACTIVE) return false;

            $rule = self::get_commission_rule($affiliate);
            if ($rule === self::COMMISION_RULE_NO_PAYMENT) return;

            try {
                $CI->affiliate_management_model->db->trans_begin();

                // Check if only first payment and payment rewarded
                if ($rule === self::COMMISION_RULE_FIRST_PAYMENT) {
                    $commissions = $CI->affiliate_management_model->get_all_commissions([
                        'affiliate_id' => $affiliate->affiliate_id,
                        'client_id' => $clientid,
                    ]);

                    // Rewared so return
                    if (!empty($commissions)) return;
                }

                // Reward affiliate
                self::reward_affiliate($affiliate, $referral, $payment);

                $CI->affiliate_management_model->db->trans_commit();
            } catch (\Throwable $th) {

                $CI->affiliate_management_model->db->trans_rollback();
                log_message('error', $th->getMessage());
            }
        }

        /**
         * Hooks into payment removal process and reverse affiliates rewards accordingly.
         * @param array $data Array containing paymentid and invoiceid of the deleted payment.
         * @return void
         */
        public static function after_payment_deleted_hook($data)
        {
            $payment = (object)$data;
            self::reverse_affiliate_reward_for_payment($payment);
        }

        public static function after_client_register_hook($client_id)
        {
            self::referral_signup($client_id);
        }

        public static function after_client_contact_verification_hook($contact)
        {
            self::referral_signup($contact->userid);
        }

        /**
         * Add referral for an affiliate
         * @param int $clientid Client ID
         * @param string $affiliate_referral_slug Option . Get from cookie if not provided
         * @return void
         */
        public static function referral_signup($clientid, $affiliate_referral_slug = '')
        {

            $CI = &get_instance();

            if (empty($affiliate_referral_slug))
                $affiliate_referral_slug = self::get_referral_cookie();

            // Get referral is existing and determine old referral affiliate slug
            $referral = $CI->affiliate_management_model->get_referral_by_client_id($clientid);
            if (!empty($referral->affiliate_slug)) {
                $affiliate_referral_slug = $referral->affiliate_slug;
            }

            if (!empty($affiliate_referral_slug)) {

                try {
                    $CI->affiliate_management_model->db->trans_begin();

                    $affiliate = $CI->affiliate_management_model->get_affiliate_by_slug($affiliate_referral_slug);

                    // Add referral log if not already logged
                    if (empty($referral)) {

                        $ua = '';
                        $ip = '';

                        if (AffiliateManagementHelper::get_option('affiliate_management_save_referral_client_info', $affiliate) == '1') {
                            $CI->load->library('user_agent');
                            $ua = $CI->agent->agent_string();
                            $ip = $CI->input->ip_address();
                        }

                        $referral_id = $CI->affiliate_management_model->add_referral([
                            'affiliate_id' => $affiliate->affiliate_id,
                            'client_id' => $clientid,
                            'ua' => $ua,
                            'ip' => $ip,
                        ]);
                        $referral = $CI->affiliate_management_model->get_referral($referral_id);
                    }

                    // Check email verification
                    if (is_email_verification_enabled()) {
                        $contact_id = get_primary_contact_user_id($clientid);

                        // Return till the method is called by verification hook
                        if (!is_contact_email_verified($contact_id)) {
                            $CI->affiliate_management_model->db->trans_commit();
                            return;
                        }
                    }

                    // Check for reward if neccessary
                    $rule = self::get_commission_rule($affiliate);
                    if ($rule === self::COMMISION_RULE_NO_PAYMENT) {

                        self::reward_affiliate($affiliate, $referral);
                    }

                    $CI->affiliate_management_model->db->trans_commit();

                    // Send notification to affiliate about new referal
                    self::notify(
                        AffiliateManagementHelper::EMAIL_TEMPLATE_SIGNUP_THROUGH_AFFILIATE,
                        $affiliate->email,
                        $affiliate->userid,
                        $affiliate->contact_id,
                        [
                            'affiliate' => $affiliate,
                            'referral' => $referral,
                        ]
                    );
                } catch (\Throwable $th) {

                    $CI->affiliate_management_model->db->trans_rollback();
                    log_message('error', $th->getMessage());
                }
            }
        }

        /**
         * Rewards the affiliate for a successful referral.
         * @param object $affiliate Affiliate object
         * @param object $referral Referral object
         * @param object|null $payment Payment object (optional)
         * @return mixed Commission details or false if commission is not enabled
         */
        public static function reward_affiliate($affiliate, $referral, object $payment = null)
        {

            $commission_enabled = self::commission_enabled($affiliate);
            if (!$commission_enabled) return false;

            if ($affiliate->status !== self::STATUS_ACTIVE) {
                log_message('info', "Affiliate $affiliate->affiliate_slug not active, denying reward");
                return false;
            }

            $CI = &get_instance();
            $commission_rule = self::get_commission_rule($affiliate);
            $commission_type = AffiliateManagementHelper::get_option('affiliate_management_commission_type', $affiliate);
            $commission_amount = (float)AffiliateManagementHelper::get_option('affiliate_management_commission_amount', $affiliate);

            $amount = $commission_amount;

            // Calculate percentage equiv
            if (isset($payment->amount) && $commission_type === self::COMMISION_TYPE_PERCENT) {
                $amount = ($amount / 100) * (float)$payment->amount;
            }


            // Check if the cap is enabled and the earnings exceed the cap
            $earnings = $affiliate->total_earnings;
            $cap = (float)AffiliateManagementHelper::get_option('affiliate_management_commission_cap', $affiliate);
            if ($cap >= 0) { // Capping enabled

                // Check if cap reached
                if ($earnings >= $cap) {
                    return false; // Exceeded or reached the cap, do not add more earnings
                }

                // Calculate potential earnings after adding the new amount
                $_earnings = $earnings + $amount;

                // Check if the potential earnings would exceed the cap
                if ($_earnings > $cap) {
                    // Calculate the amount that would exceed the cap
                    $exceeding_amount = $_earnings - $cap;

                    // Adjust the amount to be added to stay within the cap
                    $amount = $amount - $exceeding_amount;
                }
            }


            // Reward commission
            $data = [
                'amount' => $amount,
                'affiliate_id' => $affiliate->affiliate_id,
                'referral_id'  => $referral->referral_id,
                'client_id' => $referral->client_id,
                'rule_info' => $commission_type === self::COMMISION_TYPE_PERCENT ? $commission_amount . '%' : '',
            ];

            // Ensure not yet rewarded
            if ($commission_rule === self::COMMISION_RULE_NO_PAYMENT) {
                $_commissions = $CI->affiliate_management_model->get_all_commissions([
                    'affiliate_id' => $affiliate->affiliate_id,
                    'referral_id'  => $referral->referral_id,
                    'client_id' => $referral->client_id,
                ]);
                if (!empty($_commissions)) return false;
            }

            // Require payment except for no payment rule 
            if (!$payment && $commission_rule !== self::COMMISION_RULE_NO_PAYMENT) {
                throw new \Exception("Payment object is required for payment commission rules", 1);
            }

            if ($payment) {
                $data['payment_id'] = $payment->paymentid;
                $data['invoice_id'] = $payment->invoiceid;

                // Ensure payment not yet rewarded for
                $_commissions = $CI->affiliate_management_model->get_all_commissions([
                    'payment_id' => $payment->paymentid, 'invoice_id' => $payment->invoiceid
                ]);
                if (!empty($_commissions)) return false;
            }

            $commission_id = $CI->affiliate_management_model->add_commission($data);

            $commission = $CI->affiliate_management_model->get_commission($commission_id);

            $new_balance = (float)$affiliate->balance + $amount;
            $new_total_earnings = (float)$affiliate->total_earnings + $amount;

            $CI->affiliate_management_model->update_affiliate(
                $affiliate->affiliate_id,
                [
                    'balance' => $new_balance,
                    'total_earnings' => $new_total_earnings,
                ]
            );

            try {

                $affiliate = $CI->affiliate_management_model->get_affiliate($affiliate->affiliate_id);

                // Send notification to affiliate about new reward
                self::notify(
                    AffiliateManagementHelper::EMAIL_TEMPLATE_SUCCESSFUL_REFERRAL_COMMISSION,
                    $affiliate->email,
                    $affiliate->userid,
                    $affiliate->contact_id,
                    [
                        'affiliate' => $affiliate,
                        'referral' => $referral,
                        'commission' => $commission,
                        'payment' => $payment,
                    ]
                );
            } catch (\Throwable $th) {
                log_message('error', $th->getMessage());
            }

            return $commission;
        }

        /**
         * Revert affiliate reward for a payment
         * @param object|null $payment Object containing paymentid and invoiceid property
         * @return mixed Commission details or false if commission is not enabled
         */
        public static function reverse_affiliate_reward_for_payment(object $payment)
        {

            if (empty($payment->paymentid) || empty($payment->invoiceid)) return false;

            $CI = &get_instance();

            // Ensure payment was rewarded
            $_commissions = $CI->affiliate_management_model->get_all_commissions([
                'payment_id' => $payment->paymentid, 'invoice_id' => $payment->invoiceid
            ]);
            if (empty($_commissions)) return;

            foreach ($_commissions as $commission) {

                $affiliate_id = $commission->affiliate_id;
                $referral_id = $commission->referral_id;
                $amount = (float)$commission->amount;
                $payment->amount = $amount;

                if ($amount <= 0) continue;

                $affiliate = $CI->affiliate_management_model->get_affiliate($affiliate_id);
                $referral = $CI->affiliate_management_model->get_referral($referral_id);

                $new_balance = (float)$affiliate->balance - $amount;
                $new_total_earnings = (float)$affiliate->total_earnings - $amount;

                $CI->affiliate_management_model->update_affiliate(
                    $affiliate->affiliate_id,
                    [
                        'balance' => $new_balance,
                        'total_earnings' => $new_total_earnings,
                    ]
                );

                try {
                    // Send notification to affiliate about reward reversal
                    self::notify(
                        AffiliateManagementHelper::EMAIL_TEMPLATE_REFERRAL_COMMISSION_REVERSAL,
                        $affiliate->email,
                        $affiliate->userid,
                        $affiliate->contact_id,
                        [
                            'affiliate' => $affiliate,
                            'referral' => $referral,
                            'commission' => $commission,
                            'payment' => $payment,
                        ]
                    );
                } catch (\Throwable $th) {
                    log_message('error', $th->getMessage());
                }
                log_activity('Reversal of affiliate commission [Amount: ' . $amount . ', PaymentId: ' . $payment->paymentid, ', CommissionID: ' . $commission->commission_id . ', AffiliateID: ' . $affiliate->affiliate_id . ']');
                $CI->affiliate_management_model->update_commission($commission->commission_id, ['status' => self::STATUS_REVERSED]);
            }

            return true;
        }
    }
}
