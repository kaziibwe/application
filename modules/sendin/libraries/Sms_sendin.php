<?php

use Brevo\SendTransactionalSMSRequest;
use Saloon\Exceptions\SaloonException;
use Saloon\Http\Response;

defined('BASEPATH') or exit('No direct script access allowed');

class Sms_sendin extends App_sms
{
    private string $api_key;
    protected string $sender_id;
    private string $tags;

    public function __construct()
    {
        parent::__construct();
        $this->api_key = $this->get_option('sendin', 'access_key');
        $this->sender_id = $this->get_option('sendin', 'sender_id');
        $this->tags = $this->get_option('sendin', 'tags');

        $this->add_gateway('sendin', [
            'name' => 'Brevo (formerly sendinblue)',
            'info' => '<p>You must&nbsp;<a href="https://app.brevo.com/" target="_blank" rel="noreferrer noopener">sign up for Brevo account</a>&nbsp;for using APIs. The Module require HTTP authentication using access key which are accessible from your&nbsp;<a href="https://app.brevo.com/settings/keys/api" target="_blank" rel="noreferrer noopener">API Console.</a> <br> NOTE: THIS MODULE USES API V3 KEY </p>',
            'options' => [
                [
                    'name' => 'access_key',
                    'label' => 'access key',
                ],
                [
                    'name' => 'sender_id',
                    'label' => 'sender id',
                ],
                [
                    'name' => 'tags',
                    'label' => 'Enter Tags',
                ],
            ],
        ]);
    }

    public function send($number, $message): bool
    {
        $data = [
            'sender' => empty($this->sender_id) ? get_option('companyname') : $this->sender_id,
            'recipient' => $number,
            'content' => $message,
        ];

        if (!empty($this->tags)) {
            $data['tag'] = $this->tags;
        }

        try {
            $request = new SendTransactionalSMSRequest($this->api_key, $data);
            $response = $request->send();

            $response->onError(function (Response $response) {
                if ($response->serverError()) {
                    $this->set_error('<strong>Failed to send sms (Brevo) </strong><hr> Code: ' . $response->status() . '<br> Message: ' . $response->body());
                    return false;
                }

                $error = $response->object();
                $this->set_error('<strong>Failed to send sms (Brevo) </strong><hr> Code: ' . $error->code . '<br> Message: ' . $error->message);
                return false;
            });
            log_activity('<strong>SMS sent via Brevo to </strong><hr> Phone: ' . $number . '<br> Message: ' . $message);
            return true;
        } catch (SaloonException $e) {
            $this->set_error('<strong>Someting went wrong</strong><hr>' . $e->getMessage());
            return false;
        }
    }
}
