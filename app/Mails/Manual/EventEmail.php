<?php

namespace App\Mails\Manual;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject;

    public $message;

    public $fromAddress;

    public $link;

    public function __construct(string $subject, string $message, string $from, string $link)
    {
        $this->subject     = $subject;
        $this->message     = $message;
        $this->fromAddress = $from;
        $this->link        = $link;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->from($this->fromAddress)
                    ->subject($this->subject)
                    ->markdown('emails.event.email');
    }
}
