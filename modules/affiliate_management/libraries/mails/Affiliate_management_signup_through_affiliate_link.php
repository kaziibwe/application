<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/AffiliatemanagementMailTemplate.php');

/**
 * Email template class for email sent to the affiliate when there is a new signup from his ID.
 */
class Affiliate_management_signup_through_affiliate_link extends App_mail_template
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
    public $slug = AffiliateManagementHelper::EMAIL_TEMPLATE_SIGNUP_THROUGH_AFFILIATE;
}
