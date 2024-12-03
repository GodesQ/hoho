<?php

namespace App\Services;

use Carbon\Carbon;
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
        if (config('app.env') === 'production')
        {
            $url = 'https://payments.aqwire.io/api/v3/transactions/create';
            $authToken = $this->getLiveHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
        } else
        {
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

        if ($statusCode == 400)
        {
            $content = json_decode($response->getBody()->getContents());
            throw new ErrorException($content->message . ' in Aqwire Payment Gateway.', 400);
        }

        $responseData = json_decode($response->getBody(), true);
        return $responseData;
    }

    public function createRequestModel($transaction, $customer)
    {
        $customer_email = $customer->email_address ?? $customer->email;
        $customer_mobile_number = "+" . ($customer->mobile_number ?? $customer->countryCode . $customer->contact_no);

        switch ($transaction->transaction_type)
        {
            case 'book_tour':
                $success = config('aqwire.success.book_tour');
                $cancel = config('aqwire.cancel.book_tour');
                break;
            case 'travel_tax':
                $success = config('aqwire.success.travel_tax');
                $cancel = config('aqwire.cancel.travel_tax');
                break;
            case 'order':
                $success = config('aqwire.success.order');
                $cancel = config('aqwire.cancel.order');
                break;

            case 'hotel_reservation':
                $success = config('aqwire.success.hotel_reservation');
                $cancel = config('aqwire.cancel.hotel_reservation');
                break;

            default:
                $success = config('aqwire.success.book_tour');
                $cancel = config('aqwire.cancel.book_tour');
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
                'category' => $transaction->transaction_type
            ],
            'redirectUrl' => [
                'success' => $success . $transaction->id,
                'cancel' => $cancel . $transaction->id,
                'callback' => config('aqwire.callback.url')
            ],
            'note' => 'Payment for Philippines Hop On Hop Off',
            'expiresAt' => Carbon::now()->addDays(1),
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