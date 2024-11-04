<?php

namespace App\Listeners;
use App\PaymentReceipt;

use App\Events\PaymentReceiptUpdated;

class UpdatePaymentReceiptCashflow
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentReceiptUpdated $event): void
    {
        $receipt = $event->paymentReceipt;
        $user_id = ! empty(auth()->id()) ? auth()->id() : 6;
        $receipt->cashFlows()->where('cash_flow_able_id', $receipt->id)->update([
            'date'                => $receipt->created_at,
            'amount'              => $receipt->rate_estimated,
            'type'                => 'pending',
            'currency'            => $receipt->currency,
            'status'              => 1,
            'order_status'        => 'pending',
            'user_id'             => $receipt->user_id,
            'updated_by'          => $user_id,
            'cash_flow_able_id'   => $receipt->id,
            'cash_flow_able_type' => PaymentReceipt::class,
            'description'         => 'Receipt created',
        ]);
    }
}
