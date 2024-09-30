<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/WalletMailTemplate.php');

/**
 * Email template class for email sent to the contact when there is update on withdraw request.
 */
class Wallet_withdraw_request_updated extends App_mail_template
{
    use WalletMailTemplate;

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
    public $slug = 'wallet_withdraw_request_updated';
}