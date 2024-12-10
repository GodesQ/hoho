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
use App\Services\AqwireService;
use App\Services\SenangdaliService;
use App\Services\TourReservationService;
use App\Services\TravelTaxService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Log;
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
        try
        {
            DB::beginTransaction();

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

            $this->fetchAndUpdateAqwireTransaction($transaction);

            DB::commit();

            return redirect('aqwire/payment/view_success');
        } catch (Exception $e)
        {
            DB::rollBack();
            abort(500);
        }
    }

    public function travelTaxSuccess(Request $request)
    {
        try
        {
            ini_set('max_execution_time', 300); // Increase execution time
            ini_set('memory_limit', '256M'); // Optional: increase memory limit

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

            $this->fetchAndUpdateAqwireTransaction($transaction);

            return redirect('aqwire/payment/view_success');

        } catch (Exception $exception)
        {
            if (config('app.debug'))
            {
                dd($exception);
            }
            abort(500);
        }
    }

    public function orderSuccess(Request $request)
    {
        try
        {
            DB::beginTransaction();

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

            $this->fetchAndUpdateAqwireTransaction($transaction);

            DB::commit();

            return redirect('aqwire/payment/view_success');
        } catch (Exception $e)
        {
            DB::rollBack();
            abort(500);
        }
    }

    public function hotelReservationSuccess(Request $request)
    {
        try
        {
            DB::beginTransaction();

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

            $this->fetchAndUpdateAqwireTransaction($transaction);

            DB::commit();

            return redirect('aqwire/payment/view_success');
        } catch (Exception $e)
        {
            DB::rollBack();
            abort(500);
        }
    }

    public function hotelReservationCancel(Request $request)
    {
        try
        {
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
        } catch (\Throwable $th)
        {
            DB::rollBack();
            abort(500);
        }
    }

    public function orderCancel(Request $request)
    {
        try
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
        } catch (Exception $e)
        {
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
        try
        {
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

        } catch (Exception $e)
        {
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


    public function checkAuthorizationCode(Request $request)
    {


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

        foreach ($transactions as $transaction)
        {
            $this->fetchAndUpdateAqwireTransaction($transaction);
        }

        return "Ok";
    }

    private function fetchAndUpdateAqwireTransaction($transaction)
    {
        try
        {
            if (config('app.env') === 'production')
            {
                $url = 'https://payments.aqwire.io/api/v3/transactions/check';
                $authToken = $this->getLiveHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
            } else
            {
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

            if ($response->successful())
            {
                DB::transaction(function () use ($transaction, $response) {
                    $jsonData = $response->json();

                    $payment_provider_fee = $jsonData['data']['bill']['fee']['amount'] ?? 0;

                    // Update the payment status, converting it to lowercase
                    $transaction->payment_status = isset($jsonData['status']) ? Str::lower($jsonData['status']) : 'inc';

                    // Update payment provider fee with a default of 0 if it doesn't exist
                    $transaction->payment_provider_fee = $payment_provider_fee;

                    // Set payment method code, falling back to the existing code if not provided
                    $transaction->aqwire_paymentMethodCode = $jsonData['data']['paymentMethod'] ?? $transaction->aqwire_paymentMethodCode;

                    // Set the total amount, falling back to the existing total amount if not provided
                    $transaction->aqwire_totalAmount = $jsonData['data']['bill']['total']['amount'] ?? $transaction->aqwire_totalAmount;

                    // Set the reference ID, falling back to the existing reference ID if not provided
                    $transaction->aqwire_referenceId = $jsonData['data']['referenceId'] ?? $transaction->aqwire_referenceId;

                    // Parse and set the payment date if it exists
                    $transaction->payment_date = isset($jsonData['data']['paidAt']) ? Carbon::parse($jsonData['data']['paidAt'])->format('Y-m-d') : null;

                    // Encode all data as JSON for the payment details
                    $transaction->payment_details = json_encode($jsonData);

                    // Compute the total amount with payment provider fee
                    $transaction->total_amount += $payment_provider_fee;

                    // Save the transaction
                    $transaction->save();
                });

                if ($transaction->transaction_type == TransactionTypeEnum::BOOK_TOUR)
                {
                    $this->reservationsUpdated($transaction);
                }

                if ($transaction->transaction_type == TransactionTypeEnum::TRAVEL_TAX)
                {
                    $this->travelTaxUpdated($transaction);
                }

                if ($transaction->transaction_type == TransactionTypeEnum::ORDER)
                {
                    $this->orderUpdated($transaction);
                }

                if ($transaction->transaction_type == TransactionTypeEnum::HOTEL_RESERVATION)
                {
                    $this->hotelReservationUpdated($transaction);
                }

            }

        } catch (Exception $exception)
        {
            throw $exception;
        }

    }

    public function reservationsUpdated($transaction)
    {
        $reservations = TourReservation::where('order_transaction_id', $transaction->id)->with('tour', 'user', 'customer_details')->get();

        foreach ($reservations as $reservation)
        {
            $reservation->update([
                'payment_method' => $transaction->aqwire_paymentMethodCode
            ]);

            $details = [
                'tour' => $reservation->tour,
                'reservation' => $reservation,
                'transaction' => $transaction
            ];

            $user = $reservation->user ?? $reservation->customer_details;

            Mail::to(optional($reservation->customer_details)->email)->send(new InvoiceMail($details));

            $this->generateAndSendReservationCode($reservation->number_of_pass, $reservation);

            if ($reservation->has_insurance)
            {
                $senangdaliService = new SenangdaliService();
                $senangdali_insurance_request = $senangdaliService->__map_request_model($user, $reservation);
                $senangdaliService->purchasing($senangdali_insurance_request);
            }
        }
    }

    public function travelTaxUpdated($transaction)
    {
        $travel_tax_payment = TravelTaxPayment::where('transaction_id', $transaction->id)
            ->with('primary_passenger')->first();

        $travel_tax_payment->update([
            'payment_method' => $transaction->aqwire_paymentMethodCode,
            'status' => 'paid',
        ]);

        $data = $travel_tax_payment->load('passengers', 'transaction')->toArray();

        $travel_tax_qrcode_value = "AR No.: {$travel_tax_payment->ar_number}\n";
        $travel_tax_qrcode_value .= "Transaction No.: {$travel_tax_payment->transaction_number}\n";
        $travel_tax_qrcode_value .= "Reference No.: {$travel_tax_payment->reference_number}\n\n";

        foreach ($travel_tax_payment->passengers as $passenger)
        {
            $name = trim($passenger->firstname . ' ' . $passenger->middlename . ' ' . $passenger->lastname . ($passenger->suffix ? ' ' . $passenger->suffix : ''));
            $travel_tax_qrcode_value .= "Name of Applicant: {$name}\n";
            $travel_tax_qrcode_value .= "Ticket Number: {$passenger->ticket_number}\n\n";
        }

        $qrcode = base64_encode(QrCode::format('svg')->size(180)->errorCorrection('L')->generate($travel_tax_qrcode_value));

        $pdf = PDF::loadView('pdf.travel-tax', ['data' => $data, 'qrcode' => $qrcode]);

        Mail::to($travel_tax_payment->primary_passenger->email_address)->send(new TravelTaxMail($travel_tax_payment, $pdf));

        $travelTaxService = new TravelTaxService(new AqwireService());
        $travelTaxService->sendTravelTaxAPI($travel_tax_payment, $transaction, $travel_tax_payment->primary_passenger);
    }

    public function orderUpdated($transaction)
    {
        $order = Order::where('transaction_id', $transaction->id)->first();

        $order->update([
            'payment_method' => $transaction->aqwire_paymentMethodCode,
            'status' => 'paid',
        ]);
    }

    public function hotelReservationUpdated($transaction)
    {
        $hotel_reservation = HotelReservation::where('transaction_id', $transaction->id)
            ->with('reserved_user', 'room.merchant', 'transaction')
            ->firstOrFail();

        $hotel_reservation->update([
            'payment_status' => 'paid',
        ]);

        $details = [
            'reservation' => $hotel_reservation,
        ];

        Mail::to($hotel_reservation->reserved_user->email)->send(new HotelReservationReceipt($details));
    }

    public function generateAndSendReservationCode($number_of_pax, $reservation)
    {
        try
        {
            $reservations_codes = $this->generateReservationCode($number_of_pax, $reservation);

            if ($reservation->customer_details)
            {
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

                if ($reservation->type == 'DIY Tour' || $reservation->type == 'DIY')
                {
                    $qrCodes = [];
                    foreach ($reservations_codes as $code)
                    {
                        $value = $code . "&" . $reservation->id;
                        $qrCodes[] = base64_encode(QrCode::format('svg')->size(250)->errorCorrection('H')->generate($value));
                    }
                    $pdf = PDF::loadView('pdf.qrcodes', ['qrCodes' => $qrCodes]);
                }

                Mail::to(optional($reservation->customer_details)->email)->send(new BookingConfirmationMail($details, $pdf));

                return $reservations_codes;
            }
        } catch (Exception $e)
        {
            throw $e;
        }
    }

    private function generateReservationCode($number_of_pass, $reservation)
    {
        // Generate the random letter part
        // Assuming you have str_random function available
        $random_letters = strtoupper(Str::random(5));
        $reservation_codes = [];

        for ($i = 1; $i <= $number_of_pass; $i++)
        {
            // Generate the pass number with leading zeros (e.g., -001)
            $pass_number = str_pad($i, 3, '0', STR_PAD_LEFT);

            // Concatenate the parts to create the code
            $code = "GRP{$random_letters}{$reservation->id}-{$pass_number}";

            $reservation_codes_exist = ReservationUserCode::where('reservation_id', $reservation->id)->count();

            if ($reservation_codes_exist < $number_of_pass)
            {
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
