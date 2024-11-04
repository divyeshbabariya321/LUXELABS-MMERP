<?php

namespace App\Mails\Manual;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HubstuffActivitySendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $data = null;

    protected $path = null;

    public function __construct($data, $path)
    {
        $this->data = $data;
        $this->path = $path;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->subject('Hubstuff Activities Report')->view('hubstaff.hubstaff-activities-mail', ['data' => $this->data])
            ->attach($this->path);
    }
}
