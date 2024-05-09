<?php

namespace App\Services;

use ErrorException;
use Illuminate\Support\Facades\Http;

class AqwireService
{
    public function __construct()
    {

    }

    public function pay($body)
    {
        # Generate URL Endpoint and Auth Token for Payment Gateway
        if (env('APP_ENVIRONMENT') == 'LIVE') {
            $url = 'https://payments.aqwire.io/api/v3/transactions/create';
            $authToken = $this->getLiveHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
        } else {
            $url = 'https://payments-sandbox.aqwire.io/api/v3/transactions/create';
            $authToken = $this->getHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
        }

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'Qw-Merchant-Id' => config('services.aqwire.merchant_code'),
            'Authorization' => 'Bearer ' . $authToken,
        ])->post($url, $body);

        $statusCode = $response->getStatusCode();

        if ($statusCode == 400) {
            $content = json_decode($response->getBody()->getContents());
            throw new ErrorException($content->message . ' in Aqwire Payment Gateway.');
        }

        $responseData = json_decode($response->getBody(), true);
        return $responseData;
    }

    public function createRequestModel($transaction, $customer)
    {
        $requestModel = [
            'uniqueId' => $transaction->reference_no,
            'currency' => 'PHP',
            'paymentType' => 'DTP',
            'amount' => $transaction->payment_amount,
            'customer' => [
                'name' => $customer->firstname . ' ' . $customer->lastname,
                'email' => $customer->email_address,
                'mobile' => $customer->mobile_number,
            ],
            'project' => [
                'name' => 'Philippines Hop-On Hop-Off Travel Tax Payment',
                'unitNumber' => '00000',
                'category' => 'Travel Tax Payment'
            ],
            'redirectUrl' => [
                'success' => env('AQWIRE_TEST_TRAVEL_TAX_SUCCESS_URL') . $transaction->id,
                'cancel' => env('AQWIRE_TEST_TRAVEL_TAX_CANCEL_URL') . $transaction->id,
                'callback' => env('AQWIRE_TEST_CALLBACK_URL') . $transaction->id
            ],
            'note' => 'Payment for Travel Tax',
        ];

        return $requestModel;
    }


    public function getHMACSignatureHash($text, $secret_key)
    {
        $key = $secret_key;
        $message = $text;

        $hex = hash_hmac('sha256', $message, $key);
        $bin = hex2bin($hex);

        return base64_encode($bin);
    }

    public function getLiveHMACSignatureHash($text, $key)
    {
        $keyBytes = utf8_encode($key);
        $textBytes = utf8_encode($text);

        $hashBytes = hash_hmac('sha256', $textBytes, $keyBytes, true);

        $base64Hash = base64_encode($hashBytes);
        $base64Hash = str_replace(['+', '/'], ['-', '_'], $base64Hash);

        return $base64Hash;
    }
}