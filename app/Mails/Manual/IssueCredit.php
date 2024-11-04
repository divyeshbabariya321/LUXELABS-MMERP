<?php

namespace App\Mails\Manual;
use App\Helpers;

use App\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IssueCredit extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $customer;

    public $fromMailer;

    public function __construct(Customer $customer)
    {
        $this->customer   = $customer;
        $this->fromMailer = Helpers::getFromEmail($this->customer->id);
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->from($this->fromMailer)
                    ->bcc($this->fromMailer)
                    ->subject('Customer Credit Issued')
                    ->markdown('emails.customers.issue-credit');
    }
}
