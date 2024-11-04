<?php

namespace App\Observers;

use App\CallBusyMessage;

class CallBusyMessageObserver
{
    /**
     * Handle the call busy message "created" event.
     */
    public function created(CallBusyMessage $callBusyMessage): void
    {
        if ($callBusyMessage->recording_url != '') {
            $recordedText = (new CallBusyMessage)->convertSpeechToText($callBusyMessage->recording_url, 0, null, $callBusyMessage->twilio_call_sid);
            if ($recordedText != '') {
                CallBusyMessage::where('id', $callBusyMessage->id)->update(['audio_text' => $recordedText]);
            }
        }
    }

    /**
     * Handle the call busy message "updated" event.
     */
    public function updated(CallBusyMessage $callBusyMessage): void
    {
        if ($callBusyMessage->recording_url != '' && $callBusyMessage->isDirty('recording_url')) {
            $recordedText = (new CallBusyMessage)->convertSpeechToText($callBusyMessage->recording_url, 0, null, $callBusyMessage->twilio_call_sid);
            if ($recordedText != '') {
                CallBusyMessage::where('id', $callBusyMessage->id)->update(['audio_text' => $recordedText]);
            }
        }
    }

    /**
     * Handle the call busy message "deleted" event.
     */
    public function deleted(CallBusyMessage $callBusyMessage): void
    {
        //
    }

    /**
     * Handle the call busy message "restored" event.
     */
    public function restored(CallBusyMessage $callBusyMessage): void
    {
        //
    }

    /**
     * Handle the call busy message "force deleted" event.
     */
    public function forceDeleted(CallBusyMessage $callBusyMessage): void
    {
        //
    }
}
