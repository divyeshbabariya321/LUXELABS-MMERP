<?php

namespace App\Listeners;

use App\Events\SendgridEventCreated;

class SendgridEventCreatedListner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendgridEventCreated $event): void
    {
        $eventType     = $event->getEventType();
        $sendgridEvent = $event->getSendgridEvent();
        $sendgridEvent->email;
        $sendgridEvent->timestamp;
    }
}
