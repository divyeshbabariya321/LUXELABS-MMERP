<?php

namespace App\Listeners;

use App\Events\MonetaryAccountUpdated;
use App\MonetaryAccount;
use App\MonetaryAccountHistory;

class MonetaryAccountHistoryUpdate
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
    public function handle(MonetaryAccountUpdated $event): void
    {
        $account = $event->account;
        $user_id = auth()->id();
        if ($user_id <= 0) {
            $user_id = 6;
        }

        MonetaryAccountHistory::create([
            'note' => 'Account has been updated',
            'model_id' => $account->id,
            'model_type' => MonetaryAccount::class,
            'amount' => $account->amount,
            'monetary_account_id' => $account->id,
            'user_id' => $user_id,
        ]);
    }
}
