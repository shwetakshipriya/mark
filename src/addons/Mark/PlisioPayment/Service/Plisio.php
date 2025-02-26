<?php

namespace Mark\PlisioPayment\Service;

use XF\Mvc\Service\AbstractService;

class Plisio extends AbstractService
{
    protected $apiKey;
    
    public function __construct(\XF\App $app)
    {
        parent::__construct($app);
        $this->apiKey = \XF::options()->plisioApiKey;
    }
    
    public function createPaymentSession($coin, $network, $amount, $userId)
    {
        // Build the request payload for Plisio API
        $payload = [
            'coin'        => $coin,
            'network'     => $network,
            'amount'      => $amount,
            'order_id'    => uniqid('order_', true),
            'callback_url'=> $this->app->router('public')->buildLink('canonical:plisio-payment/webhook')
        ];
        
        $response = $this->makeApiRequest('create_payment', $payload);
        
        if ($response && isset($response['payment_address'])) {
            return $response;
        }
        
        return ['error' => 'API request failed'];
    }
    
    public function checkPaymentStatus($paymentAddress)
    {
        // Query the Plisio API for the payment status
        $payload = [
            'payment_address' => $paymentAddress
        ];
        
        $response = $this->makeApiRequest('payment_status', $payload);
        
        if ($response && isset($response['status'])) {
            return $response['status'];
        }
        
        return null;
    }
    
    protected function makeApiRequest($endpoint, $payload)
    {
        $url = "https://plisio.net/api/$endpoint";
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->apiKey
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($result, true);
    }
}
