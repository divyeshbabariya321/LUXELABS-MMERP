<?php

namespace App\Mails\Manual;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentEmail extends Mailable
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

    public function __construct(string $subject, string $message, array $file_paths)
    {
        $this->subject    = $subject;
        $this->message    = $message;
        $this->file_paths = $file_paths;
        $this->fromMailer = 'documents@amourint.com';
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $email = $this
            ->from('documents@amourint.com')
            ->subject($this->subject)
            ->text('emails.documents.email_plain', ['body_message' => $this->message]);

        if (count($this->file_paths) > 0) {
            foreach ($this->file_paths as $file_path) {
                $path = storage_path('app/files/' . $file_path);
                if (file_exists($path)) {
                    $email->attach($path);
                }
            }
        }

        return $email;
    }
}
