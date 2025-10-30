<?php

namespace Cronixweb\Streamline\Utils;

use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class StreamlineClient
{

    private PendingRequest $request;

    public function __construct(private readonly string $apikey, private readonly string $apiSecret)
    {
        $this->request = Http::baseUrl("https://web.streamlinevrs.com/api/json");
    }

    /**
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
    public function request(string $methodName, array $data = [])
    {
        $authParams = [
            'token_key' => $this->apikey,
            'token_secret' => $this->apiSecret,
        ];

        $body = [
            'methodName' => $methodName,
            'params' => [
                ...$authParams,
                ...$data,
            ]
        ];

        /** @var Response $response */
        $response = $this->request->post('', $body);

        if ($response->ok()) {
            return $this->parseResponse($response);
        }

        throw new StreamlineApiException("Error: " . $response->body());

    }

    /**
     * @throws StreamlineApiException
     */
    private function parseResponse($response)
    {
        $response = $response->json();

        if (isset($response['response']['data'])) {
            return $response['response']['data'];
        }
        if (isset($response['data'])) {
            return $response['data'];
        }

        throw new StreamlineApiException("Invalid Response: " . json_encode($response));
    }
}