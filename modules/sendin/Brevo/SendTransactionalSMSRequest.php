<?php

namespace Brevo;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasJsonBody;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\RequestProperties\HasHeaders;

class SendTransactionalSMSRequest extends SoloRequest implements HasBody
{
    use HasJsonBody, HasHeaders, AcceptsJson;

    protected Method $method = Method::POST;
    private string $apiKey;
    private array $payload;

    public function resolveEndpoint(): string
    {
        return 'https://api.brevo.com/v3/transactionalSMS/sms';
    }

    protected function defaultBody(): array
    {
        return [
            'type' => 'transactional',
            'unicodeEnabled' => false,
            ...$this->payload
        ];
    }

    protected function defaultHeaders(): array
    {
        return [
            'api-key' => $this->apiKey
        ];
    }

    public function __construct(string $apiKey, array $payload)
    {
        $this->apiKey = $apiKey;
        $this->payload = $payload;
    }
}
