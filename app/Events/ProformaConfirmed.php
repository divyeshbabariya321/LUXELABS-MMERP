<?php

namespace App\Events;

use App\Purchase;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ProformaConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param publicPurchase $purchase
     * @param public         $purchase_amount
     *
     * @return void
     */
    public function __construct(public Purchase $purchase, public $purchase_amount)
    {
    }
}
