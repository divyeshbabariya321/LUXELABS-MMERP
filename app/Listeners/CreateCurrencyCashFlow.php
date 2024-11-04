<?php

namespace App\Listeners;
use App\MonetaryAccountHistory;
use App\MonetaryAccount;
use App\Currency;
use App\CashFlow;

use Illuminate\Support\Facades\Log;
use App\Events\CashFlowCreated;

class CreateCurrencyCashFlow
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
    public function handle(CashFlowCreated $event): void
    {
        Log::info('this action has been called');

        $cashflow = $event->cashflow;
        if ($cashflow->amount_eur <= 0) {
            $cashflow->amount_eur = Currency::convert($cashflow->amount, 'EUR', $cashflow->currency);
            $cashflow->save();
        }

        if ($cashflow->monetary_account_id > 0 && ($cashflow->type == 'received' || $cashflow->type == 'paid')) {
            $user_id = ! empty(auth()->id) ? auth()->id : 6;
            $amount  = $cashflow->amount;
            if ($cashflow->type == 'paid') {
                $amount = 0 - $cashflow->amount;
            }

            $monetaryHistory = MonetaryAccountHistory::where('model_id', $cashflow->id)->where('model_type', CashFlow::class)->first();
            if ($monetaryHistory) {
                $monetaryHistory->update([
                    'note'                => $cashflow->description,
                    'model_id'            => $cashflow->id,
                    'model_type'          => CashFlow::class,
                    'amount'              => $amount,
                    'monetary_account_id' => $cashflow->monetary_account_id,
                    'user_id'             => $user_id,
                ]);
            } else {
                MonetaryAccountHistory::create([
                    'note'                => $cashflow->description,
                    'model_id'            => $cashflow->id,
                    'model_type'          => CashFlow::class,
                    'amount'              => $amount,
                    'monetary_account_id' => $cashflow->monetary_account_id,
                    'user_id'             => $user_id,
                ]);
            }

            $totalSum = MonetaryAccountHistory::where('monetary_account_id', $cashflow->monetary_account_id)->sum('amount');
            $ma       = MonetaryAccount::find($cashflow->monetary_account_id);
            if ($ma) {
                $ma->amount += $totalSum;
                $ma->save();
            }
        }
    }
}
