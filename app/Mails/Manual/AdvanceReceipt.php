<?php

namespace App\Mails\Manual;
use App\Helpers;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdvanceReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $product_names = '';

    public $from_email = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

        $this->from_email = Helpers::getFromEmail($order->customer->id);

        $count = count($order->order_product);
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
        return $this->from($this->from_email)
                    ->bcc($this->from_email)
                    ->markdown('emails.orders.advance-receipt');
    }
}
