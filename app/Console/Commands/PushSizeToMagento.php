<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\Size;
use App\StoreWebsite;
use App\StoreWebsiteSize;
use Exception;
use Illuminate\Console\Command;
use seo2websites\MagentoHelper\MagentoHelper;

class PushSizeToMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'size:push-to-mangento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Size to magento';

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
            $website = StoreWebsite::where('website_source', 'magento')->where('api_token', '!=', '')->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Website query finished => '.json_encode($website->toArray())]);
            $sizes = Size::all();
            foreach ($sizes as $s) {
                echo 'Size Started  : '.$s->name;
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Size Started  : '.$s->name]);
                foreach ($website as $web) {
                    echo 'Store Started  : '.$web->website;
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Store Started  : '.$web->website]);
                    $checkSite = StoreWebsiteSize::where('size_id', $s->id)->where('store_website_id', $web->id)->where('platform_id', '>', 0)->first();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Store website size query finished. => '.json_encode($checkSite->toArray())]);

                    if (! $checkSite) {
                        $id = MagentoHelper::addSize($s, $web);
                        if (! empty($id)) {
                            StoreWebsiteSize::where('size_id', $s->id)->where('store_website_id', $web->id)->delete();

                            $sws = new StoreWebsiteSize;
                            $sws->size_id = $s->id;
                            $sws->store_website_id = $web->id;
                            $sws->platform_id = $id;
                            $sws->save();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Store website size added.']);
                        }
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
