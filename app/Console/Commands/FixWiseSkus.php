<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use App\ScrapedProducts;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class FixWiseSkus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sku:fix-wise';

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

            $products = ScrapedProducts::where('website', 'Wiseboutique')->get();
            foreach ($products as $product) {
                $sku = $product->sku;
                $sku2 = str_replace(' ', '', $sku);
                $sku2 = str_replace('/', '', $sku2);

                Product::where('sku', $sku)->update([
                    'sku' => $sku2,
                ]);

                $product->sku = $sku2;
                $product->save();
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
