<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/AffiliatemanagementMailTemplate.php');

/**
 * Email template class for email sent to the affiliate when there is update on payout request.
 */
class Affiliate_management_payout_updated extends App_mail_template
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
    public $slug = AffiliateManagementHelper::EMAIL_TEMPLATE_PAYOUT_UPDATED;
}
