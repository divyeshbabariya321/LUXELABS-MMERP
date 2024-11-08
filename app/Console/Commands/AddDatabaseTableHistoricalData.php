<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\DatabaseHistoricalRecord;
use App\DatabaseTableHistoricalRecord;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddDatabaseTableHistoricalData extends Command
{
    const MAX_REACH_LIMIT = 100;

    const MAX_REACH_TOTAL_LIMIT = 4096;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:table-historical-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert historical data for tables';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            // get the historical data and store into the new table
            $db = DB::select('SELECT TABLE_NAME as "db_name", Round(Sum(data_length + index_length) / 1024, 1) as "db_size" FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = "BASE TABLE" AND TABLE_SCHEMA="'.config('settings.db_database').'" GROUP  BY TABLE_NAME'
            );

            $lastDb = DatabaseHistoricalRecord::where('database_name', config('settings.db_database'))->latest()->first();

            if (! empty($db)) {
                foreach ($db as $d) {
                    DatabaseTableHistoricalRecord::create([
                        'database_name' => $d->db_name,
                        'size' => $d->db_size,
                        'database_id' => $lastDb->id,
                    ]);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
