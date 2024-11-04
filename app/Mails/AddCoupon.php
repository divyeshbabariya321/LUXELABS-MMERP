<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AddCoupon extends Mailable
{
    use Queueable, SerializesModels;

    public $receiver_email;

    public $sender_email;

    public $coupon;

    /**
     * Create a new message instance.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->receiver_email = isset($data['receiver_email']) ? $data['receiver_email'] : '';
        $this->sender_email   = isset($data['sender_email']) ? $data['sender_email'] : '';
        $this->coupon         = isset($data['coupon']) ? $data['coupon'] : '';
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->subject('Coupon Recieved')->view('emails.coupon.coupon_recieved_email');
    }
}
