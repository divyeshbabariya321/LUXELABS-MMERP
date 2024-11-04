<?php

namespace App\Mails\Manual;
use App\MailinglistTemplate;
use App\Helpers;
use App\EmailAddress;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketAck extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order;

    public $fromMailer;

    public function __construct($ticket)
    {
        $this->ticket     = $ticket;
        $this->fromMailer = Helpers::getFromEmail();
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $subject = 'Ticket ACK';
        $ticket  = $this->ticket;

        $this->subject = $subject;
        $emailAddress  = EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
        if ($emailAddress) {
            $this->fromMailer = $emailAddress->from_address;
        }

        $template = MailinglistTemplate::getMailTemplate('Ticket ACK');

        if ($template) {
            if ($template->from_email != '') {
                $this->fromMailer = $template->from_email;
            }

            if (! empty($template->mail_tpl)) {
                // need to fix the all email address
                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view($template->mail_tpl, compact(
                        'ticket'
                    ));
            } else {
                $content = $template->static_template;

                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view('emails.blank_content', compact(
                        'ticket', 'content'
                    ));
            }
        }
    }
}
