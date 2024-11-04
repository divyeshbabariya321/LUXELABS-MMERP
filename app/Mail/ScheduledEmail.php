<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduledEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $body;

    public $subject;

    public $sendFrom;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subject = isset($data['subject']) ? $data['subject'] : '';
        $this->body    = isset($data['template']) ? $data['template'] : '';
        if (isset($data['from'])) {
            $this->sendFrom = $data['from'];
        } else {
            $this->sendFrom = 'customercare@sololuxury.co.in';
        }
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->from($this->sendFrom)
                   ->bcc($this->sendFrom)
                   ->subject($this->subject)
                   ->html($this->body, 'text/html');
    }
}
