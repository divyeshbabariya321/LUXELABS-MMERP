<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class AppointmentFound implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userAppointments;

    /**
     * Create a new event instance.
     *
     * @param mixed $userAppointments
     *
     * @return void
     */
    public function __construct($userAppointments)
    {
        $this->userAppointments = $userAppointments;
    }

    public function broadcastWith(): array
    {
        return [
            'userAppointments' => $this->userAppointments,
        ];
    }

    public function broadcastAs(): string
    {
        return 'appointment.found';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notification.user.' . $this->userAppointments['userId']);
    }
}
