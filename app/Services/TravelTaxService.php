<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TravelTaxAPILog;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use Carbon\Carbon;
use App\Enum\TransactionTypeEnum;
use Date;
use ErrorException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
                throw new ErrorException("The primary passenger is not found.", 404);
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
        try {
            $requestModel = $this->travelTaxAPIRequestModel($traveltax, $transaction, $primary_passenger);

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post("https://api-backend.tieza.online/api/fulltax_applications", $requestModel);

            $statusCode = $response->getStatusCode();

            if ($statusCode == 400 || $statusCode == 403 || $statusCode == 422) {
                $content = json_decode($response->getBody()->getContents());
                TravelTaxAPILog::create(['travel_tax_id' => $traveltax->id, 'status_code' => $statusCode, 'response' => json_encode($content), 'date_of_submission' => Carbon::now()]);
                return;
            }

            $responseData = json_decode($response->getBody(), true);
            TravelTaxAPILog::create(['travel_tax_id' => $traveltax->id, 'status_code' => $statusCode, 'response' => json_encode($responseData), 'date_of_submission' => Carbon::now()]);

            $traveltax->update([
                'is_sent_api' => true,
            ]);

            return $responseData;

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
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

        $travelTaxClass = $primary_passenger->class == 'first class' ? 'First' : 'Economy';

        return [
            'date_application' => $traveltax->transaction_time,
            'fulltax_no' => $transaction->reference_no,
            'ar_no' => $traveltax->ar_number,
            'last_name' => $primary_passenger->lastname,
            'first_name' => $primary_passenger->firstname,
            'middle_name' => $primary_passenger->middlename ?? 'N/A',
            'ext_name' => $primary_passenger->suffix ?? 'N/A',
            'passport_no' => $primary_passenger->passport_number,
            'ticket_no' => $primary_passenger->ticket_number,
            'class' => $travelTaxClass,
            'total_amount' => $traveltax->total_amount,
            'mobile_no' => $primary_passenger->mobile_number,
            'email_address' => $primary_passenger->email_address,
            'airlines_id' => 2,
            'departure_date' => $primary_passenger->departure_date,
            'no_of_pax' => $traveltax->passengers->count(),
            'user_token' => config('services.travel_tax_hoho_token'),
            'is_multiple' => $traveltax->passengers->count() > 1,
            'date' => Carbon::now(),
            'pax_info' => $traveltax->passengers->map(function ($passenger) {
                return [
                    'last_name' => $passenger->lastname,
                    'first_name' => $passenger->firstname,
                    'middle_name' => $passenger->middlename,
                    'ext_name' => $passenger->suffix ?? 'N/A',
                    'passport_no' => $passenger->passport_number,
                    'ticket_no' => $passenger->ticket_number,
                ];
            })->toArray(),
        ];
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

    private function storeAPILog($traveltax, $data, $status_code)
    {
        TravelTaxAPILog::create(['travel_tax_id' => $traveltax->id, 'status_code' => $status_code, 'response' => json_encode($data), 'date_of_submission' => Carbon::now()]);
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