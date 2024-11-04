<?php

namespace App\Mails\Manual;
use App\MailinglistTemplate;
use App\Helpers;
use App\EmailAddress;
use App\CreditLog;

use App\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Exception;

class SendIssueCredit extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

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
        $subject  = 'Customer Credit Issued';
        $customer = $this->customer;

        $this->subject = $subject;

        try {
            if ($customer) {
                if ($customer->store_website_id > 0) {
                    $emailAddress = EmailAddress::where('store_website_id', $customer->store_website_id)->first();
                    if ($emailAddress) {
                        $this->fromMailer = $emailAddress->from_address;
                    }
                    $template = MailinglistTemplate::getIssueCredit($customer->store_website_id);
                } else {
                    $emailAddress = EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
                    if ($emailAddress) {
                        $this->fromMailer = $emailAddress->from_address;
                    }
                    $template = MailinglistTemplate::getIssueCredit(null);
                }
                if ($template) {
                    if ($template->from_email != '') {
                        $this->fromMailer = $template->from_email;
                    }

                    if (! empty($template->mail_tpl)) {
                        // need to fix the all email address
                        $this->subject = $template->subject;

                        return $this->subject($template->subject)
                        ->view($template->mail_tpl, compact(
                            'customer'
                        ));
                    }

                    return false;
                }

                return $this->subject($this->subject)->markdown('emails.customers.issue-credit');
            }
        } catch (Exception $e) {
            $post = [
                'customer-id' => $customer->id,
                'subject'     => $subject,
                'from'        => $this->fromMailer,
            ];
            CreditLog::create(['customer_id' => $customer->id, 'request' => json_encode($post), 'response' => $e->getMessage(), 'status' => 'failure']);
        }
    }
}
