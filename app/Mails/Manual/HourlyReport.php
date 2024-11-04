<?php

namespace App\Mails\Manual;
use App\Helpers;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HourlyReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $path;

    public $fromMailer;

    public function __construct($path)
    {
        $this->path       = $path;
        $this->fromMailer = Helpers::getFromEmail();
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->from($this->fromMailer)
                    ->bcc($this->fromMailer)
                    ->subject('Generated Hourly Report')
                    ->markdown('emails.hourly-report')
                    ->attachFromStorageDisk('files', $this->path);
    }
}
