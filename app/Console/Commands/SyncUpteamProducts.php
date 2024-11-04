<?php

namespace App\Console\Commands;

use App\Brand;
use App\Category;
use App\ConversionRate;
use App\CronJob;
use App\Helpers\LogHelper;
use App\LogRequest;
use App\Product;
use App\ProductSupplier;
use App\UpteamLog;
use Exception;
use Illuminate\Console\Command;
use Image;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class SyncUpteamProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync_upteam_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync upteam products';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            ini_set('max_execution_time', '300');
            $api = 'https://staging.upteamco.com/1api/files/1627/in/20220104-18-Cache-cd24d9267e8f2c8f866c7bc41d4a72af-1.json';
            UpteamLog::create(['log_description' => 'Api called '.$api]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Api called '.$api]);
            $ch = curl_init();

            // set url
            curl_setopt($ch, CURLOPT_URL, $api);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // $output contains the output string
            $output = curl_exec($ch);
            $products = json_decode($output); //response deocdes
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $api, 'POST', json_encode([]), $products, $httpcode, Commands\SyncUpteamProducts::class, 'handle');
            // close curl resource to free up system resources
            curl_close($ch);
            $headings = $products[0];
            unset($products[0]);
            $productWithKeys = [];
            $i = 0;
            foreach ($products as $product) {
                foreach ($product as $key => $value) {
                    $productWithKeys[$i][$headings[$key]] = $value;
                }
                $i++;
            }
            UpteamLog::create(['log_description' => 'Total Results Found '.count($productWithKeys)]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Total Results Found '.count($productWithKeys)]);

            foreach ($productWithKeys as $product) { //dd($product);
                UpteamLog::create(['log_description' => 'Product importing '.$product['product_name'].' with details '.json_encode($product)]);
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product importing '.$product['product_name'].' with details '.json_encode($product)]);
                $category = Category::where(['title' => $product['category']])->orderByDesc('id')->first();
                UpteamLog::create(['log_description' => ' Category details found'.json_encode($category)]);
                LogHelper::createCustomLogForCron($this->signature, ['message' => ' Category details found'.json_encode($category)]);
                if ($category == null) {
                    UpteamLog::create(['log_description' => $product['category'].' Category Not found for product '.$product['product_name']]);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => ' Category Not found for product '.$product['product_name']]);
                    $mainCategory = Category::firstOrCreate(['title' => $product['main_category']]);
                    $category = Category::create(['title' => $product['category'], 'parent_id' => $mainCategory['id']]);
                    UpteamLog::create(['log_description' => $product['category'].' Category created']);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => $product['category'].' Category created']);
                }
                $brand = Brand::where(['name' => $product['brand']])->first();
                if ($brand == null) {
                    UpteamLog::create(['log_description' => $product['brand'].' brand insertion']);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => $product['brand'].' brand insertion']);
                    $brand = Brand::create(['name' => $product['brand']]);
                    UpteamLog::create(['log_description' => $product['brand'].' brand inserted']);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => $product['brand'].' brand inserted']);
                }
                $measurement_size_type = 'measurement';
                $size_value = null;
                UpteamLog::create(['log_description' => ' Size check']);
                LogHelper::createCustomLogForCron($this->signature, ['message' => ' Size check']);
                if ($product['ring_size'] != '' and $product['ring_size'] > 0) {
                    UpteamLog::create(['log_description' => $product['product_name'].' ring size assigned']);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => $product['product_name'].' ring size assigned']);
                    $measurement_size_type = 'size';
                    $size_value = $product['ring_size'];
                } elseif ($product['belt_size'] != '' and $product['belt_size'] > 0) {
                    $measurement_size_type = 'size';
                    $size_value = $product['belt_size'];
                    UpteamLog::create(['log_description' => $product['product_name'].' belt size assigned']);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => $product['product_name'].' belt size assigned']);
                }
                UpteamLog::create(['log_description' => ' Size assigned']);
                LogHelper::createCustomLogForCron($this->signature, ['message' => ' Size assigned']);
                $conversionRate = ConversionRate::where('currency', 'USD')->where('to_currency', 'INR')->pluck('price')->first();
                if ($conversionRate == null) {
                    $conversionRate = 0;
                }
                $productToInsert = [
                    'sku' => $product['sku'],
                    'short_description' => $product['description'],
                    'stock' => $product['stock'],
                    'brand' => $brand['id'],
                    'name' => $product['product_name'],
                    'category' => $category['id'],
                    'composition' => $product['type_of_material'].' '.$product['name_of_material'].'/'.$product['type_of_material2'].' '.$product['name_of_material2'],
                    'color' => $product['color1'].' '.$product['shade1'],
                    'lmeasurement' => $product['length'],
                    'hmeasurement' => $product['width'],
                    'dmeasurement' => $product['depth'],
                    'size' => $size_value,
                    'measurement_size_type' => $measurement_size_type,
                    'made_in' => $product['country_of_origin'],
                    'supplier' => 'UPTEAM',
                    'supplier_id' => 5633,
                    'comments' => $product['comments'],
                    'rating' => $product['rating'],
                    'price_usd' => $product['rrp'],
                    'price_usd_special' => $product['selling_price_usd'],
                    'status_id' => 3,
                    'is_scraped' => 1,
                    'is_on_sale' => 1,
                    'price_inr' => round($conversionRate * $product['rrp']),
                    'price_inr_special' => round($conversionRate * $product['selling_price_usd']),
                ];

                UpteamLog::create(['log_description' => 'Product to insert '.json_encode($productToInsert)]);
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product to insert '.json_encode($productToInsert)]);

                UpteamLog::create(['log_description' => 'Product values to insert '.$product['product_name'].' with details '.json_encode($productToInsert)]);
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product values to insert '.$product['product_name'].' with details '.json_encode($productToInsert)]);

                $insertedProd = Product::updateOrCreate(['sku' => $product['sku']],
                    $productToInsert
                );
                ProductSupplier::updateOrCreate(['product_id' => $insertedProd->id], [
                    'product_id' => $insertedProd->id,
                    'supplier_id' => 5633,
                    'sku' => $insertedProd->sku,
                    'title' => $insertedProd->name,
                    'description' => $insertedProd->short_description,
                    'supplier_link' => $insertedProd->supplier_link,
                    'price' => $insertedProd->price_usd,
                    'stock' => $insertedProd->stock,
                    'price_special' => $insertedProd->price_usd_special,
                    'size' => $insertedProd->size,
                    'color' => $insertedProd->color,
                    'composition' => $insertedProd->composition,
                ]);

                UpteamLog::create(['log_description' => 'Product imported '.$product['product_name']]);
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product imported '.$product['product_name']]);
                $photos = explode(',', $product['photos']);
                foreach ($photos as $photo) {
                    $jpg = Image::make($product['directory'].$photo)->encode('jpg');
                    $filename = $photo;
                    $media = MediaUploader::fromString($jpg)->toDirectory('/product/'.floor($insertedProd->id / 10000))->useFilename($filename)->upload();
                    $insertedProd->attachMedia($media, config('constants.media_tags'));
                    UpteamLog::create(['log_description' => 'Image  saved for '.$product['product_name']]);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Image  saved for '.$product['product_name']]);
                }
            }

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
