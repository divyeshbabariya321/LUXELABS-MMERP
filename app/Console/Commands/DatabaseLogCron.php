<?php

namespace App\Console\Commands;

use App\DatabaseLog;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DatabaseLogCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'databaselog:cron';

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
        $namefile = config('settings.slow_query_log_file');
        if (file_exists($namefile)) {
            $lines = @file($namefile);
            if ($lines) {
                for ($i = count($lines) - 1; $i >= 0; $i--) {
                    if (Str::contains($lines[$i], '{"url":')) {
                        $data = explode('{', $lines[$i]);
                        if ($data) {
                            $time = substr($data[0], strrpos($data[0], '2000:') + 6);
                            $logData = explode('",', $data[1]);
                            $url = str_replace('"url":', '', $logData[0]);
                            $sql = str_replace('"sql":', '', $logData[1]);
                            DatabaseLog::create(['url' => $url, 'sql_data' => $sql, 'time_taken' => $time, 'log_message' => $lines[$i]]);
                        } else {
                            dd('Wrong Database Log');
                        }
                    } else {
                        DatabaseLog::create(['log_message' => $lines[$i]]);
                    }
                }

                dd('Database Log Inserted Successfully');
            }

            dd('File not found!');
        }

        dd('File not found!');
    }
}
