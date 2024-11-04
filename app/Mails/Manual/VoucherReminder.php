<?php

namespace App\Mails\Manual;
use App\Helpers;

use App\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VoucherReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $voucher;

    public $fromMailer;

    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;

        $this->fromMailer = Helpers::getFromEmail();
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->from($this->fromMailer)
            ->bcc($this->fromMailer)
            ->subject('Voucher Reminder')
            ->markdown('emails.vouchers.reminder');
    }
}
