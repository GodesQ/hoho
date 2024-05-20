<?php

namespace App\Services;

use App\Enum\TourTypeEnum;
use App\Enum\TransactionTypeEnum;
use App\Http\Requests\TourReservation\StoreRequest;
use App\Models\LayoverTourReservationDetail;
use App\Models\TourReservationCustomerDetail;
use App\Models\User;
use ErrorException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Mail;
use App\Mail\TourProviderBookingNotification;
use App\Mail\PaymentRequestMail;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\TourReservation;
use App\Models\Tour;

use DB;

class BookingService
{
    protected $mailService;
    protected $aqwireService;

    public function __construct(MailService $mailService, AqwireService $aqwireService)
    {
        $this->mailService = $mailService;
        $this->aqwireService = $aqwireService;
    }

    public function createBookReservation(Request $request)
    {   
        try {
            $reference_no = $this->generateReferenceNo();
            $additional_charges = $this->generateAdditionalCharges();

            $subAmount = intval($request->amount) ?? 0;

            if ($request->promo_code) {
                $totalOfDiscount = (intval($request->amount) - intval($request->discounted_amount));
            } else {
                $totalOfDiscount = 0;
            }

            $totalOfAdditionalCharges = $this->getTotalOfAdditionalCharges($request->number_of_pass, $additional_charges);
            $totalAmount = $this->getTotalAmountOfBooking($subAmount, $totalOfAdditionalCharges, $totalOfDiscount);

            // if 100% discount
            if ($subAmount == $totalOfDiscount) {
                $totalAmount -= $totalOfAdditionalCharges;
            }

            $transaction = $this->storeTransaction($request, $reference_no, $totalAmount, $additional_charges, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges);

            if (!$transaction) {
                return back()->with('fail', 'Failed to Create Transaction');
            }

            $reservation = $this->createReservation($request, $transaction, $totalAmount, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges);

            // Check if reservation or transaction creation failed
            if (!$reservation || !$transaction) {
                if ($reservation) {
                    $reservation->delete();
                }
                if ($transaction) {
                    $transaction->delete();
                }
                return back()->with('fail', 'Failed to Create Reservation');
            }

            // Handle payment method`
            if ($request->payment_method == 'cash_payment' || ($request->promo_code && $subAmount == $totalOfDiscount)) {
                return redirect()->route('admin.tour_reservations.edit', $reservation->id)->withSuccess('Book Reservation Successfully');
            } else {
                $response = $this->sendPaymentRequest($transaction);

                if (!$response['status'] || !$response['status'] == 'FAIL') {
                    $reservation->delete();
                    $transaction->delete();
                    return back()->with('fail', 'Invalid Transaction');
                }

                $responseData = json_decode($response['result']->getBody(), true);

                // Update transaction after payment
                $this->updateTransactionAfterPayment($transaction, $responseData, $additional_charges);

                // Send payment request mail
                $this->mailService->sendPaymentRequestMail($transaction, $responseData['paymentUrl'], $responseData['data']['expiresAt']);

                // Send notification to tour provider
                if (env('APP_ENVIRONMENT') == 'LIVE') {
                    $tour = Tour::where('id', $request->tour_id)->first();

                    if ($tour && $tour->tour_provider && optional($tour->tour_provider)->contact_email) {

                        $details = [
                            'tour_provider_name' => optional(optional($tour->tour_provider)->merchant)->name,
                            'reserved_passenger' => $transaction->user->firstname . ' ' . $transaction->user->lastname,
                            'trip_date' => $request->trip_date,
                            'tour_name' => $tour->name
                        ];

                        Mail::to(optional($tour->tour_provider)->contact_email)->send(new TourProviderBookingNotification($details));
                    }
                }

                // Return response
                if ($request->is('api/*')) {
                    return response([
                        'status' => 'paying',
                        'url' => $responseData['paymentUrl']
                    ]);
                } else {
                    return redirect($responseData['paymentUrl']);
                }
            }

        } catch (\Exception $exception) {
           return back()->with('failed', $exception->getMessage());
        }
    }

