<?php

namespace App\Jobs;
use App\WebsiteStoreView;
use App\LogRequest;

use Illuminate\Support\Facades\Log;
use App\StoreWebsite;
use App\SystemSizeRelation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class ProceesPushSystemSize implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param private $data
     *
     * @return void
     */
    public function __construct(private $data)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('ProceesPushSystemSize Handle function');
        $systemSizeManagerId = $this->data;

        //Loop the records one by one
        $this->_processSingleSystemSize($systemSizeManagerId);
    }

    private function _processSingleSystemSize($systemSizeManagerId)
    {
        try {
            $systemSizeRelations = SystemSizeRelation::select(
                'system_size_relations.id',
                'system_size_relations.size',
                'system_size_managers.category_id'
            )
                ->leftjoin('system_size_managers', 'system_size_managers.id', 'system_size_relations.system_size_manager_id')
                ->where('system_size_manager_id', $systemSizeManagerId)
                ->get();

            if (empty($systemSizeRelations)) {
                Log::info('System size manager ID not found in table' . json_encode($systemSizeManagerId));

                return false;
            }

            Log::info('System size manager Found');

            foreach ($systemSizeRelations as $key => $systemSizeRelation) {
                $categoryId = $systemSizeRelation->category_id;
                $size       = $systemSizeRelation->size;

                $allWebsites = StoreWebsite::all();

                if (! empty($allWebsites)) {
                    foreach ($allWebsites as $websitekey => $websitevalue) {
                        if (empty($websitevalue->id)) {
                            break;
                        }

                        $store_website_id = $websitevalue->id;

                        //Get stores of every single site

                        $fetchStores = WebsiteStoreView::join('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')
                            ->join('websites as w', 'w.id', 'ws.website_id')
                            ->join('store_websites as sw', 'sw.id', 'w.store_website_id')
                            ->where('sw.id', $store_website_id)
                            ->select('website_store_views.website_store_id', 'website_store_views.*', 'website_store_views.code')
                            ->get();

                        $stores = [];
                        if (! $fetchStores->isEmpty()) {
                            foreach ($fetchStores as $fetchStore) {
                                $stores[] = $fetchStore->code;
                            }
                        }

                        //get the Magento URL and token
                        $url       = $websitevalue->magento_url;
                        $api_token = $websitevalue->api_token;

                        if (! empty($url) && ! empty($api_token) && ! empty($stores)) {
                            foreach ($stores as $key => $storeValue) {
                                $urlSystemSizePush = $url . '/rest/V1/size/config';

                                $postdata  = "{\n    \"sizeConfig\": {\n        \"value\": \"$size\",\n        \"category_id\": \"$categoryId\",\n        \"store_id\": \"$storeValue\"\n    }\n}";
                                $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                                $ch        = curl_init();

                                curl_setopt($ch, CURLOPT_URL, $urlSystemSizePush);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

                                $headers   = [];
                                $headers[] = 'Authorization: Bearer ' . $api_token;
                                $headers[] = 'Content-Type: application/json';
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                                $response = curl_exec($ch);
                                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                $response = json_decode($response);

                                curl_close($ch);

                                LogRequest::log($startTime, $urlSystemSizePush, 'POST', $postdata, $response, $httpcode, ProceesPushSystemSize::class, '_processSingleSystemSize');
                            }
                        } else {
                            Log::info('URL or API token not found For Website: ' . $store_website_id);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::info('Error while pushing system size');
            Log::info($e->getMessage());
            Log::info($e->getLine());
        }
    }
}
