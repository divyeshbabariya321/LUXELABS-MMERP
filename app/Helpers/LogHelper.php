<?php

namespace App\Helpers;

use Carbon\Carbon;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LogHelper
{
    public static function createCustomLogForCron($fileName = 'laravel', $message = [])
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        $cronPrefix = $fileName;
        $fileName   = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '-', $fileName);

        //first parameter passed to Monolog\Logger sets the logging channel name
        $cronLog = new Logger($fileName);
        $cronLog->pushHandler(new StreamHandler(storage_path('logs/' . $fileName . '-' . $currentDate . '.log')), Logger::INFO);
        $cronLog->info($cronPrefix, $message);
    }
}
