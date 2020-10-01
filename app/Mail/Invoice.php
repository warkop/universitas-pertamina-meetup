<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Invoice extends Mailable
{
    use Queueable, SerializesModels;

    public $dataPayment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dataPayment)
    {
        $this->dataPayment = $dataPayment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('meetup@universitas-pertamina.co.id')->view('emails.invoice', $this->dataPayment);
    }
}
