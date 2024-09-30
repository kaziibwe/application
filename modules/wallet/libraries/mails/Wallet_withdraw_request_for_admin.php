<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/WalletMailTemplate.php');

/**
 * Email template class for email sent to the admin when there is new withdraw request.
 */
class Wallet_withdraw_request_for_admin extends App_mail_template
{
    use WalletMailTemplate;

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
    public $slug = 'wallet_withdraw_request_for_admin';
}