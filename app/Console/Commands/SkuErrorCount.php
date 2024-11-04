<?php

namespace App\Console\Commands;

use App\CronJob;
use App\HistorialData;
use App\ScrapedProducts;
use Exception;
use Illuminate\Console\Command;

class SkuErrorCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sku-error:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs Every Hours stores the SKU Regrex error logs';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $logs = ScrapedProducts::where('validation_result', 'LIKE', '%SKU failed regex test%')->count();
            $data = new HistorialData;
            $data->object = 'sku_log';
            $data->measuring_point = now().' '.$logs;
            $data->value = $logs;

            $data->save();
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
