<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class HeaderIconNotificationsFound implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param mixed $notifications
     *
     * @return void
     */
    public function __construct(public $notifications)
    {
    }

    public function broadcastWith(): array
    {
        return [
            'notifications' => $this->notifications,
        ];
    }

    public function broadcastAs(): string
    {
        return 'HeaderIconNotificationsFound';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('headerIconNotifications');
    }
}
