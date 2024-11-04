<?php

namespace App\Mail;
use App\EmailAddress;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DobAndAnniversaryMail extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    public $body;

    public $subject;

    public $sendFrom;

    /**
     * Create a new message instance.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject = isset($data['subject']) ? $data['subject'] : '';
        $this->body    = isset($data['template']) ? $data['template'] : '';
        if (isset($data['from'])) {
            $this->sendFrom = $data['from'];
        } else {
            $emailAddress = EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
            if ($emailAddress) {
                $this->sendFrom = $emailAddress->from_address;
            }
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
