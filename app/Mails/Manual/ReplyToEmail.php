<?php

namespace App\Mails\Manual;
use App\Supplier;
use App\Customer;

use App\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReplyToEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Email
     */
    private $emailToReply;

    /**
     * @var string
     */
    private $message;

    /**
     * Create a new message instance.
     *
     * @param mixed $message
     *
     * @return void
     */
    public function __construct(Email $email, $message)
    {
        $this->emailToReply = $email;
        $this->message      = $message;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $emailToReply = $this->emailToReply;
        $message      = $this->message;

        $replyPrefix = 'Re: ';
        $subject     = substr($emailToReply->subject, 0, 4) === $replyPrefix
            ? $emailToReply->subject
            : $replyPrefix . $emailToReply->subject;

        $this->to($emailToReply->from);
        $this->from($emailToReply->to);
        $this->replyTo($emailToReply->to);
        $this->subject($subject);

        $this->withSymfonyMessage(function ($message) use ($emailToReply) {
            $references = $emailToReply->reference_id . '<' . $emailToReply->origin_id . '>';
            $message->getHeaders()->addTextHeader('In-Reply-To', $emailToReply->origin_id);
            $message->getHeaders()->addTextHeader('References', $references);
        });

        $userName = null;
        if ($emailToReply->model instanceof Supplier) {
            $userName = $emailToReply->model->supplier;
        } elseif ($emailToReply->model instanceof Customer) {
            $userName = $emailToReply->model->name;
        }

        $dateCreated       = $emailToReply->created_at->format('D, d M Y');
        $timeCreated       = $emailToReply->created_at->format('H:i');
        $originalEmailInfo = "On {$dateCreated} at {$timeCreated}, $userName <{$emailToReply->from}> wrote:";

        return $this->view('emails.reply-to-email', [
            'msg'               => $message,
            'originalEmailMsg'  => $emailToReply->message,
            'originalEmailInfo' => $originalEmailInfo,
        ]);
    }
}
