<?php

namespace App\Mails\Manual;
use App\MailinglistTemplate;
use App\Helpers;
use App\EmailAddress;

use App\Tickets;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCreate extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $ticket;

    public $fromMailer;

    public function __construct(Tickets $ticket)
    {
        $this->ticket     = $ticket;
        $this->fromMailer = Helpers::getFromEmail();
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $subject  = 'New Ticket # ' . $this->ticket->ticket_id;
        $ticket   = $this->ticket;
        $customer = $ticket->customer;

        $this->subject = $subject;
        $emailAddress  = EmailAddress::whereHas('website', function ($q) use ($ticket) {
            $q->where('website', '=', $ticket->source_of_ticket);
        })->first();

        if ($emailAddress) {
            $this->fromMailer = $emailAddress->from_address;
        }

        $template = MailinglistTemplate::getTicketCreateTemplate();

        if ($template) {
            if ($template->from_email != '') {
                $this->fromMailer = $template->from_email;
            }

            if (! empty($template->mail_tpl)) {
                // need to fix the all email address
                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view($template->mail_tpl, compact(
                        'ticket', 'customer'
                    ));
            } else {
                $content = $template->static_template;

                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view('emails.blank_content', compact(
                        'ticket', 'customer', 'content'
                    ));
            }
        }

        return $this->view('emails.tickets.created', compact(
            'ticket', 'customer'
        ));
    }
}
