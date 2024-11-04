<?php

namespace App\Listeners;

use App\Events\RefundDispatched;

class UpdateRefundCashFlow
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
     *
     * @param object $event1
     */
    public function handle(RefundDispatched $event): void
    {
        $refund    = $event->refund;
        $cash_flow = $refund->cashFlows()->first();
        $user_id   = auth()->id();
        if (! $cash_flow) {
            $refund->cashFlows()->create([
                'date'         => $refund->date_of_issue,
                'expected'     => $refund->order?->customer?->credit ?: 0,
                'actual'       => $refund->order?->customer?->credit ?: 0,
                'type'         => 'paid',
                'currency'     => '',
                'status'       => 1,
                'order_status' => 'Refund Dispatched',
                'user_id'      => $user_id,
                'updated_by'   => $user_id,
                'description'  => 'Refund Dispatched',
            ]);
        } else {
            $cash_flow->fill([
                'date'         => $refund->date_of_issue,
                'expected'     => $refund->order?->customer?->credit ?: 0,
                'actual'       => $refund->order?->customer?->credit ?: 0,
                'type'         => 'paid',
                'currency'     => '',
                'status'       => 1,
                'order_status' => 'Refund Dispatched',
                'updated_by'   => $user_id,
                'description'  => 'Refund Dispatched',
            ])->save();
        }
    }
}
