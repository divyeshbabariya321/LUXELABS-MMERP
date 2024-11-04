<?php

namespace App\Mails\Manual;
use App\MailinglistTemplate;
use App\Helpers;
use App\EmailAddress;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
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

    public function __construct(Order $order)
    {
        $this->order      = $order;
        $this->fromMailer = Helpers::getFromEmail($order->customer->id);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject        = 'New Order # ' . $this->order->order_id;
        $order          = $this->order;
        $customer       = $order->customer;
        $order_products = $order->order_products;

        $this->subject = $subject;

        // check this order is related to store website ?
        $storeWebsiteOrder = $order->storeWebsiteOrder;

        // get the template based on store
        if ($storeWebsiteOrder) {
            $emailAddress = EmailAddress::where('store_website_id', $storeWebsiteOrder->website_id)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
            $template = MailinglistTemplate::getOrderConfirmationTemplate($storeWebsiteOrder->website_id);
        } else {
            $emailAddress = EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
            $template = MailinglistTemplate::getOrderConfirmationTemplate();
        }

        if ($template) {
            if ($template->from_email != '') {
                $this->fromMailer = $template->from_email;
            }
            if (! empty($template->mail_tpl)) {
                // need to fix the all email address
                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view($template->mail_tpl, compact(
                        'order', 'customer', 'order_products'
                    ));
            } else {
                $content = $template->static_template;

                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view('emails.blank_content', compact(
                        'order', 'customer', 'order_products', 'content'
                    ));
            }
        }

        if (! $storeWebsiteOrder) {
            $attach_image_tag  = config('constants.attach_image_tag');
            return $this->view('emails.orders.confirmed-solo', compact(
                'order', 'customer', 'order_products','attach_image_tag'
            ));
        }
    }
}
