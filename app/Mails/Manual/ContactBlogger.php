<?php

namespace App\Mails\Manual;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactBlogger extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject;

    public $message;

    public $from_email;

    public function __construct(string $subject, string $message, string $from_email)
    {
        $this->subject    = $subject;
        $this->message    = $message;
        $this->from_email = $from_email;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $this->withSymfonyMessage(function ($swiftmessage) {
            Log::channel('customer')->info($swiftmessage->getId());
        });

        return $this->from($this->from_email)
            ->subject($this->subject)
            ->markdown('emails.customers.email');
    }
}
