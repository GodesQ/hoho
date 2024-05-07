<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelTax\PaymentRequest;
use App\Models\Transaction;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use App\Services\AqwireService;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TravelTaxController extends Controller
{   
    public $aqwireService;

    public function __construct(AqwireService $aqwireService)
    {
        $this->aqwireService = $aqwireService;
    }

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

            foreach ($request->passengers as $key => $passenger) {
                $passenger = TravelTaxPassenger::create(array_merge(['payment_id' => $travel_tax_payment->id], $passenger));

                if($passenger['passenger_type'] == 'primary' && !$primary_passenger) {
                    $primary_passenger = $passenger;
                }
            }

            if(!$primary_passenger) {
                throw new ErrorException("The primary passenger was not found.", 400);
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

            // Pay using aqwire
            $responseData = $this->aqwireService->pay($requestModel);
            
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
