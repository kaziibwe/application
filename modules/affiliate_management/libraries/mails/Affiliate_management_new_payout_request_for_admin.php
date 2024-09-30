<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/AffiliatemanagementMailTemplate.php');

/**
 * Email template class for email sent to the admin when there is new payout request.
 */
class Affiliate_management_new_payout_request_for_admin extends App_mail_template
{
    use AffiliatemanagementMailTemplate;

    /**
     * @inheritDoc
     */
    public $rel_type = 'contact';

    /**
     * @inheritDoc
     */
    protected $for = 'staff';

    /**
     * @inheritDoc
     */
    public $slug = AffiliateManagementHelper::EMAIL_TEMPLATE_NEW_PAYOUT_REQUEST_FOR_ADMIN;
}
