<?php

namespace App\Mails\Manual;
use App\Helpers;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public $order_id;

    public $product_names;

    public $from_email = '';

    public function __construct(string $order_id, string $product_names)
    {
        $this->order_id      = $order_id;
        $this->from_email    = Helpers::getFromEmailByOrderId($order_id);
        $this->product_names = $product_names;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->from($this->from_email)
            ->bcc($this->from_email)
            ->subject('Refund Processed')
            ->markdown('emails.orders.refund');
    }
}
