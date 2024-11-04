<?php

namespace App\Console\Commands;

use App\CashFlow;
use App\CronJob;
use App\Helpers\LogHelper;
use Exception;
use Illuminate\Console\Command;

class CashflowOverdueStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cashflow:overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cashflow over due';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $cashflow = CashFlow::where('type', 'pending')->whereDate('date', date('Y-m-d'))->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'CashFlow model query was finished']);

            if (! $cashflow->isEmpty()) {
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'CashFlow records was found']);

                foreach ($cashflow as $ca) {
                    $received = CashFlow::where('cash_flow_able_id', $ca->cash_flow_able_id)->where('cash_flow_able_type', $ca->cash_flow_able_type)->where(function ($q) {
                        $q->orWhere('type', 'received')->orWhere('type', 'paid');
                    })->get();

                    $totalSum = 0;
                    if (! $received->isEmpty()) {
                        foreach ($received as $re) {
                            $totalSum += $re->amount_eur;
                        }
                    }

                    $amountPending = $ca->amount_eur - $totalSum;
                    if ($amountPending <= 5) {
                        $ca->type = 'settled';
                    } else {
                        $ca->type = 'overdue';
                    }

                    $ca->due_amount_eur = $amountPending;
                    $ca->save();

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'CashFlow type '.$ca->type.' and due amount '.$amountPending.' updated for CashFlow ID:'.$ca->id]);
                }
            }
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
