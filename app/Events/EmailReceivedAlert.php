<?php

namespace App\Events;

use App\Email;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class EmailReceivedAlert implements ShouldBroadcastNow {

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * Create a new event instance.
     *
     *
     * @return void
     */
    public function __construct(private Email $email) {
        
    }

    public function broadcastWith(): array {
        $response = [
            'email' => [
                'id' => $this->email->id,
                'subject' => $this->email->subject,
                'message' => $this->email->message,
                'from' => $this->email->from,
                'to' => $this->email->to,
                'created_at' => $this->email->created_at,
            ],
        ];
        \Log::info("EmailReceivedAlert::broadcastWith >> email.received ", [$response]);

        return $response;
    }

    public function broadcastAs(): string {
        return 'email.received';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('emails');
    }
}
