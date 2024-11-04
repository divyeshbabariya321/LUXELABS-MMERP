<?php

namespace App\Listeners;

use App\Events\PaymentCreated;

class UpdatePaymentCashflow
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
    }
}
