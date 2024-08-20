<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\HotelReservationReceipt;
use App\Models\HotelReservation;
use App\Models\Order;
use App\Models\TravelTaxPayment;
use App\Services\TourReservationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

use App\Models\Transaction;
use App\Models\TourReservation;

use Carbon\Carbon;

class AqwireController extends Controller
{   
    private $tourReservationService;
    public function __construct(TourReservationService $tourReservationService) {
        $this->tourReservationService = $tourReservationService;
    }

    public function success(Request $request)
    {   try {
            DB::beginTransaction();

            $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

            $transaction->update([
                'aqwire_referenceId' => $request->referenceId,
                'aqwire_paymentMethodCode' => $request->paymentMethodCode,
                'aqwire_totalAmount' => $request->totalAmount,
                'payment_status' => Str::lower('success'),
                'payment_date' => Carbon::now()
            ]);

            $reservations = TourReservation::where('order_transaction_id', $transaction->id)->with('tour', 'user', 'customer_details')->get();

            foreach ($reservations as $reservation) {
                $reservation->update([
                    'payment_method' => $request->paymentMethodCode
                ]);

                $details = [
                    'tour' => $reservation->tour,
                    'reservation' => $reservation,
                    'transaction' => $transaction
                ];

                Mail::to(optional($reservation->customer_details)->email)->send(new InvoiceMail($details));

                $this->tourReservationService->generateAndSendReservationCode($reservation->number_of_pass, $reservation);
            }

            DB::commit();

            return redirect('aqwire/payment/view_success');
        } catch (Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function travelTaxSuccess(Request $request)
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

            $travel_tax_payment = TravelTaxPayment::where('transaction_id', $transaction->id)->first();

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

    public function webhook_paid(Request $request)
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo (json_encode(
                array(
                    'message' => 'Invalid request method'
                )
            ));
            http_response_code(401);
            exit();
        }

        $signature = $_GET['sign'];
        $json = file_get_contents('php://input');
        $merchantSecretKey = "sk_test_vV6i66irj2vhca4iXpqZc6THFiJz3N6Y";

        $rawSignature = hash_hmac('sha256', $json, $merchantSecretKey, true);
        $computedSignature = strtr(base64_encode($rawSignature), '+/', '-_');

        if ($signature !== $computedSignature) {
            echo (json_encode(
                array(
                    'message' => 'Unauthorized API call'
                )
            ));
            http_response_code(401);
            exit();
        }

        echo (json_encode(
            array(
                'sign' => $signature
            )
        ));
        echo (json_encode(
            array(
                'verify' => $computedSignature
            )
        ));
        echo (json_encode(
            array(
                'message' => 'Data posted'
            )
        ));
        http_response_code(200);
    }
}
