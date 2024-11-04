<?php

namespace App\Mails\Manual;
use App\MailinglistTemplate;
use App\Helpers;
use App\EmailAddress;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    public $order;

    public $fromMailer;

    /**
     * Create a new message instance.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->order      = $data;
        $this->fromMailer = Helpers::getFromEmail($this->order->customer->id);
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $subject = 'Order # ' . $this->order->order_id . ' has been cancelled';
        $order   = $this->order;

        $customer       = $order->customer;
        $order_products = $order->order_products;
        $email          = $this->fromMailer;

        $content = 'Your order request has been cancelled';

        $this->subject = $subject;

        // check this order is related to store website ?
        $storeWebsiteOrder = $order->storeWebsiteOrder;

        if ($storeWebsiteOrder) {
            $emailAddress = EmailAddress::where('store_website_id', $storeWebsiteOrder->website_id)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
            $template = MailinglistTemplate::getOrderCancellationTemplate($storeWebsiteOrder->website_id);
        } else {
            $emailAddress = EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
            $template = MailinglistTemplate::getOrderCancellationTemplate();
        }

        if ($template) {
            if ($template->from_email != '') {
                $this->fromMailer = $template->from_email;
            }

            $this->subject = $template->subject;
            if (! empty($template->mail_tpl)) {
                // need to fix the all email address
                return $this->from($email)
                    ->subject($this->subject)
                    ->view($template->mail_tpl, compact(
                        'order', 'customer', 'order_products'
                    ));
            } else {
                $content = str_replace([
                    '{FIRST_NAME}', '{ORDER_STATUS}', '{ORDER_ID}', ],
                    [$order->customer->name, $order->order_status, $order->order_id],
                    $template->static_template
                );
            }
        }

        return $this->from($email)
            ->subject($this->subject)
            ->view('emails.blank_content', compact(
                'order', 'customer', 'order_products', 'content'
            ));
    }
}
