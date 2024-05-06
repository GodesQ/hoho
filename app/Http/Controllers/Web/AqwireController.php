<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TravelTaxPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

use App\Models\Transaction;
use App\Models\TourReservation;

use Carbon\Carbon;
class AqwireController extends Controller
{
    public function success(Request $request) {
        $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();

        $update_transaction = $transaction->update([
            'aqwire_referenceId' => $request->referenceId,
            'aqwire_paymentMethodCode' => $request->paymentMethodCode,
            'aqwire_totalAmount' => $request->totalAmount,
            'payment_status' => Str::lower('success'),
            'payment_date' => Carbon::now()
        ]);

        $reservations = TourReservation::where('order_transaction_id', $transaction->id)->with('tour', 'user', 'customer_details')->get();

        foreach ($reservations as $key => $reservation) {
            $reservation->update([
                'payment_method' => $request->paymentMethodCode
            ]);

            $details = [
                'tour' => $reservation->tour,
                'reservation' => $reservation,
                'transaction' => $transaction
            ];

            Mail::to(optional($reservation->customer_details)->email)->send(new InvoiceMail($details));
        }

        if($update_transaction) {
            return redirect('aqwire/payment/view_success');
        }
    }

    public function travelTaxSuccess(Request $request) {
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

        return redirect('aqwire/payment/view_success');
    }

    public function viewSuccess(Request $request) {
        return view('misc.transaction_messages.success');
    }

    public function cancel(Request $request) {
        $transaction = Transaction::where('aqwire_transactionId', $request->transactionId)->firstOrFail();
        $reservation = TourReservation::where('order_transaction_id', $transaction->id)->firstOrFail();

        $update_transaction = $transaction->update([
            'aqwire_referenceId' => $request->referenceId,
            'aqwire_totalAmount' => $request->totalAmount,
            'payment_status' => Str::lower('cancelled')
        ]);

        $reservation = $reservation->update([
            'status' => Str::lower('failed')
        ]);

        if($update_transaction) {
            return redirect('aqwire/payment/view_cancel');
        }
    }

    public function travelTaxCancel(Request $request) {
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

    public function viewCancel(Request $request) {
        return view('misc.transaction_messages.cancel');
    }

    public function callback(Request $request) {
        dd($request->id, $request->all());
    }
}
