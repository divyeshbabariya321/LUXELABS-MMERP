<?php

namespace App\Events;

use App\MonetaryAccount;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class MonetaryAccountCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     *
     * @return void
     */
    public function __construct(public MonetaryAccount $account)
    {
    }
}
