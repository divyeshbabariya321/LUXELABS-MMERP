<?php

namespace App\Mails\Manual;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject;

    public $message;

    public function __construct(string $subject, string $message)
    {
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->from('affiliate@amourint.com')
                    ->bcc('affiliate@amourint.com')
                    ->subject($this->subject)
                    ->markdown('emails.customers.email');
    }
}