    public function createMultipleBooking(Request $request)
    {
        try {
            $user = User::where('id', $request->reserved_user_id)->first();

            if (!$user->firstname || !$user->lastname || !$user->contact_no)
                throw new ErrorException("The first name, last name and contact number must be filled in correctly in your profile to continue.");

            $phone_number = "+" . $user->countryCode . $user->contact_no;

            if (!preg_match('/^\+\d{10,12}$/', $phone_number)) {
                throw new ErrorException("The contact number must be a valid E.164 format.");
            }

            $additional_charges = $this->generateAdditionalCharges();

            $items = $request->items;

            if (is_string($items) && is_array(json_decode($items, true))) {
                $items = json_decode($items, true);
            }

            if (!is_array($items))
                throw new ErrorException("Invalid type of items.");

            $subAmount = 0;
            $totalOfDiscount = 0;
            $totalOfAdditionalCharges = 0;

            foreach ($items as $item) {
                $discounted_amount = intval($item['discounted_amount']) ?? intval($item['amount']);

                $subAmount += intval($item['amount']) ?? 0;
                $totalOfDiscount += intval($item['amount']) - $discounted_amount;
                $totalOfAdditionalCharges += $this->getTotalOfAdditionalCharges($item['number_of_pass'], $additional_charges);
            }

            $totalAmount = $this->getTotalAmountOfBooking($subAmount, $totalOfAdditionalCharges, $totalOfDiscount);

            $transaction = $this->storeTransaction($request, $totalAmount, $additional_charges, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges);

            foreach ($items as $item) {
                $reservation = $this->storeReservation($request, $transaction, $item);

                if ($item['type'] === TourTypeEnum::LAYOVER_TOUR) {
                    $this->storeLayoverTourDetails($reservation, $item);
                }
            }

            $payment_request_model = $this->aqwireService->createRequestModel($transaction, $user);

            $payment_response = $this->aqwireService->pay($payment_request_model);

            $this->updateTransactionAfterPayment($transaction, $payment_response, $additional_charges);

            $this->sendMultipleBookingNotification($items, $transaction);

            $this->mailService->sendPaymentRequestMail($transaction, $payment_response['paymentUrl'], $payment_response['data']['expiresAt']);

            return [
                'transaction' => $transaction,
                'payment_url' => $payment_response['paymentUrl']
            ];

        } catch (ErrorException $e) {
            if (isset($transaction))
                $transaction->delete();
            throw $e;
        }
    }

    # HELPERS

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

    private function getTotalAmountOfBooking($subAmount, $totalOfAdditionalCharges, $totalOfDiscount)
    {
        # NOTE: The amount of each booking was already been set, This function is for additional charges, discounted amount to sum up all of the bookings for this transaction.
        return ($subAmount - $totalOfDiscount) + $totalOfAdditionalCharges;
    }

    private function generateReferenceNo()
    {
        return date('Ym') . '-' . 'OT' . rand(100000, 10000000);
    }

    private function generateAdditionalCharges()
    {
        $charges = [
            'Convenience Fee' => 99,
        ];

        return $charges;
    }

    private function getTotalOfAdditionalCharges($number_of_pass, $additional_charges)
    {
        try {
            $convenience_fee = $additional_charges['Convenience Fee'] * $number_of_pass;
            $travel_pass = ($additional_charges['Travel Pass'] ?? 0) * $number_of_pass;

            return $convenience_fee + $travel_pass;
        } catch (ErrorException $e) {
            throw $e;
        }
    }

    private function storeTransaction($request, $totalAmount, $additional_charges, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges)
    {
        $reference_no = $this->generateReferenceNo();

        $transaction = Transaction::create([
            'reference_no' => $reference_no,
            'transaction_by_id' => $request->reserved_user_id,
            'sub_amount' => $subAmount ?? $totalAmount,
            'total_additional_charges' => $totalOfAdditionalCharges ?? 0,
            'total_discount' => $totalOfDiscount ?? 0,
            'transaction_type' => TransactionTypeEnum::BOOK_TOUR,
            'payment_amount' => $totalAmount,
            'additional_charges' => json_encode($additional_charges),
            'payment_status' => 'pending',
            'resolution_status' => 'pending',
            'aqwire_paymentMethodCode' => $request->payment_method ?? null,
            'order_date' => Carbon::now(),
            'transaction_date' => Carbon::now(),
        ]);

        return $transaction;
    }

