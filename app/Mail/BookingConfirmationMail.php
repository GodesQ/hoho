<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     protected $details;
     protected $pdf;
    public function __construct($details, $pdf)
    {
        $this->details = $details;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $details = $this->details;
        $email = $this->subject('Booking Confirmation For Passenger' . ' - ' . $details['tour_name'])->view('emails.booking-confirmation', compact('details'));

        if($details['type'] == 'DIY') {
            $email->attachData($this->pdf->output(), 'qrcodes.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
