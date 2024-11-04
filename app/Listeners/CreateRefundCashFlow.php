<?php

namespace App\Listeners;

use App\Events\RefundCreated;

class CreateRefundCashFlow
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
    public function handle(RefundCreated $event): void
    {
        $refund  = $event->refund;
        $user_id = auth()->id();
        $refund->cashFlows()->create([
            'date'         => $refund->date_of_issue,
            'expected'     => $refund->order?->customer?->credit ?: 0,
            'actual'       => 0,
            'type'         => 'paid',
            'currency'     => '',
            'status'       => 0,
            'order_status' => 'Refund to be processed',
            'user_id'      => $user_id,
            'updated_by'   => $user_id,
            'description'  => 'Refund to be processed ',
        ]);
    }
}
