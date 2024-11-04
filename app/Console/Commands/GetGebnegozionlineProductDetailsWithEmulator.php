<?php

namespace App\Console\Commands;

use App\Brand;
use App\CronJob;
use App\CronJobReport;
use App\Product;
use App\ScrapedProducts;
use App\Services\Bots\WebsiteEmulator;
use App\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GetGebnegozionlineProductDetailsWithEmulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gnb:update-price-via-dusk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $country;

    protected $IP;

    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $letters = config('settings.scrap_alphas');
            if (strpos($letters, 'G') === false) {
                return;
            }
            $products = ScrapedProducts::where('website', 'GNB')->get();
            foreach ($products as $product) {
                $this->runFakeTraffic($product->url);
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function runFakeTraffic($url): void
    {
        $url = explode('/category', $url);
        $url = $url[0];
        $duskShell = new WebsiteEmulator;
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $content = $duskShell->emulate($this, $url, '');
        } catch (Exception $exception) {
            $content = ['', ''];
        }

        if ($content === ['', '']) {
            return;
        }

        $image = ScrapedProducts::where('sku', $content[1])->first();
        $image->price = $content[0];
        $image->save();
        if (! $image) {
            return;
        }

        if ($image->is_updated_on_server == 1) {
            return;
        }

        $this->updateDataOnProductsTable($image);
    }

    private function setCountry(): void
    {
        $this->country = 'IT';
    }

    private function updateDataOnProductsTable($image)
    {
        //get product by sku...
        //now in scraped images its in euros, update that price...
        if ($product = Product::where('sku', $image->sku)->first()) {
            $brand = Brand::find($image->brand_id);

            if (strpos($image->price, ',') !== false) {
                if (strpos($image->price, '.') !== false) {
                    if (strpos($image->price, ',') < strpos($image->price, '.')) {
                        $final_price = str_replace(',', '', $image->price);
                    }
                } else {
                    $final_price = str_replace(',', '.', $image->price);
                }
            } else {
                $final_price = $image->price;
            }

            $price = round(preg_replace('/[\&euro;€,]/', '', $final_price));

            $product->price = $price;

            if (! empty($brand->euro_to_inr)) {
                $product->price_inr = $brand->euro_to_inr * $product->price;
            } else {
                $product->price_inr = Setting::get('euro_to_inr') * $product->price;
            }

            $product->price_inr = round($product->price_inr, -3);
            $product->price_inr_special = $product->price_inr - ($product->price_inr * $brand->deduction_percentage) / 100;

            $product->price_inr_special = round($product->price_inr_special, -3);

            $product->save();
        }
    }
}
