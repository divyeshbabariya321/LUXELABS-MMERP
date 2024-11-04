<?php

namespace App\Console\Commands;

use App\Product;
use App\ProductPushInformation;
use App\ProductPushInformationSummery;
use App\WebsiteProductCsv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateProductInformationFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-product:from-csv';

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
        $row = 0;
        $arr_id = [];
        $is_file_exists = null;
        $prodcutInformation = WebsiteProductCsv::pluck('path', 'store_website_id');

        foreach ($prodcutInformation as $store_website_id => $file_url) {
            $client = new Client;
            if (! $file_url) {
                $this->error('Please add url');
            } else {
                try {
                    $promise = $client->request('GET', $file_url);

                    $is_file_exists = true;
                } catch (ClientException $e) {
                    $is_file_exists = false;

                    Log::channel('product_push_information_csv')->info('file-url:'.$file_url.'  and error: '.$e->getMessage());
                    $this->error('file not exists');
                }

                if ($is_file_exists) {
                    if (($handle = fopen($file_url, 'r')) !== false) {
                        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                            $row++;
                            if ($row > 1) {
                                $availableProduct = Product::where('sku', $data[1])->select('id')->first();
                                $real_product_id = null;
                                if ($availableProduct) {
                                    $real_product_id = $availableProduct->id ?? null;
                                }

                                $updated = ProductPushInformation::updateOrCreate(
                                    ['product_id' => $data[0], 'store_website_id' => $store_website_id], [
                                        'sku' => $data[1],
                                        'status' => $data[2],
                                        'quantity' => $data[3],
                                        'stock_status' => $data[4],
                                        'is_added_from_csv' => 1,
                                        'real_product_id' => $real_product_id,
                                        'is_available' => 1,

                                    ]);
                                $arr_id[] = $updated->product_id;
                            }
                        }
                    }
                    fclose($handle);
                    ProductPushInformation::whereNotIn('product_id', $arr_id)->where('store_website_id', $store_website_id)->where('is_available', 1)->update(['is_available' => 0]);

                    $this->info('product updated successfully');
                }
            }
            ProductPushInformation::whereNotIn('product_id', $arr_id)->where('store_website_id', $store_website_id)->update(['is_available' => 0]);
        }

        $summuryOfProducts = ProductPushInformation::selectRaw('count(*) as total_product_count,sw.id as store_website_id,c.id  as       customer_id , b.id as brand_id')
            ->leftJoin('products as p', 'p.id', 'product_push_informations.real_product_id')
            ->leftJoin('brands as b', 'b.id', 'p.brand')
            ->leftJoin('categories as c', 'c.id', 'p.category')
            ->leftJoin('store_websites as sw', 'sw.id', 'product_push_informations.store_website_id')
            ->groupBy(['b.id', 'c.id', 'sw.id'])
            ->get();

        foreach ($summuryOfProducts as $summery) {
            ProductPushInformationSummery::create([
                'brand_id' => $summery->brand_id,
                'category_id' => $summery->customer_id,
                'store_website_id' => $summery->store_website_id,
                'product_push_count' => $summery->total_product_count,
            ]);
        }
    }
}
