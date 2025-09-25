<?php

namespace Cronixweb\Streamline;

use Cronixweb\Streamline\Utils\PropertiesClient;
use Cronixweb\Streamline\Utils\StreamlineClient;

class Streamline
{

    private StreamlineClient $client;

    private function __construct(string $apikey,string $apiSecret)
    {
        $this->client = new StreamlineClient($apikey, $apiSecret);
    }

    public static function api(string $apikey, string $apiSecret)
    {
        return new self($apikey, $apiSecret);
    }

    /**
     * Refresh the token
     */
    public function refreshToken()
    {
        $response = $this->client->request('RenewExpiredToken');

        $apiKey = $response['token_key'];
        $apiSecret = $response['token_secret'];

        // Update the client with the new token
        $this->client = new StreamlineClient($apiKey, $apiSecret);

        return [
            'apikey' => $apiKey,
            'apiSecret' => $apiSecret,
        ];
    }

    public function properties(): PropertiesClient
    {
        return new PropertiesClient($this->client);
    }

}