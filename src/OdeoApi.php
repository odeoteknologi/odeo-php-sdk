<?php

namespace OdeoApi;

use GuzzleHttp\Client;

class OdeoApi
{
    protected $clientId;
    protected $clientSecret;
    protected $signingKey;
    protected $accessToken;

    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->clientId = '';
        $this->clientSecret = '';
        $this->signingKey = '';
    }

    public function setCredentials($clientId, $clientSecret, $signingKey)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->signingKey = $signingKey;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function refreshAccessToken($scope = '')
    {
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
        ];

        if ($this->scopeExists($scope)) {
            $data['scope'] = $scope;
        }

        $response = $this->client->request('POST', '/oauth2/token', [
            'json' => $data,
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        $accessToken = $result['access_token'];
        $this->setAccessToken($accessToken);

        return $result;
    }

    public function createRequest($method, $path, $body = [])
    {
        if ('POST' == $method) {
            $options['json'] = $body;
        }
        $options['headers'] = $this->createHeaders($method, $path, $body);
        $options['timeout'] = 60;
        $response = $this->client->request($method, $path, $options);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function isValidSignature($signatureToCompare, $method, $path, $timestamp, $body, $accessToken = '')
    {
        $signature = $this->generateSignature($method, $path, $timestamp, $body, $accessToken);

        return $signatureToCompare == $signature;
    }

    protected function generateSignature($method, $path, $timestamp, $body, $accessToken)
    {
        if (empty($body)) {
            $body = '';
        } elseif (is_array($body)) {
            $body = json_encode($body);
        }

        $bodyHash = base64_encode(hash('sha256', $body, true));
        $messages = [$method, $path, '', $accessToken, $timestamp, $bodyHash];
        $stringToSign = implode(':', $messages);
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $this->signingKey, true));

        return $signature;
    }

    protected function createHeaders($method, $path, $body)
    {
        $timestamp = time();
        $signature = $this->generateSignature($method, $path, $timestamp, $body, $this->accessToken);

        return [
            'Authorization' => 'Bearer '.$this->accessToken,
            'X-Odeo-Timestamp' => $timestamp,
            'X-Odeo-Signature' => $signature,
        ];
    }

    public function setBaseUrl($url)
    {
        $this->client = new Client([
            'base_uri' => $url,
        ]);
    }

    public function scopeExists($scope)
    {
        return !empty($scope);
    }
}
