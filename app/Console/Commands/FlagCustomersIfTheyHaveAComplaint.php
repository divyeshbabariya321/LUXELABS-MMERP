<?php

namespace App\Console\Commands;

use App\Complaint;
use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class FlagCustomersIfTheyHaveAComplaint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flag:customers-with-complaints';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report was added.']);

            Complaint::where('is_customer_flagged', 0)->chunk(1000, function ($complaints) {
                foreach ($complaints as $complaint) {
                    $customer = $complaint->customer;
                    if ($customer) {
                        dump('flagging...');
                        $customer->is_flagged = 1;
                        $customer->save();
                        $complaint->is_customer_flagged = 1;
                        $complaint->save();
                    }
                }
            });
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Complaint query finished.']);

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
