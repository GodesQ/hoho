<?php

namespace App\Http\Controllers\Web;

use App\Enum\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmationMail;
use App\Mail\HotelReservationReceipt;
use App\Mail\TravelTaxMail;
use App\Models\HotelReservation;
use App\Models\Order;
use App\Models\ReservationUserCode;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use App\Services\SenangdaliService;
use App\Services\TourReservationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Transaction;
use App\Models\TourReservation;

use Carbon\Carbon;

class AqwireController extends Controller
{
    private $tourReservationService;
    private $senangdaliService;
    public function __construct(TourReservationService $tourReservationService, SenangdaliService $senangdaliService)
    {
        $this->tourReservationService = $tourReservationService;
        $this->senangdaliService = $senangdaliService;
    }

    // public function handlePostWebhookPaid(Request $request)
    // {
    //     // Check if the request method is POST
    //     if ($request->isMethod('post')) {
    //         // Retrieve the signature from the query string
    //         $signature = $request->query('sign');

    //         // Get the JSON payload
    //         $json = $request->getContent();

    //         // Get the secret key from the environment
    //         // $merchantSecretKey = env('AQWIRE_MERCHANT_SECURITY_KEY');
    //         $merchantSecretKey = "sk_test_vV6i66irj2vhca4iXpqZc6THFiJz3N6Y";

    //         // Compute the signature
    //         $rawSignature = hash_hmac('sha256', $json, $merchantSecretKey, true);
    //         $computedSignature = strtr(base64_encode($rawSignature), '+/', '-_');

    //         // Validate the signature
    //         if ($signature !== $computedSignature) {
    //             return response()->json([
    //                 'message' => 'Unauthorized API call'
    //             ], 401);
    //         }

    //         // Return the signature and verification for debugging purposes
    //         return response()->json([
    //             'sign' => $signature,
    //             'verify' => $computedSignature,
    //             'message' => 'Data posted'
    //         ], 200);
    //     }

    //     // If the request method is not POST, return an error
    //     return response()->json([
    //         'message' => 'Invalid request method'
    //     ], 401);
    // }

    public function success(Request $request)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

            $this->fetchAndUpdateAqwireTransaction($transaction);

            DB::commit();

