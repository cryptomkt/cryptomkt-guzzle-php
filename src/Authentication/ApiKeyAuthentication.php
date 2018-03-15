<?php

namespace Cryptomkt\Wallet\Authentication;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiKeyAuthentication implements Authentication
{
    private $apiKey;
    private $apiSecret;

    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    public function setApiSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;
    }

    public function getRequestHeaders($method, $path, $body)
    { 
        $timestamp = $this->getTimestamp();

        switch ($path) 
{            case '/v1/orders/create':
                $body_object = json_decode($body);
                $message_to_sign = $timestamp . $path . $body_object->amount . $body_object->market . $body_object->price . $body_object->type;
                var_dump($message_to_sign);
                $signature = $this->getHash('sha384', $message_to_sign, $this->apiSecret);

                // echo $timestamp;
                break;
            
            default:
                $signature = $this->getHash('sha384', $timestamp.$path.$body, $this->apiSecret);
                break;
        }

        return [
            'X-MKT-APIKEY' => $this->apiKey,
            'X-MKT-SIGNATURE' => $signature,
            'X-MKT-TIMESTAMP' => $timestamp,
        ];
    }

    public function createRefreshRequest($baseUrl)
    {
    }

    public function handleRefreshResponse(RequestInterface $request, ResponseInterface $response)
    {
    }

    public function createRevokeRequest($baseUrl)
    {
    }

    public function handleRevokeResponse(RequestInterface $request, ResponseInterface $response)
    {
    }

    // protected

    protected function getTimestamp()
    {
        return time();
    }

    protected function getHash($algo, $data, $key)
    {
        return hash_hmac($algo, $data, $key, FALSE);
    }
}
