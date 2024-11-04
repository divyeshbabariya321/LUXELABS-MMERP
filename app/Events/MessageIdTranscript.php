<?php

namespace App\Events;

use Illuminate\Mail\Events\MessageSent;

class MessageIdTranscript
{
    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        $emailModel = @$event->data['email'];
       // \Log::info('Json found here ' . json_encode([$event->message->getId(), $event->data]));
        if (isset($emailModel)) {
            //$emailModel->origin_id = (string) $event->message->getId(); // This method is used in Swift mailer get id but not used in SymfonyMailer
            if (isset($event->data['sg_message_id']) && $event->data['sg_message_id'] != '') {
                $emailModel->message_id = (string) $event->data['sg_message_id'];
            }
            $emailModel->save();
        }
    }
}