    private function createReservation($request, $transaction, $totalAmount, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges)
    {
        $user = User::findOrFail($request->reserved_user_id);
        $trip_start_date = Carbon::parse($request->trip_date);
        $trip_end_date = $request->type == 'Guided' ? $trip_start_date->addDays(1) : $this->getDateOfDIYPass($request->ticket_pass, $trip_start_date);

        $reservation = TourReservation::create([
            'tour_id' => $request->tour_id,
            'type' => $request->type,
            'total_additional_charges' => $totalOfAdditionalCharges,
            'discount' => $totalOfDiscount,
            'sub_amount' => $subAmount,
            'amount' => $totalAmount,
            'reserved_user_id' => $request->reserved_user_id,
            'passenger_ids' => $request->passenger_ids ? json_encode($request->passenger_ids) : json_encode([$request->reserved_user_id]),
            'reference_code' => $transaction->reference_no,
            'order_transaction_id' => $transaction->id,
            'start_date' => $trip_start_date,
            'end_date' => $trip_end_date,
            'status' => 'pending',
            'number_of_pass' => $request->number_of_pass,
            'ticket_pass' => $request->type == 'DIY' ? $request->ticket_pass : null,
            'payment_method' => $request->payment_method,
            'referral_code' => $request->referral_code,
            'promo_code' => $request->promo_code,
            'created_by' => Auth::guard('admin')->user()->id,
            'created_user_type' => Auth::guard('admin')->user()->role,
        ]);

        TourReservationCustomerDetail::create([
            'tour_reservation_id' => $reservation->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'contact_no' => '+' . $user->countryCode . $user->contact_no,
            'address' => null,
        ]);

        return $reservation;
    }

    private function storeReservation($request, $transaction, $item)
    {
        $user = User::findOrFail($request->reserved_user_id);

        $trip_start_date = Carbon::parse($item['trip_date']);
        $trip_end_date = $request->type == 'Guided' ? $trip_start_date->addDays(1) : $this->getDateOfDIYPass($request->ticket_pass, $trip_start_date);

        $reservation = TourReservation::create([
            'tour_id' => $item['tour_id'],
            'type' => $item['type'],
            'total_additional_charges' => $transaction->total_additional_charges,
            'discount' => $transaction->total_discount,
            'sub_amount' => $transaction->sub_amount,
            'amount' => $transaction->payment_amount,
            'reserved_user_id' => $request->reserved_user_id,
            'passenger_ids' => $request->has('passenger_ids') ? json_encode($request->passenger_ids) : json_encode([$request->reserved_user_id]),
            'reference_code' => $transaction->reference_no,
            'order_transaction_id' => $transaction->id,
            'start_date' => $trip_start_date,
            'end_date' => $trip_end_date,
            'status' => 'pending',
            'number_of_pass' => $item['number_of_pass'],
            'ticket_pass' => $item['type'] == 'DIY' ? $item['ticket_pass'] : null,
            'promo_code' => $request->promo_code,
            'requirement_file_path' => null,
            'discount_amount' => $transaction->sub_amount - $transaction->discount,
            'created_by' => $request->reserved_user_id,
            'created_user_type' => 'guest'
        ]);

        if ($request->has('requirement') && $request->file('requirement')->isValid()) {
            $file = $request->file('requirement');
            $file_name = Str::random(7) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/assets/img/tour_reservations/requirements/' . $reservation->id, $file_name);
        } else {
            $file_name = null;
        }

        $reservation->update([
            'requirement_file_path' => $file_name
        ]);

        TourReservationCustomerDetail::create([
            'tour_reservation_id' => $reservation->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'contact_no' => '+' . $user->countryCode . $user->contact_no,
            'address' => null,
        ]);

        return $reservation;
    }

