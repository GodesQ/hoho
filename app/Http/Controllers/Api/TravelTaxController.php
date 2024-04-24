<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelTax\PaymentRequest;
use App\Models\Transaction;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TravelTaxController extends Controller
{
    public function store(PaymentRequest $request)
    {
        try {
            $referenceNumber = $this->generateReferenceNo();
            $transactionNumber = $this->generateTransactionNumber();
            $totalAmount = $this->computeTotalAmount($request->amount, $request->processing_fee, $request->discount);

            $transaction = Transaction::create([
                'reference_no' => $referenceNumber,
                'sub_amount' => $request->amount,
                'total_additional_charges' => 0,
                'total_discount' => $request->discount,
                'transaction_type' => 'travel_tax',
                'payment_amount' => $totalAmount,
                'additional_charges' => null,
                'aqwire_paymentMethodCode' => $request->payment_method ?? null,
                'order_date' => Carbon::now(),
                'transaction_date' => Carbon::now(),
            ]);

            $travel_tax_payment = TravelTaxPayment::create([
                'transaction_id' => $transaction->id,
                'transaction_number' => $transactionNumber,
                'reference_number' => $transaction->reference_no,
                'transaction_time' => Carbon::now(),
                'currency' => 'PHP',
                'amount' => $request->amount,
                'processing_fee' => $request->processing_fee,
                'discount' => $request->discount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method ?? null,
                'payment_time' => Carbon::now(),
                'status' => 'unpaid',
            ]);

            $primary_passenger = null;
            $pasengers_ids = [];

            foreach ($request->passengers as $key => $passenger) {
                $passenger = TravelTaxPassenger::create([
                    'payment_id' => $travel_tax_payment->id,
                    'firstname' => $passenger['firstname'],
                    'lastname' => $passenger['lastname'],
                    'middlename' => $passenger['middlename'],
                    'suffix' => $passenger['suffix'],
                    'passport_number' => $passenger['passport_number'],
                    'ticket_number' => $passenger['ticket_number'],
                    'class' => $passenger['class'],
                    'mobile_number' => $passenger['mobile_number'],
                    'email_address' => $passenger['email_address'],
                    'destination' => $passenger['destination'],
                    'departure_date' => $passenger['departure_date'],
                    'passenger_type' => $passenger['passenger_type'],
                    'amount' => $passenger['amount']
                ]);

                if($passenger['passenger_type'] == 'primary' && !$primary_passenger) $primary_passenger = $passenger;
            }

            # Create Request Model for Payment Gateway
            $requestModel = [
                'uniqueId' => $transaction->reference_no,
                'currency' => 'PHP',
                'paymentType' => 'DTP',
                'amount' => $transaction->payment_amount,
                'customer' => [
                    'name' => $primary_passenger->firstname . ' ' . $primary_passenger->lastname,
                    'email' => $primary_passenger->email_address,
                    'mobile' => $primary_passenger->mobile_number,
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

            # Generate URL Endpoint and Auth Token for Payment Gateway
            if (env('APP_ENVIRONMENT') == 'LIVE') {
                $url_create = 'https://payments.aqwire.io/api/v3/transactions/create';
                $authToken = $this->getLiveHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
            } else {
                $url_create = 'https://payments-sandbox.aqwire.io/api/v3/transactions/create';
                $authToken = $this->getHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
            }

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'Qw-Merchant-Id' => config('services.aqwire.merchant_code'),
                'Authorization' => 'Bearer ' . $authToken,
            ])->post($url_create, $requestModel);

            $statusCode = $response->getStatusCode();

            if ($statusCode == 400) {
                $content = json_decode($response->getBody()->getContents());
                throw new ErrorException($content->message . ' in Aqwire Payment Gateway.');
            }

            $responseData = json_decode($response->getBody(), true);

            $transaction->update([
                'aqwire_transactionId' => $responseData['data']['transactionId'] ?? null,
                'payment_url' => $responseData['paymentUrl'] ?? null,
                'payment_status' => Str::lower($responseData['data']['status'] ?? ''),
                'payment_details' => json_encode($responseData),
            ]);

            return response([
                'status' => 'paying',
                'url' => $responseData['paymentUrl']
            ], 201);

        } catch (ErrorException $e) {
            $transaction->delete();
            $travel_tax_payment->delete();
            return response([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    private function getHMACSignatureHash($text, $secret_key)
    {
        $key = $secret_key;
        $message = $text;

        $hex = hash_hmac('sha256', $message, $key);
        $bin = hex2bin($hex);

        return base64_encode($bin);
    }

    private function getLiveHMACSignatureHash($text, $key)
    {
        $keyBytes = utf8_encode($key);
        $textBytes = utf8_encode($text);

        $hashBytes = hash_hmac('sha256', $textBytes, $keyBytes, true);

        $base64Hash = base64_encode($hashBytes);
        $base64Hash = str_replace(['+', '/'], ['-', '_'], $base64Hash);

        return $base64Hash;
    }

    private function computeTotalAmount($amount, $processing_fee, $discount)
    {
        return ($amount + $processing_fee) - $discount;
    }

    private function generateReferenceNo()
    {
        return date('Ym') . '-' . 'OTRX' . rand(100000, 10000000);
    }

    private function generateTransactionNumber()
    {
        return 'TN' . date('Ym') . rand(100000, 10000000);
    }
}