            return redirect('aqwire/payment/view_success');
        } catch (Exception $e) {
            DB::rollBack();

            dd($e);
            abort(500);
        }
    }

    public function travelTaxSuccess(Request $request)
    {
        try {
            ini_set('max_execution_time', 300); // Increase execution time
            ini_set('memory_limit', '256M'); // Optional: increase memory limit

            DB::beginTransaction();

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

            $this->fetchAndUpdateAqwireTransaction($transaction);

            DB::commit();

            return redirect('aqwire/payment/view_success');

        } catch (Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function orderSuccess(Request $request)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

            $transaction->update([
                'aqwire_referenceId' => $request->referenceId,
                'aqwire_paymentMethodCode' => $request->paymentMethodCode,
                'aqwire_totalAmount' => $request->totalAmount,
                'payment_status' => Str::lower('success'),
                'payment_date' => Carbon::now()
            ]);

            $travel_tax_payment = Order::where('transaction_id', $transaction->id)->first();

            $travel_tax_payment->update([
                'payment_method' => $request->paymentMethodCode,
                'status' => 'paid',
            ]);

            DB::commit();

            return redirect('aqwire/payment/view_success');
        } catch (Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function hotelReservationSuccess(Request $request)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();
            $hotel_reservation = HotelReservation::where('transaction_id', $transaction->id)->with('reserved_user', 'room.merchant', 'transaction')->firstOrFail();

            $transaction->update([
                'aqwire_referenceId' => $request->referenceId,
                'aqwire_paymentMethodCode' => $request->paymentMethodCode,
                'aqwire_totalAmount' => $request->totalAmount,
                'payment_status' => Str::lower('success'),
                'payment_date' => Carbon::now()
            ]);

            $hotel_reservation->update([
                'payment_status' => 'paid',
            ]);

            $details = [
                'reservation' => $hotel_reservation,
            ];

            Mail::to($hotel_reservation->reserved_user->email)->send(new HotelReservationReceipt($details));

            DB::commit();

            return redirect('aqwire/payment/view_success');
        } catch (Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function hotelReservationCancel(Request $request)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();
            $hotel_reservation = HotelReservation::where('transaction_id', $transaction->id)->firstOrFail();

            $transaction->update([
                'aqwire_referenceId' => $request->referenceId,
                'aqwire_paymentMethodCode' => $request->paymentMethodCode,
                'aqwire_totalAmount' => $request->totalAmount,
                'payment_status' => Str::lower('success'),
                'payment_date' => Carbon::now()
            ]);

            $hotel_reservation->update([
                'payment_status' => 'unpaid',
            ]);

            DB::commit();

            return redirect('aqwire/payment/view_cancel');
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(500);
        }
    }

    public function orderCancel(Request $request)
    {
        try {
            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

            $transaction->update([
                'aqwire_referenceId' => $request->referenceId,
                'aqwire_paymentMethodCode' => $request->paymentMethodCode,
                'aqwire_totalAmount' => $request->totalAmount,
                'payment_status' => Str::lower('cancelled'),
                'payment_date' => Carbon::now()
            ]);

            $travel_tax_payment = TravelTaxPayment::where('transaction_id', $transaction->id)->first();

            $travel_tax_payment->update([
                'payment_method' => $request->paymentMethodCode,
                'status' => 'unpaid',
            ]);

            return redirect('aqwire/payment/view_cancel');
        } catch (Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function viewSuccess(Request $request)
    {
        return view('misc.transaction_messages.success');
    }

    public function cancel(Request $request)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();
            $reservation = TourReservation::where('order_transaction_id', $transaction->id)->firstOrFail();

            $transaction->update([
                'aqwire_referenceId' => $request->referenceId,
                'aqwire_totalAmount' => $request->totalAmount,
                'payment_status' => Str::lower('cancelled')
            ]);

            $reservation = $reservation->update([
                'status' => Str::lower('failed')
            ]);

            DB::commit();

            return redirect('aqwire/payment/view_cancel');

        } catch (Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function travelTaxCancel(Request $request)
    {
        $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

        $transaction->update([
            'aqwire_referenceId' => $request->referenceId,
            'aqwire_paymentMethodCode' => $request->paymentMethodCode,
            'aqwire_totalAmount' => $request->totalAmount,
            'payment_status' => Str::lower('cancelled'),
            'payment_date' => Carbon::now()
        ]);

        $travel_tax_payment = TravelTaxPayment::where('transaction_id', $transaction->id)->first();

        $travel_tax_payment->update([
            'payment_method' => $request->paymentMethodCode,
            'status' => 'unpaid',
        ]);

        return redirect('aqwire/payment/view_cancel');
    }

    public function viewCancel(Request $request)
    {
        return view('misc.transaction_messages.cancel');
    }

    public function callback(Request $request)
    {
        dd($request->id, $request->all());
    }

    // public function webhook_paid(Request $request)
    // {
    //     header('Content-Type: application/json');

    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //         echo (json_encode(
    //             array(
    //                 'message' => 'Invalid request method'
    //             )
    //         ));
    //         http_response_code(401);
    //         exit();
    //     }

    //     $signature = $_GET['sign'];
    //     $json = file_get_contents('php://input');
    //     $merchantSecretKey = "sk_test_vV6i66irj2vhca4iXpqZc6THFiJz3N6Y";

    //     $rawSignature = hash_hmac('sha256', $json, $merchantSecretKey, true);
    //     $computedSignature = strtr(base64_encode($rawSignature), '+/', '-_');

    //     if ($signature !== $computedSignature) {
    //         echo (json_encode(
    //             array(
    //                 'message' => 'Unauthorized API call'
    //             )
    //         ));
    //         http_response_code(401);
    //         exit();
    //     }

    //     echo (json_encode(
    //         array(
    //             'sign' => $signature
    //         )
    //     ));
    //     echo (json_encode(
    //         array(
    //             'verify' => $computedSignature
    //         )
    //     ));
    //     echo (json_encode(
    //         array(
    //             'message' => 'Data posted'
    //         )
    //     ));
    //     http_response_code(200);
    // }


    public function checkAuthorizationCode(Request $request)
    {
        // $keyBytes = utf8_encode($key);
        // $textBytes = utf8_encode($text);

        // $hashBytes = hash_hmac('sha256', $textBytes, $keyBytes, true);

        // $base64Hash = base64_encode($hashBytes);
        // $base64Hash = str_replace(['+', '/'], ['-', '_'], $base64Hash);

        // return $base64Hash;

        $key = config('services.aqwire.secret_key');
        $message = config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id');

        $hex = hash_hmac('sha256', $message, $key);
        $bin = hex2bin($hex);

        return base64_encode($bin);
    }

    public function checkSingleTransaction()
    {
        $today = Carbon::today();
        $transaction = Transaction::where('payment_status', 'inc')
            ->whereDate('created_at', $today)
            ->first();

        $this->fetchAndUpdateAqwireTransaction($transaction);

        return "Ok";
    }

    public function checkTransactions(Request $request)
    {
        $today = Carbon::today();
        $transactions = Transaction::where('payment_status', 'inc')
            ->whereDate('created_at', $today)
            ->get();

        foreach ($transactions as $transaction) {
            $this->fetchAndUpdateAqwireTransaction($transaction);
        }

        return "Ok";
    }

    private function fetchAndUpdateAqwireTransaction($transaction)
    {
        if (config('app.env') === 'production') {
            $url = 'https://payments.aqwire.io/api/v3/transactions/check';
            $authToken = $this->getLiveHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
        } else {
            $url = 'https://payments-sandbox.aqwire.io/api/v3/transactions/check';
            $authToken = $this->getHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
        }

        $txnId = $transaction->aqwire_transactionId;

        // Send HTTP request to AQWIRE API to check the transaction status
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'Qw-Merchant-Id' => config('services.aqwire.merchant_code'),
            'Authorization' => 'Bearer ' . $authToken,
        ])->get("{$url}/{$txnId}");

        if ($response->successful()) {
            $data = $response->json();

            // Assuming the API response contains a 'status' field
            $transaction->payment_status = Str::lower($data['status']); // Update the status
            $transaction->aqwire_paymentMethodCode = $data['data']['paymentMethod'];
            $transaction->aqwire_totalAmount = $data['data']['bill']['total']['amount'];
            $transaction->aqwire_referenceId = $data['data']['referenceId'];
            $transaction->payment_date = Carbon::parse($data['data']['paidAt'])->format('Y-m-d');
            $transaction->payment_details = json_encode($data);
            $transaction->save();

            if ($transaction->transaction_type == TransactionTypeEnum::BOOK_TOUR) {
                $this->reservationsUpdated($transaction);
            }

            if ($transaction->transaction_type == TransactionTypeEnum::TRAVEL_TAX) {
                $this->travelTaxUpdated($transaction);
            }

            if ($transaction->transaction_type == TransactionTypeEnum::ORDER) {
                $this->orderUpdated($transaction);
            }

            if ($transaction->transaction_type == TransactionTypeEnum::HOTEL_RESERVATION) {
                $this->hotelReservationUpdated($transaction);
            }

        }
    }

    public function reservationsUpdated($transaction)
    {
        $reservations = TourReservation::where('order_transaction_id', $transaction->id)->with('tour', 'user', 'customer_details')->get();

        foreach ($reservations as $reservation) {
            $reservation->update([
                'payment_method' => $transaction->aqwire_paymentMethodCode
            ]);

            $details = [
                'tour' => $reservation->tour,
                'reservation' => $reservation,
                'transaction' => $transaction
            ];

            Mail::to(optional($reservation->customer_details)->email)->send(new InvoiceMail($details));

            $this->generateAndSendReservationCode($reservation->number_of_pass, $reservation);

            if ($reservation->has_insurance) {
                $senangdaliService = new SenangdaliService();
                $senangdali_insurance_request = $senangdaliService->__map_request_model($transaction->user, $reservation);
                $senangdaliService->purchasing($senangdali_insurance_request);
            }
        }
    }

    public function travelTaxUpdated($transaction)
    {
        $travel_tax_payment = TravelTaxPayment::where('transaction_id', $transaction->id)->first();

        $primary_passenger = TravelTaxPassenger::where('payment_id', $travel_tax_payment->id)
            ->where('passenger_type', 'primary')->first();

        $travel_tax_payment->update([
            'payment_method' => $transaction->aqwire_paymentMethodCode,
            'status' => 'paid',
        ]);

        $data = $travel_tax_payment->load('passengers', 'transaction')->toArray();

        $travel_tax_qrcode_value = [
            'transaction_number' => $travel_tax_payment->transaction_number,
            'passengers' => $travel_tax_payment->passengers->map(function ($passenger) {
                return [
                    'name' => trim($passenger->firstname . ' ' . $passenger->lastname . ($passenger->suffix ? ' ' . $passenger->suffix : '')),
                    'ticket_number' => $passenger->ticket_number,
                ];
            })->toArray()  // Convert to an array after mapping
        ];

        $qrcode = base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate(json_encode($travel_tax_qrcode_value)));

        $pdf = PDF::loadView('pdf.travel-tax', ['data' => $data, 'qrcode' => $qrcode]);

        Mail::to($primary_passenger->email_address)->send(new TravelTaxMail($travel_tax_payment, $pdf));
    }

    public function generateAndSendReservationCode($number_of_pax, $reservation)
    {
        try {
            $reservations_codes = $this->generateReservationCode($number_of_pax, $reservation);

            if ($reservation->customer_details) {
                $what = $reservation->type == 'DIY' ? (
                    $reservation->ticket_pass . " x " . $reservation->number_of_pass . " pax " . "(Valid for 24 hours from first tap)"
                )
                    : (
                        "1 Guided Tour " . '"' . $reservation->tour->name . '"' . ' x ' . $reservation->number_of_pass . ' pax'
                    );

                $trip_date = Carbon::parse($reservation->start_date);
                $when = $trip_date->format('l, F j, Y');

                $details = [
                    'name' => $reservation->customer_details->firstname . ' ' . $reservation->customer_details->lastname,
                    'what' => $what,
                    'when' => $when,
                    'where' => 'Robinson’s Manila',
                    'type' => $reservation->type,
                    'tour_name' => optional($reservation->tour)->name
                ];

                $pdf = null;

                if ($reservation->type == 'DIY Tour' || $reservation->type == 'DIY') {
                    $qrCodes = [];
                    foreach ($reservations_codes as $code) {
                        $value = $code . "&" . $reservation->id;
                        $qrCodes[] = base64_encode(QrCode::format('svg')->size(250)->errorCorrection('H')->generate($value));
                    }
                    $pdf = PDF::loadView('pdf.qrcodes', ['qrCodes' => $qrCodes]);
                }

                Mail::to(optional($reservation->customer_details)->email)->send(new BookingConfirmationMail($details, $pdf));

                return $reservations_codes;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function generateReservationCode($number_of_pass, $reservation)
    {
        // Generate the random letter part
        // Assuming you have str_random function available
        $random_letters = strtoupper(Str::random(5));
        $reservation_codes = [];

        for ($i = 1; $i <= $number_of_pass; $i++) {
            // Generate the pass number with leading zeros (e.g., -001)
            $pass_number = str_pad($i, 3, '0', STR_PAD_LEFT);

            // Concatenate the parts to create the code
            $code = "GRP{$random_letters}{$reservation->id}-{$pass_number}";

            $reservation_codes_exist = ReservationUserCode::where('reservation_id', $reservation->id)->count();

            if ($reservation_codes_exist < $number_of_pass) {
                $create_code = ReservationUserCode::create([
                    'reservation_id' => $reservation->id,
                    'code' => $code
                ]);

                array_push($reservation_codes, $create_code->code);
            }
        }

        return $reservation_codes;
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
