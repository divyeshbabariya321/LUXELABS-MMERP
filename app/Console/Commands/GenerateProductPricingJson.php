<?php

namespace App\Console\Commands;

use App\CountryGroup;
use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Helpers\StatusHelper;
use App\Product;
use App\StoreWebsite;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateProductPricingJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:product-pricing-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate product pricing json';

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
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Report was added.']);

            $storeWebsite = StoreWebsite::where('is_published', 1)->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Store website query finished.']);
            $countryGroups = CountryGroup::all();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Country group query finished.']);

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Country duty query finished.']);

            // start pricing
            $products = Product::where('status_id', StatusHelper::$finalApproval)->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product query was finished.']);
            $priceReturn = [];
            if (! $products->isEmpty()) {
                foreach ($products as $product) {
                    foreach ($storeWebsite as $website) {
                        foreach ($countryGroups as $cg) {
                            $price = $product->getPrice($website->id, $cg->id);
                            foreach ($cg->groupItems as $item) {
                                $priceReturn[$website->website][$product->sku][$item->country_code]['price'] = $price;
                                $dutyPrice = $product->getDuty($item->country_code);
                                $priceReturn[$website->website][$product->sku][$item->country_code]['price']['duty'] = $dutyPrice;
                                $priceReturn[$website->website][$product->sku][$item->country_code]['price']['total'] = (float) $price['total'] + $dutyPrice;
                            }
                        }
                    }
                }
            }

            if (! Storage::disk('s3')->put('pricing-'.date('Y-m-d').'.json', json_encode($priceReturn))) {
                return false;
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
