<?php

namespace App\Mails\Manual;
use App\StoreWebsite;

use App\Tickets;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PriceDropNotif extends Mailable
{
    use Queueable, SerializesModels;

    private $ticket;

    /**
     * Create a new message instance.
     *
     * @param  mixed  $ticket
     * @return void
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $from = 'contact@sololuxury.co.in';
        $name = $this->ticket->source_of_ticket;

        $ticketCustomer = Tickets::find($this->ticket->id);
        if($ticketCustomer){
            $from = $ticketCustomer->customer->email;
        }
        $storeWebsite     = StoreWebsite::where('website', $this->ticket->source_of_ticket)->first();
        if ($storeWebsite) {
            $name = $storeWebsite->title;
        }

        return $this->from($from, $name)
            ->subject('Price Drop Notification')
            ->view('emails.pricedropnotif', ['ticket' => $this->ticket]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
