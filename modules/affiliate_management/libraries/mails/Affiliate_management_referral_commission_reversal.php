<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/AffiliatemanagementMailTemplate.php');

/**
 * Email template class for email sent to the affiliate when rewarded for a referral is reversed
 */
class Affiliate_management_referral_commission_reversal extends App_mail_template
{
    use AffiliatemanagementMailTemplate;

    /**
     * @inheritDoc
     */
    public $rel_type = 'contact';

    /**
     * @inheritDoc
     */
    protected $for = 'client';

    /**
     * @inheritDoc
     */
    public $slug = AffiliateManagementHelper::EMAIL_TEMPLATE_REFERRAL_COMMISSION_REVERSAL;
}
