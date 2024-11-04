<?php

namespace App\Console\Commands;

use App\Loggers\LogListMagento;
use App\LogMagentoApi;
use App\StoreMagentoApiSearchProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use seo2websites\MagentoHelper\MagentoHelperv2;

class MagentoProductApiCallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Magento-Product:Api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento Product API Call';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::channel('magentoProductApi')->info('Magento Product API Call Successfully');

        $produts = $this->fetchProducts();

        if (! $produts->isEmpty()) {
            $products = $this->processProducts($produts);

            if (! empty($products)) {
                $this->storeProducts($products);
            }
        }
    }

    private function fetchProducts()
    {
        return LogListMagento::join('products as p', 'p.id', 'log_list_magentos.product_id')
            ->where('sync_status', 'success')
            ->groupBy('product_id', 'store_website_id')
            ->orderByDesc('log_list_magentos.id')
            ->get();
    }

    private function processProducts($produts)
    {
        $languages = ['arabic', 'german', 'spanish', 'french', 'italian', 'japanese', 'korean', 'russian', 'chinese'];
        $products = [];
        $magentoHelper = new MagentoHelperv2;

        foreach ($produts as $p) {
            $sku = $p->sku.'-'.$p->color;
            $websiteId = $p->store_website_id;
            $product_ref_id = uniqid();

            $this->logMagentoApi('success', 'Product Unique id Generated:', $product_ref_id);

            try {
                $get_store_website = \App\StoreWebsite::find($websiteId);
                $result = $magentoHelper->getProductBySku($sku, $get_store_website, null, $product_ref_id);

                $this->logMagentoApi('helper_output', $result, $product_ref_id);

                if (isset($result->id)) {
                    $this->prepareProduct($result, $languages, $sku, $websiteId, $get_store_website, $magentoHelper, $product_ref_id);
                    $products[] = $result;
                } else {
                    $result->success = false;
                }
            } catch (\Exception $e) {
                $this->handleException($p, $e, $product_ref_id);
            }
        }

        return $products;
    }

    private function prepareProduct(&$result, $languages, $sku, $websiteId, $get_store_website, $magentoHelper, $product_ref_id)
    {

        $result->success = true;
        $result->log_refid = $product_ref_id;
        $result->size_chart_url = '';

        $englishDescription = $this->extractProductAttributes($result);

        foreach ($languages as $language) {
            $this->compareLanguageDescription($result, $language, $sku, $get_store_website, $magentoHelper, $englishDescription);
        }

        $result->skuid = $sku;
        $result->store_website_id = $websiteId;
    }

    private function extractProductAttributes(&$result)
    {
        $englishDescription = '';
        if (! empty($result->custom_attributes)) {
            foreach ($result->custom_attributes as $attributes) {
                if ($attributes->attribute_code == 'size_chart_url') {
                    $result->size_chart_url = $attributes->value;
                }
                if ($attributes->attribute_code == 'description') {
                    $englishDescription = $attributes->value;
                    $result->english = 'Yes';
                }
            }
        }

        return $englishDescription;
    }

    private function compareLanguageDescription(&$result, $language, $sku, $get_store_website, $magentoHelper, $englishDescription)
    {
        $firstStore = \App\Website::join('website_stores as ws', 'ws.website_id', 'websites.id')
            ->join('website_store_views as wsv', 'wsv.website_store_id', 'ws.id')
            ->where('websites.store_website_id', $get_store_website->id)
            ->where('wsv.name', 'like', $language)
            ->groupBy('ws.name')
            ->select('wsv.*')
            ->first();

        if ($firstStore) {
            $exresult = $magentoHelper->getProductBySku($sku, $get_store_website, $firstStore->code);
            if (isset($exresult->id)) {
                $differentDescription = '';

                if (! empty($exresult->custom_attributes)) {
                    foreach ($exresult->custom_attributes as $attributes) {
                        if ($attributes->attribute_code == 'description') {
                            $differentDescription = $attributes->value;
                        }
                    }
                }

                $result->{$language} = $this->isDifferentDescription($englishDescription, $differentDescription) ? 'Yes' : 'No';
            }
        }
    }

    private function isDifferentDescription($englishDescription, $differentDescription)
    {
        return trim(strip_tags(strtolower($englishDescription))) != trim(strip_tags(strtolower($differentDescription))) && ! empty($differentDescription);
    }

    private function storeProducts($products)
    {
        $data = collect($this->processProductAPIResponce($products));
        foreach ($data as $value) {
            $this->saveProduct($value);
        }
    }

    private function saveProduct($value)
    {
        $StoreMagentoApiSearchProduct = new StoreMagentoApiSearchProduct;

        $StoreMagentoApiSearchProduct->website_id = $value['store_website_id'];
        // Assign other fields similarly...

        $StoreMagentoApiSearchProduct->save();

        $this->logMagentoApi('product stored', 'Product stored in Magento API Search Product', $value['log_refid']);

        if ($value['success']) {
            $this->updateOrCreateStoreWebsiteProduct($value);
        }
    }

    private function updateOrCreateStoreWebsiteProduct($value)
    {
        $StoreWebsiteProductCheck = \App\StoreWebsiteProductCheck::where('website_id', $value['store_website_id'])->first();
        $addItem = [
            // Define $addItem data...
        ];

        if ($StoreWebsiteProductCheck == null) {
            $this->logMagentoApi('add store website', $value, $value['log_refid']);
            \App\StoreWebsiteProductCheck::create($addItem);
        } else {
            $StoreWebsiteProductCheck->update($addItem);
            $this->logMagentoApi('update store website', $value, $value['log_refid']);
        }
    }

    private function handleException($product, $exception, $product_ref_id)
    {
        $this->logMagentoApi('exception', $product.' Exception : '.$exception->getMessage(), $product_ref_id);
        Log::info('Error from LogListMagentoController 448'.$exception->getMessage());
    }

    private function logMagentoApi($apiLog, $message, $product_ref_id)
    {
        LogMagentoApi::create([
            'magento_api_search_product_id' => $product_ref_id,
            'api_log' => $apiLog,
            'message' => $message,
        ]);
    }
}
