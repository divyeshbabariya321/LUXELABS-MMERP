<?php

namespace App\Mails\Manual;
use App\MailinglistTemplate;
use App\Helpers;
use App\EmailAddress;

use App\ReturnExchange;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InitializeCancelRequest extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $return;

    public $fromMailer;

    public function __construct(ReturnExchange $return)
    {
        $this->return     = $return;
        $this->fromMailer = Helpers::getFromEmail($this->return->customer->id);
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $subject  = 'Cancellation Request Initialized';
        $return   = $this->return;
        $customer = $return->customer;

        $this->subject = $subject;

        if ($customer) {
            if ($customer->store_website_id > 0) {
                $emailAddress = EmailAddress::where('store_website_id', $customer->store_website_id)->first();
                if ($emailAddress) {
                    $this->fromMailer = $emailAddress->from_address;
                }
                $template = MailinglistTemplate::getIntializeCancellation($customer->store_website_id);
            } else {
                $emailAddress = EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
                if ($emailAddress) {
                    $this->fromMailer = $emailAddress->from_address;
                }
                $template = MailinglistTemplate::getIntializeCancellation();
            }
            if ($template) {
                if ($template->from_email != '') {
                    $this->fromMailer = $template->from_email;
                }

                if (! empty($template->mail_tpl)) {
                    // need to fix the all email address
                    $this->subject = $template->subject;

                    return $this->subject($this->subject)
                        ->view($template->mail_tpl, compact(
                            'customer', 'return'
                        ));
                }
            }
        }

        return $this->subject($this->subject)->markdown('emails.customers.blank');
    }
}
