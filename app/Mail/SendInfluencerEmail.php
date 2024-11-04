<?php

namespace App\Mail;
use App\EmailAddress;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInfluencerEmail extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    public $body;

    public $subject;

    public $fromMailer;

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
            $this->fromMailer = $data['from'];
        } else {
            $emailAddress = EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
        }
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->from($this->fromMailer)
            ->bcc($this->fromMailer)
            ->subject($this->subject)
            ->html($this->body, 'text/html');
    }
}
