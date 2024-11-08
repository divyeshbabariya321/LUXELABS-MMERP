<?php

namespace App\Mails\Manual;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShipmentEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject;

    public $message;

    public $file_paths;

    public $customConfig;

    public function __construct(string $subject, string $message, array $file_paths, $customConfig = [])
    {
        $this->subject      = $subject;
        $this->message      = $message;
        $this->file_paths   = $file_paths;
        $this->customConfig = $customConfig;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $email = $this->subject($this->subject)
            ->text('emails.customers.email_plain', ['body_message' => $this->message]);

        if (count($this->file_paths) > 0) {
            foreach ($this->file_paths as $file_path) {
                $email->attachFromStorageDisk('files', $file_path);
            }
        }

        return $email;
    }
}
