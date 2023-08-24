<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use GuzzleHttp\Client;

use App\Models\TourReservation;
use App\Models\User;
use App\Models\Tour;
use App\Models\Transaction;

use DataTables;
use Carbon\Carbon;

class TourReservationController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = TourReservation::latest()->with('user', 'tour');
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('reserved_user', function($row) {
                        return $row->user->email;
                    })
                    ->addColumn('type', function($row) {
                        return $row->tour->type;
                    })
                    ->addColumn('tour', function($row) {
                        return $row->tour->name;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin/tour_reservations/edit/' .$row->id. '">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item remove-btn" href="javascript:void(0);" id="' .$row->id. '">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.tour_reservations.list-tour-reservation');
    }

    public function create(Request $request) {
        $diy_tours = Tour::where('type', 'DIY Tour')->get();
        $guided_tours = Tour::where('type', 'Guided Tour')->limit(50)->get();
        $tours = Tour::get();
        return view('admin-page.tour_reservations.create-tour-reservation', compact('diy_tours', 'guided_tours', 'tours'));
    }

    public function store(Request $request) {
        $reference_no = $this->generateReferenceNo();
        $additional_charges = $this->generateAdditionalCharges();
        $totalAmount = $request->type == 'Guided' ? $this->generateGuidedTourTotalAmount($request, $additional_charges) : $this->generateDIYTourTotalAmount($request, $additional_charges);

        // 1st Step: We need to store the following data in transactions.
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

        if(!$transaction) return back()->with('fail', 'Failed to Create Transaction');

        // 2nd Step: We need to store the data in reservations.
        $trip_date = Carbon::create($request->trip_date);

        $reservation = TourReservation::create([
            'tour_id' => $request->tour_id,
            'type' => $request->type,
            'amount' => $totalAmount,
            'reserved_user_id' => $request->reserved_user_id,
            'passenger_ids' => json_encode($request->passenger_ids),
            'reference_code' => $transaction->reference_no,
            'order_transaction_id' => $transaction->id,
            'start_date' => $trip_date,
            'end_date' => $request->type == 'Guided' ? $trip_date->addDays(1) : $this->getDateOfDIYPass($request, $trip_date),
            'status' => 'pending',
            'number_of_pass' => $request->number_of_pass,
            'ticket_pass' => $request->type  == 'DIY' ? $request->ticket_pass : null
        ]);

        if(!$reservation) {
            $transaction->delete();
            return back()->with('fail', 'Failed to Create Reservation');
        }

        $client = new Client(['verify' => 'C:\wamp64\bin\php\php8.0.26\extras\ssl\cacert.pem']);
        $requestModel = $this->setRequestModel($transaction, $reservation);

        $jsonPayload = json_encode($requestModel, JSON_UNESCAPED_UNICODE);
        $authToken = $this->GetHMACSignatureHash(env('AQWIRE_MERCHANT_CODE') . ':' . env('AQWIRE_MERCHANT_CLIENTID'), env('AQWIRE_MERCHANT_SECURITY_KEY'));

        $response = $client->post('https://payments-sandbox.aqwire.io/api/v3/transactions/create', [
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'Qw-Merchant-Id' => env('AQWIRE_MERCHANT_CODE'),
                'Authorization' => 'Bearer ' . $authToken,
            ],
            'body' => $jsonPayload, // Set the JSON payload as the request body
        ]);

        // Process the response as needed
        $responseData = json_decode($response->getBody(), true);

        // dd($httpResponse);
        if ($responseData['status'] != 'SUCCESS') {
            // $errorMessage = $responseData->getErrorMessage(); // Make sure to retrieve the actual error message from your response object
            $logMessage = "An error occurred during the payment process with the following parameters: " .
                          "{env('AQWIRE_MERCHANT_CODE)} | {env('AQWIRE_MERCHANT_CLIENTID')} | {env('AQWIRE_MERCHANT_SECURITY_KEY')}";
            dd($logMessage);
        }

        $update_transaction = $transaction->update([
            'aqwire_transactionId' => $responseData['data']['transactionId'],
            'payment_url' => $responseData['paymentUrl'],
            'payment_status' => Str::lower($responseData['data']['status']),
            'payment_details' => json_encode($responseData),
            'additional_charges' => json_encode($additional_charges)
        ]);

        return redirect($responseData['paymentUrl']);
    }

    public function edit(Request $request) {

    }

    public function update(Request $request) {

    }

    public function destroy(Request $request) {

    }

    # HELPER

    private function getHMACSignatureHash($text, $secret_key) {
        $key = $secret_key;
        $message = $text;

        $hex = hash_hmac('sha256', $message, $key);
        $bin = hex2bin($hex);

        return base64_encode($bin);
    }

    private function generateReferenceNo() {
        return date('Ym') . '_' . 'OT' . rand(10000, 1000000);
    }

    private function generateAdditionalCharges() {

        $charges = [
            'Convenience Fee' => 99,
            'Travel Pass' => 50,
        ];

        return $charges;
    }

    private function generateGuidedTourTotalAmount($request, $additional_charges) {
        $convenience_fee = $additional_charges['Convenience Fee'] * $request->number_of_pass;
        $travel_pass = $additional_charges['Travel Pass'] * $request->number_of_pass;

        return $request->amount + $convenience_fee + $travel_pass;
    }

    private function generateDIYTourTotalAmount($request, $additional_charges) {
        $convenience_fee = $additional_charges['Convenience Fee'] * $request->number_of_pass;
        $travel_pass = $additional_charges['Travel Pass'] * $request->number_of_pass;

        $totalAmount = 0;
        if($request->ticket_pass == '1 Day Pass') {
            $totalAmount = $request->amount + $convenience_fee + $travel_pass;
        }

        if($request->ticket_pass == '2 Day Pass') {
            $totalAmount = $request->amount + $convenience_fee + $travel_pass;
        }

        if($request->ticket_pass == '3 Day Pass') {
            $totalAmount = $request->amount + $convenience_fee + $travel_pass;
        }

        return $totalAmount;
    }

    private function getDateOfDIYPass($request, $trip_date) {
        if($request->ticket_pass == '1 Day Pass') {
            $date = $trip_date->addDays(1);
        }

        if($request->ticket_pass == '2 Day Pass') {
            $date = $trip_date->addDays(2);
        }

        if($request->ticket_pass == '3 Day Pass') {
            $date = $trip_date->addDays(3);
        }

        return $date;
    }

    private function setRequestModel($transaction, $reservation) {
        $model = [
            'uniqueId' => $transaction->reference_no,
            'currency' => 'PHP',
            'paymentType' => 'DTP',
            'amount' => $transaction->payment_amount,
            'description' => 'Payment for Hoho Reservation',
            'customer' => [
                'name' => $transaction->user->firstname . ' ' . $transaction->user->lastname,
                'email' => $transaction->user->email,
                'mobile' => '+639633987953',
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
            'note' => 'Test Payment',
            'metadata' => [
                'reservationNumber' => json_encode($reservation->id),
                'companyCode' => '1000',
                'projectCode' => '4200',
                'agentName' => Auth::guard('admin')->user()->username
            ]
        ];

        return $model;
    }
}
