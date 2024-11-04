<?php

namespace App\Console\Commands;

use App\CronJob;
use App\EmailLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class EmailLogsRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emailLogs:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It is daily delete 15 days old logs from email_logs table';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            // Remove 2 weeks old logs
            $twoWeeksAgo = Carbon::now()->subWeeks(2)->format('Y-m-d');
            EmailLog::whereDate('created_at', '<=', $twoWeeksAgo)->delete();

            return 0;
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
