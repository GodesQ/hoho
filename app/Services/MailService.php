<?php
namespace App\Services;

use App\Mail\PaymentRequestMail;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendPaymentRequestMail($transaction, $payment_url, $payment_expiration)
    {   
        $payment_request_details = [
            'transaction_by' => $transaction->user->firstname . ' ' . $transaction->user->lastname,
            'reference_no' => $transaction->reference_no,
            'total_additional_charges' => $transaction->total_additional_charges,
            'sub_amount' => $transaction->sub_amount,
            'total_amount' => $transaction->payment_amount,
            'payment_url' => $payment_url,
            'payment_expiration' => $payment_expiration ?? null,
        ];

        Mail::to($transaction->user->email)->send(new PaymentRequestMail($payment_request_details));
    }

    public function sendTourProviderBookingNotificationMail() {

    }
}
