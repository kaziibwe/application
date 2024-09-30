<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Webhooks_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
			[
                'name'      => 'Webhook response code',
                'key'       => '{webhook_response_code}',
                'available' => [
                    'webhooks',
                ]
            ]
        ];
    }

    public function format($response_code)
    {
        $fields = [];

        $fields['{webhook_response_code}'] = $response_code;

        return $fields;
    }
}
