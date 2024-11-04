<?php

namespace App\Listeners;
use App\MonetaryAccountHistory;
use App\Currency;
use App\CashFlow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\CashFlowUpdated;

class UpdateCurrencyCashFlow
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
    public function handle(CashFlowUpdated $event): void
    {
        Log::info('this action has been called');

        $cashflow = $event->cashflow;
        if ($cashflow->amount_eur <= 0) {

            CashFlow::where('id', $cashflow->id)->update(['amount_eur' => Currency::convert($cashflow->amount, 'EUR', $cashflow->currency)]);

        }

        if ($cashflow->monetary_account_id > 0 && ($cashflow->type == 'received' || $cashflow->type == 'paid')) {
            $user_id = ! empty(auth()->id) ? auth()->id : 6;
            $amount  = $cashflow->erp_amount;
            if ($cashflow->type == 'paid') {
                $amount = 0 - $cashflow->erp_amount;
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
        }
    }
}
