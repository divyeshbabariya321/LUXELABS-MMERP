<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use Carbon\Carbon;
use ColorThief\ColorThief;
use Exception;
use Illuminate\Console\Command;

class GraphicaImageCropper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crop:using-graphica';

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
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $domC = ColorThief::getColor(__DIR__.'/image.jpg');
            dd($domC);

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
