<?php

namespace App\Listeners;
use App\Helpers\OrderHelper;

use App\Events\OrderCreated;

class CreateOrderCashFlow
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
    public function handle(OrderCreated $event): void
    {
        $order   = $event->order;
        $user_id = ! empty(auth()->id()) ? auth()->id() : 6;
        if ($order->order_status_id == OrderHelper::$prepaid) {
            $order->cashFlows()->create([
                'date'                => $order->order_date,
                'amount'              => $order->balance_amount,
                'type'                => 'received',
                'currency'            => $order->store_currency_code,
                'status'              => 1,
                'order_status'        => 'prepaid',
                'user_id'             => $user_id,
                'updated_by'          => $user_id,
                'monetary_account_id' => $order->monetary_account_id,
                'description'         => 'Order Received with full pre payment',
            ]);
        } elseif ($order->order_status_id == OrderHelper::$followUpForAdvance) {
            $order->cashFlows()->create([
                'date'                => $order->order_date,
                'amount'              => $order->advance_detail,
                'type'                => 'pending',
                'currency'            => $order->store_currency_code,
                'status'              => 1,
                'order_status'        => 'pending',
                'user_id'             => $user_id,
                'updated_by'          => $user_id,
                'monetary_account_id' => $order->monetary_account_id,
                'description'         => 'Order Received from website with follow up for Advance',
            ]);
        } elseif ($order->order_status_id == OrderHelper::$advanceRecieved) {
            $order->cashFlows()->create([
                'date'                => $order->advance_date ?: $order->order_date,
                'amount'              => $order->advance_detail,
                'type'                => 'received',
                'currency'            => $order->store_currency_code,
                'status'              => 1,
                'order_status'        => 'advance received',
                'user_id'             => $user_id,
                'updated_by'          => $user_id,
                'monetary_account_id' => $order->monetary_account_id,
                'description'         => 'Advance Received',
            ]);
            $order->cashFlows()->create([
                'date'         => $order->date_of_delivery ?: ($order->estimated_delivery_date ?: $order->order_date),
                'amount'       => $order->balance_amount,
                'type'         => 'pending',
                'currency'     => $order->store_currency_code,
                'status'       => 0,
                'order_status' => 'pending',
                'user_id'      => $user_id,
                'updated_by'   => $user_id,
                'description'  => 'Pending balance amount',
            ]);
        } else {
            $order->cashFlows()->create([
                'date'         => $order->date_of_delivery ?: ($order->estimated_delivery_date ?: $order->order_date),
                'amount'       => ! empty($order->balance_amount) ? $order->balance_amount : 0.00,
                'type'         => 'pending',
                'currency'     => $order->store_currency_code,
                'status'       => 0,
                'order_status' => 'pending',
                'user_id'      => $user_id,
                'updated_by'   => $user_id,
                'description'  => 'Pending balance amount',
            ]);
        }
    }
}
