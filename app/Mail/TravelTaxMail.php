<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TravelTaxMail extends Mailable
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
        $email = $this->subject('Travel Tax')->view('emails.travel-tax-email', compact('details'));

        if ($this->pdf) {
            $email->attachData($this->pdf->output(), 'travel_tax.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
