<?php

namespace App\Listeners;
use App\PaymentReceipt;
use App\Currency;

use App\Events\PaymentCreated;

class CreatePaymentCashflow
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
    public function handle(PaymentCreated $event): void
    {
        $payment = $event->payment;
        $user_id = ! empty(auth()->id()) ? auth()->id() : 6;
        $receipt = PaymentReceipt::find($payment->payment_receipt_id);
        if ($receipt) {
            $cashflow = $receipt->cashFlows()->where('cash_flow_able_id', $payment->payment_receipt_id)->where('cash_flow_able_type', PaymentReceipt::class)->first();
            if ($cashflow) {
                $cashflow->update([
                    'date'                => $payment->created_at,
                    'amount'              => $payment->amount,
                    'erp_amount'          => $payment->amount,
                    'erp_eur_amount'      => Currency::convert($payment->amount, 'EUR', $payment->currency),
                    'type'                => 'paid',
                    'currency'            => $payment->currency,
                    'status'              => 1,
                    'order_status'        => 'pending',
                    'user_id'             => $user_id,
                    'updated_by'          => $user_id,
                    'cash_flow_able_id'   => $payment->payment_receipt_id,
                    'cash_flow_able_type' => PaymentReceipt::class,
                    'description'         => 'Vendor paid',
                ]);
            } else {
                $receipt->cashFlows()->create([
                    'date'                => $payment->created_at,
                    'amount'              => $payment->amount,
                    'erp_amount'          => $payment->amount,
                    'erp_eur_amount'      => Currency::convert($payment->amount, 'EUR', $payment->currency),
                    'type'                => 'paid',
                    'currency'            => $payment->currency,
                    'status'              => 1,
                    'order_status'        => 'pending',
                    'user_id'             => $user_id,
                    'updated_by'          => $user_id,
                    'cash_flow_able_id'   => $payment->payment_receipt_id,
                    'cash_flow_able_type' => PaymentReceipt::class,
                    'description'         => 'Vendor paid',
                ]);
            }
        }
    }
}
