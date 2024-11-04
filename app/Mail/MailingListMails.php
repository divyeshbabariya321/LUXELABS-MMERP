<?php

namespace App\Mail;

use App\MailinglistTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailingListMails extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     *
     * @return void
     */
    public function __construct(public MailinglistTemplate $template)
    {
        $this->fromMailer = 'customercare@sololuxury.co.in';
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $template = $this->template;
        $customer = $template->customer;
        if (! empty($template->mail_tpl)) {
            // need to fix the all email address
            $html = view($template->mail_tpl, compact(
                'customer'
            ));
        } else {
            $html = $template['static_template'];
        }
        if ($template->from_email != null) {
            $this->fromMailer = $template->from_email;
        }

        return $this->from($this->fromMailer)->html($html, 'text/html');
    }
}
