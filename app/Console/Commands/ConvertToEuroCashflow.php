<?php

namespace App\Console\Commands;

use App\CashFlow;
use App\Currency;
use Illuminate\Console\Command;

class ConvertToEuroCashflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert-to-eur:cashflow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Conver to cashflow';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $cashflow = CashFlow::where('amount_eur', '<=', 0)->where('currency', '!=', '')->get();
        if (! $cashflow->isEmpty()) {
            foreach ($cashflow as $cas) {
                $cas->amount_eur = Currency::convert($cas->amount, 'EUR', $cas->currency);
                $cas->save();
            }
        }
    }
}
