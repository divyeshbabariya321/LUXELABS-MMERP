<?php

namespace App\Mails\Manual;
use App\Helpers;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseExport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $path;

    public $subject;

    public $message;

    public $fromMailer;

    public function __construct(string $path, string $subject, string $message)
    {
        $this->path       = $path;
        $this->subject    = $subject;
        $this->message    = $message;
        $this->fromMailer = Helpers::getFromEmail();
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->bcc($this->fromMailer)
            ->subject($this->subject)
            ->text('emails.purchases.export_plain')->with(['body_message' => $this->message])
            ->attachFromStorageDisk('files', $this->path);
    }
}
