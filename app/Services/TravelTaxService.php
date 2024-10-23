<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use Carbon\Carbon;
use App\Enum\TransactionTypeEnum;
use ErrorException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TravelTaxService
{
    public $aqwireService;

    public function __construct(AqwireService $aqwireService)
    {
        $this->aqwireService = $aqwireService;
    }

    public function createTravelTax($request)
    {
        try {
            DB::beginTransaction();

            $referenceNumber = generateTravelTaxReferenceNumber();

            $totalAmount = $this->computeTotalAmount($request->amount, $request->processing_fee, $request->discount);

            $transaction = $this->storeTransaction($request, $referenceNumber, $totalAmount);

            $travel_tax_payment = $this->storeTravelTaxPayment($request, $transaction, $totalAmount);

            // Declare primary passenger for customer of aqwire payment service
            $primary_passenger = null;

            foreach ($request->passengers as $key => $passenger) {
                $passenger_data = array_merge(['payment_id' => $travel_tax_payment->id], $passenger);
                $passenger = TravelTaxPassenger::create($passenger_data);

                if ($passenger['passenger_type'] === 'primary' && ! $primary_passenger) {
                    $primary_passenger = $passenger;
                }
            }

            if (! $primary_passenger) {
                throw new ErrorException("The primary passenger is not found.", 400);
            }

            // Create request model for payment request
            $requestModel = $this->aqwireService->createRequestModel($transaction, $primary_passenger);

            // Pay using aqwire
            $responseData = $this->aqwireService->pay($requestModel);

            $transaction->update([
                'aqwire_transactionId' => $responseData['data']['transactionId'] ?? null,
                'payment_url' => $responseData['paymentUrl'] ?? null,
                'payment_status' => Str::lower($responseData['data']['status'] ?? ''),
                'payment_details' => json_encode($responseData),
            ]);

            DB::commit();

            return [
                'transaction' => $transaction,
                'travel_tax_payment' => $travel_tax_payment,
                'url' => $responseData['paymentUrl'],
            ];

        } catch (ErrorException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function sendTravelTaxAPI($traveltax, $transaction, $primary_passenger)
    {
        $requestModel = $this->travelTaxAPIRequestModel($transaction, $transaction, $primary_passenger);
    }

    private function storeTransaction($request, $referenceNumber, $totalAmount)
    {
        $transaction = Transaction::create([
            'transaction_by_id' => $request->user_id,
            'reference_no' => $referenceNumber,
            'sub_amount' => $request->amount,
            'additional_charges' => json_encode(getConvenienceFee()),
            'total_additional_charges' => $request->processing_fee,
            'total_discount' => $request->discount,
            'transaction_type' => TransactionTypeEnum::TRAVEL_TAX,
            'payment_amount' => $totalAmount,
            'aqwire_paymentMethodCode' => $request->payment_method ?? null,
            'order_date' => Carbon::now(),
            'transaction_date' => Carbon::now(),
        ]);

        return $transaction;
    }

    public function travelTaxAPIRequestModel($traveltax, $transaction, $primary_passenger)
    {
        return true;
    }

    private function storeTravelTaxPayment($request, $transaction, $totalAmount)
    {
        $transactionNumber = $this->generateTransactionNumber();
        $user = Auth::guard('admin')->user();

        $travel_tax_payment = TravelTaxPayment::create([
            'user_id' => $request->user_id,
            "ar_number" => generateARNumber(),
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
            'created_by' => $user ? $user->id : $request->user_id,
            'created_by_role' => $user ? $user->role : 'guest',
        ]);

        return $travel_tax_payment;
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