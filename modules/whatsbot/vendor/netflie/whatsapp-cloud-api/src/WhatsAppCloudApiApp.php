<?php

namespace Netflie\WhatsAppCloudApi;

class WhatsAppCloudApiApp
{
    /**
     * @const string The name of the environment variable that contains the app from phone number ID.
     */
    public const APP_FROM_PHONE_NUMBER_ENV_NAME = 'WHATSAPP_CLOUD_API_FROM_PHONE_NUMBER';

    /**
     * @const string The name of the environment variable that contains the app access token.
     */
    public const APP_TOKEN_ENV_NAME = 'WHATSAPP_CLOUD_API_TOKEN';

    /**
     * @const string The name of the environment variable that contains the app Business ID.
     */
    public const APP_BUSINESS_ID_ENV_NAME = 'WHATSAPP_CLOUD_API_BUSINESS_ID';

    /**
     * @const string Facebook Phone Number ID.
     */
    protected string $from_phone_number_id;

    /**
     * @const string Facebook Whatsapp Access Token.
     */
    protected string $access_token;

    /**
     * @const string Whatsapp Business ID.
     */
    protected string $business_id;

    /**
     * Sends a Whatsapp text message.
     *
     * @param string The Facebook Phone Number ID.
     * @param string The Facebook Whatsapp Access Token.
     * @param string The Whatsapp Business ID.
     *
     */
    public function __construct(?string $from_phone_number_id = null, ?string $access_token = null, ?string $business_id = null)
    {
        $this->loadEnv();

        $this->from_phone_number_id = $from_phone_number_id ?: $_ENV[static::APP_FROM_PHONE_NUMBER_ENV_NAME] ?? '';
        $this->access_token = $access_token ?: $_ENV[static::APP_TOKEN_ENV_NAME] ?? '';
        $this->business_id = $business_id ?: $_ENV[static::APP_BUSINESS_ID_ENV_NAME] ?? '';

        $this->validate($this->from_phone_number_id, $this->access_token, $this->business_id);
    }

    /**
     * Returns the Facebook Whatsapp Access Token.
     *
     * @return string
     */
    public function accessToken(): string
    {
        return $this->access_token;
    }

    /**
     * Returns the Facebook Phone Number ID.
     *
     * @return string
     */
    public function fromPhoneNumberId(): string
    {
        return $this->from_phone_number_id;
    }

    /**
     * Returns the Business ID.
     *
     * @return string
     */
    public function businessId(): string
    {
        return $this->business_id;
    }

    private function validate(string $from_phone_number_id, string $access_token, string $business_id): void
    {
        // validate by function type hinting
    }

    private function loadEnv(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->safeLoad();
    }
}
