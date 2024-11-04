<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class FetchMeasurementsIfScraped extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'measurements:get-from-scraped';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const PREG_MATCH_D = '/\d+/';

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

            Product::where(function ($query) {
                $query->where('lmeasurement', '')->orWhereNull('lmeasurement');
            })
                ->where(function ($query) {
                    $query->where('hmeasurement', '')->orWhereNull('hmeasurement');
                })
                ->where(function ($query) {
                    $query->where('dmeasurement', '')->orWhereNull('dmeasurement');
                })
                ->orderByDesc('created_at')->chunk(1000, function ($products) {
                    foreach ($products as $product) {
                        dump($product->id);
                        $scrapedProducts = $product->many_scraped_products;
                        foreach ($scrapedProducts as $scrapedProduct) {
                            $property = $scrapedProduct->properties['dimension'] ?? [];
                            if ($property !== [] && $property !== [null, null, null]) {
                                preg_match(self::PREG_MATCH_D, $property[0] ?? '', $lmeasurement);
                                preg_match(self::PREG_MATCH_D, $property[1] ?? '', $hmeasurement);
                                preg_match(self::PREG_MATCH_D, $property[2] ?? '', $dmeasurement);
                                dump($lmeasurement, $hmeasurement, $dmeasurement);
                                $product->lmeasurement = $lmeasurement[0] ?? null;
                                $product->hmeasurement = $hmeasurement[0] ?? null;
                                $product->dmeasurement = $dmeasurement[0] ?? null;
                                $product->save();
                            }
                        }
                    }
                });

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