    private function storeLayoverTourDetails($reservation, $item)
    {
        $layover_user_details = LayoverTourReservationDetail::create([
            'reservation_id' => $reservation->id,
            'arrival_datetime' => $item['arrival_datetime'],
            'flight_to' => $item['flight_to'],
            'departure_datetime' => $item['departure_datetime'],
            'flight_from' => $item['flight_from'],
            'passport_number' => $item['passport_number'],
            'special_instruction' => $item['special_instruction']
        ]);

        return $layover_user_details;
    }

    private function sendMultipleBookingNotification($items, $transaction)
    {
        foreach ($items as $key => $item) {

            $tour = Tour::where('id', $item['tour_id'])->first();

            $details = [
                'tour_provider_name' => $tour->tour_provider->merchant->name ?? '',
                'reserved_passenger' => $transaction->user->firstname . ' ' . $transaction->user->lastname,
                'trip_date' => $item['trip_date'],
                'tour_name' => $tour->name
            ];

            if ($tour?->tour_provider?->contact_email) {
                $recipientEmail = env('APP_ENVIRONMENT') === 'LIVE' ? $tour->tour_provider->contact_email : 'james@godesq.com';
                Mail::to($recipientEmail)->cc('philippinehoho@tourism.gov.ph')->send(new TourProviderBookingNotification($details));
            }

        }
    }

    private function sendPaymentRequest($transaction)
    {
        try {
            $requestModel = $this->setRequestModel($transaction);

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

            // Handle the response here
            $statusCode = $response->getStatusCode();
            $responseContent = $response->getBody()->getContents();

            if ($statusCode == 400) {
                return [
                    'status' => FALSE,
                    'result' => json_decode($responseContent)
                ];
            }

            return [
                'status' => TRUE,
                'result' => $response
            ];
            // Your code to process the response...

        } catch (RequestException $e) {
            $errorMessage = $e->getMessage();

            return [
                'status' => FALSE,
                'result' => $errorMessage
            ];

        } catch (\Exception $e) {
            dd($e);
            return [
                'status' => FALSE,
                'result' => 'Failed'
            ];
            // Handle other exceptions that may occur
            // Your error handling code...
        }

    }

    private function setRequestModel($transaction)
    {
        $userContactNumber = "+" . optional($transaction->user)->countryCode . optional($transaction->user)->contact_no;

        $model = [
            'uniqueId' => $transaction->reference_no,
            'currency' => 'PHP',
            'paymentType' => 'DTP',
            'amount' => $transaction->payment_amount,
            'customer' => [
                'name' => optional($transaction->user)->firstname . ' ' . optional($transaction->user)->lastname,
                'email' => optional($transaction->user)->email,
                'mobile' => $userContactNumber,
            ],
            'project' => [
                'name' => 'Philippines Hop-On Hop-Off Checkout Reservation',
                'unitNumber' => '00000',
                'category' => 'Checkout'
            ],
            'redirectUrl' => [
                'success' => env('AQWIRE_TEST_SUCCESS_URL') . $transaction->id,
                'cancel' => env('AQWIRE_TEST_CANCEL_URL') . $transaction->id,
                'callback' => env('AQWIRE_TEST_CALLBACK_URL') . $transaction->id
            ],
            'note' => 'Checkout for Tour Reservation',
            'metadata' => [
                'Convenience Fee' => '99.00' . ' ' . 'Per Pax',
            ]
        ];

        return $model;
    }

    private function updateTransactionAfterPayment($transaction, $payment_response, $additional_charges)
    {
        $update_transaction = $transaction->update([
            'aqwire_transactionId' => $payment_response['data']['transactionId'],
            'payment_url' => $payment_response['paymentUrl'],
            'payment_status' => Str::lower($payment_response['data']['status']),
            'payment_details' => json_encode($payment_response),
            'additional_charges' => json_encode($additional_charges)
        ]);

        return $update_transaction;
    }

    public function getDateOfDIYPass($ticket_pass, $trip_date)
    {
        if ($ticket_pass == '1 Day Pass') {
            $date = $trip_date->addDays(1);
        } else if ($ticket_pass == '2 Day Pass') {
            $date = $trip_date->addDays(2);
        } else if ($ticket_pass == '3 Day Pass') {
            $date = $trip_date->addDays(3);
        } else {
            $date = $trip_date->addDays(1);
        }

        return $date;
    }

}