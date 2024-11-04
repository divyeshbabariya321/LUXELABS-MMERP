<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\StoreWebsite;
use Exception;
use Illuminate\Console\Command;

class FetchStoreWebsiteOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch-store-website:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Store website orders';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $storeWebsite = StoreWebsite::all();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Store webiste query finished.']);
            foreach ($storeWebsite as $sW) {
                // if site is in magento the fetch orders
                if ($sW->website_source == 'magento') {
                    if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                        \seo2websites\MagentoHelper\MagentoHelper::fetchOrder($sW);
                    }
                }
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
