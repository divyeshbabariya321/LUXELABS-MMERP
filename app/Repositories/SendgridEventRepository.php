<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;
use App\Email;
use App\SendgridEvent;

class SendgridEventRepository implements SendgridEventRepositoryInterface
{
    /** @var SendgridEvent */
    private $model;

    /**
     * SendgridEventRepository constructor.
     */
    public function __construct(SendgridEvent $sendgridEvent)
    {
        $this->model = $sendgridEvent;
    }

    /**
     * {@inheritDoc}
     */
    public function exists($sg_event_id, $email_id, $event)
    {
        if ($email_id) {
            return $this->model->newQuery()->where('sg_event_id', $sg_event_id)->where('email_id', $email_id)->where('event', $event)->exists();
        } else {
            return $this->model->newQuery()->where('sg_event_id', $sg_event_id)->where('event', $event)->exists();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function create($event): SendgridEvent
    {
        Log::info('Send grid repo In');

        $newEvent                = new SendgridEvent();
        $newEvent->timestamp     = $event['timestamp'];
        $newEvent->email         = $event['email'];
        $newEvent->event         = $event['event'];
        $newEvent->sg_event_id   = $event['sg_event_id'];
        $newEvent->sg_message_id = $event['sg_message_id'];
        if (isset($event['email_id'])) {
            $newEvent->email_id = $event['email_id'];
        }
        $newEvent->payload = $event;

        if (isset($event['smtp-id'])) {
            $smptpID = str_replace(['<', '>'], '', $event['smtp-id']);
            Log::info('SMTP matched with record => ' . $smptpID);
            Log::info('Send grid repo params defined');
            $email = null;
            if (isset($event['email_id'])) {
                $email = Email::find($event['email_id']);
            }
            if (! $email) {
                $email = Email::where('origin_id', $smptpID)->first();
            }
            if ($email) {
                Log::info('Record found => ' . json_encode($email));
                $email->status     = $event['event'];
                $email->message_id = $event['sg_message_id'];
                $email->save();
            }
        } else {
            Log::info('message_id found => ' . $event['sg_message_id']);
            $email = null;
            if (isset($event['email_id'])) {
                $email = Email::find($event['email_id']);
            }
            if (! $email) {
                $email = Email::where('message_id', $event['sg_message_id'])->first();
            }

            if ($email) {
                Log::info('Record found => ' . json_encode($email));
                $email->status     = $event['event'];
                $email->message_id = $event['sg_message_id'];
                $email->save();
            }
        }

        Log::info('Send grid repo email updated');

        if (! empty($event['category'])) {
            Log::info('Send grid repo category In');
            $category = $event['category'];
            if (gettype($category) === 'string') {
                Log::info('Send grid repo category string');
                $newEvent->categories = [$category];
            } else {
                Log::info('Send grid repo category not string');
                $newEvent->categories = $category;
            }
        }
        Log::info('Send grid repo event save start');
        $newEvent->save();
        Log::info('Send grid repo event save end');

        return $newEvent;
    }
}
