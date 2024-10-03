<?php

namespace App\Console\Commands;

use App\Enum\TransactionTypeEnum;
use App\Mail\HotelReservationReceipt;
use App\Mail\InvoiceMail;
use App\Models\HotelReservation;
use App\Models\Order;
use App\Models\TourReservation;
use App\Models\Transaction;
use App\Models\TravelTaxPayment;
use App\Services\SenangdaliService;
use App\Services\TourReservationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckAqwireTransaction extends Command
{
    private $tourReservationService;
    private $senangdaliService;

    public function __construct(TourReservationService $tourReservationService, SenangdaliService $senangdaliService)
    {
        $this->tourReservationService = $tourReservationService;
        $this->senangdaliService = $senangdaliService;
    }

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
                $transaction->aqwire_totalAmount = $data['data']['total']['amount'];
                $transaction->aqwire_referenceId = $data['data']['referenceId'];
                $transaction->payment_date = $data['data']['paidAt'];
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

            $this->tourReservationService->generateAndSendReservationCode($reservation->number_of_pass, $reservation);

            if ($reservation->has_insurance) {
                $senangdali_insurance_request = $this->senangdaliService->__map_request_model($transaction->user, $reservation);
                $this->senangdaliService->purchasing($senangdali_insurance_request);
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
}
