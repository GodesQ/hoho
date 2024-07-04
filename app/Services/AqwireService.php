<?php

namespace App\Services;

use ErrorException;
use Exception;
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
            dd($content);
            throw new ErrorException($content->message . ' in Aqwire Payment Gateway.');
        }

        $responseData = json_decode($response->getBody(), true);
        return $responseData;
    }

    public function createRequestModel($transaction, $customer)
    {
        $customer_email = $customer->email_address ?? $customer->email;
        $customer_mobile_number = "+" . ($customer->mobile_number ?? $customer->countryCode . $customer->contact_no);

        $success = '';
        $cancel = '';

        switch ($transaction->transaction_type) {
            case 'book_tour':
                $success = env('AQWIRE_TEST_SUCCESS_URL');
                $cancel = env('AQWIRE_TEST_CANCEL_URL');
                break;
            case 'travel_tax':
                $success = env('AQWIRE_TEST_TRAVEL_TAX_SUCCESS_URL');
                $cancel = env('AQWIRE_TEST_TRAVEL_TAX_CANCEL_URL');
                break;
            case 'order':
                    $success = env('AQWIRE_TEST_ORDER_SUCCESS_URL');
                    $cancel = env('AQWIRE_TEST_ORDER_CANCEL_URL');
                break;

            case 'hotel_reservation':
                    $success = env('AQWIRE_TEST_HOTEL_RESERVATION_SUCCESS_URL');
                    $cancel = env('AQWIRE_TEST_HOTEL_RESERVATION_CANCEL_URL');
                break;

            default: 
                $success = env('AQWIRE_TEST_SUCCESS_URL');
                $cancel = env('AQWIRE_TEST_CANCEL_URL');
                break;
        }

        $requestModel = [
            'uniqueId' => $transaction->reference_no,
            'currency' => 'PHP',
            'paymentType' => 'DTP',
            'amount' => $transaction->payment_amount,
            'customer' => [
                'name' => $customer->firstname . ' ' . $customer->lastname,
                'email' => $customer_email,
                'mobile' => $customer_mobile_number,
            ],
            'project' => [
                'name' => 'Philippines Hop On Hop Off',
                'unitNumber' => '00000',
                'category' => 'payment for hoho'
            ],
            'redirectUrl' => [
                'success' => $success . $transaction->id,
                'cancel' => $cancel . $transaction->id,
                'callback' => env('AQWIRE_TEST_CALLBACK_URL') . $transaction->id
            ],
            'note' => 'Payment for Philippines Hop On Hop Off',
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