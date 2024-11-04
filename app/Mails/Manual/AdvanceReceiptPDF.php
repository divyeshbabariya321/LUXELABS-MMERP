<?php

namespace App\Mails\Manual;
use App\Helpers;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdvanceReceiptPDF extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $product_names = '';

    public $fromMailer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order      = $order;
        $this->fromMailer = Helpers::getFromEmail($this->order->customer->id);
        $count            = count($order->order_product);
        foreach ($order->order_product as $key => $order_product) {
            if ((($count - 1) == $key) && $key != 0) {
                $this->product_names .= ' and ' . $order_product->product->name;
            } elseif (((($count - 1) == $key) && $key == 0) || ((($count - 1) != $key) && $key == 0)) {
                $this->product_names .= $order_product->product->name;
            } else {
                $this->product_names .= ', ' . $order_product->product->name;
            }
        }
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->from($this->fromMailer)
                    ->bcc($this->fromMailer)
                    ->markdown('emails.orders.advance-receipt-pdf');
    }
}
