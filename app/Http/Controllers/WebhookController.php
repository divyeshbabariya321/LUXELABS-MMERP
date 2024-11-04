<?php

namespace App\Http\Controllers;
use App\Email;

use App\SendgridEvent;
use App\Enums\EventEnum;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Events\SendgridEventCreated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Repositories\SendgridEventRepositoryInterface;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Class WebhookController
 * Ingresses any Sendgrid webhooks
 */
class WebhookController extends Controller
{
    use ValidatesRequests;

    /** @var SendgridEventRepositoryInterface */
    private $sendgridEventRepository;

    /**
     * WebhookController constructor.
     *
     * @param SendgridEvent $sendgridEvent
     */
    public function __construct(SendgridEventRepositoryInterface $sendgridEventRepository)
    {
        $this->sendgridEventRepository = $sendgridEventRepository;
    }

    /**
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function post(Request $request)
    {
        Log::info('Process started');

        $payload = $request->json()->all();

        Log::info('Payload Before');
        Log::info($payload);
        Log::info('Payload After');

        $validator = Validator::make(
            $payload,
            [
                '*.email'         => 'required|email',
                '*.timestamp'     => 'required|integer',
                '*.event'         => 'required|in:' . implode(',', EventEnum::getAll()),
                '*.sg_event_id'   => 'required|string',
                '*.sg_message_id' => 'required|string',
                '*.category'      => function ($attribute, $value, $fail) {
                    if (! is_null($value) && ! in_array(gettype($value), ['string', 'array'])) {
                        $fail($attribute . ' must be a string or array.');
                    }
                },
                '*.category.*' => 'string',
            ]
        );

        Log::info('Validation Initialize');

        if ($validator->fails()) {
            $this->logMalformedPayload($payload, $validator->errors()->all());
            throw new ValidationException($validator);
        }

        Log::info('Validation Passed');

        foreach ($payload as $event) {
            Log::info('Event Before Process');
            Log::info($event);

            $this->processEvent($event);

            Log::info($event);
            Log::info('Event After Processed');
        }

        Log::info('Post added successfully');
    }

    /**
     * Processes an individual event
     */
    private function processEvent(array $event): void
    {
        Log::info('Process event start');
        if (isset($event['email_id'])) {
            if ($event['email_id']) {
                $emailId = $event['email_id'];
            } else {
                $emailId = null;
            }
        } else {
            $emailId = null;
        }

        if ($this->sendgridEventRepository->exists($event['sg_event_id'], $emailId, $event['sg_event_id'])) {
            Log::info('log duplicate start');
            $this->logDuplicateEvent($event);
            Log::info('log duplicate exit');

            return;
        }

        Log::info('Repository start');
        $sendgridEvent = $this->sendgridEventRepository->create($event);
        if (isset($event['email_id'])) {
            Email::where('id', $event['email_id'])->update(['status' => $event['event']]);
        }
        Log::info('Repository exit');

        event(new SendgridEventCreated($sendgridEvent));
        Log::info('Event sent');
    }

    /**
     * Logs a message that we have received a malformed webhook
     * If the webhook was sent by Sendgrid then this may indicate Sendgrid has changed their payload structure and
     * therefore this library will need to be updated.
     *
     * Note: there is no way of validating that this webhook was actually sent by Sendgrid, so the malformation could
     * be the result of a malicious third party.
     *
     * @param array $event
     * @param mixed $payload
     */
    private function logMalformedPayload($payload, array $validationErrors)
    {
        if (config('sendgridevents.log_malformed_payload')) {
            Log::log(
                config('sendgridevents.log_malformed_payload_level'),
                'Malformed Sendgrid webhook received',
                [
                    'payload'           => $payload,
                    'validation_errors' => $validationErrors,
                ]
            );
        }
    }

    /**
     * Logs a message that we have received a duplicate webhook for an event
     */
    private function logDuplicateEvent(array $event)
    {
        Log::info('log duplicate In');
        if (config('sendgridevents.log_duplicate_events')) {
            Log::log(
                config('sendgridevents.log_duplicate_events_level'),
                'Duplicate Sendgrid Webhook received',
                $event
            );
        }
    }
}
