<?php

namespace App\Console\Commands;

use App\AssetsManager;
use App\CashFlow;
use Illuminate\Console\Command;

class AssetsManagerDueDatePayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assetsmanagerduedate:pay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command checks due date and add to cashflow';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $results = AssetsManager::whereDate('due_date', date('Y-m-d'))->get();
        if (count($results) == 0) {
            dd($this->info(' no record exist'));
        }
        $count = count($results);

        $i = 0;
        $success = false;
        foreach ($results as $result) {
            // check already entry in cash flows
            $cashflow = CashFlow::where('date', date('Y-m-d'))->where('cash_flow_able_id', $result->id)->where('cash_flow_able_type', AssetsManager::class)->where('type', 'pending')->first();
            if (! $cashflow) {
                //create entry in table cash_flows
                CashFlow::create(
                    [
                        'description' => 'Asset Manager Payment for id '.$result->name,
                        'date' => date('Y-m-d'),
                        'amount' => $result->amount,
                        'expected' => $result->amount,
                        'actual' => $result->amount,
                        'currency' => $result->currency,
                        'type' => 'pending',
                        'cash_flow_able_id' => $result->id,
                        'cash_flow_category_id' => $result->category_id,
                        'cash_flow_able_type' => AssetsManager::class,
                    ]
                );
                $i++;
                if ($i == $count) {
                    $success = true;
                }
            }
        }
        if ($success == true) {
            dd($this->info('payment added to cashflow successfully'));
        }
    }
}
