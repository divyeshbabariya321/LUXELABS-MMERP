<?php

namespace App\Listeners;

use App\Events\VoucherApproved;

class CreateVoucherCashFlow
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
    public function handle(VoucherApproved $event): void
    {
        $voucher   = $event->voucher;
        $user_id   = auth()->id();
        $cash_flow = $voucher->cashFlows()->first();
        if (! $cash_flow) {
            $cash_flow = $voucher->cashFlows()->create([
                'user_id' => $user_id,
            ]);
        }
        $cash_flow->fill([
            'date'         => $voucher->date,
            'expected'     => $voucher->amount,
            'actual'       => $voucher->paid,
            'type'         => 'paid',
            'currency'     => 1,
            'status'       => 1,
            'order_status' => '',
            'updated_by'   => $user_id,
            'description'  => '',
        ])->save();
    }
}
