<?php

namespace App\Mails\Manual;
use App\MailinglistTemplate;
use App\Helpers;
use App\EmailAddress;

use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangeMail extends Mailable
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

    public function getDataFromHTML($order, $htmlData)
    {
        preg_match_all('/{{(.*?)}}/i', $htmlData, $matches);
        if (count($matches) != 0) {
            $matches = $matches[0];
            foreach ($matches as $match) {
                $matchString = str_replace(['{{', '}}'], '', $match);
                $value       = Arr::get($order, trim($matchString));
                $htmlData    = str_replace($match, $value, $htmlData);
            }
        }

        return $htmlData;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $subject = 'Order # ' . $this->order->order_id . ' Status has been changed';
        $order   = $this->order;

        $customer       = $order->customer;
        $order_products = $order->order_products;

        $this->subject = $subject;

        // check this order is related to store website ?
        $storeWebsiteOrder = $order->storeWebsiteOrder;
        $order_status      = $order->order_status;
        if ($storeWebsiteOrder) {
            $emailAddress = EmailAddress::where('store_website_id', $storeWebsiteOrder->website_id)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
            $template = MailinglistTemplate::getOrderStatusChangeTemplate($order_status, $storeWebsiteOrder->website_id);
        } else {
            $emailAddress = EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
            $template = MailinglistTemplate::getOrderStatusChangeTemplate($order_status);
        }

        if ($template) {
            if ($template->from_email != '') {
                $this->fromMailer = $template->from_email;
            }

            $this->subject = $template->subject;
            if (! empty($template->mail_tpl)) {
                if (! empty($template->html_text)) {
                    $htmlData = $template->html_text;
                    $re       = '/<loop-orderProducts>((.|\n)*?)<\/loop-orderProducts>/m';
                    preg_match_all($re, $htmlData, $matches, PREG_SET_ORDER, 0);
                    if (count($matches) != 0) {
                        foreach ($matches as $index => $match) {
                            $data = null;
                            foreach ($order->orderProducts as $orderProduct) {
                                $data .= $this->getDataFromHTML($orderProduct, $match[1]);
                            }
                            if ($data) {
                                $htmlData = str_replace($match[1], $data, $htmlData);
                            }
                        }
                    }
                    $content = $this->getDataFromHTML($order, $htmlData);

                    return $this->from($this->fromMailer)
                        ->subject($this->subject)
                        ->view('email-templates.content', compact(
                            'content'
                        ));
                } else {
                    // need to fix the all email address
                    return $this->from($this->fromMailer)
                        ->subject($this->subject)
                        ->view($template->mail_tpl, compact(
                            'order', 'customer', 'order_products'
                        ));
                }
            } else {
                if (! empty($template->html_text)) {
                    $htmlData = $template->html_text;
                    $re       = '/<loop-orderProducts>((.|\n)*?)<\/loop-orderProducts>/m';
                    preg_match_all($re, $htmlData, $matches, PREG_SET_ORDER, 0);
                    if (count($matches) != 0) {
                        foreach ($matches as $index => $match) {
                            $data = null;
                            foreach ($order->orderProducts as $orderProduct) {
                                $data .= $this->getDataFromHTML($orderProduct, $match[1]);
                            }
                            if ($data) {
                                $htmlData = str_replace($match[1], $data, $htmlData);
                            }
                        }
                    }
                    $content = $this->getDataFromHTML($order, $htmlData);

                    return $this->from($this->fromMailer)
                        ->subject($this->subject)
                        ->view('email-templates.content', compact('content'));
                } else {
                    $content      = $template->static_template;
                    $arrToReplace = ['{FIRST_NAME}', '{ORDER_STATUS}', '{ORDER_ID}'];
                    $valToReplace = [$order->customer->name, $order->order_status, $order->order_id];
                    $content      = str_replace($arrToReplace, $valToReplace, $content);

                    return $this->from($this->fromMailer)->subject($this->subject)
                        ->view('emails.blank_content', compact(
                            'order', 'customer', 'order_products', 'content'
                        ));
                }
            }
        }

        if (! $storeWebsiteOrder) {
            return $this->view('emails.orders.update-status', compact(
                'order', 'customer', 'order_products'
            ));
        }
    }
}
