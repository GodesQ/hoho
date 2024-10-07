<?php

namespace App\Console\Commands;

use App\Enum\TransactionTypeEnum;
use App\Mail\BookingConfirmationMail;
use App\Mail\HotelReservationReceipt;
use App\Mail\InvoiceMail;
use App\Models\HotelReservation;
use App\Models\Order;
use App\Models\ReservationUserCode;
use App\Models\TourReservation;
use App\Models\Transaction;
use App\Models\TravelTaxPayment;
use App\Services\SenangdaliService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckAqwireTransaction extends Command
{
    private $tourReservationService;
    private $senangdaliService;

    // public function __construct(TourReservationService $tourReservationService, SenangdaliService $senangdaliService)
    // {
    //     $this->tourReservationService = $tourReservationService;
    //     $this->senangdaliService = $senangdaliService;
    // }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-aqwire-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Aqwire Transaction';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $today = Carbon::today();
        $transactions = Transaction::where('payment_status', 'inc')
            ->whereDate('created_at', $today)
            ->get();

        if (config('app.env') === 'production') {
            $url = 'https://payments.aqwire.io/api/v3/transactions/check';
            $authToken = $this->getLiveHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
        } else {
            $url = 'https://payments-sandbox.aqwire.io/api/v3/transactions/check';
            $authToken = $this->getHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
        }

        foreach ($transactions as $transaction) {
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

        return Command::SUCCESS;

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

        $travel_tax_payment->update([
            'payment_method' => $transaction->aqwire_paymentMethodCode,
            'status' => 'paid',
        ]);
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
        $hotel_reservation = HotelReservation::where('transaction_id', $transaction->id)->with('reserved_user', 'room.merchant', 'transaction')->firstOrFail();
        $hotel_reservation->update([
            'payment_status' => 'paid',
        ]);

        $details = ['reservation' => $hotel_reservation];

        Mail::to($hotel_reservation->reserved_user->email)->send(new HotelReservationReceipt($details));
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
}
