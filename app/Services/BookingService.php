<?php

namespace App\Services;

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

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function createBookReservation(Request $request)
    {
        try {
            $reference_no = $this->generateReferenceNo();
            $additional_charges = $this->generateAdditionalCharges();

            $subAmount = intval($request->amount) ?? 0;

            $totalOfDiscount = (intval($request->amount) - intval($request->discounted_amount));

            $totalOfAdditionalCharges = $this->getTotalOfAdditionalCharges($request->number_of_pass, $additional_charges);

            $totalAmount = $this->getTotalAmountOfBooking($subAmount, $totalOfAdditionalCharges, $totalOfDiscount);

            // if 100% discount
            if($subAmount == $totalOfDiscount) {
                $totalAmount -= $totalOfAdditionalCharges;
            }

            $transaction = $this->createTransaction($request, $reference_no, $totalAmount, $additional_charges, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges);

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

            // Handle payment method
            if ($request->payment_method == 'cash_payment' || $subAmount == $totalOfDiscount) {
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
            dd($exception->getMessage());
        }
    }

    public function createMultipleBooking(Request $request)
    {
        return DB::transaction(function () use ($request) {
            // Remove the line below, as it's just for testing purposes

            $reference_no = $this->generateReferenceNo();
            $additional_charges = $this->generateAdditionalCharges();

            if (is_string($request->items) && is_array(json_decode($request->items, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                $items = json_decode($request->items, true);
            } else {
                $items = $request->items;
            }

            $subAmount = 0;
            $totalOfDiscount = 0;
            $totalOfAdditionalCharges = 0;

            /**
             * Calculates the subAmount, totalOfDiscount, and totalOfAdditionalCharges by iterating over the items
             */
            foreach ($items as $key => $item) {
                $subAmount += intval($item['amount']) ?? 0;

                $totalOfDiscount += (intval($item['amount']) - (intval($item['discounted_amount']) ?? intval($item['amount'])));

                $totalOfAdditionalCharges += $this->getTotalOfAdditionalCharges($item['number_of_pass'], $additional_charges);
            }

            if (config('services.checkout.type') == "HPP") {
                $totalAmount = $this->getTotalAmountOfBooking($subAmount, $totalOfAdditionalCharges, $totalOfDiscount);

                $transaction = $this->createTransaction($request, $reference_no, $totalAmount, $additional_charges, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges);

                $this->createMultipleReservation($request, $transaction, $additional_charges);


                $response = $this->sendPaymentRequest($transaction);

                if (!$response['status'] || !$response['status'] == 'FAIL') {
                    return response([
                        'status' => "failed",
                        'message' => 'Failed to submit request for transaction',
                        'data' => $response['result']->data
                    ]);
                }

                $responseData = json_decode($response['result']->getBody(), true);

                if ($responseData['status'] != 'SUCCESS') {
                    $logMessage = "An error occurred during the payment process with the following parameters: " .
                        config('services.aqwire.merchant_code') . " | " . config('services.aqwire.client_id') . " | " . config('services.aqwire.secret_key');
                    dd($logMessage);
                }

                $this->updateTransactionAfterPayment($transaction, $responseData, $additional_charges);

                if (is_array($request->items)) {
                    $items = $request->items;
                } else {
                    $items = json_decode($request->items, true);
                }

                $this->sendMultipleBookingNotification($items, $transaction);

                $this->mailService->sendPaymentRequestMail($transaction, $responseData['paymentUrl'], $responseData['data']['expiresAt']);

                if ($request->is('api/*')) {
                    return response([
                        'status' => 'paying',
                        'url' => $responseData['paymentUrl']
                    ]);
                } else {
                    return redirect($responseData['paymentUrl']);
                }
            }
        });
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
        # NOTE: The amount of each booking was already been set, This function is for additional charges, discounted amount to sum up all of the bookings for transactions.
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
            // 'Travel Pass' => 50,
        ];

        return $charges;
    }

    private function getTotalOfAdditionalCharges($number_of_pass, $additional_charges)
    {
        $convenience_fee = $additional_charges['Convenience Fee'] * $number_of_pass;
        // $travel_pass = $additional_charges['Travel Pass'] * $number_of_pass;
        $travel_pass = ($additional_charges['Travel Pass'] ?? 0) * $number_of_pass;

        return $convenience_fee + $travel_pass;
    }

    private function createTransaction($request, $reference_no, $totalAmount, $additional_charges, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges)
    {
        $transaction = Transaction::create([
            'reference_no' => $reference_no,
            'transaction_by_id' => $request->reserved_user_id,
            'sub_amount' => $subAmount ?? $totalAmount,
            'total_additional_charges' => $totalOfAdditionalCharges ?? 0,
            'total_discount' => $totalOfDiscount ?? 0,
            'transaction_type' => 'book_tour',
            'payment_amount' => $totalAmount,
            'additional_charges' => json_encode($additional_charges),
            'payment_status' => 'pending',
            'resolution_status' => 'pending',
            'order_date' => Carbon::now(),
            'transaction_date' => Carbon::now(),
        ]);

        return $transaction;
    }

    private function createReservation($request, $transaction, $totalAmount, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges)
    {
        $trip_date = Carbon::create($request->trip_date);

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
            'start_date' => $request->trip_date,
            'end_date' => $request->type == 'Guided' ? $trip_date->addDays(1) : $this->getDateOfDIYPass($request->ticket_pass, $trip_date),
            'status' => 'pending',
            'number_of_pass' => $request->number_of_pass,
            'ticket_pass' => $request->type == 'DIY' ? $request->ticket_pass : null,
            'payment_method' => $request->payment_method,
            'referral_code' => $request->referral_code,
            'promo_code' => $request->promo_code,
        ]);

        return $reservation;
    }

    private function createMultipleReservation($request, $transaction, $additional_charges)
    {
        if (is_array($request->items)) {
            $items = $request->items;
        } else {
            $items = json_decode($request->items, true);
        }

        foreach ($items as $key => $item) {
            $trip_date = Carbon::create($item['trip_date']);

            $subAmount = intval($item['amount']) ?? 0;

            $totalOfDiscount = (intval($item['amount']) - (intval($item['discounted_amount']) ?? intval($item['amount'])));

            $totalOfAdditionalCharges = $this->getTotalOfAdditionalCharges($item['number_of_pass'], $additional_charges);

            $totalAmount = $this->getTotalAmountOfBooking($subAmount, $totalOfAdditionalCharges, $totalOfDiscount);

            $reservation = TourReservation::create([
                'tour_id' => $item['tour_id'],
                'type' => $item['type'],
                'total_additional_charges' => $totalOfAdditionalCharges,
                'discount' => $totalOfDiscount,
                'sub_amount' => $subAmount,
                'amount' => $totalAmount,
                'reserved_user_id' => $request->reserved_user_id,
                'passenger_ids' => $request->has('passenger_ids') ? json_encode($request->passenger_ids) : json_encode([$request->reserved_user_id]),
                'reference_code' => $transaction->reference_no,
                'order_transaction_id' => $transaction->id,
                'start_date' => $item['trip_date'],
                'end_date' => $item['type'] == 'Guided' ? $trip_date->addDays(1) : $this->getDateOfDIYPass($item['ticket_pass'], $trip_date),
                'status' => 'pending',
                'number_of_pass' => $item['number_of_pass'],
                'ticket_pass' => $item['type'] == 'DIY' ? $item['ticket_pass'] : null,
                'promo_code' => $request->promo_code,
                'requirement_file_path' => null,
                'discount_amount' => $subAmount - $totalOfDiscount
            ]);

            if ($request->has('requirement') && $request->file('requirement')->isValid()) {
                $file = $request->file('requirement');
                $file_name = Str::random(7) . '.' . $file->getClientOriginalExtension();
                $save_file = $file->move(public_path() . '/assets/img/tour_reservations/requirements/' . $reservation->id, $file_name);
            } else {
                $file_name = null;
            }

            $reservation->update([
                'requirement_file_path' => $file_name
            ]);
        }
    }

    private function sendMultipleBookingNotification($items, $transaction)
    {
        foreach ($items as $key => $item) {

            $tour = Tour::where('id', $item['tour_id'])->first();

            $details = [
                'tour_provider_name' => optional(optional($tour->tour_provider)->merchant)->name,
                'reserved_passenger' => $transaction->user->firstname . ' ' . $transaction->user->lastname,
                'trip_date' => $item['trip_date'],
                'tour_name' => $tour->name
            ];

            if ($tour) {
                if ($tour->tour_provider) {
                    if ($tour->tour_provider->contact_email) {
                        if (env('APP_ENVIRONMENT') == 'LIVE') {
                            Mail::to($tour->tour_provider->contact_email)->send(new TourProviderBookingNotification($details));
                        } else {
                            Mail::to('james@godesq.com')->send(new TourProviderBookingNotification($details));
                        }
                    }
                }
            }
        }

    }

    private function sendPaymentRequest($transaction)
    {
        try {
            $requestModel = $this->setRequestModel($transaction);
            $jsonPayload = json_encode($requestModel, JSON_UNESCAPED_UNICODE);

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
            dd($e);

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
            'description' => 'Payment for Philippines Hop-On Hop-Off Reservation',
            'customer' => [
                'name' => optional($transaction->user)->firstname . ' ' . optional($transaction->user)->lastname,
                'email' => optional($transaction->user)->email,
                'mobile' => $userContactNumber,
            ],
            'project' => [
                'name' => 'Philippines Hop-On Hop-Off Checkout Reservation',
                'unitNumber' => '1-1234',
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
                'Amount' => number_format($transaction->payment_amount, 2),
                'agentName' => $transaction->user->firstname . ' ' . $transaction->user->lastname
            ]
        ];

        return $model;
    }

    private function updateTransactionAfterPayment($transaction, $responseData, $additional_charges)
    {
        $update_transaction = $transaction->update([
            'aqwire_transactionId' => $responseData['data']['transactionId'],
            'payment_url' => $responseData['paymentUrl'],
            'payment_status' => Str::lower($responseData['data']['status']),
            'payment_details' => json_encode($responseData),
            'additional_charges' => json_encode($additional_charges)
        ]);

        return $update_transaction;
    }

    private function getDateOfDIYPass($ticket_pass, $trip_date)
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