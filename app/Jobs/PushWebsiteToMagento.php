<?php

namespace App\Jobs;

use App\WebsitePushLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class PushWebsiteToMagento implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    protected $_website;
    public $tries = 5;
    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param  mixed  $website
     * @return void
     */
    public function __construct($website) {
        // Set product and website
        $this->_website = $website;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        // Set time limit
        set_time_limit(0);

        $website = $this->_website;
        try {
            if ($website) {
                Log::channel('jobPushWebsiteToMagento')->info('Website push start : ' . $website->id);

                $id = \seo2websites\MagentoHelper\MagentoHelper::pushWebsite([
                            'type' => 'website',
                            'name' => $website->name,
                            'code' => replace_dash(strtolower($website->code)),
                                ], $website->storeWebsite);

                if (!empty($id) && is_numeric($id)) {
                    Log::channel('jobPushWebsiteToMagento')->info('Website pushed with id : ' . $id);

                    $websitePushLog = new WebsitePushLog();
                    $websitePushLog->type = 'website';
                    $websitePushLog->name = $website->name;
                    $websitePushLog->message = "Website {$website->name} pushed successfully";
                    $website->websitePushLogs()->save($websitePushLog);

                    $website->platform_id = $id;

                    if ($website->save()) {
                        // start uploading
                        $stores = $website->stores;
                        if (!$stores->isEmpty()) {
                            Log::channel('jobPushWebsiteToMagento')->info('Website Store push start');
                            foreach ($stores as $store) {
                                $id = \seo2websites\MagentoHelper\MagentoHelper::pushWebsiteStore([
                                            'type' => 'store',
                                            'name' => $store->name,
                                            'code' => replace_dash(strtolower($store->code)),
                                            'website_id' => $website->platform_id,
                                                ], $website->storeWebsite);

                                if (!empty($id) && is_numeric($id)) {
                                    Log::channel('jobPushWebsiteToMagento')->info('Website Store pushed => ' . $id);

                                    $websiteStorePushLog = new WebsitePushLog();
                                    $websiteStorePushLog->type = 'store';
                                    $websiteStorePushLog->name = $store->name;
                                    $websiteStorePushLog->message = "Website Store {$store->name} pushed successfully";
                                    $store->websitePushLogs()->save($websiteStorePushLog);

                                    $store->platform_id = $id;
                                    if ($store->save()) {
                                        $storeView = $store->storeView;
                                        Log::channel('jobPushWebsiteToMagento')->info('Website Store View push start');
                                        if (!$storeView->isEmpty()) {
                                            foreach ($storeView as $sView) {
                                                $id = \seo2websites\MagentoHelper\MagentoHelper::pushWebsiteStoreView([
                                                            'type' => 'store_view',
                                                            'name' => $sView->name,
                                                            'code' => replace_by_sign($sView->code),
                                                            'website_id' => $website->platform_id,
                                                            'group_id' => $store->platform_id,
                                                                ], $website->storeWebsite);

                                                if (!empty($id) && is_numeric($id)) {
                                                    Log::channel('jobPushWebsiteToMagento')->info('Website Store View pushed => ' . $id);

                                                    $websiteStoreViewPushLog = new WebsitePushLog();
                                                    $websiteStoreViewPushLog->type = 'store_view';
                                                    $websiteStoreViewPushLog->name = $sView->name;
                                                    $websiteStoreViewPushLog->message = "Website Store view {$sView->name} pushed successfully";
                                                    $sView->websitePushLogs()->save($websiteStoreViewPushLog);

                                                    $sView->platform_id = $id;
                                                    $sView->save();
                                                } else {
                                                    $websiteStoreViewPushLog = new WebsitePushLog();
                                                    $websiteStoreViewPushLog->type = 'store_view';
                                                    $websiteStoreViewPushLog->name = $sView->name;
                                                    $websiteStoreViewPushLog->message = "Error while pushing Website Store View {$sView->name}";

                                                    $sView->websitePushLogs()->save($websiteStoreViewPushLog);
                                                    Log::channel('jobPushWebsiteToMagento')->info('Error while pushing Website Store View ' . $sView->name);
                                                }
                                            }
                                        } else {
                                            Log::channel('jobPushWebsiteToMagento')->info('Website Store view not found ' . $store->name);
                                        }
                                    } else {
                                        $websiteStorePushLog = new WebsitePushLog();
                                        $websiteStorePushLog->type = 'store';
                                        $websiteStorePushLog->name = $store->name;
                                        $websiteStorePushLog->message = "Error while saving Website Store {$store->name}";

                                        $store->websitePushLogs()->save($websiteStorePushLog);
                                        Log::channel('jobPushWebsiteToMagento')->info("Error while saving Website Store " . $store->name);
                                    }
                                } else {
                                    $websiteStorePushLog = new WebsitePushLog();
                                    $websiteStorePushLog->type = 'store';
                                    $websiteStorePushLog->name = $store->name;
                                    $websiteStorePushLog->message = "Error while pushing Website Store {$store->name}";

                                    $store->websitePushLogs()->save($websiteStorePushLog);
                                    Log::channel('jobPushWebsiteToMagento')->info("Error while pushing Website Store " . $store->name);
                                }
                            }
                        } else {
                            Log::channel('jobPushWebsiteToMagento')->info('Website Store not found ' . $website->name);
                        }
                    } else {
                        $websitePushLog = new WebsitePushLog();
                        $websitePushLog->type = 'website';
                        $websitePushLog->name = $website->name;
                        $websitePushLog->message = "Error while saving Website {$website->name}";

                        $website->websitePushLogs()->save($websitePushLog);
                        Log::channel('jobPushWebsiteToMagento')->info("Error while saving Website " . $website->name);
                    }
                } else {
                    $websitePushLog = new WebsitePushLog();
                    $websitePushLog->type = 'website';
                    $websitePushLog->name = $website->name;
                    $websitePushLog->message = "Error while pushing Website {$website->name}";

                    $website->websitePushLogs()->save($websitePushLog);
                    Log::channel('jobPushWebsiteToMagento')->info("Error while pushing Website " . $website->name);
                }
            }
        } catch (Exception $e) {
            $websitePushLog = new WebsitePushLog();
            $websitePushLog->type = 'website';
            $websitePushLog->name = 'Exception : ' . $website->name;
            $websitePushLog->message = $e->getMessage();

            Log::channel('jobPushWebsiteToMagento')->info('Exception [' . $website->name . '] >> ' . $e->getMessage());
            $website->websitePushLogs()->save($websitePushLog);

            throw new Exception($e->getMessage());
        }
    }

    public function tags() {
        return ['mageone', $this->_website->id];
    }
}
