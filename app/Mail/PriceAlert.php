<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PriceAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $text;
    public $title;
    public $trip_option;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($text, $title, $trip_option)
    {
        $this->text = $text;
        $this->title = $title;
        $this->trip_option = $trip_option;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Atualização de preços: $this->title")
                ->view('emails.price_alert');
    }
}
