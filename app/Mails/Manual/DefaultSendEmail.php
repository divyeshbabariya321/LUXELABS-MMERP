<?php

namespace App\Mails\Manual;
use App\EmailLog;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DefaultSendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public $attchments;

    public $template;

    public $returnExchangeProducts;

    public $fromMailer;

    /**
     * Create a new message instance.
     *
     * @param mixed      $email
     * @param mixed      $attchments
     * @param null|mixed $template
     * @param mixed      $dataArr
     * @param null|mixed $rxProducts
     * @param null|mixed $fromMailer
     *
     * @return void
     */
    public function __construct($email, $attchments, $template = null, $dataArr = [], $rxProducts = null, $fromMailer = null)
    {
        $this->email                  = $email;
        $this->attchments             = $attchments;
        $this->template               = $template;
        $this->dataArr                = $dataArr;
        $this->returnExchangeProducts = $rxProducts;
        $this->fromMailer             = $fromMailer;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $email      = $this->email;
        $content    = $email->message;
        $headerData = [
            'unique_args' => [
                'email_id' => $email->id,
            ],
        ];

        $header = $this->asString($headerData);

        $this->withSymfonyMessage(function ($message) use ($header) {
            $message->getHeaders()
                    ->addTextHeader('X-SMTPAPI', $header);
        });
        $mailObj = $this->to($email->to)
        ->from($email->from)
        ->subject($email->subject)
        ->view('emails.blank_content', compact('content'));	//->with([ 'custom_args' => $this->email ]);

        EmailLog::create([
            'email_id'  => $email->id,
            'email_log' => 'Mail Object Created in DefaultSendEmail',
            'message'   => json_encode($mailObj),
        ]);

        foreach ($this->attchments as $attchment) {
            $mailObj->attachFromStorageDisk('files', $attchment);
        }

        return $mailObj;
    }

    private function asJSON($data)
    {
        $json = json_encode($data);
        $json = preg_replace('/(["\]}])([,:])(["\[{])/', '$1$2 $3', $json);

        return $json;
    }

    private function asString($data)
    {
        $json = $this->asJSON($data);

        return wordwrap($json, 76, "\n   ");
    }
}
