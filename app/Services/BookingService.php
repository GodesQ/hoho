<?php

namespace App\Services;

use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Mail;
use App\Mail\TourProviderBookingNotification;

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
    public function createBooking(Request $request)
    {
        // dd($request->all());
        $reference_no = $this->generateReferenceNo();
        $additional_charges = $this->generateAdditionalCharges();

        $totalAmount = $request->type == 'Guided' ? $this->generateGuidedTourTotalAmount($request->number_of_pass, $request->ticket_pass, $request->amount, $additional_charges) : $this->generateDIYTourTotalAmount($request->number_of_pass, $request->ticket_pass, $request->amount, $additional_charges);
        // dd($totalAmount);
        $transaction = $this->createTransaction($request, $reference_no, $totalAmount, $additional_charges);
        if (!$transaction) {
            return back()->with('fail', 'Failed to Create Transaction');
        }

        $reservation = $this->createReservation($request, $transaction, $totalAmount);
        if (!$reservation || !$transaction) {
            $reservation->delete();
            $transaction->delete();
            return back()->with('fail', 'Failed to Create Reservation');
        }

        $response = $this->sendPaymentRequest($transaction);
        $responseData = json_decode($response['result']->getBody(), true);

        if ($responseData['status'] != 'SUCCESS') {
            $logMessage = "An error occurred during the payment process with the following parameters: " .
                env('AQWIRE_MERCHANT_CODE') . " | " . env('AQWIRE_MERCHANT_CLIENTID') . " | " . env('AQWIRE_MERCHANT_SECURITY_KEY');
            dd($logMessage);
        }

        $updateTransaction = $this->updateTransactionAfterPayment($transaction, $responseData, $additional_charges);

        if( $request->is('api/*')){
            return response([
                'status' => 'paying',
                'url' => $responseData['paymentUrl']
            ]);
        } else {
            return redirect($responseData['paymentUrl']);
        }
    }

    public function createMultipleBooking(Request $request) {
        return DB::transaction(function () use ($request) {
            // Remove the line below, as it's just for testing purposes

            $reference_no = $this->generateReferenceNo();
            $additional_charges = $this->generateAdditionalCharges();

            $totalAmount = $this->getTotalAmountOfBookings($request, $additional_charges);

            $transaction = $this->createTransaction($request, $reference_no, $totalAmount, $additional_charges);

            $reservation = $this->createMultipleReservation($request, $transaction, $additional_charges);

            if($request->has('promo_code')) {
                if($request->promo_code == 'SPECIALDISCHOHO' || $request->promo_code == 'MANILEÑOSHOHO') {
                    if($request->has('requirement')) {

                        $transaction->update([
                            'status' => 'success'
                        ]);

                        return response([
                            'status' => 'success',
                            'message' => 'Your Book s has been successfully processed. Please wait for confirmation of your booking by tour operator. Thankyou.',
                            'url' => null
                        ]);
                    }
                }
            }

            $response = $this->sendPaymentRequest($transaction);

            if(!$response['status']) {
                return response([
                    'status' => FALSE,
                    'message' => 'Failed to submit request for transaction'
                ]);
            }

            $responseData = json_decode($response['result']->getBody(), true);

            if ($responseData['status'] != 'SUCCESS') {
                $logMessage = "An error occurred during the payment process with the following parameters: " .
                    env('AQWIRE_MERCHANT_CODE') . " | " . env('AQWIRE_MERCHANT_CLIENTID') . " | " . env('AQWIRE_MERCHANT_SECURITY_KEY');
                dd($logMessage);
            }

            $updateTransaction = $this->updateTransactionAfterPayment($transaction, $responseData, $additional_charges);

            if ($request->is('api/*')) {
                return response([
                    'status' => 'paying',
                    'url' => $responseData['paymentUrl']
                ]);
            } else {
                return redirect($responseData['paymentUrl']);
            }
        });
    }


    # HELPERS

    private function getHMACSignatureHash($text, $secret_key) {
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

    private function getTotalAmountOfBookings(Request $request, $additional_charges) {

        # NOTE: The amount of each booking was already been set, This function is for adding additional charges and to sum up all of the bookings for transactions.

        if(is_array($request->items)) {
            $items = $request->items;
        } else {
            $items = json_decode($request->items, true);
        }

        $items_amount = [];

        foreach ($items as &$item) {
            $hiddenPayment = 0;

            foreach ($additional_charges as $charge => $amount) {
                $hiddenPayment += $amount;
            }

            // Add hidden payments based on number_of_pass
            $hiddenPayment *= $item['number_of_pass'];

            // Add hidden payment to amount
            $total = $item['amount'] + $hiddenPayment;
            $items_amount[] = $total;
        }

        // unset($item); // Unset to avoid any unintended changes

        $totalAmount = array_sum($items_amount);
        return $totalAmount;
    }

    private function generateReferenceNo() {
        return date('Ym') . '-' . 'OT' . rand(10000, 1000000);
    }

    private function generateAdditionalCharges() {

        $charges = [
            'Convenience Fee' => 99,
            'Travel Pass' => 50,
        ];

        return $charges;
    }

    private function generateGuidedTourTotalAmount($number_of_pass, $ticket_pass, $amount, $additional_charges) {
        $convenience_fee = $additional_charges['Convenience Fee'] * $number_of_pass;
        $travel_pass = $additional_charges['Travel Pass'] * $number_of_pass;

        return $amount + $convenience_fee + $travel_pass;
    }

    private function generateDIYTourTotalAmount($number_of_pass, $ticket_pass, $amount, $additional_charges) {
        $convenience_fee = $additional_charges['Convenience Fee'] * $number_of_pass;
        $travel_pass = $additional_charges['Travel Pass'] * $number_of_pass;
        // dd($convenience_fee, $travel_pass);
        $totalAmount = 0;
        if($ticket_pass == '1 Day Pass') {
            $totalAmount = ($amount * 1) + ($convenience_fee + $travel_pass);
        }

        if($ticket_pass == '2 Day Pass') {
            $totalAmount = ($amount * 2) + ($convenience_fee + $travel_pass);
        }

        if($ticket_pass == '3 Day Pass') {
            $totalAmount = ($amount * 3) + ($convenience_fee + $travel_pass);
            // dd($request->amount, $convenience_fee, $travel_pass, $totalAmount);
        }
        return $totalAmount;
    }

    private function createTransaction($request, $reference_no, $totalAmount, $additional_charges) {
        $transaction = Transaction::create([
            'reference_no' => $reference_no,
            'transaction_by_id' => $request->reserved_user_id,
            'payment_amount' => $totalAmount,
            'additional_charges' => json_encode($additional_charges),
            'payment_status' => 'pending',
            'resolution_status' => 'pending',
            'order_date' => date('d-m-y'),
            'transaction_date' => date('d-m-y'),
        ]);

        return $transaction;
    }

    private function createReservation($request, $transaction, $totalAmount) {
        $trip_date = Carbon::create($request->trip_date);
        $tour = Tour::where('id', $request->tour_id)->first();

        $reservation = TourReservation::create([
            'tour_id' => $request->tour_id,
            'type' => $request->type,
            'amount' => $totalAmount,
            'reserved_user_id' => $request->reserved_user_id,
            'passenger_ids' => $request->passenger_ids ? json_encode($request->passenger_ids) : json_encode([$request->reserved_user_id]),
            'reference_code' => $transaction->reference_no,
            'order_transaction_id' => $transaction->id,
            'start_date' => $trip_date,
            'end_date' => $request->type == 'Guided' ? $trip_date->addDays(1) : $this->getDateOfDIYPass($request->ticket_pass, $trip_date),
            'status' => 'pending',
            'number_of_pass' => $request->number_of_pass,
            'ticket_pass' => $request->type  == 'DIY' ? $request->ticket_pass : null,
            'referral_code' => $request->referral_code,
        ]);

        if(env('APP_ENVIRONMENT') == 'LIVE') {
            $details = [
                'tour_provider_name' => optional(optional($tour->tour_provider)->merchant)->name,
                'reserved_passenger' => $transaction->user->firstname . ' ' . $transaction->user->lastname,
                'trip_date' => $request->trip_date,
                'tour_name' => $tour->name
            ];

            if($tour) {
                if($tour->tour_provider) {
                    if(optional($tour->tour_provider)->contact_email) {
                        Mail::to(optional($tour->tour_provider)->contact_email)->send(new TourProviderBookingNotification($details));
                        // Mail::to($request->email)->send(new EmailVerification($details));
                    }
                }
            }
        }

        return $reservation;
    }

    private function createMultipleReservation($request, $transaction, $additional_charges) {
        if(is_array($request->items)) {
            $items = $request->items;
        } else {
            $items = json_decode($request->items, true);
        }

        // dd($request->file('requirement'));

        foreach ($items as $key => $item) {
            $trip_date = Carbon::create($item['trip_date']);
            $totalAmount = $item['type'] == 'Guided' ? $this->generateGuidedTourTotalAmount($item['number_of_pass'], $item['ticket_pass'], $item['amount'], $additional_charges) : $this->generateDIYTourTotalAmount($item['number_of_pass'], $item['ticket_pass'], $item['amount'], $additional_charges);

            $tour = Tour::where('id', $item['tour_id'])->first();



            $reservation = TourReservation::create([
                'tour_id' => $item['tour_id'],
                'type' => $item['type'],
                'amount' => $totalAmount,
                'reserved_user_id' => $request->reserved_user_id,
                'passenger_ids' => $request->has('passenger_ids') ? json_encode($request->passenger_ids) : json_encode([$request->reserved_user_id]),
                'reference_code' => $transaction->reference_no,
                'order_transaction_id' => $transaction->id,
                'start_date' => $trip_date,
                'end_date' => $item['type'] == 'Guided' ? $trip_date->addDays(1) : $this->getDateOfDIYPass($item['ticket_pass'], $trip_date),
                'status' => 'pending',
                'number_of_pass' => $item['number_of_pass'],
                'ticket_pass' => $item['type']  == 'DIY' ? $item['ticket_pass'] : null,
                'promo_code' => $request->promo_code,
                'requirement_file_path' => null,
                'discount_amount' => isset($item['discount']) ? $item['discount'] : null
            ]);

            if($request->has('requirement')  && $request->file('requirement')->isValid()) {
                $file = $request->file('requirement');
                $file_name = Str::random(7) . '.' . $file->getClientOriginalExtension();
                $save_file = $file->move(public_path() . '/assets/img/tour_reservations/requirements/' . $reservation->id, $file_name);
            } else {
                $file_name = null;
            }


            $details = [
                'tour_provider_name' => optional($tour->tour_provider)->merchant->name,
                'reserved_passenger' => $transaction->user->firstname . ' ' . $transaction->user->lastname,
                'trip_date' => $item['trip_date'],
                'tour_name' => $tour->name
            ];

            if($tour) {
                if($tour->tour_provider) {
                    if($tour->tour_provider->contact_email) {
                        Mail::to('james@godesq.com')->send(new TourProviderBookingNotification($details));
                        // Mail::to($request->email)->send(new EmailVerification($details));
                    }
                }
            }
        }
    }

    private function sendPaymentRequest($transaction) {
        try {
            $client = new Client();
            $requestModel = $this->setRequestModel($transaction);
            $jsonPayload = json_encode($requestModel, JSON_UNESCAPED_UNICODE);


            if(env('APP_ENVIRONMENT') == 'LIVE') {
                $url_create = 'https://payments.aqwire.io/api/v3/transactions/create';
                $authToken = $this->getLiveHMACSignatureHash(env('AQWIRE_MERCHANT_CODE') . ':' . env('AQWIRE_MERCHANT_CLIENTID'), env('AQWIRE_MERCHANT_SECURITY_KEY'));
            } else{
                $url_create = 'https://payments-sandbox.aqwire.io/api/v3/transactions/create';
                $authToken = $this->getHMACSignatureHash(env('AQWIRE_MERCHANT_CODE') . ':' . env('AQWIRE_MERCHANT_CLIENTID'), env('AQWIRE_MERCHANT_SECURITY_KEY'));
            }

            $response = $client->post($url_create, [
                'headers' => [
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'Qw-Merchant-Id' => env('AQWIRE_MERCHANT_CODE'),
                    'Authorization' => 'Bearer ' . $authToken,
                ],
                'body' => $jsonPayload, // Set the JSON payload as the request body
            ]);

            // Handle the response here
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            // dd($response);

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

    private function setRequestModel($transaction) {
        $model = [
            'uniqueId' => $transaction->reference_no,
            'currency' => 'PHP',
            'paymentType' => 'DTP',
            'amount' => $transaction->payment_amount,
            'description' => 'Payment for Hoho Reservation',
            'customer' => [
                'name' => $transaction->user->firstname . ' ' . $transaction->user->lastname,
                'email' => $transaction->user->email,
                'mobile' => $transaction->user->contact_no ? '+' . $transaction->user->contact_no : '+639633987953',
            ],
            'project' => [
                'name' => 'Hoho Checkout Reservation',
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
                'Travel Pass' => '50.00' . ' ' . 'Per Pax',
                'Amount' => number_format($transaction->payment_amount, 2),
                'agentName' => $transaction->user->firstname . ' ' . $transaction->user->lastname
            ]
        ];

        return $model;
    }

    private function updateTransactionAfterPayment($transaction, $responseData, $additional_charges) {
        $update_transaction = $transaction->update([
            'aqwire_transactionId' => $responseData['data']['transactionId'],
            'payment_url' => $responseData['paymentUrl'],
            'payment_status' => Str::lower($responseData['data']['status']),
            'payment_details' => json_encode($responseData),
            'additional_charges' => json_encode($additional_charges)
        ]);

        return $update_transaction;
    }

    private function getDateOfDIYPass($ticket_pass, $trip_date) {
        if($ticket_pass == '1 Day Pass') {
            $date = $trip_date->addDays(1);
        } else if($ticket_pass == '2 Day Pass') {
            $date = $trip_date->addDays(2);
        } else if($ticket_pass == '3 Day Pass') {
            $date = $trip_date->addDays(3);
        } else {
            $date = $trip_date->addDays(1);
        }

        return $date;
    }

}
