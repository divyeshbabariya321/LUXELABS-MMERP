<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class FixWirdSizesForAllProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sizes:fix-weird-sizes';

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

            Product::where('is_approved', 0)->orderByDesc('updated_at')->chunk(1000, function ($products) {
                foreach ($products as $product) {
                    dump('Updating..'.$product->id);
                    $product->short_description = str_replace([' ', '/', ';', '-', "\n", '\n', '_', '\\'], ' ', $product->short_description);
                    $product->composition = str_replace([' ', '/', ';', '-', "\n", '\n', '_', '\\', 'Made in', 'Made In', 'Italy', 'France', 'Portugal'], ' ', $product->composition);
                    $product->save();
                }
            });

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
